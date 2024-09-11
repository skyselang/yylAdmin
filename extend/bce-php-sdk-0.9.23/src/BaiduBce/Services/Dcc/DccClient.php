<?php
/*
* Copyright 2017 Baidu, Inc.
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

namespace BaiduBce\Services\Dcc;

use BaiduBce\Auth\BceV1Signer;
use BaiduBce\BceBaseClient;
use BaiduBce\Http\BceHttpClient;
use BaiduBce\Http\HttpHeaders;
use BaiduBce\Http\HttpMethod;
use BaiduBce\Http\HttpContentTypes;

/**
 * This module provides a client class for DCC.
 */
class DccClient extends BceBaseClient
{

    private $signer;
    private $httpClient;
    private $prefix = '/v1';

    /**
     * The BccClient constructor.
     *
     * @param array $config The client configuration
     */
    function __construct(array $config)
    {
        parent::__construct($config, 'dcc');
        $this->signer = new BceV1Signer();
        $this->httpClient = new BceHttpClient();
    }

    /**
     * Return a list of dedicatedHosts owned by the authenticated user.
     *
     * @param string $zoneName
     *          the name of available zone.
     *
     * @param string $marker
     *          The optional parameter marker specified in the original request to specify
     *          where in the results to begin listing.
     *          Together with the marker, specifies the list result which listing should begin.
     *          If the marker is not specified, the list result will listing from the first one.
     *
     * @param int $maxKeys
     *          The optional parameter to specifies the max number of list result to return.
     *          The default value is 1000.
     *
     * @param array $options
     *          The optional bce configuration, which will overwrite the
     *          default configuration that was passed while creating DccClient instance.
     *
     * @return mixed
     */
    public function listDedicatedHosts($zoneName=null, $marker=null, $maxKeys=null, $options = array())
    {
        list($config) = $this->parseOptions($options, 'config');
        $params = array();
        if ($marker !== null) {
            $params['marker'] = $marker;
        }
        if ($maxKeys !== null) {
            $params['maxKeys'] = $maxKeys;
        }
        if ($zoneName !== null) {
            $params['zoneName'] = $zoneName;
        }

        return $this->sendRequest(
            HttpMethod::GET,
            array(
                'config' => $config,
                'params' => $params,
            ),
            '/dedicatedHost'
        );
    }

    /**
     * Get the detail information of specified dedicatedHost.
     *
     * @param string $hostId
     *          The id of dedicatedHost
     *
     * @param array $options
     *          The optional bce configuration, which will overwrite the
     *          default configuration that was passed while creating DccClient instance.
     *
     * @return mixed
     */
    public function getDedicatedHost($hostId, $options = array())
    {
        list($config) = $this->parseOptions($options, 'config');
        if (empty($hostId)) {
            throw new \InvalidArgumentException(
                'request $hostId should not be empty.'
            );
        }

        return $this->sendRequest(
            HttpMethod::GET,
            array(
                'config' => $config,
            ),
            '/dedicatedHost/' . $hostId
        );
    }


    /**
     * Create HttpClient and send request
     *
     * @param string $httpMethod
     *          The Http request method
     *
     * @param array $varArgs
     *          The extra arguments
     *
     * @param string $requestPath
     *          The Http request uri
     *
     * @return mixed The Http response and headers.
     */
    private function sendRequest($httpMethod, array $varArgs, $requestPath = '/')
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
        $path = $this->prefix . $requestPath;
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