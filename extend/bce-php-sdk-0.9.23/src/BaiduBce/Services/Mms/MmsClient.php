<?php

/*
* Copyright 2019 Baidu, Inc.
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

namespace BaiduBce\Services\Mms;

use BaiduBce\Auth\BceV1Signer;
use BaiduBce\BceBaseClient;
use BaiduBce\Exception\BceClientException;
use BaiduBce\Exception\BceServiceException;
use BaiduBce\Http\BceHttpClient;
use BaiduBce\Http\HttpContentTypes;
use BaiduBce\Http\HttpHeaders;
use BaiduBce\Http\HttpMethod;
use BaiduBce\Util\DateUtils;

class MmsClient extends BceBaseClient
{
    private $signer;
    private $httpClient;
    private $prefixV2 = '/v2';

    /**
     * The MvsClient constructor
     *
     * @param array $config The client configuration
     */
    function __construct(array $config)
    {
        parent::__construct($config, 'MmsClient');
        $this->signer = new BceV1Signer();
        $this->httpClient = new BceHttpClient();
    }

    /**
     * 视频入库
     *
     * @param $lib string, lib name
     * @param $source string, media source
     * @param $notification string, notification
     * @param array $options Supported options:
     *      {
     *          config: the optional bce configuration, which will overwrite the
     *                  default client configuration that was passed in constructor.
     *      }
     * @return mixed insert detail
     * @throws BceClientException|BceServiceException
     * */
    public function insertVideo($lib, $source, $notification, $options = array())
    {
        list($description) = $this->parseOptions($options, 'description');

        if (empty($lib)) {
            throw new BceClientException("The parameter lib "
                . "should NOT be null or empty string");
        }
        if (empty($source)) {
            throw new BceClientException("The parameter source "
                . "should NOT be null or empty string");
        }
        if (empty($notification)) {
            throw new BceClientException("The parameter notification "
                . "should NOT be null or empty string");
        }

        $body = array(
            'source' => $source,
            'notification' => $notification
        );

        if ($description != null) {
            $body['description'] = $description;
        }

        return $this->sendRequest(
            HttpMethod::PUT,
            $body,
            array(),
            $this->prefixV2,
            "/videolib/$lib"
        );
    }

    /**
     * 查询视频入库任务结果
     *
     * @param string $lib
     * @param string $source
     * @return mixed
     * @throws BceClientException
     * @throws BceServiceException
     */
    public function getInsertVideoResult($lib, $source)
    {
        $parameters = array(
            'source' => $source,
        );

        return $this->sendRequest(
            HttpMethod::GET,
            array(),
            $parameters,
            $this->prefixV2,
            "/videolib/$lib"
        );
    }

    /**
     * 删除视频
     *
     */
    public function deleteVideo($lib, $source)
    {
        $parameters = array(
            'source' => $source,
            'deleteVideo' => '',
        );

        return $this->sendRequest(
            HttpMethod::POST,
            array(),
            $parameters,
            $this->prefixV2,
            "/videolib/$lib"
        );
    }

    /**
     * 图片入库
     *
     * @param $lib
     * @param $source
     * @param $description
     * @return mixed
     * @throws BceClientException
     * @throws BceServiceException
     */
    public function insertImage($lib, $source, $description)
    {

        if (empty($lib)) {
            throw new BceClientException("The parameter lib "
                . "should NOT be null or empty string");
        }
        if (empty($source)) {
            throw new BceClientException("The parameter source "
                . "should NOT be null or empty string");
        }

        $body = array(
            'source' => $source,
        );

        if ($description != null) {
            $body['description'] = $description;
        }

        return $this->sendRequest(
            HttpMethod::PUT,
            $body,
            array(),
            $this->prefixV2,
            "/imagelib/$lib"
        );
    }

    /**
     * 删除图片
     *
     */
    public function deleteImage($lib, $source)
    {
        $parameters = array(
            'source' => $source,
            'deleteImage' => '',
        );

        return $this->sendRequest(
            HttpMethod::POST,
            array(),
            $parameters,
            $this->prefixV2,
            "/imagelib/$lib"
        );
    }

    /**
     * 以图搜图
     *
     * @param $lib
     * @param $source
     * @param $description
     * @return mixed
     * @throws BceClientException
     * @throws BceServiceException
     */
    public function searchImageByImage($lib, $source, $description)
    {
        $body = array(
            'source' => $source
        );

        if ($description != null) {
            $body['description'] = $description;
        }

        return $this->sendRequest(
            HttpMethod::POST,
            $body,
            array('searchByImage' => ''),
            $this->prefixV2,
            "/imagelib/$lib"
        );
    }

    /**
     * 图片搜视频
     *
     * @param $lib
     * @param $source
     * @param $description
     * @return mixed
     * @throws BceClientException
     * @throws BceServiceException
     */
    public function searchVideoByImage($lib, $source, $description)
    {
        $body = array(
            'source' => $source
        );
        if ($description != null) {
            $body['description'] = $description;
        }

        return $this->sendRequest(
            HttpMethod::POST,
            $body,
            array('searchByImage' => ''),
            $this->prefixV2,
            "/videolib/$lib"
        );
    }

    /**
     * 视频搜视频
     *
     * @param $lib
     * @param $source
     * @param $description
     * @return mixed
     * @throws BceClientException
     * @throws BceServiceException
     */
    public function searchVideoByVideo($lib, $source, $description)
    {
        $body = array(
            'source' => $source
        );
        if ($description != null) {
            $body['description'] = $description;
        }

        return $this->sendRequest(
            HttpMethod::POST,
            $body,
            array('searchByVideo' => ''),
            $this->prefixV2,
            "/videolib/$lib"
        );
    }

    /**
     * 查询视频搜视频任务结果
     *
     * @param $lib
     * @param $source
     * @return mixed
     * @throws BceClientException
     * @throws BceServiceException
     */
    public function getSearchVideoByVideoResult($lib, $source)
    {
        return $this->sendRequest(
            HttpMethod::GET,
            array(),
            array('searchByVideo' => '', 'source' => $source),
            $this->prefixV2,
            "/videolib/$lib"
        );
    }

    /**
     * Create HttpClient and send request
     * @param string $httpMethod The Http request method
     * @param array $body request body
     * @param array $parameters request parameters
     * @param string $prefix prefix of request uri
     * @param string $requestPath request path
     * @return mixed The Http response and headers
     * @throws BceClientException|BceServiceException request exception
     */
    private function sendRequest($httpMethod, array $body, array $parameters, $prefix, $requestPath = '/')
    {
        $headers = array(
            HttpHeaders::CONTENT_TYPE => HttpContentTypes::JSON,
            HttpHeaders::DATE => DateUtils::formatRfc822Date(new \DateTime()),
        );

        $path = $prefix . $requestPath;
        $response = $this->httpClient->sendRequest(
            $this->config,
            $httpMethod,
            $path,
            json_encode($body),
            $headers,
            $parameters,
            $this->signer
        );

        return $this->parseJsonResult($response['body']);
    }

}