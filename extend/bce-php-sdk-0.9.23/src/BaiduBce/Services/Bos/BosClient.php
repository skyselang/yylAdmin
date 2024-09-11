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

namespace BaiduBce\Services\Bos;

use BaiduBce\Auth\BceV1Signer;
use BaiduBce\Auth\SignOptions;
use BaiduBce\Bce;
use BaiduBce\BceClientConfigOptions;
use BaiduBce\BceBaseClient;
use BaiduBce\Exception\BceClientException;
use BaiduBce\Exception\BceServiceException;
use BaiduBce\Http\BceHttpClient;
use BaiduBce\Http\HttpHeaders;
use BaiduBce\Http\HttpContentTypes;
use BaiduBce\Http\HttpMethod;
use BaiduBce\Util\MimeTypes;
use BaiduBce\Util\HashUtils;
use BaiduBce\Util\HttpUtils;
use BaiduBce\Util\StringUtils;


class BosClient extends BceBaseClient
{
    const MIN_PART_SIZE = 5242880;                // 5M
    const MAX_PUT_OBJECT_LENGTH = 5368709120;     // 5G
    const MAX_USER_METADATA_SIZE = 2048;          // 2 * 1024
    const MIN_PART_NUMBER = 1;
    const MAX_PART_NUMBER = 10000;
    const BOS_URL_PREFIX = "/";

    /**
     * @var \BaiduBce\Auth\SignerInterface
     */
    private $signer;
    private $httpClient;

    /**
     * The BosClient constructor
     *
     * @param array $config The client configuration
     */
    function __construct(array $config)
    {
        parent::__construct($config, 'bos');
        $this->signer = new BceV1Signer();
        $this->httpClient = new BceHttpClient();
    }


    /**
     * change default url to virtual host
     *
     */
    function genEndpoint($isBucketReq, &$config,$bucketName = "") {
        $endpoint = $config[BceClientConfigOptions::ENDPOINT];
        if (!strpos($endpoint, "://")) {
            $config_protocol = strtolower(trim($config[BceClientConfigOptions::PROTOCOL]));
            if ($config_protocol !== 'http' && $config_protocol !== 'https') {
                throw new \InvalidArgumentException(
                    "Invalid protocol $config_protocol."
                );
            }
            $endpoint = $config_protocol . "://" . $endpoint;
        }
        $parsed_endpoint = parse_url($endpoint);
        $scheme = strtolower($parsed_endpoint['scheme']);
        if ($scheme !== 'http' && $scheme !== 'https') {
            throw new \InvalidArgumentException(
                "Invalid endpoint $endpoint, unsupported scheme $scheme."
            );
        }
        $protocol = $scheme;
        $host = $parsed_endpoint['host'];
        $endpointArray = explode('.', $host);
        if ($isBucketReq) {
            $config[BceClientConfigOptions::CUSTOM] = false;
            if (strpos($host, Bce::DEFAULT_BOS_DOMAIN) !== false) {
                $lastThreeElements = array_slice($endpointArray, -3);
                $host = implode('.', $lastThreeElements);
            }
        } else {
            if (!isset($config[BceClientConfigOptions::CUSTOM]) || $config[BceClientConfigOptions::CUSTOM] === false) {
                $config[BceClientConfigOptions::CUSTOM] = true;
                if (count($endpointArray) === 3) {
                    $host = $bucketName . '.' . $host;
                } else {
                    throw new \InvalidArgumentException(
                      "Invalid endpoint, unsupport custom endpoint but config is false");
                }

            }
        }
        if (isset($parsed_endpoint['port'])) {
            $port = (int) $parsed_endpoint['port'];
        } else {
            if ($protocol == 'http') {
                $port = 80;
            } else {
                $port = 443;
            }
        }
        if (($protocol === 'http' && $port === 80)
               || ($protocol === 'https' && $port === 443)) {
            $hostHeader = $host;
        } else {
            $hostHeader = "$host:$port";
        }
        $config[BceClientConfigOptions::ENDPOINT] = "$protocol://$hostHeader";
    }

    /**
     * Get an authorization url with expire time
     *
     * @param string $bucketName The bucket name.
     * @param string $object_name The object path.
     * @param number $timestamp
     * @param number $expiration_in_seconds The valid time in seconds.
     * @param mixed $options The extra Http request headers or params.
     *
     * @return string
     */
    public function generatePreSignedUrl($bucketName, $key, $options = array())
    {
        list(
            $config,
            $headers,
            $params,
            $signOptions
        ) = $this->parseOptions(
            $options,
            BosOptions::CONFIG,
            BosOptions::HEADERS,
            BosOptions::PARAMS,
            BosOptions::SIGN_OPTIONS
        );
        if(is_null($config)) {
            $config = $this->config;
        } else {
            $config = array_merge($this->config, $config);
        }
        if(is_null($params)) {
            $params = array();
        }
        if(is_null($headers)) {
            $headers = array();
        }

        $this->genEndpoint(false, $config, $bucketName);
        $path = $this->getPath($bucketName, $key, $config);

        list($hostUrl, $hostHeader) =
            HttpUtils::parseEndpointFromConfig($config);
        $headers[HttpHeaders::HOST] = $hostHeader;

        $auth = $this->signer->sign(
            $config[BceClientConfigOptions::CREDENTIALS],
            HttpMethod::GET,
            $path,
            $headers,
            $params,
            $signOptions
        );
        $params['authorization'] = $auth;

        $url = $hostUrl . HttpUtils::urlEncodeExceptSlash($path);
        $queryString = HttpUtils::getCanonicalQueryString($params, false);
        if ($queryString !== '') {
            $url .= "?$queryString";
        }
        return $url;
    }

    /**
     * List buckets of user.
     *
     * @param array $options Supported options:
     *      <ul>
     *          <li>config: The optional bce configuration, which will overwrite
     *          the default client configuration that was passed in constructor.
     *          </li>
     *      </ul>
     * @return object the server response.
     */
    public function listBuckets($options = array())
    {
        list($config) = $this->parseOptions($options, BosOptions::CONFIG);

        return $this->sendRequest(
            HttpMethod::GET,
            array(
                BosOptions::CONFIG => $config,
                BosOptions::IS_BUCKET_API => true
            )
        );
    }

    /**
     * Create a new bucket.
     *
     * @param string $bucketName The bucket name.
     * @param array $options Supported options:
     *      <ul>
     *          <li>config: The optional bce configuration, which will overwrite
     *          the default client configuration that was passed in constructor.
     *          </li>
     *      </ul>
     * @return \stdClass
     */
    public function createBucket($bucketName, $options = array())
    {
        list($config) = $this->parseOptions($options, BosOptions::CONFIG);

        return $this->sendRequest(
            HttpMethod::PUT,
            array(
                BosOptions::CONFIG => $config,
                'bucket_name' => $bucketName,
            )
        );
    }
public function listObjectVersions($bucketName, $options = array())
    {
        list($config, $maxKeys, $prefix, $marker, $delimiter, $versionIdMarker) =
            $this->parseOptions(
                $options,
                BosOptions::CONFIG,
                BosOptions::MAX_KEYS,
                BosOptions::PREFIX,
                BosOptions::MARKER,
                BosOptions::DELIMITER,
                BosOptions::VERSIONID_MARKER
            );
        $params = array();
        if ($maxKeys !== null) {
            if (is_numeric($maxKeys)) {
                $maxKeys = number_format($maxKeys);
                $maxKeys = str_replace(',','',$maxKeys);
            }
            $params[BosOptions::MAX_KEYS] = $maxKeys;
        }
        if ($prefix !== null) {
            $params[BosOptions::PREFIX] = $prefix;
        }
        if ($marker !== null) {
            $params[BosOptions::MARKER] = $marker;
        }
        if ($delimiter !== null) {
            $params[BosOptions::DELIMITER] = $delimiter;
        }
        if ($versionIdMarker !== null) {
            $params[BosOptions::VERSIONID_MARKER] = $versionIdMarker;
        }
        $params['versions'] = '';
        return $this->sendRequest(
            HttpMethod::GET,
            array(
                BosOptions::CONFIG => $config,
                'bucket_name' => $bucketName,
                'params' => $params
            )
        );
}
    /**
     * Get Object Information of bucket.
     *
     * @param string $bucketName The bucket name.
     * @param mixed $options The optional bce configuration, which will overwrite the
     *   default configuration that was passed while creating BosClient instance.
     * @property number $maxKeys The default value is 1000.
     * @property string $prefix The default value is null.
     * @property string $marker The default value is null.
     * @property string $delimiter The default value is null.
     * @property mixed $config The optional bce configuration, which will overwrite the
     *   default configuration that was passed while creating BosClient instance.
     * @return mixed
     */
    public function listObjects($bucketName, $options = array())
    {
        list($config, $maxKeys, $prefix, $marker, $delimiter) =
            $this->parseOptions(
                $options,
                BosOptions::CONFIG,
                BosOptions::MAX_KEYS,
                BosOptions::PREFIX,
                BosOptions::MARKER,
                BosOptions::DELIMITER
            );
        $params = array();
        if ($maxKeys !== null) {
            if (is_numeric($maxKeys)) {
                $maxKeys = number_format($maxKeys);
                $maxKeys = str_replace(',','',$maxKeys);
            }
            $params[BosOptions::MAX_KEYS] = $maxKeys;
        }
        if ($prefix !== null) {
            $params[BosOptions::PREFIX] = $prefix;
        }
        if ($marker !== null) {
            $params[BosOptions::MARKER] = $marker;
        }
        if ($delimiter !== null) {
            $params[BosOptions::DELIMITER] = $delimiter;
        }

        return $this->sendRequest(
            HttpMethod::GET,
            array(
                BosOptions::CONFIG => $config,
                'bucket_name' => $bucketName,
                'params' => $params
            )
        );
    }

    /**
     * Check whether there is some user access to this bucket.
     *
     * @param string $bucketName The bucket name.
     * @param mixed $options The optional bce configuration, which will overwrite the
     *   default configuration that was passed while creating BosClient instance.
     * @return boolean true means the bucket does exists.
     */
    public function doesBucketExist($bucketName, $options = array())
    {
        list($config) = $this->parseOptions($options, BosOptions::CONFIG);

        try {
            $this->sendRequest(
                HttpMethod::HEAD,
                array(
                    BosOptions::CONFIG => $config,
                    'bucket_name' => $bucketName
                )
            );
            return true;
        } catch (BceServiceException $e) {
            if ($e->getStatusCode() === 403) {
                return true;
            }
            if ($e->getStatusCode() === 404) {
                return false;
            }
            throw $e;
        }
    }

    /**
     * Delete a Bucket
     * Must delete all the bbjects in this bucket before call this api
     *
     * @param string $bucketName The bucket name.
     * @param mixed $options The optional bce configuration, which will overwrite the
     *   default configuration that was passed while creating BosClient instance.
     * @return mixed
     */
    public function deleteBucket($bucketName, $options = array())
    {
        list($config) = $this->parseOptions($options, BosOptions::CONFIG);

        return $this->sendRequest(
            HttpMethod::DELETE,
            array(
                BosOptions::CONFIG => $config,
                'bucket_name' => $bucketName
            )
        );
    }

    /**
     * Set Access Control Level of bucket
     *
     * @param string $bucketName The bucket name.
     * @param string $cannedAcl The grant list.
     * @param mixed $options The optional bce configuration, which will overwrite the
     *   default configuration that was passed while creating BosClient instance.
     * @return mixed
     */
    public function setBucketCannedAcl(
        $bucketName,
        $cannedAcl,
        $options = array()
    ) {
        list($config) = $this->parseOptions($options, BosOptions::CONFIG);

        return $this->sendRequest(
            HttpMethod::PUT,
            array(
                BosOptions::CONFIG => $config,
                'bucket_name' => $bucketName,
                'headers' => array(
                    HttpHeaders::BCE_ACL => $cannedAcl,
                ),
                'params' => array(
                    BosOptions::ACL => '',
                )
            )
        );
    }

    /**
     * Set Access Control Level of bucket
     *
     * @param string $bucketName The bucket name.
     * @param mixed $acl The grant list.
     * @param mixed $options The optional bce configuration, which will overwrite the
     *   default configuration that was passed while creating BosClient instance.
     * @return mixed
     */
    public function setBucketAcl($bucketName, $acl, $options = array())
    {
        list($config) = $this->parseOptions($options, BosOptions::CONFIG);

        return $this->sendRequest(
            HttpMethod::PUT,
            array(
                BosOptions::CONFIG => $config,
                'bucket_name' => $bucketName,
                'body' => json_encode(array('accessControlList' => $acl)),
                'params' => array(
                    BosOptions::ACL => '',
                )
            )
        );
    }

    /**
     * Set Replication rule of bucket
     *
     * @param string $bucketName The bucket name.
     * @param string $replicationRule json format.
     * @param mixed $options The optional bce configuration, which will overwrite the
     *   default configuration that was passed while creating BosClient instance.
     * @return mixed
     */

    public function putBucketReplication($bucketName, $replicationRule, $options = array())
    {
        list($config) = $this->parseOptions($options, BosOptions::CONFIG);

        return $this->sendRequest(
            HttpMethod::PUT,
            array(
                BosOptions::CONFIG => $config,
                'bucket_name' => $bucketName,
                'body' => json_encode($replicationRule),
                'params' => array(
                    BosOptions::REPLICATION => '',
                )
            )
        );
    }

    /**
     * Get Replication rule of bucket
     *
     * @param string $bucketName The bucket name.
     * @param mixed $options The optional bce configuration, which will overwrite the
     *   default configuration that was passed while creating BosClient instance.
     * @return mixed
     */


    public function getBucketReplication($bucketName, $options = array())
    {
        list($config) = $this->parseOptions($options, BosOptions::CONFIG);

        return $this->sendRequest(
            HttpMethod::GET,
            array(
                BosOptions::CONFIG => $config,
                'bucket_name' => $bucketName,
                'params' => array(
                    BosOptions::REPLICATION => '',
                )
            )
        );
    }

    /**
     * Get Replication progress of bucket
     *
     * @param string $bucketName The bucket name.
     * @param mixed $options The optional bce configuration, which will overwrite the
     *   default configuration that was passed while creating BosClient instance.
     * @return mixed
     */

    public function getBucketReplicationProgress($bucketName, $options = array())
    {
        list($config) = $this->parseOptions($options, BosOptions::CONFIG);

        return $this->sendRequest(
            HttpMethod::GET,
            array(
                BosOptions::CONFIG => $config,
                'bucket_name' => $bucketName,
                'params' => array(
                    BosOptions::REPLICATION_PROGRESS => '',
                )
            )
        );
    }

    /**
     * Delete bucket Replication
     *
     * @param string $bucketName The bucket name.
     * @param mixed $options The optional bce configuration, which will overwrite the
     *   default configuration that was passed while creating BosClient instance.
     * @return mixed
     */


    public function deleteBucketReplication($bucketName, $options = array())
    {
        list($config) = $this->parseOptions($options, BosOptions::CONFIG);

        return $this->sendRequest(
            HttpMethod::DELETE,
            array(
                BosOptions::CONFIG => $config,
                'bucket_name' => $bucketName,
                'params' => array(
                    BosOptions::REPLICATION => '',
                )
            )
        );
    }

    /**
     * List bucket Replication
     *
     * @param string $bucketName The bucket name.
     * @param mixed $options The optional bce configuration, which will overwrite the
     *   default configuration that was passed while creating BosClient instance.
     * @return mixed
     */
    public function listBucketReplication($bucketName, $options)
    {
        list($config) = $this->parseOptions($options, BosOptions::CONFIG);
        return $this->sendRequest(
            HttpMethod::GET,
            array(
                BosOptions::CONFIG => $config,
                'bucket_name' => $bucketName,
                'params' => array(
                    BosOptions::REPLICATION => '',

                )
            )
        );
    }
    /**
     * Get Access Control Level of bucket
     *
     * @param string $bucketName The bucket name.
     * @param mixed $options The optional bce configuration, which will overwrite the
     *   default configuration that was passed while creating BosClient instance.
     * @return mixed
     */
    public function getBucketAcl($bucketName, $options = array())
    {
        list($config) = $this->parseOptions($options, BosOptions::CONFIG);

        return $this->sendRequest(
            HttpMethod::GET,
            array(
                BosOptions::CONFIG => $config,
                'bucket_name' => $bucketName,
                'params' => array(
                    BosOptions::ACL => '',
                )
            )
        );
    }
    public function getBucketVersioning($bucketName, $options = array())
    {
        list($config) = $this->parseOptions($options, BosOptions::CONFIG);

        $res =  $this->sendRequest(
            HttpMethod::GET,
            array(
                BosOptions::CONFIG => $config,
                'bucket_name' => $bucketName,
                'params' => array(
                    "versioning" => '',
                )
            )
          );
        return $res;
    }
    public function putBucketVersioning($bucketName, $status = 'enabled', $options = array())
    {
        list($config) = $this->parseOptions($options, BosOptions::CONFIG);

        $res =  $this->sendRequest(
            HttpMethod::PUT,
            array(
                BosOptions::CONFIG => $config,
                'bucket_name' => $bucketName,
                'body' => json_encode(array('status' => $status)),
                'params' => array(
                    "versioning" => '',
                )
            )
          );
        return $res;
    }

    /**
     * Get Region of bucket
     *
     * @param string $bucketName The bucket name.
     * @param mixed $options The optional bce configuration, which will overwrite the
     *   default configuration that was passed while creating BosClient instance.
     * @return mixed
     */
    public function getBucketLocation($bucketName, $options = array())
    {
        list($config) = $this->parseOptions($options, BosOptions::CONFIG);
        $response = $this->sendRequest(
            HttpMethod::GET,
            array(
                BosOptions::CONFIG => $config,
                'bucket_name' => $bucketName,
                'params' => array(
                    BosOptions::LOCATION => '',
                ),
            )
        );
        return $response->locationConstraint;
    }

    /**
     * Set Lifecycle of bucket
     *
     * @param string $bucketName The bucket name.
     * @param string $lifecycleRule json format.
     * @param mixed $options The optional bce configuration, which will overwrite the
     *   default configuration that was passed while creating BosClient instance.
     * @return mixed
     */

    public function putBucketLifecycle($bucketName, $lifecycleRule, $options = array())
    {
        list($config) = $this->parseOptions($options, BosOptions::CONFIG);

        return $this->sendRequest(
            HttpMethod::PUT,
            array(
                BosOptions::CONFIG => $config,
                'bucket_name' => $bucketName,
                'body' => json_encode(array("rule" => $lifecycleRule)),
                'params' => array(
                    BosOptions::LIFECYCLE => '',
                )
            )
        );
    }

    /**
     * Get Lifecycle rule of bucket
     *
     * @param string $bucketName The bucket name.
     * @param mixed $options The optional bce configuration, which will overwrite the
     *   default configuration that was passed while creating BosClient instance.
     * @return mixed
     */


    public function getBucketLifecycle($bucketName, $options = array())
    {
        list($config) = $this->parseOptions($options, BosOptions::CONFIG);

        return $this->sendRequest(
            HttpMethod::GET,
            array(
                BosOptions::CONFIG => $config,
                'bucket_name' => $bucketName,
                'params' => array(
                    BosOptions::LIFECYCLE => '',
                )
            )
        );
    }

    /**
     * Delete bucket Lifecycle
     *
     * @param string $bucketName The bucket name.
     * @param mixed $options The optional bce configuration, which will overwrite the
     *   default configuration that was passed while creating BosClient instance.
     * @return mixed
     */


    public function deleteBucketLifecycle($bucketName, $options = array())
    {
        list($config) = $this->parseOptions($options, BosOptions::CONFIG);

        return $this->sendRequest(
            HttpMethod::DELETE,
            array(
                BosOptions::CONFIG => $config,
                'bucket_name' => $bucketName,
                'params' => array(
                    BosOptions::LIFECYCLE => '',
                )
            )
        );
    }

    /**
     * Set Logging of bucket
     *
     * @param string $bucketName The bucket name.
     * @param string $logging json format.
     * @param mixed $options The optional bce configuration, which will overwrite the
     *   default configuration that was passed while creating BosClient instance.
     * @return mixed
     */

    public function putBucketLogging(
        $bucketName,
        $logging,
        $options = array()
    ) {
        list($config) = $this->parseOptions($options, BosOptions::CONFIG);

        return $this->sendRequest(
            HttpMethod::PUT,
            array(
                BosOptions::CONFIG => $config,
                'bucket_name' => $bucketName,
                'body' => json_encode($logging),
                'params' => array(
                    BosOptions::LOGGING => '',
                )
            )
        );
    }

    /**
     * Get Logging of bucket
     *
     * @param string $bucketName The bucket name.
     * @param mixed $options The optional bce configuration, which will overwrite the
     *   default configuration that was passed while creating BosClient instance.
     * @return mixed
     */


    public function getBucketLogging($bucketName, $options = array())
    {
        list($config) = $this->parseOptions($options, BosOptions::CONFIG);

        return $this->sendRequest(
            HttpMethod::GET,
            array(
                BosOptions::CONFIG => $config,
                'bucket_name' => $bucketName,
                'params' => array(
                    BosOptions::LOGGING => '',
                )
            )
        );
    }

    /**
     * Delete bucket Logging
     *
     * @param string $bucketName The bucket name.
     * @param mixed $options The optional bce configuration, which will overwrite the
     *   default configuration that was passed while creating BosClient instance.
     * @return mixed
     */


    public function deleteBucketLogging($bucketName, $options = array())
    {
        list($config) = $this->parseOptions($options, BosOptions::CONFIG);

        return $this->sendRequest(
            HttpMethod::DELETE,
            array(
                BosOptions::CONFIG => $config,
                'bucket_name' => $bucketName,
                'params' => array(
                    BosOptions::LOGGING => '',
                )
            )
        );
    }

    /**
     * Set Trash of bucket
     *
     * @param string $bucketName The bucket name.
     * @param string $trashDir json format.
     * @param mixed $options The optional bce configuration, which will overwrite the
     *   default configuration that was passed while creating BosClient instance.
     * @return mixed
     */

    public function putBucketTrash(
        $bucketName,
        $trashDir = ".trash",
        $options = array()
    ) {
        list($config) = $this->parseOptions($options, BosOptions::CONFIG);

        return $this->sendRequest(
            HttpMethod::PUT,
            array(
                BosOptions::CONFIG => $config,
                'bucket_name' => $bucketName,
                'body' => json_encode(array("trashDir" => $trashDir)),
                'params' => array(
                    BosOptions::TRASH => '',
                )
            )
        );
    }

    /**
     * Get Trash of bucket
     *
     * @param string $bucketName The bucket name.
     * @param mixed $options The optional bce configuration, which will overwrite the
     *   default configuration that was passed while creating BosClient instance.
     * @return mixed
     */


    public function getBucketTrash($bucketName, $options = array())
    {
        list($config) = $this->parseOptions($options, BosOptions::CONFIG);

        return $this->sendRequest(
            HttpMethod::GET,
            array(
                BosOptions::CONFIG => $config,
                'bucket_name' => $bucketName,
                'params' => array(
                    BosOptions::TRASH => '',
                )
            )
        );
    }

    /**
     * Delete bucket Trash
     *
     * @param string $bucketName The bucket name.
     * @param mixed $options The optional bce configuration, which will overwrite the
     *   default configuration that was passed while creating BosClient instance.
     * @return mixed
     */


    public function deleteBucketTrash($bucketName, $options = array())
    {
        list($config) = $this->parseOptions($options, BosOptions::CONFIG);

        return $this->sendRequest(
            HttpMethod::DELETE,
            array(
                BosOptions::CONFIG => $config,
                'bucket_name' => $bucketName,
                'params' => array(
                    BosOptions::TRASH => '',
                )
            )
        );
    }

    /**
     * Set StaticWebsite of bucket
     *
     * @param string $bucketName The bucket name.
     * @param string $trashDir json format.
     * @param mixed $options The optional bce configuration, which will overwrite the
     *   default configuration that was passed while creating BosClient instance.
     * @return mixed
     */

    public function putBucketStaticWebsite(
        $bucketName,
        $staticWebsite,
        $options = array()
    ) {
        list($config) = $this->parseOptions($options, BosOptions::CONFIG);

        return $this->sendRequest(
            HttpMethod::PUT,
            array(
                BosOptions::CONFIG => $config,
                'bucket_name' => $bucketName,
                'body' => json_encode($staticWebsite),
                'params' => array(
                    BosOptions::WEBSITE => '',
                )
            )
        );
    }

    /**
     * Get StaticWebsite of bucket
     *
     * @param string $bucketName The bucket name.
     * @param mixed $options The optional bce configuration, which will overwrite the
     *   default configuration that was passed while creating BosClient instance.
     * @return mixed
     */


    public function getBucketStaticWebsite($bucketName, $options = array())
    {
        list($config) = $this->parseOptions($options, BosOptions::CONFIG);

        return $this->sendRequest(
            HttpMethod::GET,
            array(
                BosOptions::CONFIG => $config,
                'bucket_name' => $bucketName,
                'params' => array(
                    BosOptions::WEBSITE => '',
                )
            )
        );
    }

    /**
     * Delete bucket StaticWebsite
     *
     * @param string $bucketName The bucket name.
     * @param mixed $options The optional bce configuration, which will overwrite the
     *   default configuration that was passed while creating BosClient instance.
     * @return mixed
     */


    public function deleteBucketStaticWebsite($bucketName, $options = array())
    {
        list($config) = $this->parseOptions($options, BosOptions::CONFIG);

        return $this->sendRequest(
            HttpMethod::DELETE,
            array(
                BosOptions::CONFIG => $config,
                'bucket_name' => $bucketName,
                'params' => array(
                    BosOptions::WEBSITE => '',
                )
            )
        );
    }

    /**
     * Set Encryption of bucket
     *
     * @param string $bucketName The bucket name.
     * @param string $encryptionAlgorithm string, only support 'AES256'.
     * @param mixed $options The optional bce configuration, which will overwrite the
     *   default configuration that was passed while creating BosClient instance.
     * @return mixed
     */

    public function putBucketEncryption($bucketName, $encryptionAlgorithm, $options = array())
    {
        list($config) = $this->parseOptions($options, BosOptions::CONFIG);

        return $this->sendRequest(
            HttpMethod::PUT,
            array(
                BosOptions::CONFIG => $config,
                'bucket_name' => $bucketName,
                'body' => json_encode(array("encryptionAlgorithm" => $encryptionAlgorithm)),
                'params' => array(
                    BosOptions::ENCRYPTION => '',
                )
            )
        );
    }

    /**
     * Get Encryption of bucket
     *
     * @param string $bucketName The bucket name.
     * @param mixed $options The optional bce configuration, which will overwrite the
     *   default configuration that was passed while creating BosClient instance.
     * @return mixed
     */


    public function getBucketEncryption($bucketName, $options = array())
    {
        list($config) = $this->parseOptions($options, BosOptions::CONFIG);

        return $this->sendRequest(
            HttpMethod::GET,
            array(
                BosOptions::CONFIG => $config,
                'bucket_name' => $bucketName,
                'params' => array(
                    BosOptions::ENCRYPTION => '',
                )
            )
        );
    }

    /**
     * Delete bucket Encryption
     *
     * @param string $bucketName The bucket name.
     * @param mixed $options The optional bce configuration, which will overwrite the
     *   default configuration that was passed while creating BosClient instance.
     * @return mixed
     */


    public function deleteBucketEncryption($bucketName, $options = array())
    {
        list($config) = $this->parseOptions($options, BosOptions::CONFIG);

        return $this->sendRequest(
            HttpMethod::DELETE,
            array(
                BosOptions::CONFIG => $config,
                'bucket_name' => $bucketName,
                'params' => array(
                    BosOptions::ENCRYPTION => '',
                )
            )
        );
    }

    /**
     * Set Cors of bucket
     *
     * @param string $bucketName The bucket name.
     * @param string $cors.
     * @param mixed $options The optional bce configuration, which will overwrite the
     *   default configuration that was passed while creating BosClient instance.
     * @return mixed
     */

    public function putBucketCors($bucketName, $cors, $options = array())
    {
        list($config) = $this->parseOptions($options, BosOptions::CONFIG);

        return $this->sendRequest(
            HttpMethod::PUT,
            array(
                BosOptions::CONFIG => $config,
                'bucket_name' => $bucketName,
                'body' => json_encode(array("corsConfiguration" => $cors)),
                'params' => array(
                    BosOptions::CORS => '',
                )
            )
        );
    }

    /**
     * Get Cors of bucket
     *
     * @param string $bucketName The bucket name.
     * @param mixed $options The optional bce configuration, which will overwrite the
     *   default configuration that was passed while creating BosClient instance.
     * @return mixed
     */


    public function getBucketCors($bucketName, $options = array())
    {
        list($config) = $this->parseOptions($options, BosOptions::CONFIG);

        return $this->sendRequest(
            HttpMethod::GET,
            array(
                BosOptions::CONFIG => $config,
                'bucket_name' => $bucketName,
                'params' => array(
                    BosOptions::CORS => '',
                )
            )
        );
    }

    /**
     * Delete bucket Cors
     *
     * @param string $bucketName The bucket name.
     * @param mixed $options The optional bce configuration, which will overwrite the
     *   default configuration that was passed while creating BosClient instance.
     * @return mixed
     */


    public function deleteBucketCors($bucketName, $options = array())
    {
        list($config) = $this->parseOptions($options, BosOptions::CONFIG);

        return $this->sendRequest(
            HttpMethod::DELETE,
            array(
                BosOptions::CONFIG => $config,
                'bucket_name' => $bucketName,
                'params' => array(
                    BosOptions::CORS => '',
                )
            )
        );
    }

    /**
     * Set CopyrightProtection of bucket
     *
     * @param string $bucketName The bucket name.
     * @param string $copyrightProtection.
     * @param mixed $options The optional bce configuration, which will overwrite the
     *   default configuration that was passed while creating BosClient instance.
     * @return mixed
     */

    public function putBucketCopyrightProtection($bucketName, $copyrightProtection, $options = array())
    {
        list($config) = $this->parseOptions($options, BosOptions::CONFIG);

        return $this->sendRequest(
            HttpMethod::PUT,
            array(
                BosOptions::CONFIG => $config,
                'bucket_name' => $bucketName,
                'body' => json_encode(array("resource" => $copyrightProtection)),
                'params' => array(
                    BosOptions::COPYRIGHTPROTECTION => '',
                )
            )
        );
    }

    /**
     * Get CopyrightProtection of bucket
     *
     * @param string $bucketName The bucket name.
     * @param mixed $options The optional bce configuration, which will overwrite the
     *   default configuration that was passed while creating BosClient instance.
     * @return mixed
     */


    public function getBucketCopyrightProtection($bucketName, $options = array())
    {
        list($config) = $this->parseOptions($options, BosOptions::CONFIG);

        return $this->sendRequest(
            HttpMethod::GET,
            array(
                BosOptions::CONFIG => $config,
                'bucket_name' => $bucketName,
                'params' => array(
                    BosOptions::COPYRIGHTPROTECTION => '',
                )
            )
        );
    }

    /**
     * Delete bucket CopyrightProtection
     *
     * @param string $bucketName The bucket name.
     * @param mixed $options The optional bce configuration, which will overwrite the
     *   default configuration that was passed while creating BosClient instance.
     * @return mixed
     */


    public function deleteBucketCopyrightProtection($bucketName, $options = array())
    {
        list($config) = $this->parseOptions($options, BosOptions::CONFIG);

        return $this->sendRequest(
            HttpMethod::DELETE,
            array(
                BosOptions::CONFIG => $config,
                'bucket_name' => $bucketName,
                'params' => array(
                    BosOptions::COPYRIGHTPROTECTION => '',
                )
            )
        );
    }

    /**
     * Put symlink of object
     *
     * @param string $bucketName The bucket name.
     * @param string $key The object path.
     * @param string $symlink x-bce-symlink-target.
     * @param mixed $options The optional bce configuration, which will overwrite the
     *   default configuration that was passed while creating BosClient instance.
     * @return mixed
     */
    public function putObjectSymlink(
        $bucketName,
        $key,
        $symlink,
        $options = array()
    ) {
        if (empty($key)) {
            throw new \InvalidArgumentException('$key should not be empty or null.');
        }
        if (empty($symlink) || strcmp($key, $symlink) == 0) {
            throw new \InvalidArgumentException('$symlink should not be empty or null or equals to $key.');
        }

        $headers = array();
        $headers[HttpHeaders::BCE_SYMLINK_TARGET] = $symlink;
        $this->populateRequestHeadersWithOptions($headers, $options);
        list($config) = $this->parseOptions($options,
            BosOptions::CONFIG,
            BosOptions::BCE_SYMLINK_TARGET,
            BosOptions::BCE_FORBID_OVERWRITE,
            BosOptions::STORAGE_CLASS,
            BosOptions::USER_METADATA
        );

        return $this->sendRequest(
            HttpMethod::PUT,
            array(
                BosOptions::CONFIG => $config,
                'bucket_name' => $bucketName,
                'key' => $key,
                'headers' => $headers,
                'params' => array(
                    BosOptions::SYMLINK => '',
                )
            )
        );
    }

    /**
     * Get symlink of object
     *
     * @param string $bucketName The bucket name.
     * @param string $key The object path.
     * @param mixed $options The optional bce configuration, which will overwrite the
     *   default configuration that was passed while creating BosClient instance.
     * @return mixed
     */
    public function getObjectSymLink($bucketName, $key, $options = array())
    {
        list($config) = $this->parseOptions($options, BosOptions::CONFIG);

        return $this->sendRequest(
            HttpMethod::GET,
            array(
                BosOptions::CONFIG => $config,
                'bucket_name' => $bucketName,
                'key' => $key,
                'params' => array(
                    BosOptions::SYMLINK => '',
                )
            )
        );
    }

    /**
     * Set Access Control Level of object
     *
     * @param string $bucketName The bucket name.
     * @param string $key The object path.
     * @param array $cannedAcl x-bce-acl/x-bce-grant-permission.
     * @param mixed $options The optional bce configuration, which will overwrite the
     *   default configuration that was passed while creating BosClient instance.
     * @return mixed
     */
    public function setObjectCannedAcl(
        $bucketName,
        $key,
        $cannedAcl = array(),
        $options = array()
    ) {
        list($config) = $this->parseOptions($options, BosOptions::CONFIG);

        return $this->sendRequest(
            HttpMethod::PUT,
            array(
                BosOptions::CONFIG => $config,
                'bucket_name' => $bucketName,
                'key' => $key,
                'headers' => $cannedAcl,
                'params' => array(
                    BosOptions::ACL => '',
                )
            )
        );
    }

    /**
     * Set Access Control Level of object
     *
     * @param string $bucketName The bucket name.
     * @param string $key The object path.
     * @param mixed $acl The grant list.
     * @param mixed $options The optional bce configuration, which will overwrite the
     *   default configuration that was passed while creating BosClient instance.
     * @return mixed
     */

public function setObjectCannedTag(
        $bucketName,
        $key,
        $cannedTag = array(),
        $options = array()
    ) {
        list($config) = $this->parseOptions($options, BosOptions::CONFIG);
        return $this->sendRequest(
            HttpMethod::PUT,
            array(
                BosOptions::CONFIG => $config,
                'bucket_name' => $bucketName,
                'key' => $key,
                'headers' => $cannedTag,
                'params' => array(
                    BosOptions::TAG => '',
                )
            )
        );
    }

    public function setObjectAcl($bucketName, $key, $acl, $options = array())
    {
        list($config) = $this->parseOptions($options, BosOptions::CONFIG);

        return $this->sendRequest(
            HttpMethod::PUT,
            array(
                BosOptions::CONFIG => $config,
                'bucket_name' => $bucketName,
                'key' => $key,
                'body' => json_encode(array('accessControlList' => $acl)),
                'params' => array(
                    BosOptions::ACL => '',
                )
            )
        );
    }
    public function setObjectTag($bucketName, $key, $tag, $options = array())
    {
        list($config) = $this->parseOptions($options, BosOptions::CONFIG);

        return $this->sendRequest(
            HttpMethod::PUT,
            array(
                BosOptions::CONFIG => $config,
                'bucket_name' => $bucketName,
                'key' => $key,
                'body' => json_encode(array('tagSet' => $tag)),
                'params' => array(
                    BosOptions::TAG => '',
                )
            )
          );
    }
    public function getObjectTag($bucketName, $key, $options = array())
    {
        list($config) = $this->parseOptions($options, BosOptions::CONFIG);

        return $this->sendRequest(
            HttpMethod::GET,
            array(
                BosOptions::CONFIG => $config,
                'bucket_name' => $bucketName,
                'key' => $key,
                'params' => array(
                    'tagging' => '',
                )
            )
          );
    }
    public function deleteObjectTag($bucketName, $key, $options = array())
    {
        list($config) = $this->parseOptions($options, BosOptions::CONFIG);

        return $this->sendRequest(
            HttpMethod::DELETE,
            array(
                BosOptions::CONFIG => $config,
                'bucket_name' => $bucketName,
                'key' => $key,
                'params' => array(
                    BosOptions::TAG => '',
                )
            )
        );
    }

    /**
     * Delete Access Control Level of object
     *
     * @param string $bucketName The bucket name.
     * @param string $key The object path.
     * @param mixed $acl The grant list.
     * @param mixed $options The optional bce configuration, which will overwrite the
     *   default configuration that was passed while creating BosClient instance.
     * @return mixed
     */
    public function deleteObjectAcl($bucketName, $key, $options = array())
    {
        list($config) = $this->parseOptions($options, BosOptions::CONFIG);

        return $this->sendRequest(
            HttpMethod::DELETE,
            array(
                BosOptions::CONFIG => $config,
                'bucket_name' => $bucketName,
                'key' => $key,
                'params' => array(
                    BosOptions::ACL => '',
                )
            )
        );
    }

    /**
     * Get Access Control Level of object
     *
     * @param string $bucketName The bucket name.
     * @param string $key The object path.
     * @param mixed $options The optional bce configuration, which will overwrite the
     *   default configuration that was passed while creating BosClient instance.
     * @return mixed
     */
    public function getObjectAcl($bucketName, $key, $options = array())
    {
        list($config) = $this->parseOptions($options, BosOptions::CONFIG);

        return $this->sendRequest(
            HttpMethod::GET,
            array(
                BosOptions::CONFIG => $config,
                'bucket_name' => $bucketName,
                'key' => $key,
                'params' => array(
                    BosOptions::ACL => '',
                )
            )
        );
    }

    /**
     * Create object and put content of string to the object
     *
     * @param string $bucketName The bucket name.
     * @param string $key The object path.
     * @param string $data The object content.
     * @param mixed $options The optional bce configuration, which will overwrite the
     *   default configuration that was passed while creating BosClient instance.
     *
     * @return mixed
     */
    public function putObjectFromString(
        $bucketName,
        $key,
        $data,
        $options = array()
    ) {
        return $this->putObject(
            $bucketName,
            $key,
            $data,
            strlen($data),
            base64_encode(md5($data, true)),
            $options
        );
    }

    /**
     * Put object and copy content of file to the object
     *
     * @param string $bucketName The bucket name.
     * @param string $key The object path.
     * @param string $filename The absolute file path.
     * @param mixed $options The optional bce configuration, which will overwrite the
     *   default configuration that was passed while creating BosClient instance.
     *
     * @return mixed
     */
    public function putObjectFromFile(
        $bucketName,
        $key,
        $filename,
        $options = array()
    ) {
        if (!isset($options[BosOptions::CONTENT_TYPE])) {
            $options[BosOptions::CONTENT_TYPE] = MimeTypes::guessMimeType(
                $filename
            );
        }

        list($contentLength, $contentMd5) = $this->parseOptionsIgnoreExtra(
            $options,
            BosOptions::CONTENT_LENGTH,
            BosOptions::CONTENT_MD5
        );

        if ($contentLength === null) {
            $contentLength = filesize($filename);
        } else {
            if (!is_int($contentLength) && !is_long($contentLength)) {
                throw new \InvalidArgumentException(
                    '$contentLength should be int or long.'
                );
            }
            unset($options[BosOptions::CONTENT_LENGTH]);
        }

        $fp = fopen($filename, 'rb');
        if ($contentMd5 === null) {
            $contentMd5 = base64_encode(HashUtils::md5FromStream($fp, 0, $contentLength));
        } else {
            unset($options[BosOptions::CONTENT_MD5]);
        }

        try {
            $response = $this->putObject(
                $bucketName,
                $key,
                $fp,
                $contentLength,
                $contentMd5,
                $options);
            //streams are close in the destructor of stream object in guzzle
            if (is_resource($fp)) {
                fclose($fp);
            }
            return $response;
        } catch (\Exception $e) {
            if (is_resource($fp)) {
                fclose($fp);
            }
            throw $e;
        }
    }

    /**
     * Upload a object to one bucket
     * @param string $bucketName The bucket name.
     * @param string $key The object path.
     * @param string|resource $data The object content, which can be a string or a resource.
     * @param int $contentLength
     * @param string $contentMd5
     * @param mixed $options The optional bce configuration, which will overwrite the
     *   default configuration that was passed while creating BosClient instance.
     * @return mixed
     */
    public function putObject(
        $bucketName,
        $key,
        $data,
        $contentLength,
        $contentMd5,
        $options = array()
    ) {
        if (empty($key)) {
            throw new \InvalidArgumentException('$key should not be empty or null.');
        }
        if (!is_int($contentLength) && !is_long($contentLength)) {
            throw new \InvalidArgumentException(
                '$contentLength should be int or long.'
            );
        }
        if ($contentLength < 0) {
            throw new \InvalidArgumentException(
                '$contentLength should not be negative.'
            );
        }
        if (empty($contentMd5)) {
            throw new \InvalidArgumentException(
                '$contentMd5 should not be empty or null.'
            );
        }
        $this->checkData($data);

        $headers = array();
        $headers[HttpHeaders::CONTENT_MD5] = $contentMd5;
        $headers[HttpHeaders::CONTENT_LENGTH] = $contentLength;
        $this->populateRequestHeadersWithOptions($headers, $options);
        list($config) = $this->parseOptions($options, BosOptions::CONFIG);

        return $this->sendRequest(
            HttpMethod::PUT,
            array(
                BosOptions::CONFIG => $config,
                'bucket_name' => $bucketName,
                'key' => $key,
                'body' => $data,
                'headers' => $headers,
            )
        );
    }
    
    public function RenameObject(
        $bucketName,
        $srcKey,
        $targetKey,
        $options = array()
    ) {
        $headers = array();
        $headers[HttpHeaders::BCE_RENAME_SRC] = $srcKey;
        $this->populateRequestHeadersWithOptions($headers, $options);
        list($config) = $this->parseOptions($options, BosOptions::CONFIG);
        return $this->sendRequest(
            HttpMethod::POST,
            array(
                BosOptions::CONFIG => $config,
                'bucket_name' => $bucketName,
                'key' => $targetKey,
                'headers' => $headers,
            )
        );
    }


    /**
     * Fetch a object to one bucket
     * @param string $bucketName The bucket name.
     * @param string $key The object path.
     * @param string $fetchSource
     * @param mixed $options The optional bce configuration, which will overwrite the
     *   default configuration that was passed while creating BosClient instance.
     * @return mixed
     */
    public function fetchObject(
        $bucketName,
        $key,
        $fetchSource,
        $options = array()
    ) {
        if (empty($key)) {
            throw new \InvalidArgumentException('$key should not be empty or null.');
        }

        if (empty($fetchSource)) {
            throw new \InvalidArgumentException('$fetchSource should not be empty or null.');
        }
        $headers = array();
        $headers[HttpHeaders::BCE_FETCH_SOURCE] = $fetchSource;
        $this->populateRequestHeadersWithOptions($headers, $options);
        list($config, $storageClass, $fetchMode) = $this->parseOptions($options,
            BosOptions::CONFIG,
            BosOptions::STORAGE_CLASS,
            BosOptions::BCE_FETCH_MODE);
        if ($fetchMode !== null) {
            $headers[HttpHeaders::BCE_FETCH_MODE] = $fetchMode;
        }
        return $this->sendRequest(
            HttpMethod::POST,
            array(
                BosOptions::CONFIG => $config,
                'bucket_name' => $bucketName,
                'key' => $key,
                'headers' => $headers,
                'params' => array(
                    BosOptions::FETCH => '',
                )
            )
        );
    }

    /**
     * create a append object or append the string to a existing object
     * @param string $bucketName The bucket name.
     * @param string $key The object path.
     * @param string|resource $data The object content, which can be a string or a resource.
     * @param int offset the current offset of append object
     * @param mixed $options The optional bce configuration, which will overwrite the
     *   default configuration that was passed while creating BosClient instance.
     * @return the new offset of append object
     */

    public function appendObjectFromString(
        $bucketName,
        $key,
        $data,
        $offset,
        $options = array()
    ) {
        if (empty($key)) {
            throw new \InvalidArgumentException('$key should not be empty or null.');
        }
        if (!is_int($offset) && !is_long($offset)) {
            throw new \InvalidArgumentException(
                '$offset should be int or long.'
            );
        }
        $contentLength = strlen($data);
        $contentMd5 = base64_encode(md5($data, true));

        $this->checkData($data);

        $headers = array();
        $headers[HttpHeaders::CONTENT_MD5] = $contentMd5;
        $headers[HttpHeaders::CONTENT_LENGTH] = $contentLength;
        $this->populateRequestHeadersWithOptions($headers, $options);
        list($config) = $this->parseOptions($options, BosOptions::CONFIG);

        $param = array();
        if ($offset !== 0) {
            $param['offset'] = $offset;
        }
        $param['append'] = '';
        return $this->sendRequest(
            HttpMethod::POST,
            array(
                BosOptions::CONFIG => $config,
                'bucket_name' => $bucketName,
                'key' => $key,
                'body' => $data,
                'headers' => $headers,
                'params' => $param,
            )
        );
    }
     /**
     * create a append object or append the file to a existing object
     * @param string $bucketName The bucket name.
     * @param string $key The object path.
     * @param string $filename.
     * @param int offset the current offset of append object
     * @param mixed $options The optional bce configuration, which will overwrite the
     *   default configuration that was passed while creating BosClient instance.
     * @return mixed
     */

    public function appendObjectFromFile(
        $bucketName,
        $key,
        $filename,
        $offset,
        $options = array()
    ) {

        $contentLength = filesize($filename);

        $fp = fopen($filename, 'rb');
        $contentMd5 = base64_encode(HashUtils::md5FromStream($fp, 0, $contentLength));

        $headers = array();
        $headers[HttpHeaders::CONTENT_MD5] = $contentMd5;
        $headers[HttpHeaders::CONTENT_LENGTH] = $contentLength;
        $this->populateRequestHeadersWithOptions($headers, $options);
        list($config) = $this->parseOptions($options, BosOptions::CONFIG);

        $param = array();
        if ($offset !== 0) {
            $param['offset'] = $offset;
        }
        $param['append'] = '';

        try {
            $this->checkData($fp);
            $response = $this->sendRequest(
                HttpMethod::POST,
                array(
                    BosOptions::CONFIG => $config,
                    'bucket_name' => $bucketName,
                    'key' => $key,
                    'body' => $fp,
                    'headers' => $headers,
                    'params' => $param,
                )
            );
            if (is_resource($fp)) {
                fclose($fp);
            }
            return $response;

        } catch (\Exception $e) {
            if (is_resource($fp)) {
                fclose($fp);
            }
            throw $e;
        }
    }

    /**
     * Get the object from a bucket.
     *
     * @param string $bucketName The bucket name.
     * @param string $key The object path.
     * @param resource $outputStream
     * @param mixed $options The optional bce configuration, which will overwrite the
     *   default configuration that was passed while creating BosClient instance.
     * @return mixed
     */
    public function getObject(
        $bucketName,
        $key,
        $outputStream,
        $versionId = '',
        $options = array()
    ) {
        list($config, $range, $tag_req) = $this->parseOptions(
            $options,
            BosOptions::CONFIG,
            BosOptions::RANGE,
            BosOptions::BCE_TAG_DIRECTIVE
        );
        $headers = array();
        if ($tag_req !== null) {
            $headers[HttpHeaders::BCE_TAG_DIRECTIVE] = $tag_req;
        }
        if ($range !== null) {
            switch(gettype($range)) {
                case 'array':
                    if (!isset($range[0]) || !(is_int($range[0]) || is_long($range[0]))) {
                        throw new \InvalidArgumentException(
                            'range[0] is not defined.'
                        );
                    }
                    if (!isset($range[1]) || !(is_int($range[1]) || is_long($range[1]))) {
                        throw new \InvalidArgumentException(
                            'range[1] is not defined.'
                        );
                    }
                    $range = sprintf('%d-%d', $range[0], $range[1]);
                    break;
                case 'string':
                    break;
                default:
                    throw new \InvalidArgumentException(
                        'Option "range" should be either an array of two '
                        . 'integers or a string'
                    );
            }
            $headers[HttpHeaders::RANGE] = sprintf('bytes=%s', $range);
        }

        $response = $this->sendRequest(
            HttpMethod::GET,
            array(
                BosOptions::CONFIG => $config,
                'bucket_name' => $bucketName,
                'key' => $key,
                'headers' => $headers,
                'outputStream' => $outputStream,
                'parseUserMetadata' => true,
                'params' => array(
                    BosOptions::VERSION_ID => $versionId,
                )
            )
        );
        return $response;
    }

    /**
     * Get the object cotent as string
     *
     * @param string $bucketName The bucket name.
     * @param string $key The object path.
     * @param string $range If specified, only get the range part.
     * @param mixed $options The optional bce configuration, which will overwrite the
     *   default configuration that was passed while creating BosClient instance.
     * @return mixed
     */
    public function getObjectAsString(
        $bucketName,
        $key,
        $versionId = '',
        $options = array()
    ) {
        $outputStream = fopen('php://memory', 'r+');
        try {
            $this->getObject($bucketName, $key, $outputStream, $versionId, $options);
            rewind($outputStream);
            $result = stream_get_contents($outputStream);
            if (is_resource($outputStream)) {
                fclose($outputStream);
            }
            return $result;
        } catch (\Exception $e) {
            if (is_resource($outputStream)) {
                fclose($outputStream);
            }
            throw $e;
        }
    }

    /**
     * Get Content of Object and Put Content to File
     *
     * @param string $bucketName The bucket name.
     * @param string $key The object path.
     * @param string $filename The destination file name.
     * @param string $range The HTTP 'Range' header.
     * @param mixed $options The optional bce configuration, which will overwrite the
     *   default configuration that was passed while creating BosClient instance.
     *
     * @return mixed
     */
    public function getObjectToFile(
        $bucketName,
        $key,
        $filename,
        $versionId = '',
        $options = array()
    )
    {
        $outputStream = fopen($filename, 'w+');
        try {
            $response = $this->getObject(
                $bucketName,
                $key,
                $outputStream,
                $options
            );
            if(is_resource($outputStream)) {
                fclose($outputStream);
            }
            return $response;
        } catch (\Exception $e) {
            if(is_resource($outputStream)) {
                fclose($outputStream);
            }
            throw $e;
        }
    }

    /**
     * Delete Object
     *
     * @param string $bucketName The bucket name.
     * @param string $key The object path.
     * @param mixed $options The optional bce configuration, which will overwrite the
     *   default configuration that was passed while creating BosClient instance.
     *
     * @return mixed
     */
    public function deleteObject($bucketName, $key, $versionId = '', $options = array())
    {
        list($config) = $this->parseOptions(
            $options,
            BosOptions::CONFIG
        );

        return $this->sendRequest(
            HttpMethod::DELETE,
            array(
                BosOptions::CONFIG => $config,
                'bucket_name' => $bucketName,
                'key' => $key,
                'params' => array(
                    BosOptions::VERSION_ID => $versionId,
                )
            )
        );
    }
    /**
     * Delete objects in bulk.
     *
     * @param $bucketName string The bucket name.
     * @param $objects string The list of objects to be deleted.
     * @param mixed $options The optional bce configuration, which will overwrite the
     *   default configuration that was passed while creating BosClient instance.
     *
     * @return mixed
     */
    public function deleteMultipleObjects($bucketName, $objects, $options=array())
    {
        list($config) = $this->parseOptions( $options, BosOptions::CONFIG);
        return $this->sendRequest(
            HttpMethod::POST,
            array(
                BosOptions::CONFIG => $config,
                'bucket_name' => $bucketName,
                'body' => json_encode(array(
                    'objects' => $objects
                )),
                'params' => array(
                    BosOptions::DELETE => '',
                )
            )
        );
    }
    /**
     * Get Object meta information
     *
     * @param string $bucketName The bucket name.
     * @param string $key The object path.
     * @param mixed $options The optional bce configuration, which will overwrite the
     *   default configuration that was passed while creating BosClient instance.
     *
     * @return mixed
     */
    public function getObjectMetadata($bucketName, $key, $versionId = '', $options = array())
    {
        list($config) = $this->parseOptions($options, BosOptions::CONFIG);

        $response = $this->sendRequest(
            HttpMethod::HEAD,
            array(
                BosOptions::CONFIG => $config,
                'bucket_name' => $bucketName,
                'key' => $key,
                'parseUserMetadata' => true,
                'params' => array(
                    BosOptions::VERSION_ID => $versionId,
                )
            )
        );
        return $response->metadata;
    }

    /**
     * Copy one object to another.
     *
     * @param string $sourceBucketName The source bucket name.
     * @param string $sourceKey The source object path.
     * @param string $targetBucketName The target bucket name.
     * @param string $targetKey The target object path.
     * @param mixed $options The optional bce configuration, which will overwrite the
     *   default configuration that was passed while creating BosClient instance.
     *
     * @return mixed
     */
    public function copyObject(
        $sourceBucketName,
        $sourceKey,
        $targetBucketName,
        $targetKey,
        $options = array()
    ) {
        if (empty($sourceBucketName)) {
            throw new \InvalidArgumentException(
                '$sourceBucketName should not be empty or null.'
            );
        }
        if (empty($sourceKey)) {
            throw new \InvalidArgumentException(
                '$sourceKey should not be empty or null.'
            );
        }
        if (!isset($this->config[BceClientConfigOptions::CUSTOM]) && empty($targetBucketName)) {
            throw new \InvalidArgumentException(
                '$targetBucketName should not be empty or null.'
            );
        }
        if (empty($targetKey)) {
            throw new \InvalidArgumentException(
                '$targetKey should not be empty or null.'
            );
        }

        list($config, $userMetadata, $etag, $storageClass, $keepTime, $contentType, $tag_req) = $this->parseOptions(
            $options,
            BosOptions::CONFIG,
            BosOptions::USER_METADATA,
            BosOptions::ETAG,
            BosOptions::STORAGE_CLASS,
            BosOptions::BCE_KEEP_TIME,
            BosOptions::CONTENT_TYPE,
            BosOptions::BCE_TAG_DIRECTIVE
        );

        $headers = array();

        if ($tag_req !== null) {
            $headers[HttpHeaders::BCE_TAG_DIRECTIVE] = $tag_req;
        }
        $headers[HttpHeaders::BCE_COPY_SOURCE] =
            HttpUtils::urlEncodeExceptSlash(
                sprintf("/%s/%s", $sourceBucketName, $sourceKey)
            );
        if ($etag !== null) {
            $etag = trim($etag, '"');
            $headers[HttpHeaders::BCE_COPY_SOURCE_IF_MATCH] = '"' . $etag . '"';
        }
        if ($userMetadata === null) {
            $headers[HttpHeaders::BCE_COPY_METADATA_DIRECTIVE] = 'copy';
        } else {
            $headers[HttpHeaders::BCE_COPY_METADATA_DIRECTIVE] = 'replace';
            $this->populateRequestHeadersWithUserMetadata(
                $headers,
                $userMetadata
            );
        }
        if ($storageClass !== null) {
            $headers[HttpHeaders::BCE_STORAGE_CLASS] = $storageClass;
        }
        if ($keepTime !== null) {
            $headers[HttpHeaders::BCE_KEEP_TIME] = $headers[HttpHeaders::BCE_COPY_SOURCE];
        }
        if ($contentType !== null) {
           $headers[HttpHeaders::CONTENT_TYPE] = $contentType;
        }

        return $this->sendRequest(
            HttpMethod::PUT,
            array(
                BosOptions::CONFIG => $config,
                'bucket_name' => $targetBucketName,
                'key' => $targetKey,
                'headers' => $headers,
            )
        );
    }

    /**
     * Initialize multi_upload_file.
     *
     * @param string $bucketName The bucket name.
     * @param string $key The object path.
     * @param mixed $options The optional bce configuration, which will overwrite the
     *   default configuration that was passed while creating BosClient instance.
     *
     * @return mixed
     */
    public function initiateMultipartUpload(
        $bucketName,
        $key,
        $options = array()
    ) {
        $headers = array();
        $this->populateRequestHeadersWithOptions($headers, $options);
        list($config,
            $copySourceBucket,
            $copySourceObject) = $this->parseOptions(
            $options, 
            BosOptions::CONFIG,
            BosOptions::COPY_SOURCE_BUCKET,
            BosOptions::COPY_SOURCE_OBJECT);

        if ($copySourceBucket != null && $copySourceObject != null) {
            $headers[HttpHeaders::BCE_KEEP_TIME] =
                HttpUtils::urlEncodeExceptSlash(
                    sprintf("/%s/%s", $copySourceBucket, $copySourceObject));
        }

        return $this->sendRequest(
            HttpMethod::POST,
            array(
                BosOptions::CONFIG => $config,
                'bucket_name' => $bucketName,
                'key' => $key,
                'headers' => $headers,
                'params' => array('uploads' => ''),
            )
        );
    }

    /**
     * Abort upload a part which is being uploading.
     *
     * @param string $bucketName The bucket name.
     * @param string $key The object path.
     * @param string $uploadId The uploadId returned by initiateMultipartUpload.
     * @param mixed $options The optional bce configuration, which will overwrite the
     *   default configuration that was passed while creating BosClient instance.
     *
     * @return mixed
     */
    public function abortMultipartUpload(
        $bucketName,
        $key,
        $uploadId,
        $options = array()
    ) {
        list($config) = $this->parseOptions($options, BosOptions::CONFIG);

        return $this->sendRequest(
            HttpMethod::DELETE,
            array(
                BosOptions::CONFIG => $config,
                'bucket_name' => $bucketName,
                'key' => $key,
                'params' => array('uploadId' => $uploadId),
            )
        );
    }
    /**
     * upload a part by copy from another object from bos
     *
     * @param string $sourceBucketName  
     * @param string $sourceKey source object
     * @param string $targetBucketName
     * @param string $targetKey target object
     * @param string $uploadId The uploadId returned by initiateMultipartUpload.
     * @param int $partNumber The part index, 1-based.
     * @param mixed $options The optional bce configuration, which will overwrite the
     *   default configuration that was passed while creating BosClient instance.
     *
     * @return mixed
     */
    public function uploadPartCopy(
        $sourceBucketName,
        $sourceKey,
        $targetBucketName,
        $targetKey,
        $uploadId,
        $partNumber,
        $options = array()
    ) {
    
        if (empty($sourceBucketName)) {
            throw new \InvalidArgumentException(
                '$sourceBucketName should not be empty or null.'
            );
        }
        if (empty($sourceKey)) {
            throw new \InvalidArgumentException(
                '$sourceKey should not be empty or null.'
            );
        }
        if (empty($targetBucketName)) {
            throw new \InvalidArgumentException(
                '$targetBucketName should not be empty or null.'
            );
        }
        if (empty($targetKey)) {
            throw new \InvalidArgumentException(
                '$targetKey should not be empty or null.'
            );
        }
        if ($partNumber < BosClient::MIN_PART_NUMBER
            || $partNumber > BosClient::MAX_PART_NUMBER
        ) {
            throw new \InvalidArgumentException(
                sprintf(
                    'Invalid $partNumber %d. The valid range is from %d to %d.',
                    $partNumber,
                    BosClient::MIN_PART_NUMBER,
                    BosClient::MAX_PART_NUMBER
                )
            );
        }

        list($config,$etag,$unEtag,$unModified,$modified,$range) = $this->parseOptions(
            $options,
            BosOptions::CONFIG,
            BosOptions::OBJECT_COPY_SOURCE_IF_MATCH_TAG,
            BosOptions::OBJECT_COPY_SOURCE_IF_NONE_MATCH_TAG,
            BosOptions::OBJECT_COPY_SOURCE_IF_UNMODIFIED_SINCE,
            BosOptions::OBJECT_COPY_SOURCE_IF_MODIFIED_SINCE,
            BosOptions::RANGE
        );

        $headers = array();

        $headers[HttpHeaders::BCE_COPY_SOURCE] =
            HttpUtils::urlEncodeExceptSlash(
                sprintf("/%s/%s", $sourceBucketName, $sourceKey)
            );
        if ($etag != null) {
            $etag = trim($etag, '"');
            $headers[HttpHeaders::BCE_COPY_SOURCE_IF_MATCH] = '"' . $etag . '"';
        }
        if ($unEtag != null) {
            $unEtag = trim($unEtag,'"');
            $headers[HttpHeaders::BCE_COPY_SOURCE_IF_NONE_MATCH] = '"' . $unEtag . '"';
        }
        if ($unModified != null) {
            $headers[HttpHeaders::BCE_COPY_SOURCE_IF_UNMODIFIED_SINCE]
                = $unModified;
        }
        if ($modified != null) {
            $headers[HttpHeaders::BCE_COPY_SOURCE_IF_MODIFIED_SINCE]
                = $modified;
        }
        if ($range !== null) {
            switch(gettype($range)) {
                case 'array':
                    if (!isset($range[0]) || !(is_int($range[0]) || is_long($range[0]))) {
                        throw new \InvalidArgumentException(
                            'range[0] is not defined.'
                        );
                    }
                    if (!isset($range[1]) || !(is_int($range[1]) || is_long($range[1]))) {
                        throw new \InvalidArgumentException(
                            'range[1] is not defined.'
                        );
                    }
                    $range = sprintf('%d-%d', $range[0], $range[1]);
                    break;
                case 'string':
                    break;
                default:
                    throw new \InvalidArgumentException(
                        'Option "range" should be either an array of two '
                        . 'integers or a string'
                    );
            }
            $headers[HttpHeaders::BCE_COPY_RANGE] = sprintf('bytes=%s', $range);
        }

        return $this->sendRequest(
            HttpMethod::PUT,
            array(
                BosOptions::CONFIG => $config,
                'bucket_name' => $targetBucketName,
                'key' => $targetKey,
                'params' => array(
                    'partNumber' => $partNumber,
                    'uploadId' => $uploadId,
                ),
                'headers' => $headers,
            )
        );
    }

    /**
     * Upload a part from a file handle
     *
     * @param string $bucketName The bucket name.
     * @param string $key The object path.
     * @param string $uploadId The uploadId returned by initiateMultipartUpload.
     * @param int $partNumber The part index, 1-based.
     * @param int $contentLength The uploaded part size.
     * @param string $contentMd5 The part md5 check sum.
     * @param string $data The file pointer.
     * @param mixed $options The optional bce configuration, which will overwrite the
     *   default configuration that was passed while creating BosClient instance.
     *
     * @return mixed
     */
    public function uploadPart(
        $bucketName,
        $key,
        $uploadId,
        $partNumber,
        $contentLength,
        $contentMd5,
        $data,
        $options = array()
    ) {
        if (!isset($this->config[BceClientConfigOptions::CUSTOM]) && empty($bucketName)) {
            throw new \InvalidArgumentException(
                '$bucketName should not be empty or null.'
            );
        }
        if (empty($key)) {
            throw new \InvalidArgumentException(
                '$key should not be empty or null.'
            );
        }
        if (!is_int($contentLength) && !is_long($contentLength)) {
            throw new \InvalidArgumentException(
                '$contentLength should be int or long.'
            );
        }
        if ($partNumber < BosClient::MIN_PART_NUMBER
            || $partNumber > BosClient::MAX_PART_NUMBER
        ) {
            throw new \InvalidArgumentException(
                sprintf(
                    'Invalid $partNumber %d. The valid range is from %d to %d.',
                    $partNumber,
                    BosClient::MIN_PART_NUMBER,
                    BosClient::MAX_PART_NUMBER
                )
            );
        }

        if ($contentMd5 === null) {
            throw new \InvalidArgumentException(
                '$contentMd5 should not be null.'
            );
        }

        $this->checkData($data);

        list($config, $contentCRC32, $contentSHA256) = $this->parseOptions($options, 
            BosOptions::CONFIG, 
            BosOptions::CONTENT_CRC32,
            BosOptions::CONTENT_SHA256
        );

        $headers = array();
        $headers[HttpHeaders::CONTENT_MD5] = $contentMd5;
        $headers[HttpHeaders::CONTENT_LENGTH] = $contentLength;
        $headers[HttpHeaders::CONTENT_TYPE] = HttpContentTypes::OCTET_STREAM;
        if ($contentCRC32 !== null) {
            $headers[HttpHeaders::BCE_CONTENT_CRC32] = $contentCRC32;
        }
        if ($contentSHA256 !== null) {
            $headers[HttpHeaders::BCE_CONTENT_SHA256] = $contentSHA256;
        }

        return $this->sendRequest(
            HttpMethod::PUT,
            array(
                BosOptions::CONFIG => $config,
                'bucket_name' => $bucketName,
                'key' => $key,
                'body' => $data,
                'params' => array(
                    'partNumber' => $partNumber,
                    'uploadId' => $uploadId
                ),
                'headers' => $headers,
            )
        );
    }

    /**
     * Upload a part from starting with offset.
     *
     * @param string $bucketName The bucket name.
     * @param string $key The object path.
     * @param string $uploadId The uploadId returned by initiateMultipartUpload.
     * @param number $partNumber The part index, 1-based.
     * @param number $length The uploaded part size.
     * @param string $filename The file name.
     * @param number $offset The file offset.
     * @param number $contentMd5 The part md5 check sum.
     * @param mixed $options The optional bce configuration, which will overwrite the
     *   default configuration that was passed while creating BosClient instance.
     *
     * @return mixed
     */
    public function uploadPartFromFile(
        $bucketName,
        $key,
        $uploadId,
        $partNumber,
        $filename,
        $offset = 0,
        $length = -1,
        $options = array()
    ) {
        if (!is_int($offset) && !is_long($offset)) {
            throw new \InvalidArgumentException(
                '$offset should be int or long.'
            );
        }
        if (!is_int($length) && !is_long($length)) {
            throw new \InvalidArgumentException(
                '$length should be int or long.'
            );
        }

        $fp = fopen($filename, 'rb');
        try {
            if ($length < 0) {
                fseek($fp, 0, SEEK_END);
                $length = ftell($fp) - $offset;
            }
            $contentMd5 = base64_encode(HashUtils::md5FromStream($fp, $offset, $length));
            fseek($fp, $offset, SEEK_SET);
            $response = $this->uploadPart(
                $bucketName,
                $key,
                $uploadId,
                $partNumber,
                $length,
                $contentMd5,
                $fp,
                $options
            );
            //guzzle will close fp
            if (is_resource($fp)) {
                fclose($fp);
            }
            return $response;
        } catch (\Exception $e) {
            if(is_resource($fp)) {
                fclose($fp);
            }
            throw $e;
        }
    }

    /**
     * List parts that have been upload success.
     *
     * @param string $bucketName The bucket name.
     * @param string $key The object path.
     * @param string $uploadId The uploadId returned by initiateMultipartUpload.
     * @param mixed $options The optional bce configuration, which will overwrite the
     *   default configuration that was passed while creating BosClient instance.
     *
     * @return mixed
     */
    public function listParts($bucketName, $key, $uploadId, $options = array())
    {
        if (!isset($this->config[BceClientConfigOptions::CUSTOM]) && empty($bucketName)) {
            throw new \InvalidArgumentException(
                '$bucketName should not be empty or null.'
            );
        }
        if (empty($key)) {
            throw new \InvalidArgumentException(
                '$key should not be empty or null.'
            );
        }
        if (empty($uploadId)) {
            throw new \InvalidArgumentException(
                '$uploadId should not be empty or null.'
            );
        }

        list($config, $maxParts, $partNumberMarker) = $this->parseOptions(
            $options,
            BosOptions::CONFIG,
            BosOptions::LIMIT,
            BosOptions::MARKER
        );
        $params = array();
        $params['uploadId'] = $uploadId;
        if ($maxParts !== null) {
            if (is_numeric($maxParts)) {
                $maxParts = number_format($maxParts);
                $maxParts = str_replace(',','',$maxParts);
            }
            $params['maxParts'] = $maxParts;
        }
        if ($partNumberMarker !== null) {
            $params['partNumberMarker'] = $partNumberMarker;
        }

        return $this->sendRequest(
            HttpMethod::GET,
            array(
                BosOptions::CONFIG => $config,
                'bucket_name' => $bucketName,
                'key' => $key,
                'params' => $params,
            )
        );
    }

    /**
     * After finish all the task, complete multi_upload_file.
     * bucket, key, upload_id, part_list, options=None
     *
     * @param string $bucketName The bucket name.
     * @param string $key The object path.
     * @param string $uploadId The upload id.
     * @param array $partList (partnumber and etag) list
     * @param array $options
     *
     * @return mixed
     */
    public function completeMultipartUpload(
        $bucketName,
        $key,
        $uploadId,
        array $partList,
        $options = array()
    ) {
        if (!isset($this->config[BceClientConfigOptions::CUSTOM]) && empty($bucketName)) {
            throw new \InvalidArgumentException(
                '$bucketName should not be empty or null.'
            );
        }
        if (empty($key)) {
            throw new \InvalidArgumentException(
                '$key should not be empty or null.'
            );
        }
        if (empty($uploadId)) {
            throw new \InvalidArgumentException(
                '$uploadId should not be empty or null.'
            );
        }

        $headers = array();
        $this->populateRequestHeadersWithOptions($headers, $options);
        list($config) = $this->parseOptions($options, BosOptions::CONFIG);

        return $this->sendRequest(
            HttpMethod::POST,
            array(
                BosOptions::CONFIG => $config,
                'bucket_name' => $bucketName,
                'key' => $key,
                'body' => json_encode(array('parts' => $partList)),
                'headers' => $headers,
                'params' => array('uploadId' => $uploadId),
            )
        );
    }

    /**
     * List Multipart upload task which haven't been ended.
     * call initiateMultipartUpload but not call completeMultipartUpload or abortMultipartUpload
     *
     * @param string $bucketName The bucket name.
     * @param mixed $options The optional bce configuration, which will overwrite the
     *   default configuration that was passed while creating BosClient instance.
     *
     * @return mixed
     */
    public function listMultipartUploads($bucketName, $options = array())
    {
        list(
            $config,
            $keyMarker,
            $maxUploads,
            $delimiter,
            $prefix
        ) = $this->parseOptions(
            $options,
            BosOptions::CONFIG,
            BosOptions::MARKER,
            BosOptions::LIMIT,
            BosOptions::DELIMITER,
            BosOptions::PREFIX
        );
        $params = array();
        $params['uploads'] = '';
        if ($keyMarker !== null) {
            $params['keyMarker'] = $keyMarker;
        }
        if ($maxUploads !== null) {
            if (is_numeric($maxUploads)) {
                $maxUploads = number_format($maxUploads);
                $maxUploads = str_replace(',','',$maxUploads);
            }
            $params['maxUploads'] = $maxUploads;
        }
        if ($delimiter !== null) {
            $params['delimiter'] = $delimiter;
        }
        if ($prefix !== null) {
            $params['prefix'] = $prefix;
        }

        return $this->sendRequest(
            HttpMethod::GET,
            array(
                BosOptions::CONFIG => $config,
                'bucket_name' => $bucketName,
                'params' => $params,
            )
        );
    }

    /**
     * Put super file to the object using the multipart upload interface
     *
     * @param string $bucketName The bucket name.
     * @param string $key The object path.
     * @param string $filename The absolute file path.
     * @param mixed $options The optional bce configuration, which will overwrite the
     *   default configuration that was passed while creating BosClient instance.
     *
     * @return mixed
     */
    public function putSuperObjectFromFile(
        $bucketName,
        $key,
        $filename,
        $options = array()
    ) {
        $partSize = self::MIN_PART_SIZE; // default part size
        if (isset($options[BosOptions::PART_SIZE])) {
            $partSize = $options[BosOptions::PART_SIZE];
            if ($partSize < 1024 * 1024 || $partSize > self::MAX_PUT_OBJECT_LENGTH) {
                throw new InvalidArgumentException(
                    'multipart size should not be less than 1MB or greater than 5GB'
                );
            }
        }

        // step1: init multipart upload
        $offset = 0;
        $partNumber = 1;
        $bytesLeft = filesize($filename);
        $partList = array();
        $response = $this->initiateMultipartUpload($bucketName, $key, $options);
        $uploadId =$response->uploadId;

        // step2: upload multipart one by one
        while ($bytesLeft > 0) {
            $partSize = ($partSize > $bytesLeft) ? $bytesLeft : $partSize;
            try {
                $response = $this->uploadPartFromFile($bucketName,
                    $key,
                    $uploadId,
                    $partNumber,
                    $filename,
                    $offset,
                    $partSize);
            } catch (BceServiceException $e) {
                $this->abortMultipartUpload($bucketName, $key, $uploadId);
                throw $e;
            }
            array_push(
                $partList,
                array("partNumber"=>$partNumber, "eTag"=>$response->metadata[BosOptions::ETAG],)
            );
            $offset += $partSize;
            $partNumber++;
            $bytesLeft -= $partSize;
        }

        // step3: complete multipart upload with user-metadata
        $completeOptions = array();
        if (isset($options[BosOptions::USER_METADATA])) {
            $completeOptions = array(
                BosOptions::USER_METADATA => $options[BosOptions::USER_METADATA]

            );
        }
        return $this->completeMultipartUpload($bucketName, $key, $uploadId, $partList, $completeOptions);
    }

    /**
     * Create HttpClient and send request
     * @param string $httpMethod The Http request method
     * @param array $varArgs The extra arguments
     * @return mixed The Http response and headers.
     */
    private function sendRequest($httpMethod, array $varArgs)
    {
        $defaultArgs = array(
            BosOptions::CONFIG => array(),
            'bucket_name' => null,
            'key' => null,
            'body' => null,
            'headers' => array(),
            'params' => array(),
            'outputStream' => null,
            'parseUserMetadata' => false
        );

        $args = array_merge($defaultArgs, $varArgs);
        if (empty($args[BosOptions::CONFIG])) {
            $config = $this->config;
        } else {
            $config = array_merge(
                array(),
                $this->config,
                $args[BosOptions::CONFIG]
            );
        }
        if (!isset($args['headers'][HttpHeaders::CONTENT_TYPE])) {
            $args['headers'][HttpHeaders::CONTENT_TYPE] =
                HttpContentTypes::JSON;
        }
        // prevent low version curl add a default pragma:no-cache
        if (!isset($args['headers'][HttpHeaders::PRAGMA])) {
            $args['headers'][HttpHeaders::PRAGMA] = '';
        }

        if (isset($varArgs[BosOptions::IS_BUCKET_API]) && $varArgs[BosOptions::IS_BUCKET_API] == true) {
            $this->genEndpoint(true, $config, $args['bucket_name']);
        } else {
            $this->genEndpoint(false, $config, $args['bucket_name']);
        }
        $path = $this->getPath($args['bucket_name'], $args['key'], $config);

        $response = $this->httpClient->sendRequest(
            $config,
            $httpMethod,
            $path,
            $args['body'],
            $args['headers'],
            $args['params'],
            $this->signer,
            $args['outputStream']
        );
        if ($args['outputStream'] === null) {
            $result = $this->parseJsonResult($response['body']);
        } else {
            $result = new \stdClass();
        }
        $result->metadata =
            $this->convertHttpHeadersToMetadata($response['headers']);
        if ($args['parseUserMetadata']) {
            $userMetadata = array();
            foreach ($response['headers'] as $key => $value) {
                if (StringUtils::startsWith($key, HttpHeaders::BCE_USER_METADATA_PREFIX)) {
                    $key = substr($key, strlen(HttpHeaders::BCE_USER_METADATA_PREFIX));
                    $userMetadata[urldecode($key)] = urldecode($value);
                }
            }
            $result->metadata[BosOptions::USER_METADATA] = $userMetadata;
        }

        return $result;

    }

    /**
     * @param string $bucketName The bucket name.
     * @param string $key The object path.
     *
     * @return string
     */
    private function getPath($bucketName = null, $key = null, $config = null)
    {
        if (!isset($config)) {
            if (isset($this->config[BceClientConfigOptions::CUSTOM]) && $this->config[BceClientConfigOptions::CUSTOM] === true) {
              $bucketName = null;
            }
        } else {
            if (isset($config[BceClientConfigOptions::CUSTOM]) && $config[BceClientConfigOptions::CUSTOM] === true) {
              $bucketName = null;
            }
        }
        return HttpUtils::appendUri(self::BOS_URL_PREFIX, $bucketName, $key);
    }

    /**
     * @param array $headers
     * @param array $options
     */
    private function populateRequestHeadersWithOptions(
        array &$headers,
        array &$options
    ) {
        list(
            $contentType,
            $contentSHA256,
            $storageClass,
            $contentCRC32,
            $xBceAcl,
            $xBceGrantRead,
            $xBceGrantFullControl,
            $cacheControl,
            $contentDisposition,
            $expires,
            $xBceSideEncryption,
            $userMetadata,
            $xBceProcess,
            $xBceForbidOverwrite,
            $xBceTagging
        ) = $this->parseOptionsIgnoreExtra(
            $options,
            BosOptions::CONTENT_TYPE,
            BosOptions::CONTENT_SHA256,
            BosOptions::STORAGE_CLASS,
            BosOptions::CONTENT_CRC32,
            BosOptions::BCE_ACL,
            BosOptions::BCE_ACL_GRANT_READ,
            BosOptions::BCE_ACL_GRANT_FULL_CONTROL,
            BosOptions::CACHE_CONTROL,
            BosOptions::CONTENT_DISPOSITION,
            BosOptions::EXPIRES,
            BosOptions::BCE_SERVER_SIDE_ENCRYPTION,
            BosOptions::USER_METADATA,
            BosOptions::BCE_PROCESS,
            BosOptions::BCE_FORBID_OVERWRITE,
            BosOptions::BCE_TAG
        );
        if ($contentType !== null) {
            $headers[HttpHeaders::CONTENT_TYPE] = $contentType;
            unset($options[BosOptions::CONTENT_TYPE]);
        }
        if ($contentSHA256 !== null) {
            $headers[HttpHeaders::BCE_CONTENT_SHA256] = $contentSHA256;
            unset($options[BosOptions::CONTENT_SHA256]);
        }
        if ($contentCRC32 !== null) {
            $headers[HttpHeaders::BCE_CONTENT_CRC32] = $contentCRC32;
            unset($options[BosOptions::CONTENT_CRC32]);
        }
        if ($storageClass !== null) {
            $headers[HttpHeaders::BCE_STORAGE_CLASS] = $storageClass;
            unset($options[BosOptions::STORAGE_CLASS]);
        }
        if ($xBceAcl !== null) {
            $headers[HttpHeaders::BCE_ACL] = $xBceAcl;
            unset($options[BosOptions::BCE_ACL]);
        }
        if ($xBceGrantRead !== null) {
            $headers[HttpHeaders::BCE_ACL_GRANT_READ] = $xBceGrantRead;
            unset($options[BosOptions::BCE_ACL_GRANT_READ]);
        }
        if ($xBceGrantFullControl !== null) {
            $headers[HttpHeaders::BCE_ACL_GRANT_FULL_CONTROL] = $xBceGrantFullControl;
            unset($options[BosOptions::BCE_ACL_GRANT_FULL_CONTROL]);
        }
        if ($cacheControl !== null) {
            $headers[HttpHeaders::CACHE_CONTROL] = $cacheControl;
            unset($options[BosOptions::CACHE_CONTROL]);
        }
        if ($contentDisposition !== null) {
            $headers[HttpHeaders::CONTENT_DISPOSITION] = $contentDisposition;
            unset($options[BosOptions::CONTENT_DISPOSITION]);
        }
        if ($expires !== null) {
            $headers[HttpHeaders::EXPIRES] = $expires;
            unset($options[BosOptions::EXPIRES]);
        }
        if ($xBceSideEncryption !== null) {
            $headers[HttpHeaders::BCE_SERVER_SIDE_ENCRYPTION] = $xBceSideEncryption;
            unset($options[BosOptions::BCE_SERVER_SIDE_ENCRYPTION]);
        }
        if ($userMetadata !== null) {
            $this->populateRequestHeadersWithUserMetadata($headers, $userMetadata);
            unset($options[BosOptions::USER_METADATA]);
        }
        if ($xBceProcess !== null) {
            $headers[HttpHeaders::BCE_PROCESS] = $xBceProcess;
            unset($options[BosOptions::BCE_PROCESS]);
        }
        if ($xBceForbidOverwrite !== null) {
            $headers[HttpHeaders::BCE_FORBID_OVERWRITE] = $xBceForbidOverwrite;
            unset($options[BosOptions::BCE_FORBID_OVERWRITE]);
        }
        if ($xBceTagging !== null) {
            $headers[HttpHeaders::BCE_TAG] = $xBceTagging;
            unset($options[BosOptions::BCE_TAG]);
        }

        reset($options);
    }

    /**
     * @param array $headers
     * @param array $userMetadata
     */
    private function populateRequestHeadersWithUserMetadata(
        array &$headers,
        array $userMetadata
    ) {
        $metaSize = 0;
        foreach ($userMetadata as $key => $value) {
            $key = HttpHeaders::BCE_USER_METADATA_PREFIX
                . HttpUtils::urlEncode(trim($key));
            $value = HttpUtils::urlEncode($value);
            $metaSize += strlen($key) + strlen($value);
            if ($metaSize > BosClient::MAX_USER_METADATA_SIZE) {
                throw new BceClientException(
                    'User metadata size should not be greater than '
                    . BosClient::MAX_USER_METADATA_SIZE
                );
            }
            $headers[$key] = $value;
        }
    }

    /**
     * @param string|resource $data
     */
    private function checkData($data)
    {
        switch(gettype($data)) {
            case 'string':
                break;
            case 'resource':
                $streamMetadata = stream_get_meta_data($data);
                if (!$streamMetadata['seekable']) {
                    throw new \InvalidArgumentException(
                        '$data should be seekable.'
                    );
                }
                break;
            default:
                throw new \InvalidArgumentException(
                    'Invalid data type:' . gettype($data)
                    . ' Only string or resource is accepted.'
                );
        }
    }
    /**
     * Set user's max bucket count  and  object count.
     *
     * @param int $maxBucketCount The max bucket count.
     * @param int $maxCapacityMegaBytes The maximum capacity limit of a bucket.
     * @param int $maxObjectCount The max object count of a bucket.
     * @param mixed $options The optional bce configuration, which will overwrite the
     *   default configuration that was passed while creating BosClient instance.
    */
    public function putUserQuota($maxBucketCount, $maxCapacityMegaBytes, $maxObjectCount, $options = array()){
        if (!is_int($maxBucketCount) || !is_int($maxCapacityMegaBytes) || $maxBucketCount < -1 || $maxCapacityMegaBytes < -1) {
            throw new InvalidArgumentException("Parameters should be valid");
        }
        list($config) = $this->parseOptions($options, BosOptions::CONFIG);
        $quotaArray = array(
            'maxBucketCount' => $maxBucketCount,
            'maxCapacityMegaBytes' => $maxCapacityMegaBytes,
        );
        if($maxObjectCount) {
            if (is_int($maxObjectCount) && $maxObjectCount >= 1) {
                $quotaArray['maxObjectCount'] = $maxObjectCount;
            }
        }
        return $this->sendRequest(
            HttpMethod::PUT,
            array(
                BosOptions::CONFIG => $config,
                'body' => json_encode($quotaArray),
                'params' => array(
                    BosOptions::USER_QUOTA => '',
                )
            )
        );
    }

    /**
     *Get user quota.
     *
     * @param mixed $options The optional bce configuration, which will overwrite the
     *   default configuration that was passed while creating BosClient instance.
     *
     * @return string
     */
    public function getUserQuota($options = array()) {
        list($config) = $this->parseOptions($options, BosOptions::CONFIG);
        return $this->sendRequest(
            HttpMethod::GET,
            array(
                BosOptions::CONFIG => $config,
                'params' => array(
                    BosOptions::USER_QUOTA => '',
                )
            )
        );
    }
    /**
     * Delete user quota.
     *
     * @param mixed $options The optional bce configuration, which will overwrite the
     *   default configuration that was passed while creating BosClient instance.
     */
    public function deleteUserQuota($options = array())
    {
        list($config) = $this->parseOptions($options, BosOptions::CONFIG);
        return $this->sendRequest(
            HttpMethod::DELETE,
            array(
                BosOptions::CONFIG => $config,
                'params' => array(
                    BosOptions::USER_QUOTA => '',
                )
            )
        );
    }

    /**
     * Initialize compliance reservation.
     *
     * @param $bucketName string The bucket name.
     * @param $retentionDays int The retention days.
     * @param mixed $options The optional bce configuration, which will overwrite the
     *   default configuration that was passed while creating BosClient instance.
     *
     * @return mixed
     */
    public function initBucketObjectLock($bucketName, $retentionDays, $options = array())
    {
        list($config) = $this->parseOptions($options, BosOptions::CONFIG);
        return $this->sendRequest(
            HttpMethod::POST,
            array(
                BosOptions::CONFIG => $config,
                'bucket_name' => $bucketName,
                'body' =>json_encode(array(
                    'retentionDays' => $retentionDays
                )),
                'params' => array(
                    BosOptions::OBJECT_LOCK => '',
                )
            )
        );
    }

    /**
     * Get bucket compliance reservation.
     *
     * @param $bucketName string The bucket name.
     * @param mixed $options The optional bce configuration, which will overwrite the
     *   default configuration that was passed while creating BosClient instance.
     *
     * @return mixed
     */
    public function getBucketObjectLock($bucketName, $options = array()){
        list($config) = $this->parseOptions( $options, BosOptions::CONFIG);
        return $this->sendRequest(
            HttpMethod::GET,
            array(
                BosOptions::CONFIG => $config,
                'bucket_name' => $bucketName,
                'params' => array(
                    BosOptions::OBJECT_LOCK => '',
                )
            )
        );
    }

    /**
     * Delete bucket compliance reservation.
     * @param $bucketName string The bucket name.
     * @param mixed $options The optional bce configuration, which will overwrite the
     *   default configuration that was passed while creating BosClient instance.
     *
     * @return mixed
     */
    public function deleteBucketObjectLock($bucketName, $options = array())
    {
        list($config) = $this->parseOptions($options, BosOptions::CONFIG);
        return $this->sendRequest(
            HttpMethod::DELETE,
            array(
                BosOptions::CONFIG => $config,
                'bucket_name' => $bucketName,
                'params' => array(
                    BosOptions::OBJECT_LOCK => '',
                )
            )
        );
    }

    /**
     * Extend the days of bucket compliance reservation.
     *
     * @param $bucketName string The bucket name.
     * @param $extendRetentionDays int The extendRetention days.
     * @param mixed $options The optional bce configuration, which will overwrite the
     *   default configuration that was passed while creating BosClient instance.
     *
     * @return mixed
     */
    public function extendBucketObjectLock($bucketName, $extendRetentionDays, $options = array())
    {
        if (!is_int($extendRetentionDays) || $extendRetentionDays < 0) {
            throw new InvalidArgumentException("Parameters should be valid");
        }
        list($config) = $this->parseOptions($options, BosOptions::CONFIG);
        return $this->sendRequest(
            HttpMethod::POST,
            array(
                BosOptions::CONFIG => $config,
                'bucket_name' => $bucketName,
                'body' => json_encode(array(
                    'extendRetentionDays' => $extendRetentionDays,
                )),
                'params' => array(
                    BosOptions::EXTEND_OBJECT_LOCK => '',
                )
            )
        );
    }

    /**
     * The retention policy compliance immediately locked into a locked state LOCKED.
     *
     * @param $bucketName string The bucket name.
     * @param mixed $options The optional bce configuration, which will overwrite the
     *   default configuration that was passed while creating BosClient instance.
     *
     * @return mixed
     */
    public function completeBucketObjectLock($bucketName, $options = array())
    {
        list($config) = $this->parseOptions($options, BosOptions::CONFIG);
        return $this->sendRequest(
            HttpMethod::POST,
            array(
                BosOptions::CONFIG => $config,
                'bucket_name' => $bucketName,
                'params' => array(
                    BosOptions::COMPLETE_OBJECT_LOCK => '',
                )
            )
        );
    }

    /**
     * Set the notification rules on the specified bucket.
     *
     * @param $bucketName string The bucket name.
     * @param $notifications string The content of notifications.
     * @param mixed $options The optional bce configuration, which will overwrite the
     *   default configuration that was passed while creating BosClient instance.
     *
     * @return mixed
     */
    public function putNotification($bucketName, $notifications, $options = array())
    {
        list($config) = $this->parseOptions($options, BosOptions::CONFIG);
        return $this->sendRequest(
            HttpMethod::PUT,
            array(
                BosOptions::CONFIG => $config,
                'bucket_name' => $bucketName,
                'body' => json_encode(array(
                    'notifications' => $notifications
                )),
                'params' => array(
                    BosOptions::NOTIFICATION => '',
                )
            )
        );
    }

    /**
     * Get the notification rules on the specified bucket.
     *
     * @param $bucketName string The bucket name.
     * @param mixed $options The optional bce configuration, which will overwrite the
     *   default configuration that was passed while creating BosClient instance.
     *
     * @return mixed
     */
    public function getNotification($bucketName, $options = array()) {
        list($config) = $this->parseOptions( $options, BosOptions::CONFIG);
        return $this->sendRequest(
            HttpMethod::GET,
            array(
                BosOptions::CONFIG => $config,
                'bucket_name' => $bucketName,
                'params' => array(
                    BosOptions::NOTIFICATION => '',
                )
            )
        );
    }

    /**
     * Delete the notification rules on the specified bucket.
     *
     * @param $bucketName string The bucket name.
     * @param mixed $options The optional bce configuration, which will overwrite the
     *   default configuration that was passed while creating BosClient instance.
     *
     * @return mixed
     */
    public function deleteNotification($bucketName, $options = array())
    {
        list($config) = $this->parseOptions($options, BosOptions::CONFIG);
        return $this->sendRequest(
            HttpMethod::DELETE,
            array(
                BosOptions::CONFIG => $config,
                'bucket_name' => $bucketName,
                'params' => array(
                    BosOptions::NOTIFICATION => '',
                )
            )
        );
    }

    /**
     * Set the default storage type of Bucket.
     *
     * @param $bucketName string The bucket name.
     * @param $storageClass string The storage class.
     * @param mixed $options The optional bce configuration, which will overwrite the
     *   default configuration that was passed while creating BosClient instance.
     *
     * @return mixed
     */
    public function putBucketStorageClass($bucketName, $storageClass, $options = array()) {
        list($config) = $this->parseOptions($options, BosOptions::CONFIG);
        return $this->sendRequest(
            HttpMethod::PUT,
            array(
                BosOptions::CONFIG => $config,
                'bucket_name' => $bucketName,
                'body' => json_encode(array(
                    'storageClass' => $storageClass
                )),
                'params' => array(
                    BosOptions::STORAGE_CLASS => '',
                )
            )
        );
    }

    /**
     * Get the default storage type of Bucket.
     *
     * @param $bucketName string The bucket name.
     * @param mixed $options The optional bce configuration, which will overwrite the
     *   default configuration that was passed while creating BosClient instance.

     * @return mixed
     */
    public function getBucketStorageClass($bucketName, $options = array()) {
        list($config) = $this->parseOptions( $options, BosOptions::CONFIG);
        return $this->sendRequest(
            HttpMethod::GET,
            array(
                BosOptions::CONFIG => $config,
                'bucket_name' => $bucketName,
                'params' => array(
                    BosOptions::STORAGE_CLASS => '',
                )
            )
        );
    }

    /**
     * Retrieve archived storage files.
     *
     * @param $bucketName string The bucket name.
     * @param $key string The target key.
     * @param $restoreDay int The duration after thawing.
     * @param $tier string The restore speed 
     * @param mixed $options The optional bce configuration, which will overwrite the
     *   default configuration that was passed while creating BosClient instance.
     *
     * @return mixed
     */
    public function restoreObject($bucketName, $key, $restoreDay = 7, $tier = "Standard", $options = array()) {
        list($config) = $this->parseOptions($options, BosOptions::CONFIG);
        if (!is_int($restoreDay)) {
            throw new \InvalidArgumentException(
                '$restoreDay should be int.'
            );
        }
        if (!in_array($tier, array("Standard", "Expedited"))) {
            throw new \InvalidArgumentException(
            "tier is not valid."
           );
        }
        return $this->sendRequest(
            HttpMethod::POST,
            array(
                BosOptions::CONFIG => $config,
                'bucket_name' => $bucketName,
                'headers' => array(
                    HttpHeaders::BCE_RESTORE_DAYS => $restoreDay,
                    HttpHeaders::BCE_RESTORE_TIER => $tier,
                ),
                'key' => $key,
                'params' => array(
                    BosOptions::RESTORE => '',
                )
            )
        );
      }

}
