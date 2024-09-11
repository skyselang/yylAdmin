<?php
/*
* Copyright 2015 Baidu, Inc.
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

namespace BaiduBce\Services\Mvs;

use BaiduBce\Auth\BceV1Signer;
use BaiduBce\BceBaseClient;
use BaiduBce\Exception\BceClientException;
use BaiduBce\Http\BceHttpClient;
use BaiduBce\Http\HttpContentTypes;
use BaiduBce\Http\HttpHeaders;
use BaiduBce\Http\HttpMethod;

class MvsClient extends BceBaseClient
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
        parent::__construct($config, 'MvsClient');
        $this->signer = new BceV1Signer();
        $this->httpClient = new BceHttpClient();
    }

    /**
     * Insert one media.
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
     * @throws BceClientException
     */
    public function insertVideo($lib, $source, $notification, $options = array())
    {
        list($config, $description) = $this->parseOptions($options, 'config', 'description');

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
            array(
                'config' => $config,
                'body' => json_encode($body),
            ),
            $this->prefixV2,
            "/videolib/$lib"
        );
    }

    /**
     * Search video by video.
     *
     * @param $lib string, lib name
     * @param $source string, media source
     * @param $notification string, notification
     * @param array $options Supported options:
     *      {
     *          config: the optional bce configuration, which will overwrite the
     *                  default client configuration that was passed in constructor.
     *      }
     * @return mixed search video by video detail
     * @throws BceClientException
     */
    public function searchVideoByVideo($lib, $source, $notification, $options = array())
    {
        list($config, $description) = $this->parseOptions($options, 'config', 'description');

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
            HttpMethod::POST,
            array(
                'config' => $config,
                'body' => json_encode($body),
                'params' => array('searchByVideo' => '')
            ),
            $this->prefixV2,
            "/videolib/$lib"
        );
    }


    /**
     * Insert one image.
     *
     * @param $lib string, lib name
     * @param $source string, media source
     * @param array $options Supported options:
     *      {
     *          config: the optional bce configuration, which will overwrite the
     *                  default client configuration that was passed in constructor.
     *      }
     * @return mixed insert detail
     * @throws BceClientException
     */
    public function insertImage($lib, $source, $options = array())
    {
        list($config, $description) = $this->parseOptions($options, 'config', 'description');

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
            array(
                'config' => $config,
                'body' => json_encode($body),
                'params' => array('search' => ''),
            ),
            $this->prefixV2,
            "/imagelib/$lib"
        );
    }

    /**
     * Insert one image.
     *
     * @param $lib string, lib name
     * @param $source string, media source
     * @param array $options Supported options:
     *      {
     *          config: the optional bce configuration, which will overwrite the
     *                  default client configuration that was passed in constructor.
     *      }
     * @return mixed insert detail
     * @throws BceClientException
     */
    public function searchImageByImage($lib, $source, $options = array())
    {
        list($config, $description) = $this->parseOptions($options, 'config', 'description');

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
            HttpMethod::POST,
            array(
                'config' => $config,
                'body' => json_encode($body),
                'params' => array('searchByImage' => ''),
            ),
            $this->prefixV2,
            "/imagelib/$lib"
        );
    }


    /**
     * Create HttpClient and send request
     * @param string $httpMethod The Http request method
     * @param array $varArgs The extra arguments
     * @param string $requestPath The Http request uri
     * @return mixed The Http response and headers.
     */
    private function sendRequest($httpMethod, array $varArgs, $prefix, $requestPath = '/')
    {
        $defaultArgs = array(
            'config' => array(),
            'body' => null,
            'headers' => array(),
            'params' => array(),
        );

        $args = array_merge($defaultArgs, $varArgs);
        if (empty($args['config'])) {
            $config = $this->config;
        } else {
            $config = array_merge(
                array(),
                $this->config,
                $args['config']
            );
        }
        if (!isset($args['headers'][HttpHeaders::CONTENT_TYPE])) {
            $args['headers'][HttpHeaders::CONTENT_TYPE] = HttpContentTypes::JSON;
        }
        $path = $prefix . $requestPath;
        $response = $this->httpClient->sendRequest(
            $config,
            $httpMethod,
            $path,
            $args['body'],
            $args['headers'],
            $args['params'],
            $this->signer
        );

        $result = $this->parseJsonResult($response['body']);

        return $result;
    }
}
