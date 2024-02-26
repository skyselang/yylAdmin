<?php

include 'BaiduBce.phar';
require 'SampleConf.php';

use BaiduBce\Services\Sms\SmsClient;

global $SMS_TEST_CONFIG;
$client = new SmsClient($SMS_TEST_CONFIG);

// 手机号,支持单个或多个手机号，多个手机号之间以英文逗号分隔，e.g. 13800138000,13800138001，一次请求最多支持200个手机号
$mobile = '17615151711';
// 模板编码
$template = 'sms-tmpl-VlYLOm62611';
// 签名
$signatureId = 'sms-sign-yxveGQ86111';
// 模板内容
$contentVar = array(
    'content' => '验证码有效期5min'
);

/**
 *
 * 响应数据格式
 *
 *{
 *    "requestId":"ac17060505d4453ca1c9a7c5be82042b",
 *    "code":"1000",
 *    "message":"成功",
 *    "data":[
 *        {
 *            "code":"1000",
 *            "message":"成功",
 *            "mobile":"13800138000",
 *            "messageId":"6edf7cffa7434d2e8335a0d021ee2b2f"
 *        }
 *    ]
 *}
 */
$response = $client->sendMessage($mobile, $signatureId, $template, $contentVar);
print_r($response);