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

include 'BaiduBce.phar';
require 'CdnSampleConf.php';

use BaiduBce\Services\Cdn\CdnClient;
use BaiduBce\BceClientConfigOptions;

use BaiduBce\Log\LogFactory;

class CdnClientTest extends PHPUnit_Framework_TestCase
{
    public function __construct()
    {
        global $g_CDN_TEST_CONFIG;

        parent::__construct();
        $this->client = new CdnClient($g_CDN_TEST_CONFIG);
        $this->logger = LogFactory::getLogger(get_class($this));
    }

    /**
     * test create domain   
     */
    public static function setUpBeforeClass()
    {
        global $g_CDN_TEST_CONFIG;

        $client = new CdnClient($g_CDN_TEST_CONFIG);
        $domain = "test-sdk.sys-qa.com";
        $origins = array(
            array("peer" => "test.origin.domain.com"),
        );
        $client->createDomain($domain, $origins);
    }

    /**
     * test delete domain
     */
    public static function tearDownAfterClass()
    {
        global $g_CDN_TEST_CONFIG;

        $client = new CdnClient($g_CDN_TEST_CONFIG);
        $domain = "test-sdk.sys-qa.com";
        $client->deleteDomain($domain);
    }

    /**
     * test list domain
     */
    public function testListDomain()
    {
        $resp = $this->client->listDomains();
        $this->assertNotNull($resp);
    }

    /**
     * test valid domain
     */
    public function testValidDomain() {
        global $g_CDN_TEST_CONFIG;

        $client = new CdnClient($g_CDN_TEST_CONFIG);
        $domain = "test-sdk.sys-qa.com";
        $resp = $client->validDomain($domain);
        $this->assertNotNull($resp);
    }

    /**
     * test list user domains
     */
    public function testListUserDomains() {
        global $g_CDN_TEST_CONFIG;

        $client = new CdnClient($g_CDN_TEST_CONFIG);

        $status = "RUNNING";
        $rule = "www";

        $param = array(
            'status' => $status,
            'rule' => $rule
        );

        $resp = $client->listUserDomains($param);

        $this->assertNotNull($resp);
    }

    /**
     * test valid domain
     * @expectedException    Exception
     * @throws \BaiduBce\Exception\BceClientException
     */
    public function testValidDomainThrow() {
        global $g_CDN_TEST_CONFIG;

        $client = new CdnClient($g_CDN_TEST_CONFIG);
        $domain = "";
        $resp = $client->validDomain($domain);
    }

    /**
     * test start domain
     */
    public function testEnableDomain()
    {
        $domain = "test-sdk.sys-qa.com";
        $resp = $this->client->enableDomain($domain);
        $this->assertNotNull($resp);
    }

    /**
     * test stop domain
     */
    public function testDisableDomain()
    {
        $domain = "test-sdk.sys-qa.com";
        $resp = $this->client->disableDomain($domain);
        $this->assertNotNull($resp);
    }

    /**
     * test update domain origin address
     */
    public function testSetDomainOrigin()
    {
        $domain = "test-sdk.sys-qa.com";
        $origins = array(
            array(
                "peer" => "test.origin-new.domain.com",
                'host' => 'www.origin-host.com'
            ),
        );
        $resp = $this->client->setDomainOrigin($domain, $origins);
        $this->assertNotNull($resp);
    }

    /**
     * test get domain config
     */
    public function testGetDomainConfig()
    {
        $domain = "test-sdk.sys-qa.com";
        $resp = $this->client->getDomainConfig($domain);
        $this->assertNotNull($resp);
    }

    /**
     * test get domain cacheFullUrl
     */
    public function testGetDomainCacheFullUrl() {
        global $g_CDN_TEST_CONFIG;

        $client = new CdnClient($g_CDN_TEST_CONFIG);
        $domain = "test-sdk.sys-qa.com";
        $resp = $client->getDomainCacheFullUrl($domain);
        $this->assertNotNull($resp);
    }

    /**
     * test get domain cacheFullUrl
     * @expectedException    Exception
     * @throws \BaiduBce\Exception\BceClientException
     */
    public function testGetDomainCacheFullUrlThrow() {
        global $g_CDN_TEST_CONFIG;

        $client = new CdnClient($g_CDN_TEST_CONFIG);
        $domain = "";
        $resp = $client->getDomainCacheFullUrl($domain);
    }

    /**
     * test set domain errorPage
     */
    public function testSetDomainErrorPage() {
        global $g_CDN_TEST_CONFIG;

        $errorPage = array(
            'errorPage' => array(
                array(
                    'code' => 404,
                    "redirectCode" => 302,
                    "url" => "customer_404.html"
                ),
                array(
                    'code' => 403,
                    "url" => "customer_403.html"
                )
            )
        );

        $client = new CdnClient($g_CDN_TEST_CONFIG);
        $domain = "test-sdk.sys-qa.com";
        $resp = $client->setDomainErrorPage($domain, $errorPage);
        $this->assertNotNull($resp);
    }

    /**
     * test set domain errorPage
     * @expectedException    Exception
     * @throws \BaiduBce\Exception\BceClientException
     */
    public function testSetDomainErrorPageThrow() {
        global $g_CDN_TEST_CONFIG;

        $client = new CdnClient($g_CDN_TEST_CONFIG);
        $domain = "";
        $errorPage = array();
        $resp = $client->setDomainErrorPage($domain, $errorPage);
    }

    /**
     * test get domain errorPage
     */
    public function testGetDomainErrorPage() {
        global $g_CDN_TEST_CONFIG;

        $client = new CdnClient($g_CDN_TEST_CONFIG);
        $domain = "test-sdk.sys-qa.com";
        $resp = $client->getDomainErrorPage($domain);
        $this->assertNotNull($resp);
    }

    /**
     * test get domain errorPage
     * @expectedException    Exception
     * @throws \BaiduBce\Exception\BceClientException
     */
    public function testGetDomainErrorPageThrow() {
        global $g_CDN_TEST_CONFIG;

        $client = new CdnClient($g_CDN_TEST_CONFIG);
        $domain = "";
        $resp = $client->getDomainErrorPage($domain);
    }

    /**
     * test set domain requestAuth
     */
    public function testSetDomainRequestAuth() {
        global $g_CDN_TEST_CONFIG;

        $requestAuth = array(
            'requestAuth' => array(
                "type" => "c",
                "key1" => "secretekey1",
                "key2" => "secretekey2",
                "timeout" => 300,
                "whiteList" => array("/crossdomain.xml"),
                "signArg" => "sign",
                "timeArg" => "t"
            )
        );

        $client = new CdnClient($g_CDN_TEST_CONFIG);
        $domain = "test-sdk.sys-qa.com";
        $resp = $client->setDomainRequestAuth($domain, $requestAuth);
        $this->assertNotNull($resp);
    }

    /**
     * test set domain requestAuth
     * @expectedException    Exception
     * @throws \BaiduBce\Exception\BceClientException
     */
    public function testSetDomainRequestAuthThrow() {
        global $g_CDN_TEST_CONFIG;

        $client = new CdnClient($g_CDN_TEST_CONFIG);
        $domain = "";
        $requestAuth = array();
        $client->setDomainRequestAuth($domain, $requestAuth);
    }

    /**
     * test set domain cors
     */
    public function testSetDomainCors() {
        global $g_CDN_TEST_CONFIG;

        $cors = array(
            'cors' => array(
                "allow" => "on",
                'originList' => array(
                    "www.baidu.com",
                )
            )
        );

        $client = new CdnClient($g_CDN_TEST_CONFIG);
        $domain = "test-sdk.sys-qa.com";
        $resp = $client->setDomainCors($domain, $cors);
        $this->assertNotNull($resp);
    }

    /**
     * test set domain cors
     * @expectedException    Exception
     * @throws \BaiduBce\Exception\BceClientException
     */
    public function testSetDomainCorsThrow() {
        global $g_CDN_TEST_CONFIG;

        $client = new CdnClient($g_CDN_TEST_CONFIG);
        $domain = "";
        $cors = array();
        $client->setDomainCors($domain, $cors);
    }

    /**
     * test get domain cors
     */
    public function testGetDomainCors() {
        global $g_CDN_TEST_CONFIG;

        $client = new CdnClient($g_CDN_TEST_CONFIG);
        $domain = "test-sdk.sys-qa.com";
        $resp = $client->getDomainCors($domain);
        $this->assertNotNull($resp);
    }

    /**
     * test get domain cors
     * @expectedException    Exception
     * @throws \BaiduBce\Exception\BceClientException
     */
    public function testGetDomainCorsThrow() {
        global $g_CDN_TEST_CONFIG;

        $client = new CdnClient($g_CDN_TEST_CONFIG);
        $domain = "";
        $client->getDomainCors($domain);
    }

    /**
     * test set domain accessLimit
     */
    public function testSetDomainAccessLimit() {
        global $g_CDN_TEST_CONFIG;

        $accessLimit = array(
            'accessLimit' => array(
                "enabled" => true,
                "limit" => 200
            )
        );

        $client = new CdnClient($g_CDN_TEST_CONFIG);
        $domain = "test-sdk.sys-qa.com";
        $resp = $client->setDomainAccessLimit($domain, $accessLimit);
        $this->assertNotNull($resp);
    }

    /**
     * test set domain accessLimit
     * @expectedException    Exception
     * @throws \BaiduBce\Exception\BceClientException
     */
    public function testSetDomainAccessLimitThrow() {
        global $g_CDN_TEST_CONFIG;

        $client = new CdnClient($g_CDN_TEST_CONFIG);
        $domain = "";
        $accessLimit = array();
        $client->setDomainAccessLimit($domain, $accessLimit);
    }

    /**
     * test get domain accessLimit
     */
    public function testGetDomainAccessLimit() {
        global $g_CDN_TEST_CONFIG;

        $client = new CdnClient($g_CDN_TEST_CONFIG);
        $domain = "test-sdk.sys-qa.com";
        $resp = $client->getDomainAccessLimit($domain);
        $this->assertNotNull($resp);
    }

    /**
     * test get domain accessLimit
     * @expectedException    Exception
     * @throws \BaiduBce\Exception\BceClientException
     */
    public function testGetDomainAccessLimitThrow() {
        global $g_CDN_TEST_CONFIG;

        $client = new CdnClient($g_CDN_TEST_CONFIG);
        $domain = "";
        $client->getDomainAccessLimit($domain);
    }

    /**
     * test set domain clientIp
     */
    public function testSetDomainClientIp() {
        global $g_CDN_TEST_CONFIG;

        $clientIp = array(
            'clientIp' => array(
                "enabled" => true,
                "name" => "X-Real-IP"
            )
        );

        $client = new CdnClient($g_CDN_TEST_CONFIG);
        $domain = "test-sdk.sys-qa.com";
        $resp = $client->setDomainClientIp($domain, $clientIp);
        $this->assertNotNull($resp);
    }

    /**
     * test set domain clientIp
     * @expectedException    Exception
     * @throws \BaiduBce\Exception\BceClientException
     */
    public function testSetDomainClientIpThrow() {
        global $g_CDN_TEST_CONFIG;

        $client = new CdnClient($g_CDN_TEST_CONFIG);
        $domain = "";
        $clientIp = array();
        $client->setDomainClientIp($domain, $clientIp);
    }

    /**
     * test get domain clientIp
     */
    public function testGetDomainClientIp() {
        global $g_CDN_TEST_CONFIG;

        $client = new CdnClient($g_CDN_TEST_CONFIG);
        $domain = "test-sdk.sys-qa.com";
        $resp = $client->getDomainClientIp($domain);
        $this->assertNotNull($resp);
    }

    /**
     * test get domain clientIp
     * @expectedException    Exception
     * @throws \BaiduBce\Exception\BceClientException
     */
    public function testGetDomainClientIpThrow() {
        global $g_CDN_TEST_CONFIG;

        $client = new CdnClient($g_CDN_TEST_CONFIG);
        $domain = "";
        $client->getDomainClientIp($domain);
    }

    /**
     * test set domain followProtocol
     */
    public function testSetDomainFollowProtocol() {
        global $g_CDN_TEST_CONFIG;

        $followProtocol = array(
            'followProtocol' => true
        );

        $client = new CdnClient($g_CDN_TEST_CONFIG);
        $domain = "test-sdk.sys-qa.com";
        $resp = $client->setDomainFollowProtocol($domain, $followProtocol);
        $this->assertNotNull($resp);
    }

    /**
     * test set domain followProtocol
     * @expectedException    Exception
     * @throws \BaiduBce\Exception\BceClientException
     */
    public function testSetDomainFollowProtocolThrow() {
        global $g_CDN_TEST_CONFIG;

        $client = new CdnClient($g_CDN_TEST_CONFIG);
        $domain = "";
        $followProtocol = array();
        $client->setDomainFollowProtocol($domain, $followProtocol);
    }

    /**
     * test set domain rangeSwitch
     */
    public function testSetDomainRangeSwitch() {
        global $g_CDN_TEST_CONFIG;

        $rangeSwitch = array(
            'rangeSwitch' => true
        );

        $client = new CdnClient($g_CDN_TEST_CONFIG);
        $domain = "test-sdk.sys-qa.com";
        $resp = $client->setDomainRangeSwitch($domain, $rangeSwitch);
        $this->assertNotNull($resp);
    }

    /**
     * test set domain rangeSwitch
     * @expectedException    Exception
     * @throws \BaiduBce\Exception\BceClientException
     */
    public function testSetDomainRangeSwitchThrow() {
        global $g_CDN_TEST_CONFIG;

        $client = new CdnClient($g_CDN_TEST_CONFIG);
        $domain = "";
        $rangeSwitch = array();
        $client->setDomainRangeSwitch($domain, $rangeSwitch);
    }

    /**
     * test get domain rangeSwitch
     */
    public function testGetDomainRangeSwitch() {
        global $g_CDN_TEST_CONFIG;

        $client = new CdnClient($g_CDN_TEST_CONFIG);
        $domain = "test-sdk.sys-qa.com";
        $resp = $client->getDomainRangeSwitch($domain);
        $this->assertNotNull($resp);
    }

    /**
     * test get domain rangeSwitch
     * @expectedException    Exception
     * @throws \BaiduBce\Exception\BceClientException
     */
    public function testGetDomainRangeSwitchThrow() {
        global $g_CDN_TEST_CONFIG;

        $client = new CdnClient($g_CDN_TEST_CONFIG);
        $domain = "";
        $client->getDomainRangeSwitch($domain);
    }

    /**
     * test set domain cacheShare off
     */
    public function testSetDomainCacheShareOff() {
        global $g_CDN_TEST_CONFIG;

        $cacheShare = array(
            'cacheShare' => array(
                "enabled" => false,
            )
        );
        $client = new CdnClient($g_CDN_TEST_CONFIG);
        $domain = "test-sdk.sys-qa.com";
        $resp = $client->setDomainCacheShare($domain, $cacheShare);
        $this->assertNotNull($resp);
    }

    /**
     * test set domain cacheShare on
     */
    public function testSetDomainCacheShareOn() {
        global $g_CDN_TEST_CONFIG;

        $cacheShare = array(
            'cacheShare' => array(
                "enabled" => true,
                "domain" => "duanhuiyan.top"
            )
        );
        $client = new CdnClient($g_CDN_TEST_CONFIG);
        $domain = "test-sdk.sys-qa.com";
        $resp = $client->setDomainCacheShare($domain, $cacheShare);
        $this->assertNotNull($resp);
    }

    /**
     * test get domain cacheShare
     */
    public function testGetDomainCacheShare() {
        global $g_CDN_TEST_CONFIG;

        $client = new CdnClient($g_CDN_TEST_CONFIG);
        $domain = "test-sdk.sys-qa.com";
        $resp = $client->getDomainCacheShare($domain);
        $this->assertNotNull($resp);
    }

    /**
     * test set domain trafficLimit off
     */
    public function testSetDomainTrafficLimitOff() {
        global $g_CDN_TEST_CONFIG;

        $trafficLimit = array(
            'trafficLimit' => array(
                "enable" => false,
            )
        );
        $client = new CdnClient($g_CDN_TEST_CONFIG);
        $domain = "test-sdk.sys-qa.com";
        $resp = $client->setDomainTrafficLimit($domain, $trafficLimit);
        $this->assertNotNull($resp);
    }

    /**
     * test set domain trafficLimit on
     */
    public function testSetDomainTrafficLimitOn() {
        global $g_CDN_TEST_CONFIG;

        $trafficLimit = array(
            'trafficLimit' => array(
                "enable" => true,
                "limitRate" => 10485760,
                "limitStartHour" => 10,
                "limitEndHour" => 19,
                "limitRateAfter" => 0,
                "trafficLimitArg" => "rate",
                "trafficLimitUnit" => "m"
            )
        );
        $client = new CdnClient($g_CDN_TEST_CONFIG);
        $domain = "test-sdk.sys-qa.com";
        $resp = $client->setDomainTrafficLimit($domain, $trafficLimit);
        $this->assertNotNull($resp);
    }

    /**
     * test get domain trafficLimit
     */
    public function testGetDomainTrafficLimit() {
        global $g_CDN_TEST_CONFIG;

        $client = new CdnClient($g_CDN_TEST_CONFIG);
        $domain = "test-sdk.sys-qa.com";
        $resp = $client->getDomainTrafficLimit($domain);
        $this->assertNotNull($resp);
    }

    /**
     * test set domain ua whiteList
     */
    public function testSetDomainUaWhiteList() {
        global $g_CDN_TEST_CONFIG;

        $flag = "white";
        $aclList = array(
            "MQQBrowser/5.3/Mozilla/5.0",
            "Mozilla/5.0 (Linux; Android 7.0"
        );
        $client = new CdnClient($g_CDN_TEST_CONFIG);
        $domain = "test-sdk.sys-qa.com";
        $resp = $client->setDomainUaAcl($domain, $flag, $aclList);
        $this->assertNotNull($resp);
    }

    /**
     * test delete domain ua whiteList
     */
    public function testDeleteDomainUaWhiteList() {
        global $g_CDN_TEST_CONFIG;

        $flag = "white";
        $aclList = array();
        $client = new CdnClient($g_CDN_TEST_CONFIG);
        $domain = "test-sdk.sys-qa.com";
        $resp = $client->setDomainUaAcl($domain, $flag, $aclList);
        $this->assertNotNull($resp);
    }

    /**
     * test set domain ua blackList
     */
    public function testSetDomainUaBlackList() {
        global $g_CDN_TEST_CONFIG;

        $flag = "black";
        $aclList = array(
            "MQQBrowser/5.3/Mozilla/5.0",
            "Mozilla/5.0 (Linux; Android 7.0"
        );
        $client = new CdnClient($g_CDN_TEST_CONFIG);
        $domain = "test-sdk.sys-qa.com";
        $resp = $client->setDomainUaAcl($domain, $flag, $aclList);
        $this->assertNotNull($resp);
    }

    /**
     * test delete domain ua blackList
     */
    public function testDeleteDomainUaBlackList() {
        global $g_CDN_TEST_CONFIG;

        $flag = "black";
        $aclList = array();
        $client = new CdnClient($g_CDN_TEST_CONFIG);
        $domain = "test-sdk.sys-qa.com";
        $resp = $client->setDomainUaAcl($domain, $flag, $aclList);
        $this->assertNotNull($resp);
    }

    /**
     * test get domain uaAcl
     */
    public function testGetDomainUaAcl() {
        global $g_CDN_TEST_CONFIG;

        $client = new CdnClient($g_CDN_TEST_CONFIG);
        $domain = "test-sdk.sys-qa.com";
        $resp = $client->getDomainUaACL($domain);
        $this->assertNotNull($resp);
    }

    /**
     * test set domain originProtocol https, you need to turn on HTTPS first
     */
    public function testSetDomainOriginProtocolHttps() {
        global $g_CDN_TEST_CONFIG;

        $this->testSetDomainHttpsOn();

        $originProtocol = array(
            "originProtocol" => array(
                "value" => "https"
            )
        );
        $client = new CdnClient($g_CDN_TEST_CONFIG);
        $domain = "test-sdk.sys-qa.com";
        $resp = $client->setDomainOriginProtocol($domain, $originProtocol);
        $this->assertNotNull($resp);
    }

    /**
     * test set domain originProtocol http
     */
    public function testSetDomainOriginProtocolHttp() {
        global $g_CDN_TEST_CONFIG;

        $originProtocol = array(
            "originProtocol" => array(
                "value" => "http"
            )
        );
        $client = new CdnClient($g_CDN_TEST_CONFIG);
        $domain = "test-sdk.sys-qa.com";
        $resp = $client->setDomainOriginProtocol($domain, $originProtocol);
        $this->assertNotNull($resp);
    }

    /**
     * test set domain originProtocol follow
     */
    public function testSetDomainOriginProtocolFollow() {
        global $g_CDN_TEST_CONFIG;

        $originProtocol = array(
            "originProtocol" => array(
                "value" => "*"
            )
        );
        $client = new CdnClient($g_CDN_TEST_CONFIG);
        $domain = "test-sdk.sys-qa.com";
        $resp = $client->setDomainOriginProtocol($domain, $originProtocol);
        $this->assertNotNull($resp);
    }

    /**
     * test set domain retryOrigin
     */
    public function testSetDomainRetryOrigin() {
        global $g_CDN_TEST_CONFIG;

        $retryOrigin = array(
            "retryOrigin" => array(
                "codes" => array(
                    500,
                    502,
                    503
                )
            )
        );
        $client = new CdnClient($g_CDN_TEST_CONFIG);
        $domain = "test-sdk.sys-qa.com";
        $resp = $client->setDomainRetryOrigin($domain, $retryOrigin);
        $this->assertNotNull($resp);
    }

    /**
     * test set domain retryOrigin off
     */
    public function testSetDomainRetryOriginOff() {
        global $g_CDN_TEST_CONFIG;

        $retryOrigin = array(
            "retryOrigin" => array(
                "codes" => array()
            )
        );
        $client = new CdnClient($g_CDN_TEST_CONFIG);
        $domain = "test-sdk.sys-qa.com";
        $resp = $client->setDomainRetryOrigin($domain, $retryOrigin);
        $this->assertNotNull($resp);
    }

    /**
     * test get domain retryOrigin
     */
    public function testGetDomainRetryOrigin() {
        global $g_CDN_TEST_CONFIG;

        $client = new CdnClient($g_CDN_TEST_CONFIG);
        $domain = "test-sdk.sys-qa.com";
        $resp = $client->getDomainRetryOrigin($domain);
        $this->assertNotNull($resp);
    }

    /**
     * test set domain ipv6Dispatch off
     */
    public function testSetDomainIpv6DispatchOff() {
        global $g_CDN_TEST_CONFIG;

        $ipv6Dispatch = array(
            "ipv6Dispatch" => array(
                "enable" => false
            )
        );
        $client = new CdnClient($g_CDN_TEST_CONFIG);
        $domain = "test-sdk.sys-qa.com";
        $resp = $client->setDomainIpv6Dispatch($domain, $ipv6Dispatch);
        $this->assertNotNull($resp);
    }

    /**
     * test get domain ipv6Dispatch
     */
    public function testGetDomainIpv6Dispatch() {
        global $g_CDN_TEST_CONFIG;

        $client = new CdnClient($g_CDN_TEST_CONFIG);
        $domain = "test-sdk.sys-qa.com";
        $resp = $client->getDomainIpv6Dispatch($domain);
        $this->assertNotNull($resp);
    }

    /**
     * test set domain quic on
     */
    public function testSetDomainQuicOn() {
        global $g_CDN_TEST_CONFIG;

        $this->testSetDomainHttpsOn();

        $quic = array(
            "quic" => true
        );
        $client = new CdnClient($g_CDN_TEST_CONFIG);
        $domain = "test-sdk.sys-qa.com";
        $resp = $client->setDomainQuic($domain, $quic);
        $this->assertNotNull($resp);
    }

    /**
     * test set domain quic off
     */
    public function testSetDomainQuicOff() {
        global $g_CDN_TEST_CONFIG;

        $quic = array(
            "quic" => false
        );
        $client = new CdnClient($g_CDN_TEST_CONFIG);
        $domain = "test-sdk.sys-qa.com";
        $resp = $client->setDomainQuic($domain, $quic);
        $this->assertNotNull($resp);
    }

    /**
     * test get domain quic
     */
    public function testGetDomainQuic() {
        global $g_CDN_TEST_CONFIG;

        $client = new CdnClient($g_CDN_TEST_CONFIG);
        $domain = "test-sdk.sys-qa.com";
        $resp = $client->getDomainQuic($domain);
        $this->assertNotNull($resp);
    }

    /**
     * test set domain offlineMode on
     */
    public function testSetDomainOfflineModeOn() {
        global $g_CDN_TEST_CONFIG;

        $offlineMode = array(
            "offlineMode" => true
        );
        $client = new CdnClient($g_CDN_TEST_CONFIG);
        $domain = "test-sdk.sys-qa.com";
        $resp = $client->setDomainOfflineMode($domain, $offlineMode);
        $this->assertNotNull($resp);
    }

    /**
     * test set domain offlineMode off
     */
    public function testSetDomainOfflineModeOff() {
        global $g_CDN_TEST_CONFIG;

        $offlineMode = array(
            "offlineMode" => false
        );
        $client = new CdnClient($g_CDN_TEST_CONFIG);
        $domain = "test-sdk.sys-qa.com";
        $resp = $client->setDomainOfflineMode($domain, $offlineMode);
        $this->assertNotNull($resp);
    }

    /**
     * test get domain offlineMode
     */
    public function testGetDomainOfflineMode() {
        global $g_CDN_TEST_CONFIG;

        $client = new CdnClient($g_CDN_TEST_CONFIG);
        $domain = "test-sdk.sys-qa.com";
        $resp = $client->getDomainOfflineMode($domain);
        $this->assertNotNull($resp);
    }

    /**
     * test set domain ocsp on
     */
    public function testSetDomainOcspOn() {
        global $g_CDN_TEST_CONFIG;
        
        $this->testSetDomainHttpsOn();

        $ocsp = array(
            "ocsp" => true
        );
        $client = new CdnClient($g_CDN_TEST_CONFIG);
        $domain = "test-sdk.sys-qa.com";
        $resp = $client->setDomainOcsp($domain, $ocsp);
        $this->assertNotNull($resp);
    }

    /**
     * test set domain ocsp off
     */
    public function testSetDomainOcspOff() {
        global $g_CDN_TEST_CONFIG;

        $ocsp = array(
            "ocsp" => false
        );
        $client = new CdnClient($g_CDN_TEST_CONFIG);
        $domain = "test-sdk.sys-qa.com";
        $resp = $client->setDomainOcsp($domain, $ocsp);
        $this->assertNotNull($resp);
    }

    /**
     * test get domain ocsp
     */
    public function testGetDomainOcsp() {
        global $g_CDN_TEST_CONFIG;

        $client = new CdnClient($g_CDN_TEST_CONFIG);
        $domain = "test-sdk.sys-qa.com";
        $resp = $client->getDomainOcsp($domain);
        $this->assertNotNull($resp);
    }

    /**
     * test get nodes list
     */
    public function testGetNodesList() {
        global $g_CDN_TEST_CONFIG;

        $client = new CdnClient($g_CDN_TEST_CONFIG);
        $resp = $client->getNodesList();
        $this->assertNotNull($resp);
    }

    /**
     * test set domain mobileAccess
     */
    public function testSetDomainMobileAccess() {
        global $g_CDN_TEST_CONFIG;

        $mobileAccess = array(
            'mobileAccess' => array(
                "distinguishClient" => true
            )
        );

        $client = new CdnClient($g_CDN_TEST_CONFIG);
        $domain = "test-sdk.sys-qa.com";
        $resp = $client->setDomainMobileAccess($domain, $mobileAccess);
        $this->assertNotNull($resp);
    }

    /**
     * test set domain mobileAccess
     * @expectedException    Exception
     * @throws \BaiduBce\Exception\BceClientException
     */
    public function testSetDomainMobileAccessThrow() {
        global $g_CDN_TEST_CONFIG;

        $client = new CdnClient($g_CDN_TEST_CONFIG);
        $domain = "";
        $mobileAccess = array();
        $client->setDomainMobileAccess($domain, $mobileAccess);
    }

    /**
     * test get domain mobileAccess
     */
    public function testGetDomainMobileAccess() {
        global $g_CDN_TEST_CONFIG;

        $client = new CdnClient($g_CDN_TEST_CONFIG);
        $domain = "test-sdk.sys-qa.com";
        $resp = $client->getDomainMobileAccess($domain);
        $this->assertNotNull($resp);
    }

    /**
     * test get domain mobileAccess
     * @expectedException    Exception
     * @throws \BaiduBce\Exception\BceClientException
     */
    public function testGetDomainMobileAccessThrow() {
        global $g_CDN_TEST_CONFIG;

        $client = new CdnClient($g_CDN_TEST_CONFIG);
        $domain = "";
        $client->getDomainMobileAccess($domain);
    }

    /**
     * test set domain httpHeader
     */
    public function testSetDomainHttpHeader() {
        global $g_CDN_TEST_CONFIG;

        $httpHeader = array(
            'httpHeader' => array(
                array(
                    "type" => "origin",
                    "header" => "x-auth-cn",
                    "value" => "xxxxxxxxx",
                    "action" => "add"
                )
            )
        );

        $client = new CdnClient($g_CDN_TEST_CONFIG);
        $domain = "test-sdk.sys-qa.com";
        $resp = $client->setDomainHttpHeader($domain, $httpHeader);
        $this->assertNotNull($resp);
    }

    /**
     * test set domain httpHeader
     * @expectedException    Exception
     * @throws \BaiduBce\Exception\BceClientException
     */
    public function testSetDomainHttpHeaderThrow() {
        global $g_CDN_TEST_CONFIG;

        $client = new CdnClient($g_CDN_TEST_CONFIG);
        $domain = "";
        $httpHeader = array();
        $client->setDomainHttpHeader($domain, $httpHeader);
    }

    /**
     * test get domain httpHeader
     */
    public function testGetDomainHttpHeader() {
        global $g_CDN_TEST_CONFIG;

        $client = new CdnClient($g_CDN_TEST_CONFIG);
        $domain = "test-sdk.sys-qa.com";
        $resp = $client->getDomainHttpHeader($domain);
        $this->assertNotNull($resp);
    }

    /**
     * test get domain httpHeader
     * @expectedException    Exception
     * @throws \BaiduBce\Exception\BceClientException
     */
    public function testGetDomainHttpHeaderThrow() {
        global $g_CDN_TEST_CONFIG;

        $client = new CdnClient($g_CDN_TEST_CONFIG);
        $domain = "";
        $client->getDomainHttpHeader($domain);
    }

    /**
     * test set domain seoSwitch
     */
    public function testSetDomainSeoSwitch() {
        global $g_CDN_TEST_CONFIG;

        $seoSwitch = array(
            'seoSwitch' => array(
                "diretlyOrigin" => "ON",
                "pushRecord" => "OFF"
            )
        );

        $client = new CdnClient($g_CDN_TEST_CONFIG);
        $domain = "test-sdk.sys-qa.com";
        $resp = $client->setDomainSeoSwitch($domain, $seoSwitch);
        $this->assertNotNull($resp);
    }

    /**
     * test set domain seoSwitch
     * @expectedException    Exception
     * @throws \BaiduBce\Exception\BceClientException
     */
    public function testSetDomainSeoSwitchThrow() {
        global $g_CDN_TEST_CONFIG;

        $client = new CdnClient($g_CDN_TEST_CONFIG);
        $domain = "";
        $seoSwitch = array();
        $client->setDomainSeoSwitch($domain, $seoSwitch);
    }

    /**
     * test get domain seoSwitch
     */
    public function testGetDomainSeoSwitch() {
        global $g_CDN_TEST_CONFIG;

        $client = new CdnClient($g_CDN_TEST_CONFIG);
        $domain = "test-sdk.sys-qa.com";
        $resp = $client->getDomainSeoSwitch($domain);
        $this->assertNotNull($resp);
    }

    /**
     * test get domain seoSwitch
     * @expectedException    Exception
     * @throws \BaiduBce\Exception\BceClientException
     */
    public function testGetDomainSeoSwitchThrow() {
        global $g_CDN_TEST_CONFIG;

        $client = new CdnClient($g_CDN_TEST_CONFIG);
        $domain = "";
        $client->getDomainSeoSwitch($domain);
    }

    /**
     * test set domain fileTrim
     */
    public function testSetDomainFileTrim() {
        global $g_CDN_TEST_CONFIG;

        $fileTrim = array(
            'fileTrim' => true
        );

        $client = new CdnClient($g_CDN_TEST_CONFIG);
        $domain = "test-sdk.sys-qa.com";
        $resp = $client->setDomainFileTrim($domain, $fileTrim);
        $this->assertNotNull($resp);
    }

    /**
     * test set domain fileTrim
     * @expectedException    Exception
     * @throws \BaiduBce\Exception\BceClientException
     */
    public function testSetDomainFileTrimThrow() {
        global $g_CDN_TEST_CONFIG;

        $client = new CdnClient($g_CDN_TEST_CONFIG);
        $domain = "";
        $fileTrim = array();
        $client->setDomainFileTrim($domain, $fileTrim);
    }

    /**
     * test get domain fileTrim
     */
    public function testGetDomainFileTrim() {
        global $g_CDN_TEST_CONFIG;

        $client = new CdnClient($g_CDN_TEST_CONFIG);
        $domain = "test-sdk.sys-qa.com";
        $resp = $client->getDomainFileTrim($domain);
        $this->assertNotNull($resp);
    }

    /**
     * test get domain fileTrim
     * @expectedException    Exception
     * @throws \BaiduBce\Exception\BceClientException
     */
    public function testGetDomainFileTrimThrow() {
        global $g_CDN_TEST_CONFIG;

        $client = new CdnClient($g_CDN_TEST_CONFIG);
        $domain = "";
        $client->getDomainFileTrim($domain);
    }

    /**
     * test set domain mediaDrag
     */
    public function testSetDomainMediaDrag() {
        global $g_CDN_TEST_CONFIG;

        $mediaDrag = array(
            'mediaDragConf' => array(
                'mp4' => array(
                    'fileSuffix' => array('mp4'),
                    'startArgName' => 'startIndex',
                    'dragMode' => 'second',
                    'endArgName' => 'end'
                )
            )
        );

        $client = new CdnClient($g_CDN_TEST_CONFIG);
        $domain = "test-sdk.sys-qa.com";
        $resp = $client->setDomainMediaDrag($domain, $mediaDrag);
        $this->assertNotNull($resp);
    }

    /**
     * test set domain mediaDrag
     * @expectedException    Exception
     * @throws \BaiduBce\Exception\BceClientException
     */
    public function testSetDomainMediaDragThrow() {
        global $g_CDN_TEST_CONFIG;

        $client = new CdnClient($g_CDN_TEST_CONFIG);
        $domain = "";
        $mediaDrag = array();
        $client->setDomainMediaDrag($domain, $mediaDrag);
    }

    /**
     * test get domain mediaDrag
     */
    public function testGetDomainMediaDrag() {
        global $g_CDN_TEST_CONFIG;

        $client = new CdnClient($g_CDN_TEST_CONFIG);
        $domain = "test-sdk.sys-qa.com";
        $resp = $client->getDomainMediaDrag($domain);
        $this->assertNotNull($resp);
    }

    /**
     * test get domain mediaDrag
     * @expectedException    Exception
     * @throws \BaiduBce\Exception\BceClientException
     */
    public function testGetDomainMediaDragThrow() {
        global $g_CDN_TEST_CONFIG;

        $client = new CdnClient($g_CDN_TEST_CONFIG);
        $domain = "";
        $client->getDomainMediaDrag($domain);
    }

    /**
     * test set domain compress
     */
    public function testSetDomainCompress() {
        global $g_CDN_TEST_CONFIG;

        $compress = array(
            'compress' => array(
                "allow" => true,
                "type" => "br"
            )
        );

        $client = new CdnClient($g_CDN_TEST_CONFIG);
        $domain = "test-sdk.sys-qa.com";
        $resp = $client->setDomainCompress($domain, $compress);
        $this->assertNotNull($resp);
    }

    /**
     * test set domain compress
     * @expectedException    Exception
     * @throws \BaiduBce\Exception\BceClientException
     */
    public function testSetDomainCompressThrow() {
        global $g_CDN_TEST_CONFIG;

        $client = new CdnClient($g_CDN_TEST_CONFIG);
        $domain = "";
        $compress = array();
        $client->setDomainCompress($domain, $compress);
    }

    /**
     * test get domain compress
     */
    public function testGetDomainCompress() {
        global $g_CDN_TEST_CONFIG;

        $client = new CdnClient($g_CDN_TEST_CONFIG);
        $domain = "test-sdk.sys-qa.com";
        $resp = $client->getDomainCompress($domain);
        $this->assertNotNull($resp);
    }

    /**
     * test get domain compress
     * @expectedException    Exception
     * @throws \BaiduBce\Exception\BceClientException
     */
    public function testGetDomainCompressThrow() {
        global $g_CDN_TEST_CONFIG;

        $client = new CdnClient($g_CDN_TEST_CONFIG);
        $domain = "";
        $client->getDomainCompress($domain);
    }

    /**
     * test set domain https off
     */
    public function testSetDomainHttpsOff() {
        global $g_CDN_TEST_CONFIG;

        $https = array(
            'https' => array(
                "enabled" => false,
                "certId" => "----"//当enabled为true时该参数要为有效当证书id
            )
        );

        $client = new CdnClient($g_CDN_TEST_CONFIG);
        $domain = "test-sdk.sys-qa.com";
        $resp = $client->setDomainHttps($domain, $https);
        $this->assertNotNull($resp);
    }

    /**
     * test set domain https on
     */
    public function testSetDomainHttpsOn() {
        global $g_CDN_TEST_CONFIG;

        $https = array(
            'https' => array(
                "enabled" => true,
                "certId" => "cert-qtah8qqwki6w"
            )
        );

        $client = new CdnClient($g_CDN_TEST_CONFIG);
        $domain = "test-sdk.sys-qa.com";
        $resp = $client->setDomainHttps($domain, $https);
        $this->assertNotNull($resp);
    }

    /**
     * test set domain https
     * @expectedException    Exception
     * @throws \BaiduBce\Exception\BceClientException
     */
    public function testSetDomainHttpsThrow() {
        global $g_CDN_TEST_CONFIG;

        $client = new CdnClient($g_CDN_TEST_CONFIG);
        $domain = "";
        $https = array();
        $client->setDomainHttps($domain, $https);
    }

    /**
     * test cache records
     */
    public function testGetRecords()
    {
        $resp = $this->client->getRecords();
        $this->assertNotNull($resp);
    }

    /**
     * test get domain cache ttl
     */
    public function testGetDomainCacheTTL()
    {
        $domain = "test-sdk.sys-qa.com";
        $resp = $this->client->getDomainCacheTTL($domain);
        $this->assertNotNull($resp);
    }

    /**
     * test set domain cache ttl
     */
    public function testSetDomainCacheTTL()
    {
        $domain = "test-sdk.sys-qa.com";
        $rules = array(
            array(
                "type" => "suffix",
                "value" => ".jpg",
                "ttl" => 36000,
                "weight" => 30,
            ),
        );
        $resp = $this->client->setDomainCacheTTL($domain, $rules);
        $this->assertNotNull($resp);
    }

    /**
     * test set domain cache full url
     */
    public function testSetDomainCacheFullUrl()
    {
        $domain = "test-sdk.sys-qa.com";
        $flag = true;
        $resp = $this->client->setDomainCacheFullUrl($domain, $flag);
        $this->assertNotNull($resp);
    }

    /**
     * test set domain ip acl
     */
    public function testSetDomainIpAcl()
    {
        $domain = "test-sdk.sys-qa.com";
        $aclList = array(
            "1.2.3.4",
            "5.6.7.8",
        );
        $flag = "white";
        $resp = $this->client->setDomainIpAcl($domain, $flag, $aclList);
        $this->assertNotNull($resp);
    }

    /**
     * test get domain ip acl   
     */
    public function testGetDomainIpACL() {
        global $g_CDN_TEST_CONFIG;

        $client = new CdnClient($g_CDN_TEST_CONFIG);
        $domain = "test-sdk.sys-qa.com";
        $resp = $client->getDomainIpACL($domain);
        $this->assertNotNull($resp);
    }

    /**
     * test set domain refererAcl
     */
    public function testSetDomainRefererAcl()
    {
        $domain = "test-sdk.sys-qa.com";
        $aclList = array(
            "your.black.list1",
            "your.black.list2",
        );
        $flag = "black";
        $allowEmpty=true;
        $resp = $this->client->setDomainRefererAcl($domain, $flag, $allowEmpty, $aclList);
        $this->assertNotNull($resp);
    }

    /**
     * test get domain refererAcl
     */
    public function testGetDomainRefererACL() {
        global $g_CDN_TEST_CONFIG;

        $client = new CdnClient($g_CDN_TEST_CONFIG);
        $domain = "test-sdk.sys-qa.com";
        $resp = $client->getDomainRefererAcl($domain);
        $this->assertNotNull($resp);
    }

    /**
     * test set domain limit rate
     */
    public function testSetDomainLimitRate()
    {
        $domain = "test-sdk.sys-qa.com";
        $rate = 1024;
        $resp = $this->client->setDomainLimitRate($domain, $rate);
        $this->assertNotNull($resp);
    }

    /**
     * test get domain pv stat
     */
    public function testGetDomainPvStat()
    {
        $domain = 'test-sdk.sys-qa.com';
        $period = 300;
        $withRegion = 'true';

        $endTime = time();
        $startTime = $endTime - $period * 10;

        $endTime = gmdate("Y-m-d\TH:i:s\Z", $endTime);
        $startTime = gmdate("Y-m-d\TH:i:s\Z", $startTime);

        $resp = $this->client->getDomainPvStat($domain, $startTime, $endTime,
            $period, $withRegion);
        $this->assertNotNull($resp);
    }

    /**
     * test get domain uv stat
     */
    public function testGetDomainUvStat()
    {
        $domain = 'test-sdk.sys-qa.com';
        $period = 3600;
        $withRegion = 'true';

        $endTime = time();
        $startTime = $endTime - $period * 10;

        $endTime = gmdate("Y-m-d\TH:i:s\Z", $endTime);
        $startTime = gmdate("Y-m-d\TH:i:s\Z", $startTime);

        $resp = $this->client->getDomainUvStat($domain, $startTime, $endTime, $period);
        $this->assertNotNull($resp);
    }

    /**
     * test get domain avg speed stat
     */
    public function testGetDomainAvgSpeedStat()
    {
        $domain = 'test-sdk.sys-qa.com';
        $period = 300;

        $endTime = time();
        $startTime = $endTime - $period * 10;

        $endTime = gmdate("Y-m-d\TH:i:s\Z", $endTime);
        $startTime = gmdate("Y-m-d\TH:i:s\Z", $startTime);

        $resp = $this->client->getDomainAvgSpeedStat($domain, $startTime, $endTime, $period);
        $this->assertNotNull($resp);
    }

    /**
     * test get domain flow stat
     */
    public function testGetDomainFlowStat()
    {
        $domain = 'test-sdk.sys-qa.com';
        $period = 300;
        $withRegion = 'true';

        $endTime = time();
        $startTime = $endTime - $period * 10;

        $endTime = gmdate("Y-m-d\TH:i:s\Z", $endTime);
        $startTime = gmdate("Y-m-d\TH:i:s\Z", $startTime);

        $resp = $this->client->getDomainFlowStat($domain, $startTime, $endTime,
            $period, $withRegion);
        $this->assertNotNull($resp);
    }


    /**
     * test get domain src flow stat
     */
    public function testGetDomainSrcFlowStat()
    {
        $domain = 'test-sdk.sys-qa.com';
        $period = 300;

        $endTime = time();
        $startTime = $endTime - $period * 10;

        $endTime = gmdate("Y-m-d\TH:i:s\Z", $endTime);
        $startTime = gmdate("Y-m-d\TH:i:s\Z", $startTime);

        $resp = $this->client->getDomainSrcFlowStat($domain, $startTime, $endTime, $period);
        $this->assertNotNull($resp);
    }

    /**
     * test get domain hit rate stat
     */
    public function testGetDomainHitRateStat()
    {
        $domain = 'test-sdk.sys-qa.com';
        $period = 300;

        $endTime = time();
        $startTime = $endTime - $period * 10;

        $endTime = gmdate("Y-m-d\TH:i:s\Z", $endTime);
        $startTime = gmdate("Y-m-d\TH:i:s\Z", $startTime);

        $resp = $this->client->getDomainHitRateStat($domain, $startTime, $endTime, $period);
        $this->assertNotNull($resp);
    }

    /**
     * test get domain http code stat
     */
    public function testGetDomainHttpCodeStat()
    {
        $domain = 'test-sdk.sys-qa.com';
        $period = 300;
        $withRegion = 'true';

        $endTime = time();
        $startTime = $endTime - $period * 10;

        $endTime = gmdate("Y-m-d\TH:i:s\Z", $endTime);
        $startTime = gmdate("Y-m-d\TH:i:s\Z", $startTime);

        $resp = $this->client->getDomainHttpCodeStat($domain, $startTime, $endTime,
            $period, $withRegion);
        $this->assertNotNull($resp);
    }

    /**
     * test get domain top url stat
     */
    public function testGetDomainTopUrlStat()
    {
        $domain = 'test-sdk.sys-qa.com';
        $period = 300;

        $endTime = time();
        $startTime = $endTime - $period * 10;

        $endTime = gmdate("Y-m-d\TH:i:s\Z", $endTime);
        $startTime = gmdate("Y-m-d\TH:i:s\Z", $startTime);

        $resp = $this->client->getDomainTopUrlStat($domain, $startTime, $endTime, $period);
        $this->assertNotNull($resp);
    }

    /**
     * test get domain top referer stat
     */
    public function testGetDomainTopRefererStat()
    {
        $domain = 'test-sdk.sys-qa.com';
        $period = 300;

        $endTime = time();
        $startTime = $endTime - $period * 10;

        $endTime = gmdate("Y-m-d\TH:i:s\Z", $endTime);
        $startTime = gmdate("Y-m-d\TH:i:s\Z", $startTime);

        $resp = $this->client->getDomainTopRefererStat($domain, $startTime, $endTime, $period);
        $this->assertNotNull($resp);
    }

    /**
     * test get domain stats avg speed, new version
     */
    public function testGetDomainStatsAvgSpeed() {
        $period = 300;
        $endTime = time();
        $startTime = $endTime - $period * 10;
        $endTime = gmdate("Y-m-d\TH:i:s\Z", $endTime);
        $startTime = gmdate("Y-m-d\TH:i:s\Z", $startTime);
        $statParam = array(
            'startTime' => $startTime,
            'endTime' => $endTime,
            'period' => $period,
            'key_type' => 0,
            'key' => array('test-sdk.sys-qa.com'),
            'groupBy' => '',
            'metric' => 'avg_speed',
        );

        $resp = $this->client->getDomainStats($statParam);
        $this->assertNotNull($resp);
    }

    /**
     * test get domain stats avg speed by region, new version
     */
    public function testGetDomainStatsAvgSpeedRegion() {
        $period = 300;
        $endTime = time();
        $startTime = $endTime - $period * 10;
        $endTime = gmdate("Y-m-d\TH:i:s\Z", $endTime);
        $startTime = gmdate("Y-m-d\TH:i:s\Z", $startTime);
        $statParam = array(
            'startTime' => $startTime,
            'endTime' => $endTime,
            'period' => $period,
            'key_type' => 0,
            'key' => array('test-sdk.sys-qa.com'),
            'groupBy' => '',
            'prov' => 'beijing',
            'isp' => 'ct',
            'metric' => 'avg_speed_region',
        );

        $resp = $this->client->getDomainStats($statParam);
        $this->assertNotNull($resp);
    }

    /**
     * test get domain stats pv, new version
     */
    public function testGetDomainStatsPv() {
        $period = 300;
        $endTime = time();
        $startTime = $endTime - $period * 10;
        $endTime = gmdate("Y-m-d\TH:i:s\Z", $endTime);
        $startTime = gmdate("Y-m-d\TH:i:s\Z", $startTime);
        $statParam = array(
            'startTime' => $startTime,
            'endTime' => $endTime,
            'period' => $period,
            'key_type' => 0,
            'key' => array('test-sdk.sys-qa.com'),
            'groupBy' => '',
            'level' => 'edge',
            'metric' => 'pv',
        );

        $resp = $this->client->getDomainStats($statParam);
        $this->assertNotNull($resp);
    }

    /**
     * test get domain stats pv by region, new version
     */
    public function testGetDomainStatsPvRegion() {
        $period = 300;
        $endTime = time();
        $startTime = $endTime - $period * 10;
        $endTime = gmdate("Y-m-d\TH:i:s\Z", $endTime);
        $startTime = gmdate("Y-m-d\TH:i:s\Z", $startTime);
        $statParam = array(
            'startTime' => $startTime,
            'endTime' => $endTime,
            'period' => $period,
            'key_type' => 0,
            'key' => array('test-sdk.sys-qa.com'),
            'groupBy' => '',
            'prov' => 'beijing',
            'isp' => 'ct',
            'metric' => 'pv_region',
        );

        $resp = $this->client->getDomainStats($statParam);
        $this->assertNotNull($resp);
    }

    /**
     * test get domain stats pv src, new version
     */
    public function testGetDomainStatsPvSrc() {
        $period = 300;
        $endTime = time();
        $startTime = $endTime - $period * 10;
        $endTime = gmdate("Y-m-d\TH:i:s\Z", $endTime);
        $startTime = gmdate("Y-m-d\TH:i:s\Z", $startTime);
        $statParam = array(
            'startTime' => $startTime,
            'endTime' => $endTime,
            'period' => $period,
            'key_type' => 0,
            'key' => array('test-sdk.sys-qa.com'),
            'groupBy' => '',
            'metric' => 'pv_src',
        );

        $resp = $this->client->getDomainStats($statParam);
        $this->assertNotNull($resp);
    }

    /**
     * test get domain stats uv, new version
     */
    public function testGetDomainStatsUv() {
        $period = 300;
        $endTime = time();
        $startTime = $endTime - $period * 10;
        $endTime = gmdate("Y-m-d\TH:i:s\Z", $endTime);
        $startTime = gmdate("Y-m-d\TH:i:s\Z", $startTime);
        $statParam = array(
            'startTime' => $startTime,
            'endTime' => $endTime,
            'period' => $period,
            'key_type' => 0,
            'key' => array('test-sdk.sys-qa.com'),
            'groupBy' => '',
            'metric' => 'uv',
        );

        $resp = $this->client->getDomainStats($statParam);
        $this->assertNotNull($resp);
    }

    /**
     * test get domain stats flow, new version
     */
    public function testGetDomainStatsFlow() {
        $period = 300;
        $endTime = time();
        $startTime = $endTime - $period * 10;
        $endTime = gmdate("Y-m-d\TH:i:s\Z", $endTime);
        $startTime = gmdate("Y-m-d\TH:i:s\Z", $startTime);
        $statParam = array(
            'startTime' => $startTime,
            'endTime' => $endTime,
            'period' => $period,
            'key_type' => 0,
            'key' => array('test-sdk.sys-qa.com'),
            'groupBy' => '',
            'level' => 'edge',
            'metric' => 'flow',
        );

        $resp = $this->client->getDomainStats($statParam);
        $this->assertNotNull($resp);
    }

    /**
     * test get domain stats flow by protocol, new version
     */
    public function testGetDomainStatsFlowProtocol() {
        $period = 300;
        $endTime = time();
        $startTime = $endTime - $period * 10;
        $endTime = gmdate("Y-m-d\TH:i:s\Z", $endTime);
        $startTime = gmdate("Y-m-d\TH:i:s\Z", $startTime);
        $statParam = array(
            'startTime' => $startTime,
            'endTime' => $endTime,
            'period' => $period,
            'key_type' => 0,
            'key' => array('test-sdk.sys-qa.com'),
            'groupBy' => '',
            'protocol' => 'https',
            'metric' => 'flow_protocol',
        );

        $resp = $this->client->getDomainStats($statParam);
        $this->assertNotNull($resp);
    }

    /**
     * test get domain stats flow by region, new version
     */
    public function testGetDomainStatsFLowRegion() {
        $period = 300;
        $endTime = time();
        $startTime = $endTime - $period * 10;
        $endTime = gmdate("Y-m-d\TH:i:s\Z", $endTime);
        $startTime = gmdate("Y-m-d\TH:i:s\Z", $startTime);
        $statParam = array(
            'startTime' => $startTime,
            'endTime' => $endTime,
            'period' => $period,
            'key_type' => 0,
            'key' => array('test-sdk.sys-qa.com'),
            'groupBy' => '',
            'prov' => 'beijing',
            'isp' => 'ct',
            'metric' => 'flow_region',
        );

        $resp = $this->client->getDomainStats($statParam);
        $this->assertNotNull($resp);
    }

    /**
     * test get domain stats flow src, new version
     */
    public function testGetDomainStatsFlowSrc() {
        $period = 300;
        $endTime = time();
        $startTime = $endTime - $period * 10;
        $endTime = gmdate("Y-m-d\TH:i:s\Z", $endTime);
        $startTime = gmdate("Y-m-d\TH:i:s\Z", $startTime);
        $statParam = array(
            'startTime' => $startTime,
            'endTime' => $endTime,
            'period' => $period,
            'key_type' => 0,
            'key' => array('test-sdk.sys-qa.com'),
            'groupBy' => '',
            'metric' => 'src_flow',
        );

        $resp = $this->client->getDomainStats($statParam);
        $this->assertNotNull($resp);
    }

    /**
     * test get domain stats hit rate, new version
     */
    public function testGetDomainStatsHit() {
        $period = 300;
        $endTime = time();
        $startTime = $endTime - $period * 10;
        $endTime = gmdate("Y-m-d\TH:i:s\Z", $endTime);
        $startTime = gmdate("Y-m-d\TH:i:s\Z", $startTime);
        $statParam = array(
            'startTime' => $startTime,
            'endTime' => $endTime,
            'period' => $period,
            'key_type' => 0,
            'key' => array('test-sdk.sys-qa.com'),
            'groupBy' => '',
            'metric' => 'real_hit',
        );

        $resp = $this->client->getDomainStats($statParam);
        $this->assertNotNull($resp);
    }

    /**
     * test get domain stats hit by pv, new version
     */
    public function testGetDomainStatsHitPv() {
        $period = 300;
        $endTime = time();
        $startTime = $endTime - $period * 10;
        $endTime = gmdate("Y-m-d\TH:i:s\Z", $endTime);
        $startTime = gmdate("Y-m-d\TH:i:s\Z", $startTime);
        $statParam = array(
            'startTime' => $startTime,
            'endTime' => $endTime,
            'period' => $period,
            'key_type' => 0,
            'key' => array('test-sdk.sys-qa.com'),
            'groupBy' => '',
            'metric' => 'pv_hit',
        );

        $resp = $this->client->getDomainStats($statParam);
        $this->assertNotNull($resp);
    }

    /**
     * test get domain stats http code, new version
     */
    public function testGetDomainStatsHttpCode() {
        $period = 300;
        $endTime = time();
        $startTime = $endTime - $period * 10;
        $endTime = gmdate("Y-m-d\TH:i:s\Z", $endTime);
        $startTime = gmdate("Y-m-d\TH:i:s\Z", $startTime);
        $statParam = array(
            'startTime' => $startTime,
            'endTime' => $endTime,
            'period' => $period,
            'key_type' => 0,
            'key' => array('test-sdk.sys-qa.com'),
            'groupBy' => '',
            'metric' => 'httpcode',
        );

        $resp = $this->client->getDomainStats($statParam);
        $this->assertNotNull($resp);
    }

    /**
     * test get domain stats http code by region, new version
     */
    public function testGetDomainStatsHttpCodeRegion() {
        $period = 300;
        $endTime = time();
        $startTime = $endTime - $period * 10;
        $endTime = gmdate("Y-m-d\TH:i:s\Z", $endTime);
        $startTime = gmdate("Y-m-d\TH:i:s\Z", $startTime);
        $statParam = array(
            'startTime' => $startTime,
            'endTime' => $startTime,
            'period' => $period,
            'key_type' => 0,
            'key' => array('test-sdk.sys-qa.com'),
            'groupBy' => '',
            'prov' => 'beijing',
            'isp' => 'ct',
            'metric' => 'httpcode_region',
        );

        $resp = $this->client->getDomainStats($statParam);
        $this->assertNotNull($resp);
    }

    /**
     * test get domain stats http code src, new version
     */
    public function testGetDomainStatsHttpCodeSrc() {
        $period = 300;
        $endTime = time();
        $startTime = $endTime - $period * 10;
        $endTime = gmdate("Y-m-d\TH:i:s\Z", $endTime);
        $startTime = gmdate("Y-m-d\TH:i:s\Z", $startTime);
        $statParam = array(
            'startTime' => $startTime,
            'endTime' => $endTime,
            'period' => $period,
            'key_type' => 0,
            'key' => array('test-sdk.sys-qa.com'),
            'groupBy' => '',
            'metric' => 'src_httpcode',
        );

        $resp = $this->client->getDomainStats($statParam);
        $this->assertNotNull($resp);
    }

    /**
     * test get domain stats top urls, new version
     */
    public function testGetDomainStatsTopUrls() {
        $period = 300;
        $endTime = time();
        $startTime = $endTime - $period * 10;
        $endTime = gmdate("Y-m-d\TH:i:s\Z", $endTime);
        $startTime = gmdate("Y-m-d\TH:i:s\Z", $startTime);
        $statParam = array(
            'startTime' => $startTime,
            'endTime' => $endTime,
            'period' => $period,
            'key_type' => 0,
            'key' => array('test-sdk.sys-qa.com'),
            'groupBy' => '',
            'extra' => 200,
            'metric' => 'top_urls',
        );

        $resp = $this->client->getDomainStats($statParam);
        $this->assertNotNull($resp);
    }

    /**
     * test get domain stats top referers, new version
     */
    public function testGetDomainStatsTopReferers() {
        $period = 300;
        $endTime = time();
        $startTime = $endTime - $period * 10;
        $endTime = gmdate("Y-m-d\TH:i:s\Z", $endTime);
        $startTime = gmdate("Y-m-d\TH:i:s\Z", $startTime);
        $statParam = array(
            'startTime' => $startTime,
            'endTime' => $endTime,
            'period' => $period,
            'key_type' => 0,
            'key' => array('test-sdk.sys-qa.com'),
            'groupBy' => '',
            'extra' => 200,
            'metric' => 'top_referers',
        );

        $resp = $this->client->getDomainStats($statParam);
        $this->assertNotNull($resp);
    }

    /**
     * test get domain stats top domains, new version
     */
    public function testGetDomainStatsTopDomains() {
        $period = 300;
        $endTime = time();
        $startTime = $endTime - $period * 10;
        $endTime = gmdate("Y-m-d\TH:i:s\Z", $endTime);
        $startTime = gmdate("Y-m-d\TH:i:s\Z", $startTime);
        $statParam = array(
            'startTime' => $startTime,
            'endTime' => $endTime,
            'period' => $period,
            'groupBy' => '',
            'extra' => 200,
            'metric' => 'top_domains',
        );

        $resp = $this->client->getDomainStats($statParam);
        $this->assertNotNull($resp);
    }

    /**
     * test get domain stats 4xx/5xx error reason, new version
     */
    public function testGetDomainStatsError() {
        $period = 300;
        $endTime = time();
        $startTime = $endTime - $period * 10;
        $endTime = gmdate("Y-m-d\TH:i:s\Z", $endTime);
        $startTime = gmdate("Y-m-d\TH:i:s\Z", $startTime);
        $statParam = array(
            'startTime' => $startTime,
            'endTime' => $endTime,
            'period' => $period,
            'key_type' => 0,
            'key' => array('test-sdk.sys-qa.com'),
            'groupBy' => '',
            'metric' => 'error',
        );

        $resp = $this->client->getDomainStats($statParam);
        $this->assertNotNull($resp);
    }

    /**
     * test prefetch
     */
    public function testPrefetch()
    {
        $this->markTestSkipped(
            'skip'
        );
        $tasks = array(
            array(
                'url' => 'http://test-sdk.sys-qa.com/path/to/file',
            ),
        );

        $resp = $this->client->prefetch($tasks);
        $this->assertNotNull($resp);
        $this->assertNotNull($resp->id);

        $resp = $this->client->listPrefetchStatus($resp->id);
        $this->assertNotNull($resp);
    }

    /**
     * test get prefetch status
     */
    public function testListPrefetchStatus()
    {
        $url = 'http://test-sdk.sys-qa.com/1.jpg';

        $endTime = time();
        $startTime = $endTime - 1000;

        $endTime = gmdate("Y-m-d\TH:i:s\Z", $endTime);
        $startTime = gmdate("Y-m-d\TH:i:s\Z", $startTime);

        $resp = $this->client->listPrefetchStatus('', $url, $startTime, $endTime);
        $this->assertNotNull($resp);
    }

    /**
     * test purge
     */
    public function testPurge()
    {
        $this->markTestSkipped(
            'skip'
        );
        $tasks = array(
            array(
                'url' => 'http://test-sdk.sys-qa.com/path/to/file',
            ),
            array(
                'url' => 'http://test-sdk.sys-qa.com/path/to/directory/',
                'type' => 'directory',
            ),
        );

        $resp = $this->client->purge($tasks);
        $this->assertNotNull($resp);
        $this->assertNotNull($resp->id);

        $resp = $this->client->listPurgeStatus($resp->id);
        $this->assertNotNull($resp);
    }

    /**
     * test purge status
     */
    public function testListPurgeStatus()
    {
        $url = 'http://test-sdk.sys-qa.com/1.jpg';

        $endTime = time();
        $startTime = $endTime - 1000;

        $endTime = gmdate("Y-m-d\TH:i:s\Z", $endTime);
        $startTime = gmdate("Y-m-d\TH:i:s\Z", $startTime);

        $resp = $this->client->listPurgeStatus('', $url, $startTime, $endTime);
        $this->assertNotNull($resp);
    }

    /**
     * test list purge/prefetch quota
     */
    public function testListQuota()
    {
        $resp = $this->client->listQuota();
        $this->assertNotNull($resp);
    }

    /**
     * test get domain log
     */
    public function testGetDomainLog()
    {
        $domain = "test-sdk.sys-qa.com";
        $startTime = "2017-12-07T16:00:00Z";
        $endTime = "2017-12-07T18:00:00Z";

        $resp = $this->client->getDomainLog($domain, $startTime, $endTime);
        $this->assertNotNull($resp);
    }

    /**
     * test get domains log
     */
    public function testGetDomainsLog()
    {
        $domain = "test-sdk.sys-qa.com";
        $startTime = "2017-12-07T16:00:00Z";
        $endTime = "2017-12-07T18:00:00Z";

        $options = array(
            'startTime' => $startTime,
            'endTime' => $endTime,
            'domains' => array(
                $domain
            )
        );

        $resp = $this->client->getDomainsLog($options);
        $this->assertNotNull($resp);
    }

    /**
     * test get domains log
     * @expectedException    Exception
     * @throws \BaiduBce\Exception\BceClientException
     */
    public function testGetDomainsLogThrow() {
        global $g_CDN_TEST_CONFIG;

        $client = new CdnClient($g_CDN_TEST_CONFIG);
        $options = array();
        $client->getDomainsLog($options);
    }

    /**
     * test query ip
     */
    public function testIpQuery()
    {
        $ip = '1.2.3.4';
        $action = 'describeIp';
        $resp = $this->client->ipQuery($action, $ip);
        $this->assertNotNull($resp);
    }

    /**
     * test cache set das
     */
    public function testSetDsa()
    {
        $this->markTestSkipped(
            'skip'
        );

        $action = array(
            "action" => "enable"
        );

        $resp = $this->client->setDsa($action);

        $this->assertEquals($resp, '');
    }

    /**
     * test set domain dsa config
     */
    public function testSetDomainDsa()
    {
        $dsa = array(
            'dsa' => array(
                'enabled' => true,
                'rules' => array(
                    array(
                        'type' => 'suffix',
                        'value' => '.mp4;.jpg;.php'
                    )
                )
            )
        );

        $domain = "test-sdk.sys-qa.com";

        $resp = $this->client->setDomainDsa($domain, $dsa);

        $this->assertNotNull($resp);
    }

    /**
     * test cache set domain dsa config
     * @expectedException    Exception
     * @throws \BaiduBce\Exception\BceClientException
     */
    public function testSetDomainDsaThrow() {
        global $g_CDN_TEST_CONFIG;

        $client = new CdnClient($g_CDN_TEST_CONFIG);
        $domain = "";
        $dsa = array();
        $client->setDomainDsa($domain, $dsa);
    }

    /**
     * test get dsa domain list
     */
    public function testGetDomainDsa()
    {
        $domain = "test-sdk.sys-qa.com";

        $resp = $this->client->getDomainDsa();

        $this->assertNotNull($resp);
    }
}
