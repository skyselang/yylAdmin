<?php
/*
* Copyright (c) 2014 Baidu.com, Inc. All Rights Reserved
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

namespace BaiduBce\Services\Cdn;

use BaiduBce\Auth\BceV1Signer;
use BaiduBce\BceBaseClient;
use BaiduBce\Exception\BceClientException;
use BaiduBce\Http\BceHttpClient;
use BaiduBce\Http\HttpContentTypes;
use BaiduBce\Http\HttpHeaders;
use BaiduBce\Http\HttpMethod;

class CdnClient extends BceBaseClient
{
    /**
     * @var \BaiduBce\Auth\SignerInterface
     */
    private $signer;
    private $httpClient;
    private $prefix = '/v2';

    /**
     * The CdnClient constructor
     *
     * @param array $config The client configuration
     */
    function __construct(array $config)
    {
        parent::__construct($config, 'CdnClient');
        $this->signer = new BceV1Signer();
        $this->httpClient = new BceHttpClient();
    }

    /**
     * List all domains of current user.
     *
     * @param array $options None
     * @return domain list the server response.
     * @throws BceClientException
     */
    public function listDomains($options = array())
    {
        list($config) = $this->parseOptions($options, 'config');
        $params = array();

        return $this->sendRequest(
            HttpMethod::GET,
            array(
                'config' => $config,
                'params' => $params,
            ),
            '/domain'
        );
    }

    /**
     * list all domains of this user where options
     *
     * @param $options
     * @return mixed
     */
    public function listUserDomains($options)
    {
        $params = $options;

        return $this->sendRequest(
            HttpMethod::GET,
            array(
                'params' => $params,
            ),
            '/user/domains'
        );
    }

    /**
     *query if domain can be add
     *
     * @param $domain
     * @return mixed
     * @throws BceClientException
     */
    public function validDomain($domain)
    {
        if (empty($domain)) {
            throw new BceClientException("The parameter domain should NOT be null");
        }

        $params = array();

        return $this->sendRequest(
            HttpMethod::GET,
            array(
                'params' => $params,
            ),
            '/domain/'.$domain.'/valid'
        );
    }

    /**
     * create domain
     * @param array $options None
     * @param string $domain [<the domain name>]
     * @param array $origin [<the origin address list>]
     * @return response
     * @throws BceClientException
     */
    public function createDomain($domain, $origin, $options = array())
    {
        if (empty($domain)) {
            throw new BceClientException("The parameter domain should NOT be null");
        }

        if (empty($origin)) {
            throw new BceClientException("The parameter origin should NOT be null");
        }

        list($config) = $this->parseOptionsIgnoreExtra($options, 'config');

        if (!empty($config)) {
            unset($options['config']);
        }
        $params = $options;

        return $this->sendRequest(
            HttpMethod::PUT,
            array(
                'config' => $config,
                'params' => $params,
                'body' => json_encode(array('origin' => $origin)),
            ),
            '/domain/'.$domain
        );
    }

    /**
     * delete a domain
     * @param array $options None
     * @param string $domain [<the domain name>]
     * @return response
     * @throws BceClientException
     */
    public function deleteDomain($domain, $options = array())
    {
        if (empty($domain)) {
            throw new BceClientException("The parameter domain should NOT be null");
        }

        list($config) = $this->parseOptionsIgnoreExtra($options, 'config');

        if (!empty($config)) {
            unset($options['config']);
        }
        $params = $options;

        return $this->sendRequest(
            HttpMethod::DELETE,
            array(
                'config' => $config,
                'params' => $params,
            ),
            '/domain/'.$domain
        );
    }

    /**
     * enable a domain
     * @param array $options None
     * @param string $domain [<the domain name>]
     * @return response
     * @throws BceClientException
     */
    public function enableDomain($domain, $options = array())
    {
        if (empty($domain)) {
            throw new BceClientException("The parameter domain should NOT be null");
        }

        list($config) = $this->parseOptionsIgnoreExtra($options, 'config');

        if (!empty($config)) {
            unset($options['config']);
        }
        $params = array('enable' => '');

        return $this->sendRequest(
            HttpMethod::POST,
            array(
                'config' => $config,
                'params' => $params,
            ),
            '/domain/'.$domain
        );
    }

    /**
     * disable a domain
     * @param array $options None
     * @param string $domain [<the domain name>]
     * @return response
     * @throws BceClientException
     */
    public function disableDomain($domain, $options = array())
    {
        if (empty($domain)) {
            throw new BceClientException("The parameter domain should NOT be null");
        }

        list($config) = $this->parseOptionsIgnoreExtra($options, 'config');

        if (!empty($config)) {
            unset($options['config']);
        }
        $params = array('disable' => '');

        return $this->sendRequest(
            HttpMethod::POST,
            array(
                'config' => $config,
                'params' => $params,
            ),
            '/domain/'.$domain
        );
    }

    /**
     * update origin address of the domain
     * @param array $options None
     * @param string $domain [<the domain name>]
     * @param array $origin [<the origin address list>]
     * @return response
     * @throws BceClientException
     */
    public function setDomainOrigin($domain, $origin, $options = array())
    {
        if (empty($domain)) {
            throw new BceClientException("The parameter domain should NOT be null");
        }

        if (empty($origin)) {
            throw new BceClientException("The parameter origin should NOT be null");
        }

        list($config) = $this->parseOptionsIgnoreExtra($options, 'config');

        if (!empty($config)) {
            unset($options['config']);
        }
        $params = array('origin' => '');

        return $this->sendRequest(
            HttpMethod::PUT,
            array(
                'config' => $config,
                'params' => $params,
                'body' => json_encode(array('origin' => $origin)),
            ),
            '/domain/'.$domain.'/config'
        );
    }

    /**
     * get configuration of the domain
     * @param array $options None
     * @param string $domain [<the domain name>]
     * @return response
     * @throws BceClientException
     */
    public function getDomainConfig($domain, $options = array())
    {
        if (empty($domain)) {
            throw new BceClientException("The parameter domain should NOT be null");
        }

        list($config) = $this->parseOptionsIgnoreExtra($options, 'config');

        if (!empty($config)) {
            unset($options['config']);
        }
        $params = $options;

        return $this->sendRequest(
            HttpMethod::GET,
            array(
                'config' => $config,
                'params' => $params,
            ),
            '/domain/'.$domain.'/config'
        );
    }

    /**
     * get domain cacheShare
     *
     * @param $domain
     * @return mixed
     * @throws BceClientException
     */
    public function getDomainCacheShare($domain)
    {
        if (empty($domain)) {
            throw new BceClientException("The parameter domain should NOT be null");
        }

        $params = array('cacheShare' => '');

        return $this->sendRequest(
            HttpMethod::GET,
            array(
                'params' => $params,
            ),
            '/domain/'.$domain.'/config'
        );
    }

    /**
     * set domain cacheShare
     *
     * @param $domain
     * @param $cacheShare
     * @return mixed
     * @throws BceClientException
     */
    public function setDomainCacheShare($domain, $cacheShare)
    {
        if (empty($domain)) {
            throw new BceClientException("The parameter domain should NOT be empty");
        }
        if (empty($cacheShare)) {
            throw new BceClientException("The parameter cacheShare should NOT be empty");
        }

        $params = array('cacheShare' => '');

        return $this->sendRequest(
            HttpMethod::PUT,
            array(
                'params' => $params,
                'body' => json_encode($cacheShare)
            ),
            '/domain/'.$domain.'/config'
        );
    }

    /**
     * get domain trafficLimit
     *
     * @param $domain
     * @return mixed
     * @throws BceClientException
     */
    public function getDomainTrafficLimit($domain)
    {
        if (empty($domain)) {
            throw new BceClientException("The parameter domain should NOT be null");
        }

        $params = array('trafficLimit' => '');

        return $this->sendRequest(
            HttpMethod::GET,
            array(
                'params' => $params,
            ),
            '/domain/'.$domain.'/config'
        );
    }

    /**
     * set domain trafficLimit
     *
     * @param $domain
     * @param $trafficLimit
     * @return mixed
     * @throws BceClientException
     */
    public function setDomainTrafficLimit($domain, $trafficLimit)
    {
        if (empty($domain)) {
            throw new BceClientException("The parameter domain should NOT be empty");
        }
        if (empty($trafficLimit)) {
            throw new BceClientException("The parameter trafficLimit should NOT be empty");
        }

        $params = array('trafficLimit' => '');

        return $this->sendRequest(
            HttpMethod::PUT,
            array(
                'params' => $params,
                'body' => json_encode($trafficLimit)
            ),
            '/domain/'.$domain.'/config'
        );
    }

    /**
     * get domain uaAcl
     *
     * @param $domain
     * @return mixed
     * @throws BceClientException
     */
    public function getDomainUaACL($domain)
    {
        if (empty($domain)) {
            throw new BceClientException("The parameter domain should NOT be null");
        }

        $params = array('uaAcl' => '');

        return $this->sendRequest(
            HttpMethod::GET,
            array(
                'params' => $params,
            ),
            '/domain/'.$domain.'/config'
        );
    }

    /**
     * set request ua access control
     *
     * @param string $domain
     * @param string $flag white or black
     * @param array $aclList
     * @param array $options
     * @return response
     * @throws BceClientException
     */
    public function setDomainUaAcl($domain, $flag, $aclList)
    {
        if (empty($domain)) {
            throw new BceClientException("The parameter domain should NOT be null");
        }
        if (empty($flag) || (($flag != 'black') && ($flag != 'white'))) {
            throw new BceClientException("The parameter flag should be black or white");

        }
        if (!is_array($aclList)) {
            throw new BceClientException("Acl list must be array, please check your input");
        }

        $params = array('uaAcl' => '');

        $acl = array();
        if ($flag == 'white') {
            $acl['whiteList'] = $aclList;
        }
        if ($flag == 'black') {
            $acl['blackList'] = $aclList;
        }

        return $this->sendRequest(
            HttpMethod::PUT,
            array(
                'params' => $params,
                'body' => json_encode(array('uaAcl' => $acl)),
            ),
            '/domain/'.$domain.'/config'
        );
    }

    /**
     * get domain originProtocol
     *
     * @param $domain
     * @return mixed
     * @throws BceClientException
     */
    public function getDomainOriginProtocol($domain)
    {
        if (empty($domain)) {
            throw new BceClientException("The parameter domain should NOT be null");
        }

        $params = array('originProtocol' => '');

        return $this->sendRequest(
            HttpMethod::GET,
            array(
                'params' => $params,
            ),
            '/domain/'.$domain.'/config'
        );
    }

    /**
     * set domain originProtocol
     *
     * @param $domain
     * @param $originProtocol
     * @return mixed
     * @throws BceClientException
     */
    public function setDomainOriginProtocol($domain, $originProtocol)
    {
        if (empty($domain)) {
            throw new BceClientException("The parameter domain should NOT be empty");
        }
        if (empty($originProtocol)) {
            throw new BceClientException("The parameter originProtocol should NOT be empty");
        }

        $params = array('originProtocol' => '');

        return $this->sendRequest(
            HttpMethod::PUT,
            array(
                'params' => $params,
                'body' => json_encode($originProtocol)
            ),
            '/domain/'.$domain.'/config'
        );
    }

    /**
     * get domain retryOrigin
     *
     * @param $domain
     * @return mixed
     * @throws BceClientException
     */
    public function getDomainRetryOrigin($domain)
    {
        if (empty($domain)) {
            throw new BceClientException("The parameter domain should NOT be null");
        }

        $params = array('retryOrigin' => '');

        return $this->sendRequest(
            HttpMethod::GET,
            array(
                'params' => $params,
            ),
            '/domain/'.$domain.'/config'
        );
    }

    /**
     * set domain retryOrigin
     *
     * @param $domain
     * @param $retryOrigin
     * @return mixed
     * @throws BceClientException
     */
    public function setDomainRetryOrigin($domain, $retryOrigin)
    {
        if (empty($domain)) {
            throw new BceClientException("The parameter domain should NOT be empty");
        }
        if (empty($retryOrigin)) {
            throw new BceClientException("The parameter retryOrigin should NOT be empty");
        }

        $params = array('retryOrigin' => '');

        return $this->sendRequest(
            HttpMethod::PUT,
            array(
                'params' => $params,
                'body' => json_encode($retryOrigin)
            ),
            '/domain/'.$domain.'/config'
        );
    }

    /**
     * get domain ipv6Dispatch
     *
     * @param $domain
     * @return mixed
     * @throws BceClientException
     */
    public function getDomainIpv6Dispatch($domain)
    {
        if (empty($domain)) {
            throw new BceClientException("The parameter domain should NOT be null");
        }

        $params = array('ipv6Dispatch' => '');

        return $this->sendRequest(
            HttpMethod::GET,
            array(
                'params' => $params,
            ),
            '/domain/'.$domain.'/config'
        );
    }

    /**
     * set domain ipv6Dispatch
     *
     * @param $domain
     * @param $ipv6Dispatch
     * @return mixed
     * @throws BceClientException
     */
    public function setDomainIpv6Dispatch($domain, $ipv6Dispatch)
    {
        if (empty($domain)) {
            throw new BceClientException("The parameter domain should NOT be empty");
        }
        if (empty($ipv6Dispatch)) {
            throw new BceClientException("The parameter ipv6Dispatch should NOT be empty");
        }

        $params = array('ipv6Dispatch' => '');

        return $this->sendRequest(
            HttpMethod::PUT,
            array(
                'params' => $params,
                'body' => json_encode($ipv6Dispatch)
            ),
            '/domain/'.$domain.'/config'
        );
    }

    /**
     * get domain quic
     *
     * @param $domain
     * @return mixed
     * @throws BceClientException
     */
    public function getDomainQuic($domain)
    {
        if (empty($domain)) {
            throw new BceClientException("The parameter domain should NOT be null");
        }

        $params = array('quic' => '');

        return $this->sendRequest(
            HttpMethod::GET,
            array(
                'params' => $params,
            ),
            '/domain/'.$domain.'/config'
        );
    }

    /**
     * set domain quic
     *
     * @param $domain
     * @param $quic
     * @return mixed
     * @throws BceClientException
     */
    public function setDomainQuic($domain, $quic)
    {
        if (empty($domain)) {
            throw new BceClientException("The parameter domain should NOT be empty");
        }
        if (empty($quic)) {
            throw new BceClientException("The parameter quic should NOT be empty");
        }

        $params = array('quic' => '');

        return $this->sendRequest(
            HttpMethod::PUT,
            array(
                'params' => $params,
                'body' => json_encode($quic)
            ),
            '/domain/'.$domain.'/config'
        );
    }

    /**
     * get domain offlineMode
     *
     * @param $domain
     * @return mixed
     * @throws BceClientException
     */
    public function getDomainOfflineMode($domain)
    {
        if (empty($domain)) {
            throw new BceClientException("The parameter domain should NOT be null");
        }

        $params = array('offlineMode' => '');

        return $this->sendRequest(
            HttpMethod::GET,
            array(
                'params' => $params,
            ),
            '/domain/'.$domain.'/config'
        );
    }

    /**
     * set domain offlineMode
     *
     * @param $domain
     * @param $offlineMode
     * @return mixed
     * @throws BceClientException
     */
    public function setDomainOfflineMode($domain, $offlineMode)
    {
        if (empty($domain)) {
            throw new BceClientException("The parameter domain should NOT be empty");
        }
        if (empty($offlineMode)) {
            throw new BceClientException("The parameter offlineMode should NOT be empty");
        }

        $params = array('offlineMode' => '');

        return $this->sendRequest(
            HttpMethod::PUT,
            array(
                'params' => $params,
                'body' => json_encode($offlineMode)
            ),
            '/domain/'.$domain.'/config'
        );
    }

    /**
     * get domain ocsp
     *
     * @param $domain
     * @return mixed
     * @throws BceClientException
     */
    public function getDomainOcsp($domain)
    {
        if (empty($domain)) {
            throw new BceClientException("The parameter domain should NOT be null");
        }

        $params = array('ocsp' => '');

        return $this->sendRequest(
            HttpMethod::GET,
            array(
                'params' => $params,
            ),
            '/domain/'.$domain.'/config'
        );
    }

    /**
     * set domain ocsp
     *
     * @param $domain
     * @param $ocsp
     * @return mixed
     * @throws BceClientException
     */
    public function setDomainOcsp($domain, $ocsp)
    {
        if (empty($domain)) {
            throw new BceClientException("The parameter domain should NOT be empty");
        }
        if (empty($ocsp)) {
            throw new BceClientException("The parameter ocsp should NOT be empty");
        }

        $params = array('ocsp' => '');

        return $this->sendRequest(
            HttpMethod::PUT,
            array(
                'params' => $params,
                'body' => json_encode($ocsp)
            ),
            '/domain/'.$domain.'/config'
        );
    }

    /**
     * get baidu back to source ip address segment
     * 
     * @return mixed
     */
    public function getNodesList()
    {
        $params = array();
        return $this->sendRequest(
            HttpMethod::GET,
            array(
                'params' => $params,
            ),
            '/nodes/list'
        );
    }

    /**
     * get domain errorPage
     *
     * @param $domain
     * @return mixed
     * @throws BceClientException
     */
    public function getDomainErrorPage($domain)
    {
        if (empty($domain)) {
            throw new BceClientException("The parameter domain should NOT be null");
        }

        $params = array('errorPage' => '');

        return $this->sendRequest(
            HttpMethod::GET,
            array(
                'params' => $params,
            ),
            '/domain/'.$domain.'/config'
        );
    }

    /**
     * set domain errorPage
     *
     * @param $domain
     * @param $errorPage
     * @return mixed
     * @throws BceClientException
     */
    public function setDomainErrorPage($domain, $errorPage)
    {
        if (empty($domain) || empty($errorPage)) {
            throw new BceClientException("The parameter domain and errorPage should NOT be null");
        }

        $params = array('errorPage' => '');

        return $this->sendRequest(
            HttpMethod::PUT,
            array(
                'params' => $params,
                'body' => json_encode($errorPage)
            ),
            '/domain/'.$domain.'/config'
        );
    }

    /**
     * set domain requestAuth
     *
     * @param $domain
     * @param $requestAuth
     * @return mixed
     * @throws BceClientException
     */
    public function setDomainRequestAuth($domain, $requestAuth)
    {
        if (empty($domain) || empty($requestAuth)) {
            throw new BceClientException("The parameter domain and requestAuth should NOT be null");
        }

        $params = array('requestAuth' => '');

        return $this->sendRequest(
            HttpMethod::PUT,
            array(
                'params' => $params,
                'body' => json_encode($requestAuth)
            ),
            '/domain/'.$domain.'/config'
        );
    }

    /**
     * get Cross-domain setting of domain
     *
     * @param $domain
     * @return mixed
     * @throws BceClientException
     */
    public function getDomainCors($domain)
    {
        if (empty($domain)) {
            throw new BceClientException("The parameter domain should NOT be null");
        }

        $params = array('cors' => '');

        return $this->sendRequest(
            HttpMethod::GET,
            array(
                'params' => $params
            ),
            '/domain/'.$domain.'/config'
        );
    }

    /**
     * set Cross-domain setting of domain
     *
     * @param $domain
     * @param array $cors
     * @return mixed
     * @throws BceClientException
     */
    public function setDomainCors($domain, $cors = array())
    {
        if (empty($domain) || empty($cors)) {
            throw new BceClientException("The parameter domain and cors should NOT be null");
        }

        $params = array('cors' => '');
        $body = $cors;

        return $this->sendRequest(
            HttpMethod::PUT,
            array(
                'params' => $params,
                'body' => json_encode($body)
            ),
            '/domain/'.$domain.'/config'
        );
    }

    /**
     * get IP access frequency limit settings
     *
     * @param $domain
     * @return mixed
     * @throws BceClientException
     */
    public function getDomainAccessLimit($domain)
    {
        if (empty($domain)) {
            throw new BceClientException("The parameter domain should NOT be null");
        }

        $params = array('accessLimit' => '');

        return $this->sendRequest(
            HttpMethod::GET,
            array(
                'params' => $params
            ),
            '/domain/'.$domain.'/config'
        );
    }

    /**
     * set IP access frequency limit
     *
     * @param $domain
     * @param array $accessLimit
     * @return mixed
     * @throws BceClientException
     */
    public function setDomainAccessLimit($domain, $accessLimit = array())
    {
        if (empty($domain) || empty($accessLimit)) {
            throw new BceClientException("The parameter domain and accessLimit should NOT be null");
        }

        $params = array('accessLimit' => '');
        $body = $accessLimit;

        return $this->sendRequest(
            HttpMethod::PUT,
            array(
                'params' => $params,
                'body' => json_encode($body)
            ),
            '/domain/'.$domain.'/config'
        );
    }

    /**
     * get User Real IP
     *
     * @param $domain
     * @return mixed
     * @throws BceClientException
     */
    public function getDomainClientIp($domain)
    {
        if (empty($domain)) {
            throw new BceClientException("The parameter domain should NOT be null");
        }

        $params = array('clientIp' => '');

        return $this->sendRequest(
            HttpMethod::GET,
            array(
                'params' => $params
            ),
            '/domain/'.$domain.'/config'
        );
    }

    /**
     * set User Real IP setting
     *
     * @param $domain
     * @param array $clientIp
     * @return mixed
     * @throws BceClientException
     */
    public function setDomainClientIp($domain, $clientIp = array())
    {
        if (empty($domain) || empty($clientIp)) {
            throw new BceClientException("The parameter domain and clientIp should NOT be null");
        }

        $params = array('clientIp' => '');
        $body = $clientIp;

        return $this->sendRequest(
            HttpMethod::PUT,
            array(
                'params' => $params,
                'body' => json_encode($body)
            ),
            '/domain/'.$domain.'/config'
        );
    }

    /**
     * set Protocol Following Back Source
     *
     * @param $domain
     * @param array $followProtocol
     * @return mixed
     * @throws BceClientException
     */
    public function setDomainFollowProtocol($domain, $followProtocol = array())
    {
        if (empty($domain) || empty($followProtocol)) {
            throw new BceClientException("The parameter domain and followProtocol should NOT be null");
        }

        $params = array('followProtocol' => '');
        $body = $followProtocol;

        return $this->sendRequest(
            HttpMethod::PUT,
            array(
                'params' => $params,
                'body' => json_encode($body)
            ),
            '/domain/'.$domain.'/config'
        );
    }

    /**
     * get range back source settings
     *
     * @param $domain
     * @return mixed
     * @throws BceClientException
     */
    public function getDomainRangeSwitch($domain)
    {
        if (empty($domain)) {
            throw new BceClientException("The parameter domain should NOT be null");
        }

        $params = array('rangeSwitch' => '');

        return $this->sendRequest(
            HttpMethod::GET,
            array(
                'params' => $params
            ),
            '/domain/'.$domain.'/config'
        );
    }

    /**
     * set range back source
     *
     * @param $domain
     * @param array $rangeSwitch
     * @return mixed
     * @throws BceClientException
     */
    public function setDomainRangeSwitch($domain, $rangeSwitch = array())
    {
        if (empty($domain) || empty($rangeSwitch)) {
            throw new BceClientException("The parameter domain and rangeSwitch should NOT be null");
        }

        $params = array('rangeSwitch' => '');
        $body = $rangeSwitch;

        return $this->sendRequest(
            HttpMethod::PUT,
            array(
                'params' => $params,
                'body' => json_encode($body)
            ),
            '/domain/'.$domain.'/config'
        );
    }

    /**
     * get Mobile Access Control Configuration
     *
     * @param $domain
     * @return mixed
     * @throws BceClientException
     */
    public function getDomainMobileAccess($domain)
    {
        if (empty($domain)) {
            throw new BceClientException("The parameter domain should NOT be null");
        }

        $params = array('mobileAccess' => '');

        return $this->sendRequest(
            HttpMethod::GET,
            array(
                'params' => $params
            ),
            '/domain/'.$domain.'/config'
        );
    }

    /**
     * set Mobile Access Control Configuration
     *
     * @param $domain
     * @param array $mobileAccess
     * @return mixed
     * @throws BceClientException
     */
    public function setDomainMobileAccess($domain, $mobileAccess = array())
    {
        if (empty($domain) || empty($mobileAccess)) {
            throw new BceClientException("The parameter domain and mobileAccess should NOT be null");
        }

        $params = array('mobileAccess' => '');
        $body = $mobileAccess;

        return $this->sendRequest(
            HttpMethod::PUT,
            array(
                'params' => $params,
                'body' => json_encode($body)
            ),
            '/domain/'.$domain.'/config'
        );
    }

    /**
     * get HttpHeader Configuration
     *
     * @param $domain
     * @return mixed
     * @throws BceClientException
     */
    public function getDomainHttpHeader($domain)
    {
        if (empty($domain)) {
            throw new BceClientException("The parameter domain should NOT be null");
        }

        $params = array('httpHeader' => '');

        return $this->sendRequest(
            HttpMethod::GET,
            array(
                'params' => $params
            ),
            '/domain/'.$domain.'/config'
        );
    }

    /**
     * set HttpHeader Configuration
     *
     * @param $domain
     * @param array $httpHeader
     * @return mixed
     * @throws BceClientException
     */
    public function setDomainHttpHeader($domain, $httpHeader = array())
    {
        if (empty($domain) || empty($httpHeader)) {
            throw new BceClientException("The parameter domain and httpHeader should NOT be null");
        }

        $params = array('httpHeader' => '');
        $body = $httpHeader;

        return $this->sendRequest(
            HttpMethod::PUT,
            array(
                'params' => $params,
                'body' => json_encode($body)
            ),
            '/domain/'.$domain.'/config'
        );
    }

    /**
     * get seoSwitch Configuration
     *
     * @param $domain
     * @return mixed
     * @throws BceClientException
     */
    public function getDomainSeoSwitch($domain)
    {
        if (empty($domain)) {
            throw new BceClientException("The parameter domain should NOT be null");
        }

        $params = array('seoSwitch' => '');

        return $this->sendRequest(
            HttpMethod::GET,
            array(
                'params' => $params
            ),
            '/domain/'.$domain.'/config'
        );
    }

    /**
     * set seoSwitch Configuration
     *
     * @param $domain
     * @param array $seoSwitch
     * @return mixed
     * @throws BceClientException
     */
    public function setDomainSeoSwitch($domain, $seoSwitch = array())
    {
        if (empty($domain) || empty($seoSwitch)) {
            throw new BceClientException("The parameter domain and seoSwitch should NOT be null");
        }

        $params = array('seoSwitch' => '');
        $body = $seoSwitch;

        return $this->sendRequest(
            HttpMethod::PUT,
            array(
                'params' => $params,
                'body' => json_encode($body)
            ),
            '/domain/'.$domain.'/config'
        );
    }

    /**
     * get fileTrim Configuration
     *
     * @param $domain
     * @return mixed
     * @throws BceClientException
     */
    public function getDomainFileTrim($domain)
    {
        if (empty($domain)) {
            throw new BceClientException("The parameter domain should NOT be null");
        }

        $params = array('fileTrim' => '');

        return $this->sendRequest(
            HttpMethod::GET,
            array(
                'params' => $params
            ),
            '/domain/'.$domain.'/config'
        );
    }

    /**
     * set fileTrim Configuration
     *
     * @param $domain
     * @param array $fileTrim
     * @return mixed
     * @throws BceClientException
     */
    public function setDomainFileTrim($domain, $fileTrim = array())
    {
        if (empty($domain) || empty($fileTrim)) {
            throw new BceClientException("The parameter domain and fileTrim should NOT be null");
        }

        $params = array('fileTrim' => '');
        $body = $fileTrim;

        return $this->sendRequest(
            HttpMethod::PUT,
            array(
                'params' => $params,
                'body' => json_encode($body)
            ),
            '/domain/'.$domain.'/config'
        );
    }

    /**
     * get mediaDrag Configuration
     *
     * @param $domain
     * @return mixed
     * @throws BceClientException
     */
    public function getDomainMediaDrag($domain)
    {
        if (empty($domain)) {
            throw new BceClientException("The parameter domain should NOT be null");
        }

        $params = array('mediaDrag' => '');

        return $this->sendRequest(
            HttpMethod::GET,
            array(
                'params' => $params
            ),
            '/domain/'.$domain.'/config'
        );
    }

    /**
     * set mediaDrag Configuration
     *
     * @param $domain
     * @param array $mediaDrag
     * @return mixed
     * @throws BceClientException
     */
    public function setDomainMediaDrag($domain, $mediaDrag = array())
    {
        if (empty($domain) || empty($mediaDrag)) {
            throw new BceClientException("The parameter domain and mediaDrag should NOT be null");
        }

        $params = array('mediaDrag' => '');
        $body = $mediaDrag;

        return $this->sendRequest(
            HttpMethod::PUT,
            array(
                'params' => $params,
                'body' => json_encode($body)
            ),
            '/domain/'.$domain.'/config'
        );
    }

    /**
     * get compress Configuration
     *
     * @param $domain
     * @return mixed
     * @throws BceClientException
     */
    public function getDomainCompress($domain)
    {
        if (empty($domain)) {
            throw new BceClientException("The parameter domain should NOT be null");
        }

        $params = array('compress' => '');

        return $this->sendRequest(
            HttpMethod::GET,
            array(
                'params' => $params
            ),
            '/domain/'.$domain.'/config'
        );
    }

    /**
     * set compress Configuration
     *
     * @param $domain
     * @param array $compress
     * @return mixed
     * @throws BceClientException
     */
    public function setDomainCompress($domain, $compress = array())
    {
        if (empty($domain) || empty($compress)) {
            throw new BceClientException("The parameter domain and compress should NOT be null");
        }

        $params = array('compress' => '');
        $body = $compress;

        return $this->sendRequest(
            HttpMethod::PUT,
            array(
                'params' => $params,
                'body' => json_encode($body)
            ),
            '/domain/'.$domain.'/config'
        );
    }

    /**
     * set https Configuration
     *
     * @param $domain
     * @param array $https
     * @return mixed
     * @throws BceClientException
     */
    public function setDomainHttps($domain, $https = array())
    {
        if (empty($domain) || empty($https)) {
            throw new BceClientException("The parameter domain and https should NOT be null");
        }

        $params = array('https' => '');
        $body = $https;

        return $this->sendRequest(
            HttpMethod::PUT,
            array(
                'params' => $params,
                'body' => json_encode($body)
            ),
            '/domain/'.$domain.'/config'
        );
    }

    /**
     * get cache rules of a domain
     * @param array $options None
     * @param string $domain [<the domain name>]
     * @return response
     * @throws BceClientException
     */
    public function getDomainCacheTTL($domain, $options = array())
    {
        if (empty($domain)) {
            throw new BceClientException("The parameter domain should NOT be null");
        }

        list($config) = $this->parseOptionsIgnoreExtra($options, 'config');

        if (!empty($config)) {
            unset($options['config']);
        }
        $params = array('cacheTTL' => '');

        return $this->sendRequest(
            HttpMethod::GET,
            array(
                'config' => $config,
                'params' => $params,
            ),
            '/domain/'.$domain.'/config'
        );
    }

    /**
     * set cache rules of a domain
     * @param array $options None
     * @param string $domain [<the domain name>]
     * @param array $rules [<cache ruless>]
     * @return response
     * @throws BceClientException
     */
    public function setDomainCacheTTL($domain, $rules, $options = array())
    {
        if (empty($domain)) {
            throw new BceClientException("The parameter domain should NOT be null");
        }
        if (empty($rules) || !is_array($rules)) {
            throw new BceClientException("The parameter rules should be a non empty array");
        }
        list($config) = $this->parseOptionsIgnoreExtra($options, 'config');

        if (!empty($config)) {
            unset($options['config']);
        }

        $params = array('cacheTTL' => '');

        return $this->sendRequest(
            HttpMethod::PUT,
            array(
                'config' => $config,
                'params' => $params,
                'body' => json_encode(array('cacheTTL' => $rules)),
            ),
            '/domain/'.$domain.'/config'
        );
    }

    /**
     * get the cacheFullUrl
     *
     * @param $domain
     * @return mixed
     * @throws BceClientException
     */
    public function getDomainCacheFullUrl($domain)
    {
        if (empty($domain)) {
            throw new BceClientException("The parameter domain should NOT be null");
        }

        $params = array('cacheFullUrl' => '');

        return $this->sendRequest(
            HttpMethod::GET,
            array(
                'params' => $params,
            ),
            '/domain/'.$domain.'/config'
        );
    }

    /**
     * set if use the full url as cache key
     * @param array $options None
     * @param string $domain [<the domain name>]
     * @param bool $flag [<if use the full url as cache key>]
     * @return response
     * @throws BceClientException
     */
    public function setDomainCacheFullUrl($domain, $flag, $options = array())
    {
        if (empty($domain) || empty($flag)) {
            throw new BceClientException("The parameter domain or flag should NOT be null");
        }
        list($config) = $this->parseOptionsIgnoreExtra($options, 'config');

        if (!empty($config)) {
            unset($options['config']);
        }

        $params = array('cacheFullUrl' => '');

        return $this->sendRequest(
            HttpMethod::PUT,
            array(
                'config' => $config,
                'params' => $params,
                'body' => json_encode(array('cacheFullUrl' => $flag)),
            ),
            '/domain/'.$domain.'/config'
        );
    }

    /**
     * get domain ipACL
     *
     * @param $domain
     * @return mixed
     * @throws BceClientException
     */
    public function getDomainIpACL($domain)
    {
        if (empty($domain)) {
            throw new BceClientException("The parameter domain should NOT be null");
        }

        $params = array('ipACL' => '');

        return $this->sendRequest(
            HttpMethod::GET,
            array(
                'params' => $params,
            ),
            '/domain/'.$domain.'/config'
        );
    }

    /**
     * set request ip access control
     * @param array $options None
     * @param string $domain [<the domain name>]
     * @param array $blackList [<ip black list>]
     * @param array $whiteList [<ip white list>]
     * @return response
     * @throws BceClientException
     */
    public function setDomainIpAcl($domain, $flag, $aclList, $options = array())
    {
        if (empty($domain)) {
            throw new BceClientException("The parameter domain should NOT be null");
        }
        if (empty($flag) || (($flag != 'black') && ($flag != 'white'))) {
            throw new BceClientException("The parameter flag should be black or white");

        }
        if (empty($aclList)) {
            throw new BceClientException("Acl list is empty, please check your input");
        }

        list($config) = $this->parseOptionsIgnoreExtra($options, 'config');

        if (!empty($config)) {
            unset($options['config']);
        }
        $params = array('ipACL' => '');

        $acl = array();
        if ($flag == 'white') {
            $acl['whiteList'] = $aclList;
        }
        if ($flag == 'black') {
            $acl['blackList'] = $aclList;
        }

        return $this->sendRequest(
            HttpMethod::PUT,
            array(
                'config' => $config,
                'params' => $params,
                'body' => json_encode(array('ipACL' => $acl)),
            ),
            '/domain/'.$domain.'/config'
        );
    }

    /**
     * get domain refererAcl
     *
     * @param $domain
     * @return mixed
     * @throws BceClientException
     */
    public function getDomainRefererAcl($domain)
    {
        if (empty($domain)) {
            throw new BceClientException("The parameter domain should NOT be null");
        }

        $params = array('refererACL' => '');

        return $this->sendRequest(
            HttpMethod::GET,
            array(
                'params' => $params,
            ),
            '/domain/'.$domain.'/config'
        );
    }

    /**
     * set request referer access control
     * @param array $options None
     * @param string $domain [<the domain name>]
     * @param array $blackList [<referer black list>]
     * @param array $whiteList [<referer white list>]
     * @return response
     * @throws BceClientException
     */
    public function setDomainRefererAcl($domain, $flag, $allowEmpty,
                                        $aclList, $options = array())
    {
        if (empty($domain)) {
            throw new BceClientException("The parameter domain should NOT be null");
        }
        if (empty($flag) || (($flag != 'black') && ($flag != 'white'))) {
            throw new BceClientException("The parameter flag should be black or white");

        }
        if (empty($aclList)) {
            throw new BceClientException("Acl list is empty, please check your input");
        }

        list($config) = $this->parseOptionsIgnoreExtra($options, 'config');

        if (!empty($config)) {
            unset($options['config']);
        }
        $params = array('refererACL' => '');

        $acl = array();
        $acl['allowEmpty'] = $allowEmpty;
        if ($flag == 'white') {
            $acl['whiteList'] = $aclList;
        }
        if ($flag == 'black') {
            $acl['blackList'] = $aclList;
        }

        return $this->sendRequest(
            HttpMethod::PUT,
            array(
                'config' => $config,
                'params' => $params,
                'body' => json_encode(array('refererACL' => $acl)),
            ),
            '/domain/'.$domain.'/config'
        );
    }

    /**
     * set limit rate
     * @param array $options None
     * @param string $domain [<the domain name>]
     * @param int $rate [<limit rate value (Byte/s)>]
     * @return response
     * @throws BceClientException
     */
    public function setDomainLimitRate($domain, $rate, $options = array())
    {
        if (empty($domain)) {
            throw new BceClientException("The parameter domain should NOT be null");
        }
        if (empty($rate) || !is_int($rate)) {
            throw new BceClientException("The parameter rate should NOT be null");
        }

        list($config) = $this->parseOptionsIgnoreExtra($options, 'config');

        if (!empty($config)) {
            unset($options['config']);
        }
        $params = array('limitRate' => '');

        return $this->sendRequest(
            HttpMethod::PUT,
            array(
                'config' => $config,
                'params' => $params,
                'body' => json_encode(array('limitRate' => $rate)),
            ),
            '/domain/'.$domain.'/config'
        );
    }

    /**
     * query pv and qps of the domain
     * @param array $options None
     * @param string $domain [<the domain name>]
     * @param timestamp $startTime [<query start time>]
     * @param timestamp $endTime [<query end time>]
     * @param int $period [<time interval of query result>]
     * @param int $withRegion [<if need client region distribution>]
     * @return response
     * @throws BceClientException
     */
    public function getDomainPvStat($domain=null, $startTime=null, $endTime=null,
                                    $period=300, $withRegion=null, $options = array())
    {
        list($config) = $this->parseOptionsIgnoreExtra($options, 'config');

        if (!empty($config)) {
            unset($options['config']);
        }
        $params = array();
        $params['domain'] = $domain;
        if (!empty($startTime)) {
            $params['startTime'] = $startTime;
        }
        if (!empty($endTime)) {
            $params['endTime'] = $endTime;
        }
        $params['period'] = $period;
        $params['withRegion'] = $withRegion;

        return $this->sendRequest(
            HttpMethod::GET,
            array(
                'config' => $config,
                'params' => $params,
            ),
            '/stat/pv'
        );
    }

    /**
     * query the total number of client of a domain or all domains of the user
     * @param array $options None
     * @param string $domain [<the domain name>]
     * @param timestamp $startTime [<query start time>]
     * @param timestamp $endTime [<query end time>]
     * @param int $period [<time interval of query result>]
     * @return response
     * @throws BceClientException
     */
    public function getDomainUvStat($domain=null, $startTime=null, $endTime=null,
                                    $period=3600, $options = array())
    {
        list($config) = $this->parseOptionsIgnoreExtra($options, 'config');

        if (!empty($config)) {
            unset($options['config']);
        }
        $params = array();
        $params['domain'] = $domain;
        if (!empty($startTime)) {
            $params['startTime'] = $startTime;
        }
        if (!empty($endTime)) {
            $params['endTime'] = $endTime;
        }
        $params['period'] = $period;

        return $this->sendRequest(
            HttpMethod::GET,
            array(
                'config' => $config,
                'params' => $params,
            ),
            '/stat/uv'
        );
    }

    /**
     * query average of the domain or all domains of the user
     * @param array $options None
     * @param string $domain [<the domain name>]
     * @param timestamp $startTime [<query start time>]
     * @param timestamp $endTime [<query end time>]
     * @param int $period [<time interval of query result>]
     * @return response
     * @throws BceClientException
     */
    public function getDomainAvgSpeedStat($domain=null, $startTime=null, $endTime=null,
                                          $period=300, $options = array())
    {
        list($config) = $this->parseOptionsIgnoreExtra($options, 'config');

        if (!empty($config)) {
            unset($options['config']);
        }
        $params = array();
        $params['domain'] = $domain;
        if (!empty($startTime)) {
            $params['startTime'] = $startTime;
        }
        if (!empty($endTime)) {
            $params['endTime'] = $endTime;
        }
        $params['period'] = $period;

        return $this->sendRequest(
            HttpMethod::GET,
            array(
                'config' => $config,
                'params' => $params,
            ),
            '/stat/avgspeed'
        );
    }

    /**
     * query bandwidth of the domain
     * @param array $options None
     * @param string $domain [<the domain name>]
     * @param timestamp $startTime [<query start time>]
     * @param timestamp $endTime [<query end time>]
     * @param int $period [<time interval of query result>]
     * @param int $withRegion [<if need client region distribution>]
     * @return response
     * @throws BceClientException
     */
    public function getDomainFlowStat($domain=null, $startTime=null, $endTime=null,
                                      $period=300, $withRegion=null, $options = array())
    {
        list($config) = $this->parseOptionsIgnoreExtra($options, 'config');

        if (!empty($config)) {
            unset($options['config']);
        }
        $params = array();
        $params['domain'] = $domain;
        if (!empty($startTime)) {
            $params['startTime'] = $startTime;
        }
        if (!empty($endTime)) {
            $params['endTime'] = $endTime;
        }
        $params['period'] = $period;
        $params['withRegion'] = $withRegion;

        return $this->sendRequest(
            HttpMethod::GET,
            array(
                'config' => $config,
                'params' => $params,
            ),
            '/stat/flow'
        );
    }

    /**
     * @param $options array
     * @return response
     */
    public function getDomainSrcFlowStat($domain=null, $startTime=null, $endTime=null,
                                         $period=300, $options = array())
    {
        list($config) = $this->parseOptionsIgnoreExtra($options, 'config');

        if (!empty($config)) {
            unset($options['config']);
        }
        $params = array();
        $params['domain'] = $domain;
        if (!empty($startTime)) {
            $params['startTime'] = $startTime;
        }
        if (!empty($endTime)) {
            $params['endTime'] = $endTime;
        }
        $params['period'] = $period;

        return $this->sendRequest(
            HttpMethod::GET,
            array(
                'config' => $config,
                'params' => $params,
            ),
            '/stat/srcflow'
        );
    }

    /**
     * query hit rate of the domain
     * @param array $options None
     * @param string $domain [<the domain name>]
     * @param timestamp $startTime [<query start time>]
     * @param timestamp $endTime [<query end time>]
     * @param int $period [<time interval of query result>]
     * @return response
     * @throws BceClientException
     */
    public function getDomainHitRateStat($domain=null, $startTime=null, $endTime=null,
                                         $period=300, $options = array())
    {
        list($config) = $this->parseOptionsIgnoreExtra($options, 'config');

        if (!empty($config)) {
            unset($options['config']);
        }
        $params = array();
        $params['domain'] = $domain;
        if (!empty($startTime)) {
            $params['startTime'] = $startTime;
        }
        if (!empty($endTime)) {
            $params['endTime'] = $endTime;
        }
        $params['period'] = $period;

        return $this->sendRequest(
            HttpMethod::GET,
            array(
                'config' => $config,
                'params' => $params,
            ),
            '/stat/hitrate'
        );
    }

    /**
     * query http response code of a domain or all domains of the user
     * @param array $options None
     * @param string $domain [<the domain name>]
     * @param timestamp $startTime [<query start time>]
     * @param timestamp $endTime [<query end time>]
     * @param int $period [<time interval of query result>]
     * @param int $withRegion [<if need client region distribution>]
     * @return response
     * @throws BceClientException
     */
    public function getDomainHttpCodeStat($domain=null, $startTime=null, $endTime=null,
                                          $period=300, $withRegion=null, $options = array())
    {
        list($config) = $this->parseOptionsIgnoreExtra($options, 'config');

        if (!empty($config)) {
            unset($options['config']);
        }
        $params = array();
        $params['domain'] = $domain;
        if (!empty($startTime)) {
            $params['startTime'] = $startTime;
        }
        if (!empty($endTime)) {
            $params['endTime'] = $endTime;
        }
        $params['period'] = $period;
        $params['withRegion'] = $withRegion;

        return $this->sendRequest(
            HttpMethod::GET,
            array(
                'config' => $config,
                'params' => $params,
            ),
            '/stat/httpcode'
        );
    }

    /**
     * query top n url of the domain or all domains of the user
     * @param array $options None
     * @param string $domain [<the domain name>]
     * @param timestamp $startTime [<query start time>]
     * @param timestamp $endTime [<query end time>]
     * @param int $period [<time interval of query result>]
     * @return response
     * @throws BceClientException
     */
    public function getDomainTopUrlStat($domain=null, $startTime=null, $endTime=null,
                                        $period=3600, $options = array())
    {
        list($config) = $this->parseOptionsIgnoreExtra($options, 'config');

        if (!empty($config)) {
            unset($options['config']);
        }
        $params = array();
        $params['domain'] = $domain;
        if (!empty($startTime)) {
            $params['startTime'] = $startTime;
        }
        if (!empty($endTime)) {
            $params['endTime'] = $endTime;
        }
        $params['period'] = $period;

        return $this->sendRequest(
            HttpMethod::GET,
            array(
                'config' => $config,
                'params' => $params,
            ),
            '/stat/topn/url'
        );
    }

    /**
     * query top n referer of the domain or all domains of the user
     * @param array $options None
     * @param string $domain [<the domain name>]
     * @param timestamp $startTime [<query start time>]
     * @param timestamp $endTime [<query end time>]
     * @param int $period [<time interval of query result>]
     * @return response
     * @throws BceClientException
     */
    public function getDomainTopRefererStat($domain=null, $startTime=null, $endTime=null,
                                            $period=3600, $options = array())
    {
        list($config) = $this->parseOptionsIgnoreExtra($options, 'config');

        if (!empty($config)) {
            unset($options['config']);
        }
        $params = array();
        $params['domain'] = $domain;
        if (!empty($startTime)) {
            $params['startTime'] = $startTime;
        }
        if (!empty($endTime)) {
            $params['endTime'] = $endTime;
        }
        $params['period'] = $period;

        return $this->sendRequest(
            HttpMethod::GET,
            array(
                'config' => $config,
                'params' => $params,
            ),
            '/stat/topn/referer'
        );
    }

    /**
     * get refresh and preload records for specified conditions
     *
     * @param $domain
     * @return mixed
     * @throws BceClientException
     */
    public function getRecords($recordsParams = array())
    {
        $params = $recordsParams;

        return $this->sendRequest(
            HttpMethod::GET,
            array(
                'params' => $params,
            ),
            '/cache/records'
        );
    }

    /**
     * purge the cache of specified url or directory
     * @param array $tasks The task list
     *      {
     *          url: The url to be purged.
     *          type: 'file' or 'directory'
     *      }
     * @param array $options None
     * @return task id
     * @throws BceClientException
     */
    public function purge(array $tasks, $options = array())
    {
        if (empty($tasks)) {
            throw new BceClientException("The parameter tasks "
                ."should NOT be null or empty array");
        }

        list($config) = $this->parseOptionsIgnoreExtra($options, 'config');
        if (!empty($config)) {
            unset($options['config']);
        }
        $params = $options;

        return $this->sendRequest(
            HttpMethod::POST,
            array(
                'config' => $config,
                'body' => json_encode(array(
                    'tasks' => $tasks
                )),
                'params' => $params,
            ),
            '/cache/purge'
        );
    }

    /**
     * Get status of specified purge task.
     *
     * @param array $options None
     * @param string $taskId [<purge task id to query>]
     * @param string $url [<purge url to query>]
     * @param timestamp $startTime [<query start time>]
     * @param timestamp $endTime [<query end time>]
     * @param int $marker [<'nextMarker' get from last query>]
     * @throws BceClientException
     * @return response
     */
    public function listPurgeStatus($taskId = null, $url = null, $startTime = null,
                                    $endTime = null, $marker = null, $options = array())
    {
        list($config) = $this->parseOptionsIgnoreExtra($options, 'config');

        if (!empty($config)) {
            unset($options['config']);
        }
        $params = array();

        if (!empty($taskId)) {
            $params['id'] = $taskId;
        }
        if (!empty($url)) {
            $params['url'] = $url;
        }
        if (!empty($startTime)) {
            $params['startTime'] = $startTime;
        }
        if (!empty($endTime)) {
            $params['endTime'] = $endTime;
        }
        if (!empty($marker)) {
            $params['marker'] = $marker;
        }

        return $this->sendRequest(
            HttpMethod::GET,
            array(
                'config' => $config,
                'params' => $params,
            ),
            '/cache/purge'
        );
    }

    /**
     * prefetch the source of specified url from origin
     * @param array $options None
     * @param array $tasks The task list
     *      {
     *          url: The url to be prefetch.
     *          speed: The flowrate limit of this prefetch task.
     *          startTime: Schedule the prefetch task to specified time.
     *      }
     * @return task id
     * @throws BceClientException
     */
    public function prefetch(array $tasks, $options = array())
    {
        if (empty($tasks)) {
            throw new BceClientException("The parameter tasks "
                ."should NOT be null or empty array");
        }

        list($config) = $this->parseOptionsIgnoreExtra($options, 'config');
        if (!empty($config)) {
            unset($options['config']);
        }
        $params = $options;

        return $this->sendRequest(
            HttpMethod::POST,
            array(
                'config' => $config,
                'body' => json_encode(array(
                    'tasks' => $tasks
                )),
                'params' => $params,
            ),
            '/cache/prefetch'
        );
    }

    /**
     * query the status of prefetch tasks
     * @param array $options None
     * @param string $taskId [<purge task id to query>]
     * @param string $url [<purge url to query>]
     * @param timestamp $startTime [<query start time>]
     * @param timestamp $endTime [<query end time>]
     * @param int $marker [<'nextMarker' get from last query>]
     * @throws BceClientException
     * @return response
     */
    public function listPrefetchStatus($taskId = null, $url = null, $startTime = null,
                                       $endTime = null, $marker = null, $options = array())
    {
        list($config) = $this->parseOptionsIgnoreExtra($options, 'config');

        if (!empty($config)) {
            unset($options['config']);
        }
        $params = array();

        if (!empty($taskId)) {
            $params['id'] = $taskId;
        }
        if (!empty($url)) {
            $params['url'] = $url;
        }
        if (!empty($startTime)) {
            $params['startTime'] = $startTime;
        }
        if (!empty($endTime)) {
            $params['endTime'] = $endTime;
        }
        if (!empty($marker)) {
            $params['marker'] = $marker;
        }

        return $this->sendRequest(
            HttpMethod::GET,
            array(
                'config' => $config,
                'params' => $params,
            ),
            '/cache/prefetch'
        );
    }

    /**
     * query purge quota of the user
     * @param $options array None
     * @throws BceClientException
     * @return response
     */
    public function listQuota($options = array())
    {
        list($config) = $this->parseOptions($options, 'config');
        $params = array();

        return $this->sendRequest(
            HttpMethod::GET,
            array(
                'config' => $config,
                'params' => $params,
            ),
            '/cache/quota'
        );
    }

    /**
     * get log of the domain in specified period of time
     * @param $options array None
     * @param string $domain [<the domain name>]
     * @param timestamp $startTime [<query start time>]
     * @param timestamp $endTime [<query end time>]
     * @return response
     * @throws BceClientException
     */
    public function getDomainLog($domain, $startTime = null, $endTime = null, $options = array())
    {
        if (empty($domain)) {
            throw new BceClientException("The parameter domain should NOT be null");
        }

        list($config) = $this->parseOptionsIgnoreExtra($options, 'config');

        if (!empty($config)) {
            unset($options['config']);
        }
        $params = array();
        if (!empty($startTime)) {
            $params['startTime'] = $startTime;
        }
        if (!empty($endTime)) {
            $params['endTime'] = $endTime;
        }

        return $this->sendRequest(
            HttpMethod::GET,
            array(
                'config' => $config,
                'params' => $params,
            ),
            '/log/'.$domain.'/log'
        );
    }

    /**
     * get log of the domain list in specified period of time
     *
     * @param array $options
     * @return mixed
     * @throws BceClientException
     */
    public function getDomainsLog($options = array())
    {
        if (empty($options)) {
            throw new BceClientException("The parameter options should NOT be null");
        }

        $body = $options;

        return $this->sendRequest(
            HttpMethod::POST,
            array(
                'body' => json_encode($body)
            ),
            '/log/list'
        );
    }

    /**
     * check specified ip if belongs to Baidu CDN
     * @param $options array None
     * @param string $ip [<specified ip>]
     * @return response
     * @throws BceClientException
     */
    public function ipQuery($action, $ip, $options = array())
    {
        list($config) = $this->parseOptionsIgnoreExtra($options, 'config');

        if (!empty($config)) {
            unset($options['config']);
        }
        $params = array();
        $params['action'] = $action;
        $params['ip'] = $ip;

        return $this->sendRequest(
            HttpMethod::GET,
            array(
                'config' => $config,
                'params' => $params,
            ),
            '/utils'
        );
    }

    /**
     * query stats of the domain or uid or tagId, eg : flow pv
     * @param $statParam array stat query parameters
     * @param $options array None
     * @return response
     * @throws BceClientException
     */
    public function getDomainStats(array $statParam, $options = array()) {
        list($config) = $this->parseOptionsIgnoreExtra($options, 'config');
        $params = $options;

        return $this->sendRequest(
            HttpMethod::POST,
            array(
                'config' => $config,
                'body' => json_encode($statParam),
                'params' => $params,
            ),
            '/stat/query'
        );
    }

    /**
     * open/close dynamic acceleration service
     *
     * @param array $action
     * @return mixed
     * @throws BceClientException
     */
    public function setDsa($action = array())
    {
        if (empty($action)) {
            throw new BceClientException("The parameter action should NOT be null");
        }

        $body = $action;

        return $this->sendRequest(
            HttpMethod::PUT,
            array(
                'body' => json_encode($body)
            ),
            '/dsa'
        );
    }

    /**
     * set Dynamic Acceleration Rules
     *
     * @param array $dsa
     * @return mixed
     * @throws BceClientException
     */
    public function setDomainDsa($domain, $dsa = array())
    {
        if (empty($domain) || empty($dsa)) {
            throw new BceClientException("The parameter domain and dsa should NOT be null");
        }

        $body = $dsa;
        $params = array('dsa' => '');

        return $this->sendRequest(
            HttpMethod::PUT,
            array(
                'params' => $params,
                'body' => json_encode($body)
            ),
            '/domain/'.$domain.'/config'
        );
    }

    /**
     * get dsa domain list
     *
     * @return mixed
     */
    public function getDomainDsa()
    {

        return $this->sendRequest(
            HttpMethod::GET,
            array(

            ),
            '/dsa/domain'
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
        $result->metadata = $this->convertHttpHeadersToMetadata($response['headers']);
        return $result;
    }
}
