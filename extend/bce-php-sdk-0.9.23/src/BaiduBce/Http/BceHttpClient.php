<?php
/*
* Copyright 2014 Baidu, Inc.
*
* Licensed under the Apache License, Version 2.0 (the "License"); you may not
* use this file except in compliance with the License. You may obtain a copy of
* the License at
*
* Http://www.apache.org/licenses/LICENSE-2.0
*
* Unless required by applicable law or agreed to in writing, software
* distributed under the License is distributed on an "AS IS" BASIS, WITHOUT
* WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied. See the
* License for the specific language governing permissions and limitations under
* the License.
*/

namespace BaiduBce\Http;

use BaiduBce\Auth\SignerInterface;
use BaiduBce\Bce;
use BaiduBce\BceClientConfigOptions;
use BaiduBce\Exception\BceClientException;
use BaiduBce\Exception\BceServiceException;
use BaiduBce\Util\HttpUtils;
use BaiduBce\Util\DateUtils;


use Guzzle\Plugin\Log\LogPlugin;
use Guzzle\Http\EntityBody;
use Guzzle\Http\ReadLimitEntityBody;

use Psr\Http\Message\StreamInterface;

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use GuzzleHttp\Middleware;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\MessageFormatter;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Psr7\Utils;
use BaiduBce\Log\LogFactory;


/**
 * Standard Http request of BCE.
 */
class BceHttpClient
{
    /**
     * @var Client
     */
    private $guzzleClient;

    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct()
    {
        $stack = HandlerStack::create();

        // 创建日志记录器
        $this->logger = LogFactory::getLogger(get_class($this));

        if (!($this->logger instanceof NullLogger)) {
            $logger = new Logger('http_logger');
            $logger->pushHandler(new StreamHandler('path/to/your.log', Logger::DEBUG));
            $stack->push(
                Middleware::log(
                    $logger,
                    new MessageFormatter('{method} {uri} HTTP/{version} {req_body} - {res_status} {res_body}')
                )
            );
        }

        // 创建 Guzzle 客户端
        $this->guzzleClient = new Client(['handler' => $stack]);


        // $this->guzzleClient = new Client();
        // $this->logger = LogFactory::getLogger(get_class($this));
        // if (!($this->logger instanceof NullLogger)) {
        //     $logPlugin = new LogPlugin(
        //         new GuzzleLogAdapter(),
        //         MessageFormatter::DEFAULT_FORMAT
        //     );
        //     $this->guzzleClient->addSubscriber($logPlugin);
        // }
    }

    /**
     * @param mixed $body The request body.
     * @return number
     */
    private function guessContentLength($body)
    {
        if (is_null($body)) {
            return 0;
        } else {
            if (is_string($body)) {
                return strlen($body);
            } else {
                if (is_resource($body)) {
                    $stat = fstat($body);
                    return $stat['size'];
                } else {
                    if (is_object($body) && method_exists($body, 'getSize')) {
                        return $body->getSize();
                    }
                }
            }
        }
        throw new \InvalidArgumentException(
            sprintf('No %s is specified.', HttpHeaders::CONTENT_LENGTH)
        );
    }


    /**
     * Send request to BCE.
     *
     * @param array $config
     * @param string $httpMethod The Http request method, uppercase.
     * @param string $path The resource path.
     * @param string|resource $body The Http request body.
     * @param array $headers The extra Http request headers.
     * @param array $params The extra Http url query strings.
     * @param SignerInterface $signer This function will generate authorization header.
     * @param resource|string $outputStream Write the Http response to this stream.
     *
     * @return \Guzzle\Http\Message\Response body and http_headers
     *
     * @throws BceClientException
     * @throws BceServiceException
     */
    public function sendRequest(
        array $config,
        $httpMethod,
        $path,
        $body,
        array $headers,
        array $params,
        SignerInterface $signer,
        $outputStream = null,
        $options = array()
    ) {
        $headers[HttpHeaders::USER_AGENT] =
            sprintf(
                'bce-sdk-php/%s/%s/%s',
                Bce::SDK_VERSION,
                php_uname(),
                phpversion()
            );
        if (!isset($headers[HttpHeaders::BCE_DATE])) {
            $now = new \DateTime();
            $now->setTimezone(DateUtils::$UTC_TIMEZONE);
            $headers[HttpHeaders::BCE_DATE] =
                DateUtils::formatAlternateIso8601Date($now);
        }
        list($hostUrl, $hostHeader) =
            HttpUtils::parseEndpointFromConfig($config);
        $headers[HttpHeaders::HOST] = $hostHeader;
        $url = $hostUrl . HttpUtils::urlEncodeExceptSlash($path);
        $queryString = HttpUtils::getCanonicalQueryString($params, false);
        if ($queryString !== '') {
            $url .= "?$queryString";
        }

        if (!isset($headers[HttpHeaders::CONTENT_LENGTH])) {
            $headers[HttpHeaders::CONTENT_LENGTH] =
                $this->guessContentLength($body);
        }
        $entityBody = null;
        if ($headers[HttpHeaders::CONTENT_LENGTH] == 0) {
            //if passing a stream and content length is 0, guzzle will remove
            //"Content-Length:0" from header, to work around this, we have to 
            //set body to a empty string
            $entityBody = "";
        } else if (is_resource($body)) {
            $offset = ftell($body);
            $original = EntityBody::factory($body);
            $entityBody = new ReadLimitEntityBody($original, $headers[HttpHeaders::CONTENT_LENGTH], $offset);
        } else {
            $entityBody = $body;
        }

        $credentials = $config[BceClientConfigOptions::CREDENTIALS];
        // if the request is send through the STS certification
        if(array_key_exists(BceClientConfigOptions::SESSION_TOKEN, $credentials)) {
            $headers[HttpHeaders::BCE_SESSION_TOKEN] = $credentials[BceClientConfigOptions::SESSION_TOKEN];
        }
        $headers[HttpHeaders::AUTHORIZATION] = $signer->sign(
            $credentials,
            $httpMethod,
            $path,
            $headers,
            $params,
            $options
        );

        if (LogFactory::isDebugEnabled()) {
            $this->logger->debug('HTTP method: ' . $httpMethod);
            $this->logger->debug('HTTP url: ' . $url);
            $this->logger->debug('HTTP headers: ' . print_r($headers, true));
        }

        $requestOptions = [
            'headers' => $headers,
            'body' => $entityBody,
            'http_errors' => false, // Do not throw exceptions for HTTP errors
        ];
        if (isset($config[BceClientConfigOptions::CONNECTION_TIMEOUT_IN_MILLIS])) {
            $requestOptions['connect_timeout'] =
                $config[BceClientConfigOptions::CONNECTION_TIMEOUT_IN_MILLIS] / 1000.0;
        }

        if (isset($config[BceClientConfigOptions::SOCKET_TIMEOUT_IN_MILLIS])) {
            $requestOptions['timeout'] =
                $config[BceClientConfigOptions::SOCKET_TIMEOUT_IN_MILLIS] / 1000.0;
        }

        // 创建请求
        $request = new Request($httpMethod, $url, $headers, $entityBody);

        try {
            $response = $this->guzzleClient->send($request, $requestOptions);
        } catch (RequestException $e) {
            throw new BceClientException($e->getMessage(), $e->getCode(), $e);
        }
        if ($response->getStatusCode() >= 100 && $response->getStatusCode() < 200) {
            throw new BceClientException('Cannot handle 1xx HTTP status code');
        }
        if ($response->hasHeader('Transfer-Encoding') && $response->getHeaderLine('Transfer-Encoding') === 'chunked') {
        // 检查 Content-Type 是否为 JSON
            if (strpos($response->getHeaderLine('Content-Type'), 'application/json') !== false) {
                // 获取响应主体内容
                $responseBody = json_decode($response->getBody()->getContents(), true);

                // 检查是否存在 InternalError
                if (isset($responseBody['code']) && $responseBody['code'] === 'InternalError') {
                    // 返回一个新的响应，设置 HTTP 状态码为 500
                    return $response->withStatus(500);
                }
            }

        }
        if ($response->getStatusCode() >= 300) {
            // 获取请求 ID（假设是通过头部传递的）
            $requestId = $response->hasHeader(HttpHeaders::BCE_REQUEST_ID) ? $response->getHeaderLine(HttpHeaders::BCE_REQUEST_ID) : null;
            $message = $response->getReasonPhrase();
            $code = null;

            // 检查 Content-Type 是否为 JSON
            if (strpos($response->getHeaderLine('Content-Type'), 'application/json') !== false) {
                try {
                    // 解析响应体
                    $responseBody = json_decode($response->getBody()->getContents(), true);

                    if (isset($responseBody['message'])) {
                        $message = $responseBody['message'];
                    }
                    if (isset($responseBody['code'])) {
                        $code = $responseBody['code'];
                    }
                } catch (\Exception $e) {
                    // 忽略解析错误
                    $this->logger->warning(
                        'Failed to parse error response body: ' . $e->getMessage()
                    );
                }
            }

            // 抛出自定义异常
            throw new BceServiceException(
                $requestId,
                $code,
                $message,
                $response->getStatusCode()
            );
        }
        $body = $response->getBody();
        // 处理响应
        $this->handleResponse($body, $outputStream);
        return array(
            'headers' => $this->parseHeaders($response),
            'body' => $body
        );
    }
    function handleResponse(StreamInterface $stream, $outputStream = null)
    {
        if ($outputStream === null) {
            // 获取响应体内容
            $body = (string) $stream;
        } else {
            $body = null;
            // 确保流从开始位置读取
            $stream->rewind();
            // 将响应体写入输出流
            while (!$stream->eof()) {
                fwrite($outputStream, $stream->read(8192)); // 逐块读取并写入
            }
        }
    }
    /**
     * @param \Guzzle\Http\Message\Response $guzzleResponse
     * @return array
     */
    // private function parseHeaders($guzzleResponse)
    // {
    //     $responseHeaders = array();
    //     foreach ($guzzleResponse->getHeaders() as $header) {
    //         $value = $header->toArray();
    //         $responseHeaders[$header->getName()] = $value[0];
    //     }
    //     return $responseHeaders;
    // }
    private function parseHeaders($response)
    {
        $responseHeaders = [];
        
        // 获取所有头部信息
        foreach ($response->getHeaders() as $name => $values) {
            // 选择第一个值作为头部值
            $responseHeaders[$name] = $values[0];
        }

        return $responseHeaders;
    }
}
