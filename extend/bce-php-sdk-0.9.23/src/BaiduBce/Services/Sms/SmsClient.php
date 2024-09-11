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

namespace BaiduBce\Services\Sms;

use BaiduBce\Auth\BceV1Signer;
use BaiduBce\Auth\SignOptions;
use BaiduBce\BceBaseClient;
use BaiduBce\Exception\BceClientException;
use BaiduBce\Exception\BceServiceException;
use BaiduBce\Http\BceHttpClient;
use BaiduBce\Http\HttpContentTypes;
use BaiduBce\Http\HttpHeaders;
use BaiduBce\Http\HttpMethod;
use BaiduBce\Services\Sms\model\StatisticsResult;

class SmsClient extends BceBaseClient
{
    private $signer;
    private $httpClient;

    /**
     * SmsClient constructor.
     * @param array $config The client configuration
     */
    function __construct(array $config)
    {
        parent::__construct($config, 'SmsClient');
        $this->signer = new BceV1Signer();
        $this->httpClient = new BceHttpClient();
    }

    /**
     * Create HttpClient and send request
     *
     * @param string $httpMethod The Http request method
     * @param array $varArgs The extra arguments
     * @param string $requestPath The Http request uri
     * @throws BceClientException
     * @throws BceServiceException
     * @return mixed
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
        // 自定义签名内容
        $options[SignOptions::HEADERS_TO_SIGN]= array(
            strtolower(HttpHeaders::HOST) => strtolower(HttpHeaders::HOST),
            strtolower(HttpHeaders::BCE_DATE) => strtolower(HttpHeaders::BCE_DATE),
        );

        $path = $requestPath;
        $response = $this->httpClient->sendRequest(
            $config,
            $httpMethod,
            $path,
            $args['body'],
            $args['headers'],
            $args['params'],
            $this->signer,
            null,
            $options
        );
        $result = $this->parseJsonResult($response['body']);
        $result->metadata = $this->convertHttpHeadersToMetadata($response['headers']);
        return $result;
    }

    /**
     * check Template Param
     *
     * @param $name
     * @param $content
     * @param $smsType
     * @param $countryType
     * @throws BceClientException
     * @return void
     */
    private function checkTemplateParam($name, $content, $smsType, $countryType)
    {
        if (empty($name)) {
            throw new BceClientException("name should not be null or empty");
        }

        if (empty($content)) {
            throw new BceClientException("content should not be null or empty");
        }

        if (empty($smsType)) {
            throw new BceClientException("smsType should not be null or empty");
        }

        if (empty($countryType)) {
            throw new BceClientException("countryType should not be null or empty");
        }
    }

    /**
     * check Signature Param
     *
     * @param $content
     * @param $contentType
     * @param $countryType
     * @throws BceClientException
     * @return void
     */
    private function checkSignatureParam($content, $contentType, $countryType)
    {
        if (empty($content)) {
            throw new BceClientException("content should not be null or empty");
        }

        if (empty($contentType)) {
            throw new BceClientException("contentType should not be null or empty");
        }

        if (empty($countryType)) {
            throw new BceClientException("countryType should not be null or empty");
        }
    }

    /**
     * Get clientToken
     *
     * @return string
     */
    private function getClientToken()
    {
        return md5(uniqid(mt_rand(), true));
    }

    /**
     * Send the sms message v3
     *
     * @param String $mobile 手机号码,支持单个或多个手机号，多个手机号之间以英文逗号分隔，e.g. 13800138000,13800138001，一次请求最多支持200个手机号
     * @param String $signatureId 短信签名，需在平台申请，并审核通过后方可使用
     * @param String $templateId 短信模板ID，模板申请成功后自动创建，全局内唯一。e.g. smsTpl:6nHdNumZ4ZtGaKO
     * @param array $contentVar 模板变量内容，用于替换短信模板中定义的变量，为json字符串格式
     *        array('content' => '您的验证码为123456')
     * @param String $custom option: The user self defined param
     * @param String $userExtId option: The user self defined channel code
     * @param String $merchantUrlId option: The id of callback url specified by user
     * @param String $clientToken option: The parameter for idempotence of http post
     * @param array $options
     * @throws BceClientException
     */
    public function sendMessage($mobile, $signatureId, $templateId, $contentVar, $custom = null, $userExtId = null,
                                $merchantUrlId = null, $clientToken = null, $options = array())
    {
        list($config) = $this->parseOptionsIgnoreExtra($options, 'config');
        $requestParam = array();

        if (empty($mobile)) {
            throw new BceClientException("The parameter mobile "
                ."should NOT be null or empty string");
        }
        $requestParam['mobile'] = $mobile;

        if (empty($signatureId)) {
            throw new BceClientException("The parameter signatureId "
                ."should NOT be null or empty string");
        }
        $requestParam['signatureId'] = $signatureId;

        if (empty($templateId)) {
            throw new BceClientException("The parameter templateId "
                ."should NOT be null or empty string");
        }
        $requestParam['template'] = $templateId;

        if (empty($contentVar)) {
            throw new BceClientException("The parameter contentVar "
                ."should NOT be null or empty string");
        }
        $requestParam['contentVar'] = $contentVar;

        if (!empty($custom)) {
            $requestParam['custom'] = $custom;
        }

        if (!empty($userExtId)) {
            $requestParam['userExtId'] = $userExtId;
        }

        if (!empty($merchantUrlId)) {
            $requestParam['merchantUrlId'] = $merchantUrlId;
        }

        $params = array();
        if (!empty($clientToken)) {
            $params['clientToken'] = $clientToken;
        }

        return $this->sendRequest(
            HttpMethod::POST,
            array(
                'config' => $config,
                'body' => json_encode($requestParam),
                'params' => $params
            ),
            '/api/v3/sendsms'
        );
    }

    /**
     * Create message template
     *
     * @param String $name Template's name
     * @param String $content Template's content
     * @param String $smsType The sms type of the template content
     * @param String $countryType The countryType indicates the countries or regions in which the template can be used.
     *        The value of countryType could be DOMESTIC or INTERNATIONAL or GLOBAL.
     * @param String $description Description of the template
     * @throws BceClientException|BceServiceException
     * @return mixed
     */
    public function createTemplate($name, $content, $smsType, $countryType, $description, $options = array())
    {
        $requestParam = array();
        $this->checkTemplateParam($name, $content, $smsType, $countryType);
        if (empty($description)) {
            throw new BceClientException("description should not be null or empty");
        }

        list($config) = $this->parseOptionsIgnoreExtra($options, 'config');
        $requestParam['name'] = $name;
        $requestParam['content'] = $content;
        $requestParam['smsType'] = $smsType;
        $requestParam['countryType'] = $countryType;
        $requestParam['description'] = $description;

        return $this->sendRequest(
            HttpMethod::POST,
            array(
                'config' => $config,
                'body' => json_encode($requestParam),
                'params' => array(
                    'clientToken' => $this->getClientToken()
                )
            ),
            '/sms/v3/template'
        );
    }

    /**
     * Update message template
     *
     * @param String $templateId TemplateId
     * @param String $name Template's name
     * @param String $content Template's content
     * @param String $smsType The sms type of the template content
     * @param String $countryType The countryType indicates the countries or regions in which the template can be used.
     *                     The value of countryType could be DOMESTIC or INTERNATIONAL or GLOBAL.
     * @param String $description option: Description of the template
     * @return mixed
     * @throws BceClientException|BceServiceException
     */
    public function updateTemplate($templateId, $name, $content, $smsType, $countryType, $description = null,
                                   $options = array())
    {
        $requestParam = array();
        $this->checkTemplateParam($name, $content, $smsType, $countryType);

        if (empty($templateId)) {
            throw new BceClientException("templateId should not be null or empty");
        }

        if (!empty($description)) {
            $requestParam['description'] = $description;
        }

        list($config) = $this->parseOptionsIgnoreExtra($options, 'config');
        $requestParam['name'] = $name;
        $requestParam['content'] = $content;
        $requestParam['smsType'] = $smsType;
        $requestParam['countryType'] = $countryType;
        $requestParam['templateId'] = $templateId;

        return $this->sendRequest(
            HttpMethod::PUT,
            array(
                'config' => $config,
                'body' => json_encode($requestParam)
            ),
            '/sms/v3/template/'.$templateId
        );
    }

    /**
     * Delete template
     *
     * @param String $templateId
     * @param array $options
     * @return void
     * @throws BceClientException|BceServiceException
     */
    public function deleteTemplate($templateId, $options = array())
    {
        if (empty($templateId)) {
            throw new BceClientException("templateId should not be null or empty");
        }

        list($config) = $this->parseOptionsIgnoreExtra($options, 'config');

        return $this->sendRequest(
            HttpMethod::DELETE,
            array(
                'config' => $config
            ),
            '/sms/v3/template/'.$templateId
        );
    }

    /**
     * Query template detail
     *
     * @param String $templateId
     * @param array $options
     * @return mixed
     * @throws BceClientException|BceServiceException
     */
    public function getTemplateDetail($templateId, $options = array())
    {
        if (empty($templateId)) {
            throw new BceClientException("templateId should not be null or empty");
        }

        list($config) = $this->parseOptionsIgnoreExtra($options, 'config');

        return $this->sendRequest(
            HttpMethod::GET,
            array(
                'config' => $config
            ),
            '/sms/v3/template/'.$templateId
        );
    }

    /**
     * Create signature
     *
     * @param String $content Text content of the signature. e.g. Baidu
     * @param String $contentType The type of the signature.
     *        The value of contentType could be Enterprise or MobileApp or Web or WeChatPublic or Brand or Else.
     * @param String $countryType The countryType indicates the countries or regions in which the signature can be used
     *        The value of countryType could be DOMESTIC or INTERNATIONAL or GLOBAL.
     * @param String $description Description of the signature
     * @param String $signatureFileBase64 The base64 encoding string of the signature certificate picture.
     * @param String $signatureFileFormat The format of the signature certificate picture.
     *              which can only be one of JPG、PNG、JPEG.
     * @param array $options
     * @return mixed
     * @throws BceClientException
     * @throws BceServiceException
     */
    public function createSignature($content, $contentType, $countryType, $description = null,
                                    $signatureFileBase64 = null, $signatureFileFormat = null, $options = array())
    {
        $requestParam = array();

        $this->checkSignatureParam($content, $contentType, $countryType);

        if (!empty($description)) {
            $requestParam['description'] = $description;
        }

        if (!empty($signatureFileBase64)) {
            $requestParam['signatureFileBase64'] = $signatureFileBase64;
        }

        if (!empty($signatureFileFormat)) {
            $requestParam['signatureFileFormat'] = $signatureFileFormat;
        }

        list($config) = $this->parseOptionsIgnoreExtra($options, 'config');
        $requestParam['content'] = $content;
        $requestParam['contentType'] = $contentType;
        $requestParam['countryType'] = $countryType;

        return $this->sendRequest(
            HttpMethod::POST,
            array(
                'config' => $config,
                'body' => json_encode($requestParam),
                'params' => array(
                    'clientToken' => $this->getClientToken()
                )
            ),
            '/sms/v3/signatureApply'
        );


    }

    /**
     * Update signature
     *
     * @param String $signatureId signatureId
     * @param String $content Text content of the signature. e.g. Baidu
     * @param String $contentType The type of the signature.
     *        The value of contentType could be Enterprise or MobileApp or Web or WeChatPublic or Brand or Else.
     * @param String $countryType The countryType indicates the countries or regions in which the signature can be used
     *        The value of countryType could be DOMESTIC or INTERNATIONAL or GLOBAL.
     * @param String $description Description of the signature
     * @param String $signatureFileBase64 The base64 encoding string of the signature certificate picture.
     * @param String $signatureFileFormat The format of the signature certificate picture.
     *              which can only be one of JPG、PNG、JPEG.
     * @param array $options
     * @return mixed
     * @throws BceClientException
     * @throws BceServiceException
     */
    public function updateSignature($signatureId, $content, $contentType, $countryType, $description = null,
                                    $signatureFileBase64 = null, $signatureFileFormat = null, $options = array())
    {
        $requestParam = array();

        $this->checkSignatureParam($content, $contentType, $countryType);

        if (empty($signatureId)) {
            throw new BceClientException("signatureId should not be null or empty");
        }

        if (!empty($description)) {
            $requestParam['description'] = $description;
        }

        if (!empty($signatureFileBase64)) {
            $requestParam['signatureFileBase64'] = $signatureFileBase64;
        }

        if (!empty($signatureFileFormat)) {
            $requestParam['signatureFileFormat'] = $signatureFileFormat;
        }

        list($config) = $this->parseOptionsIgnoreExtra($options, 'config');
        $requestParam['content'] = $content;
        $requestParam['contentType'] = $contentType;
        $requestParam['countryType'] = $countryType;
        $requestParam['signatureId'] = $signatureId;

        return $this->sendRequest(
            HttpMethod::PUT,
            array(
                'config' => $config,
                'body' => json_encode($requestParam)
            ),
            '/sms/v3/signatureApply/'.$signatureId
        );
    }

    /**
     * Delete Signature
     *
     * @param String $signatureId
     * @param array $options
     * @return mixed
     * @throws BceClientException
     * @throws BceServiceException
     */
    public function deleteSignature($signatureId, $options = array())
    {
        if (empty($signatureId)) {
            throw new BceClientException("signatureId should not be null or empty");
        }

        list($config) = $this->parseOptionsIgnoreExtra($options, 'config');

        return $this->sendRequest(
            HttpMethod::DELETE,
            array(
                'config' => $config
            ),
            '/sms/v3/signatureApply/'.$signatureId
        );
    }

    /**
     * Get Signature Detail
     *
     * @param String $signatureId
     * @param array $options
     * @return mixed
     * @throws BceClientException
     * @throws BceServiceException
     */
    public function getSignatureDetail($signatureId, $options = array())
    {
        if (empty($signatureId)) {
            throw new BceClientException("signatureId should not be null or empty");
        }

        list($config) = $this->parseOptionsIgnoreExtra($options, 'config');

        return $this->sendRequest(
            HttpMethod::GET,
            array(
                'config' => $config
            ),
            '/sms/v3/signatureApply/'.$signatureId
        );
    }

    /**
     * Get QuotaRate
     * @param array $options
     * @return mixed
     * @throws BceClientException
     * @throws BceServiceException
     */
    public function getQuotaRate($options = array())
    {
        list($config) = $this->parseOptionsIgnoreExtra($options, 'config');

        return $this->sendRequest(
            HttpMethod::GET,
            array(
                'config' => $config,
                'params' => array(
                    'userQuery' => ''
                )
            ),
            '/sms/v3/quota'
        );
    }

    /**
     * UpdateQuotaRate
     *
     * @param Integer $quotaPerDay
     * @param Integer $quotaPerMonth
     * @param Integer $rateLimitPerMobilePerSignByMinute
     * @param Integer $rateLimitPerMobilePerSignByHour
     * @param Integer $rateLimitPerMobilePerSignByDay
     * @param array $options
     * @return void
     * @throws BceClientException
     * @throws BceServiceException
     */
    public function updateQuotaRate($quotaPerDay, $quotaPerMonth, $rateLimitPerMobilePerSignByMinute,
                                    $rateLimitPerMobilePerSignByHour, $rateLimitPerMobilePerSignByDay,
                                    $options = array())
    {
        $requestParam = array();

        if (empty($quotaPerDay)) {
            throw new BceClientException("quotaPerDay should not be null or empty");
        }

        if (empty($quotaPerMonth)) {
            throw new BceClientException("quotaPerMonth should not be null or empty");
        }

        if (empty($rateLimitPerMobilePerSignByMinute)) {
            throw new BceClientException("rateLimitPerMobilePerSignByMinute should not be null or empty");
        }

        if (empty($rateLimitPerMobilePerSignByHour)) {
            throw new BceClientException("rateLimitPerMobilePerSignByHour should not be null or empty");
        }

        if (empty($rateLimitPerMobilePerSignByDay)) {
            throw new BceClientException("rateLimitPerMobilePerSignByDay should not be null or empty");
        }

        list($config) = $this->parseOptionsIgnoreExtra($options, 'config');
        $requestParam['quotaPerDay'] = $quotaPerDay;
        $requestParam['quotaPerMonth'] = $quotaPerMonth;
        $requestParam['rateLimitPerMobilePerSignByMinute'] = $rateLimitPerMobilePerSignByMinute;
        $requestParam['rateLimitPerMobilePerSignByHour'] = $rateLimitPerMobilePerSignByHour;
        $requestParam['rateLimitPerMobilePerSignByDay'] = $rateLimitPerMobilePerSignByDay;

        return $this->sendRequest(
            HttpMethod::PUT,
            array(
                'config' => $config,
                'body' => json_encode($requestParam)
            ),
            '/sms/v3/quota'
        );
    }

    /**
     * 增加手机号新名单
     * @param String $countryType The value of type could be DOMESTIC or INTERNATIONAL.
     * @param String $type The value of type could be MerchantBlack or SignatureBlack.
     * @param String $phone Support multiple mobile phone numbers, up to 200 maximum, separated by comma.
     * @param String $smsType When the value of "type" is "SignatureBlack", this field is required.
     * @param String $signatureId When the value of "type" is "SignatureBlack", this field is required.
     * @param array $options
     * @throws BceClientException
     * @throws BceServiceException
     */
    public function createMobileBlack($type, $phone, $countryType, $smsType = null, $signatureIdStr = null,
                                      $options = array())
    {
        $requestParam = array();

        if (empty($type)) {
            throw new BceClientException("type should not be null or empty");
        }
        $requestParam['type'] = $type;

        if (empty($phone)) {
            throw new BceClientException("phone should not be null or empty");
        }
        $requestParam['phone'] = $phone;

        if (empty($countryType)) {
            throw new BceClientException("countryType should not be null or empty");
        }
        $requestParam['countryType'] = $countryType;

        if ("SignatureBlack" == $type) {
            if (empty($smsType)) {
                throw new BceClientException("smsType should not be null or empty, 
                        when 'type' is 'SignatureBlack'.");
            }
            if (empty($signatureIdStr)) {
                throw new BceClientException("signatureIdStr should not be null or empty, 
                        when 'type' is 'SignatureBlack'.");
            }

            $requestParam['signatureIdStr'] = $signatureIdStr;
        }

        if (!empty($smsType)) {
            $requestParam['smsType'] = $smsType;
        }

        list($config) = $this->parseOptionsIgnoreExtra($options, 'config');
        $this->sendRequest(
            HttpMethod::POST,
            array(
                'config' => $config,
                'body' => json_encode($requestParam)
            ),
            '/sms/v3/blacklist'
        );
    }

    /**
     * @param String $phone Support multiple mobile phone numbers, up to 200 maximum, separated by comma.
     * @param String $countryType The value of type could be DOMESTIC or INTERNATIONAL.
     * @param String $smsType Black smsType
     * @param String $signatureId signatureId
     * @param String $startTime The format is 'yyyy-MM-dd'
     * @param String $endTime The format is 'yyyy-MM-dd'
     * @param String $pageNo The current page number
     * @param String $pageSize The current page size, range from 1 to 99999
     * @param array $options
     * @throws BceClientException
     * @throws BceServiceException
     */
    public function getMobileBlackList($phone = null, $countryType = null, $smsType = null, $signatureIdStr = null,
                                       $startTime = null, $endTime = null, $pageNo = null, $pageSize = null,
                                       $options = array())
    {
        $requestParam = array();
        if (!empty($countryType)) {
            $requestParam['countryType'] = $countryType;
        }
        if (!empty($phone)) {
            $requestParam['phone'] = $phone;
        }
        if (!empty($smsType)) {
            $requestParam['smsType'] = $smsType;
        }
        if (!empty($signatureIdStr)) {
            $requestParam['signatureIdStr'] = $signatureIdStr;
        }
        if (!empty($startTime)) {
            $requestParam['startTime'] = $startTime;
        }
        if (!empty($endTime)) {
            $requestParam['endTime'] = $endTime;
        }
        if (!empty($pageNo)) {
            $requestParam['pageNo'] = $pageNo;
        }
        if (!empty($pageSize)) {
            $requestParam['pageSize'] = $pageSize;
        }

        list($config) = $this->parseOptionsIgnoreExtra($options, 'config');
        return $this->sendRequest(
            HttpMethod::GET,
            array(
                'config' => $config,
                'params' => $requestParam
            ),
            '/sms/v3/blacklist'
        );
    }

    /**
     * @param String $phones Support multiple mobile phone numbers, up to 200 maximum, separated by comma.
     * @param array $options
     * @throws BceClientException
     * @throws BceServiceException
     */
    public function deleteMobileBlack($phones, $options = array())
    {
        if (empty($phones)) {
            throw new BceClientException("phones should not be null or empty");
        }
        $requestParam['phones'] = $phones;

        list($config) = $this->parseOptionsIgnoreExtra($options, 'config');
        $this->sendRequest(
            HttpMethod::DELETE,
            array(
                'config' => $config,
                'params' => $requestParam
            ),
            '/sms/v3/blacklist/delete'
        );
    }

    /**
     * @param String $startTime The start of time condition, format: yyyy-MM-dd
     * @param String $endTime The end of time condition, format: yyyy-MM-dd
     * @param String $smsType Queried message type, "all" as default
     * @param String $signatureId Queried signature id
     * @param String $templateCode Queried template code, for instance: "sms-tmpl-xxxxxxxx"
     * @param String $countryType Queried country type, available values: "domestic", "international"
     * @param array  $options
     * @throws BceClientException
     * @throws BceServiceException
     */
    public function listStatistics($startTime, $endTime, $smsType = "all", $countryType = "domestic",
                                   $templateCode = null, $signatureId = null, $options = array())
    {
        $requestParam = array();

        if (empty($startTime)) {
            throw new BceClientException("startTime should not be null or empty!");
        }

        if (empty($endTime)) {
            throw new BceClientException("endTime should not be null or empty!");
        }

        $requestParam['startTime'] = $startTime." 00:00:00";
        $requestParam['endTime'] = $endTime." 23:59:59";
        $requestParam['smsType'] = $smsType;
        $requestParam['dimension'] = "day";
        $requestParam['countryType'] = $countryType;

        if (!empty($signatureId)) {
            $requestParam['signatureId'] = $signatureId;
        }

        if (!empty($templateCode)) {
            $requestParam['templateCode'] = $templateCode;
        }

        list($config) = $this->parseOptionsIgnoreExtra($options, 'config');
        $res = $this->sendRequest(
            HttpMethod::GET,
            array(
                'config' => $config,
                'params' => $requestParam
            ),
            '/sms/v3/summary'
        );

        foreach ($res->statisticsResults as &$result) {
            $result = new StatisticsResult($result);
        }

        return $res;
    }
}