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

namespace BaiduBce\Services\Vcr;

use BaiduBce\Auth\BceV1Signer;
use BaiduBce\BceBaseClient;
use BaiduBce\Exception\BceClientException;
use BaiduBce\Http\BceHttpClient;
use BaiduBce\Http\HttpContentTypes;
use BaiduBce\Http\HttpHeaders;
use BaiduBce\Http\HttpMethod;
use BaiduBce\Util\DateUtils;

class VcrClient extends BceBaseClient
{

    private $signer;
    private $httpClient;

    /**
     * The VcrClient constructor
     *
     * @param array $config The client configuration
     */
    function __construct(array $config)
    {
        parent::__construct($config, 'VcrClient');
        $this->signer = new BceV1Signer();
        $this->httpClient = new BceHttpClient();
    }

    /**
     * Check a media.
     *
     * @param $source string, media source
     * @param array $options Supported options:
     *      {
     *          config: the optional bce configuration, which will overwrite the
     *                  default client configuration that was passed in constructor.
     *          auth: string, media auth
     *          description: string, media description
     *          preset: string, analyze preset name
     *          notification: string, notification name
     *      }
     * @return mixed nothing
     * @throws BceClientException
     */
    public function putMedia($source, $options = array())
    {
        list($config, $auth, $description, $preset, $notification) = $this->parseOptions($options,
            'config', 'auth', 'description', 'preset', 'notification');

        if (empty($source)) {
            throw new BceClientException("The parameter source "
                . "should NOT be null or empty string");
        }

        $body = array(
            'source' => $source,
        );
        if ($auth !== null) {
            $body['auth'] = $auth;
        }
        if ($description !== null) {
            $body['description'] = $description;
        }
        if ($preset !== null) {
            $body['preset'] = $preset;
        }
        if ($notification !== null) {
            $body['notification'] = $notification;
        }


        return $this->sendRequest(
            HttpMethod::PUT,
            array(
                'config' => $config,
                'body' => json_encode($body),
            ),
            "/v1/media"
        );
    }

    /**
     * Get check result of media.
     *
     * @param $source string, media source
     * @param array $options Supported options:
     *      {
     *          config: the optional bce configuration, which will overwrite the
     *                  default client configuration that was passed in constructor.
     *      }
     * @return mixed media check result
     * @throws BceClientException
     */
    public function getMedia($source, $options = array())
    {
        list($config) = $this->parseOptions($options, 'config');

        if (empty($source)) {
            throw new BceClientException("The parameter source "
                . "should NOT be null or empty string");
        }

        return $this->sendRequest(
            HttpMethod::GET,
            array(
                'config' => $config,
                'params' => array('source' => $source),
            ),
            "/v1/media"
        );
    }

    /**
     * Check a audio.
     *
     * @param $source string, audio source
     * @param array $options Supported options:
     *      {
     *          config: the optional bce configuration, which will overwrite the
     *                  default client configuration that was passed in constructor.
     *          preset: string, check preset name
     *          notification: string, notification name
     *      }
     * @return mixed nothing
     * @throws BceClientException
     */
    public function putAudio($source, $options = array())
    {
        list($config, $auth, $description, $preset, $notification) = $this->parseOptions($options,
            'config', 'auth', 'description', 'preset', 'notification');

        if (empty($source)) {
            throw new BceClientException("The parameter source "
                . "should NOT be null or empty string");
        }

        $body = array(
            'source' => $source
        );
        if ($auth !== null) {
            $body['auth'] = $auth;
        }
        if ($description !== null) {
            $body['description'] = $description;
        }
        if ($preset !== null) {
            $body['preset'] = $preset;
        }
        if ($notification !== null) {
            $body['notification'] = $notification;
        }
        return $this->sendRequest(
            HttpMethod::PUT,
            array(
                'config' => $config,
                'body' => json_encode($body),
            ),
            "/v2/audio"
        );
    }


    /**
     * Get check result of audio.
     *
     * @param $source string, audio source
     * @param array $options Supported options:
     *      {
     *          config: the optional bce configuration, which will overwrite the
     *                  default client configuration that was passed in constructor.
     *      }
     * @return mixed audio check result
     * @throws BceClientException
     */
    public function getAudio($source, $options = array())
    {
        list($config) = $this->parseOptions($options, 'config');

        if (empty($source)) {
            throw new BceClientException("The parameter source "
                . "should NOT be null or empty string");
        }

        return $this->sendRequest(
            HttpMethod::GET,
            array(
                'config' => $config,
                'params' => array('source' => $source),
            ),
            "/v2/audio"
        );
    }


    /**
     * Get a preset.
     *
     * @param $name string, preset name
     * @param array $options Supported options:
     *      {
     *          config: the optional bce configuration, which will overwrite the
     *                  default client configuration that was passed in constructor.
     *      }
     * @return mixed preset detail
     * @throws BceClientException
     */
    public function getPreset($name, $options = array())
    {
        list($config) = $this->parseOptions($options, 'config');

        if (empty($name)) {
            throw new BceClientException("The parameter name "
                . "should NOT be null or empty string");
        }

        return $this->sendRequest(
            HttpMethod::GET,
            array(
                'config' => $config,
            ),
            "/v1/preset/$name"
        );
    }

    /**
     * List presets.
     *
     * @param array $options Supported options:
     *      {
     *          config: the optional bce configuration, which will overwrite the
     *                  default client configuration that was passed in constructor.
     *      }
     * @return mixed preset lists
     */
    public function listPresets($options = array())
    {
        list($config) = $this->parseOptions($options, 'config');

        return $this->sendRequest(
            HttpMethod::GET,
            array(
                'config' => $config,
            ),
            '/v1/preset'
        );
    }

    /**
     * Check a image
     *
     * @param $source string, image source
     *      vcr support 2 image source format:
     *      1. bos path, source="bos://{bucket}/{object}, e.g. bos://test_bkt/test_dir/test_image.png
     *      2. http/https url
     * @param array $options Supported options:
     *      {
     *          config: the optional bce configuration, which will overwrite the
     *                  default client configuration that was passed in constructor.
     *      }
     * @return mixed image check result
     * @throws BceClientException
     */
    public function putImage($source, $options = array())
    {
        list($config, $preset) = $this->parseOptions($options, 'config', 'preset');

        if (empty($source)) {
            throw new BceClientException("The parameter source "
                . "should NOT be null or empty string");
        }

        $body = array(
            'source' => $source
        );
        if ($preset !== null) {
            $body['preset'] = $preset;
        }

        return $this->sendRequest(
            HttpMethod::PUT,
            array(
                'config' => $config,
                'body' => json_encode($body),
            ),
            "/v1/image"
        );
    }

    /**
     * Check a image async, support gif
     *
     * @param $source string, image source
     *      vcr support 2 image source format:
     *      1. bos path, source="bos://{bucket}/{object}, e.g. bos://test_bkt/test_dir/test_image.png
     *      2. http/https url
     * @param array $options Supported options:
     *      {
     *          config: the optional bce configuration, which will overwrite the
     *                  default client configuration that was passed in constructor.
     *          description: string, image description
     *          preset: string, analyze preset name
     *          notification: string, notification name
     *      }
     * @return mixed image check result
     * @throws BceClientException
     */
    public function putImageAsync($source, $options = array())
    {
        list($config, $description, $preset, $notification) = $this->parseOptions($options,
            'config', 'description', 'preset', 'notification');

        if (empty($source)) {
            throw new BceClientException("The parameter source "
                . "should NOT be null or empty string");
        }

        $body = array(
            'source' => $source,
        );
        if ($description !== null) {
            $body['description'] = $description;
        }
        if ($preset !== null) {
            $body['preset'] = $preset;
        }
        if ($notification !== null) {
            $body['notification'] = $notification;
        }

        return $this->sendRequest(
            HttpMethod::PUT,
            array(
                'config' => $config,
                'body' => json_encode($body),
            ),
            "/v2/image"
        );
    }


    /**
     * Get check result of image async check.
     *
     * @param $source string, media source
     * @param array $options Supported options:
     *      {
     *          config: the optional bce configuration, which will overwrite the
     *                  default client configuration that was passed in constructor.
     *          preset: string, analyze preset name
     *      }
     * @return mixed media check result
     * @throws BceClientException
     */
    public function getImageAsync($source, $options = array())
    {
        list($config, $preset) = $this->parseOptions($options, 'config', 'preset');

        if (empty($source)) {
            throw new BceClientException("The parameter source "
                . "should NOT be null or empty string");
        }
        return $this->sendRequest(
            HttpMethod::GET,
            array(
                'config' => $config,
                'params' => array('source' => $source, 'preset' => $preset),
            ),
            "/v2/image"
        );
    }

    /**
     * Check a text
     *
     * @param $text string, text to check
     * @param array $options Supported options:
     *      {
     *          config: the optional bce configuration, which will overwrite the
     *                  default client configuration that was passed in constructor.
     *      }
     * @return mixed text check result
     * @throws BceClientException
     */
    public function putText($text, $options = array())
    {
        list($config, $preset) = $this->parseOptions($options, 'config', 'preset');

        if (empty($text)) {
            throw new BceClientException("The parameter source "
                . "should NOT be null or empty string");
        }

        $body = array(
            'text' => $text
        );
        if ($preset !== null) {
            $body['preset'] = $preset;
        }

        return $this->sendRequest(
            HttpMethod::PUT,
            array(
                'config' => $config,
                'body' => json_encode($body),
            ),
            "/v1/text"
        );
    }

    /**
     * add face image
     * @param $lib string, face lib name.
     * @param $brief string, brief name.
     * @param $image string, image url.
     * @param array $options Supported options:
     *      {
     *          config: the optional bce configuration, which will overwrite the
     *                  default client configuration that was passed in constructor.
     *      }
     * @return mixed nothing
     * @throws BceClientException
     */
    public function addFaceImage($lib, $brief, $image, $options = array())
    {
        list($config) = $this->parseOptions($options, 'config');
        if (empty($lib)) {
            throw new BceClientException("The parameter lib "
                . "should NOT be null or empty string");
        }
        if (empty($brief)) {
            throw new BceClientException("The parameter brief "
                . "should NOT be null or empty string");
        }
        if (empty($image)) {
            throw new BceClientException("The parameter image "
                . "should NOT be null or empty string");
        }
        $body = array(
            'brief' => $brief,
            'image' => $image
        );
        return $this->sendRequest(
            HttpMethod::POST,
            array(
                'config' => $config,
                'body' => json_encode($body),
            ),
            "/v1/face/lib/$lib"
        );
    }
    /**
     * delete face image
     * @param $lib string, face lib name.
     * @param $brief string, brief name.
     * @param $image string, image url.
     * @param array $options Supported options:
     *      {
     *          config: the optional bce configuration, which will overwrite the
     *                  default client configuration that was passed in constructor.
     *      }
     * @return mixed nothing
     * @throws BceClientException
     */
    public function delFaceImage($lib, $brief, $image, $options = array())
    {
        list($config) = $this->parseOptions($options, 'config');
        if (empty($lib)) {
            throw new BceClientException("The parameter lib "
                . "should NOT be null or empty string");
        }
        if (empty($brief)) {
            throw new BceClientException("The parameter brief "
                . "should NOT be null or empty string");
        }
        if (empty($image)) {
            throw new BceClientException("The parameter image "
                . "should NOT be null or empty string");
        }
        $params = array(
            'brief' => $brief,
            'image' => $image
        );
        return $this->sendRequest(
            HttpMethod::DELETE,
            array(
                'config' => $config,
                'params' => $params,
            ),
            "/v1/face/lib/$lib"
        );
    }
    /**
     * get face brief
     * @param $lib string, face lib name.
     * @param $brief string, brief name.
     * @param array $options Supported options:
     *      {
     *          config: the optional bce configuration, which will overwrite the
     *                  default client configuration that was passed in constructor.
     *      }
     * @return mixed face brief result
     * @throws BceClientException
     */
    public function getFaceBrief($lib, $brief, $options = array())
    {
        list($config) = $this->parseOptions($options, 'config');
        if (empty($lib)) {
            throw new BceClientException("The parameter lib "
                . "should NOT be null or empty string");
        }
        if (empty($brief)) {
            throw new BceClientException("The parameter brief "
                . "should NOT be null or empty string");
        }
        $params = array(
            'brief' => $brief,
        );
        return $this->sendRequest(
            HttpMethod::GET,
            array(
                'config' => $config,
                'params' => $params,
            ),
            "/v1/face/lib/$lib"
        );
    }
    /**
     * delete face brief
     * @param $lib string, face lib name.
     * @param $brief string, brief name.
     * @param array $options Supported options:
     *      {
     *          config: the optional bce configuration, which will overwrite the
     *                  default client configuration that was passed in constructor.
     *      }
     * @return mixed nothing
     * @throws BceClientException
     */
    public function delFaceBrief($lib, $brief, $options = array())
    {
        list($config) = $this->parseOptions($options, 'config');
        if (empty($lib)) {
            throw new BceClientException("The parameter lib "
                . "should NOT be null or empty string");
        }
        if (empty($brief)) {
            throw new BceClientException("The parameter brief "
                . "should NOT be null or empty string");
        }
        return $this->sendRequest(
            HttpMethod::DELETE,
            array(
                'config' => $config,
                'params' => array('brief' => $brief),
            ),
            "/v1/face/lib/$lib"
        );
    }
    /**
     * get face lib
     * @param $lib string, face lib name
     * @param array $options Supported options:
     *      {
     *          config: the optional bce configuration, which will overwrite the
     *                  default client configuration that was passed in constructor.
     *      }
     * @return mixed check face lib result
     * @throws BceClientException
     */
    public function getFaceLib($lib, $options = array())
    {
        list($config) = $this->parseOptions($options, 'config');
        if (empty($lib)) {
            throw new BceClientException("The parameter lib "
                . "should NOT be null or empty string");
        }
        return $this->sendRequest(
            HttpMethod::GET,
            array(
                'config' => $config,
            ),
            "/v1/face/lib/$lib"
        );
    }
    /**
     * add logo image
     * @param $lib string, logo lib name.
     * @param $brief string, brief name.
     * @param $image string, image url.
     * @param array $options Supported options:
     *      {
     *          config: the optional bce configuration, which will overwrite the
     *                  default client configuration that was passed in constructor.
     *      }
     * @return mixed nothing
     * @throws BceClientException
     */
    public function addLogoImage($lib, $brief, $image, $options = array())
    {
        list($config) = $this->parseOptions($options, 'config');
        if (empty($lib)) {
            throw new BceClientException("The parameter lib "
                . "should NOT be null or empty string");
        }
        if (empty($brief)) {
            throw new BceClientException("The parameter brief "
                . "should NOT be null or empty string");
        }
        if (empty($image)) {
            throw new BceClientException("The parameter image "
                . "should NOT be null or empty string");
        }
        $body = array(
            'brief' => $brief,
            'image' => $image
        );
        return $this->sendRequest(
            HttpMethod::POST,
            array(
                'config' => $config,
                'body' => json_encode($body),
            ),
            "/v1/logo/lib/$lib"
        );
    }
    /**
     * delete logo image
     * @param $lib string, logo lib name.
     * @param $image string, image url.
     * @param array $options Supported options:
     *      {
     *          config: the optional bce configuration, which will overwrite the
     *                  default client configuration that was passed in constructor.
     *      }
     * @return mixed nothing
     * @throws BceClientException
     */
    public function delLogoImage($lib, $image, $options = array())
    {
        list($config) = $this->parseOptions($options, 'config');
        if (empty($lib)) {
            throw new BceClientException("The parameter lib "
                . "should NOT be null or empty string");
        }
        if (empty($image)) {
            throw new BceClientException("The parameter image "
                . "should NOT be null or empty string");
        }
        return $this->sendRequest(
            HttpMethod::DELETE,
            array(
                'config' => $config,
                'params' => array('image' => $image),
            ),
            "/v1/logo/lib/$lib"
        );
    }
    /**
     * get logo brief
     * @param $lib string, logo lib name.
     * @param $brief string, brief name.
     * @param array $options Supported options:
     *      {
     *          config: the optional bce configuration, which will overwrite the
     *                  default client configuration that was passed in constructor.
     *      }
     * @return mixed check logo brief result
     * @throws BceClientException
     */
    public function getLogoBrief($lib, $brief, $options = array())
    {
        list($config) = $this->parseOptions($options, 'config');
        if (empty($lib)) {
            throw new BceClientException("The parameter lib "
                . "should NOT be null or empty string");
        }
        if (empty($brief)) {
            throw new BceClientException("The parameter brief "
                . "should NOT be null or empty string");
        }
        $params = array(
            'brief' => $brief,
        );
        return $this->sendRequest(
            HttpMethod::GET,
            array(
                'config' => $config,
                'params' => $params,
            ),
            "/v1/logo/lib/$lib"
        );
    }
    /**
     * delete logo brief
     * @param $lib string, logo lib name.
     * @param $brief string, brief name.
     * @param array $options Supported options:
     *      {
     *          config: the optional bce configuration, which will overwrite the
     *                  default client configuration that was passed in constructor.
     *          brief: string, delete brief.
     *      }
     * @return mixed nothing
     * @throws BceClientException
     */
    public function delLogoBrief($lib, $brief, $options = array())
    {
        list($config) = $this->parseOptions($options, 'config');
        if (empty($lib)) {
            throw new BceClientException("The parameter lib "
                . "should NOT be null or empty string");
        }
        if (empty($brief)) {
            throw new BceClientException("The parameter brief "
                . "should NOT be null or empty string");
        }
        return $this->sendRequest(
            HttpMethod::DELETE,
            array(
                'config' => $config,
                'params' => array('brief' => $brief),
            ),
            "/v1/logo/lib/$lib"
        );
    }
    /**
     * get logo lib
     * @param $lib string, logo lib name
     * @param array $options Supported options:
     *      {
     *          config: the optional bce configuration, which will overwrite the
     *                  default client configuration that was passed in constructor.
     *      }
     * @return mixed check logo lib result
     * @throws BceClientException
     */
    public function getLogoLib($lib, $options = array())
    {
        list($config) = $this->parseOptions($options, 'config');
        if (empty($lib)) {
            throw new BceClientException("The parameter lib "
                . "should NOT be null or empty string");
        }
        return $this->sendRequest(
            HttpMethod::GET,
            array(
                'config' => $config,
            ),
            "/v1/logo/lib/$lib"
        );
    }

    /**
     * Create HttpClient and send request
     * @param string $httpMethod The Http request method
     * @param array $varArgs The extra arguments
     * @param string $requestPath The Http request uri
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
        $response = $this->httpClient->sendRequest(
            $config,
            $httpMethod,
            $requestPath,
            $args['body'],
            $args['headers'],
            $args['params'],
            $this->signer
        );

        $result = $this->parseJsonResult($response['body']);

        return $result;
    }
}
