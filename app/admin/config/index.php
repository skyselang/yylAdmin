<?php
/*
 * @Description  : 前台配置
 * @Author       : skyselang 215817969@qq.com
 * @Date         : 2020-04-07
 */

return [
    // token 
    'token' => [
        // 密钥
        'key' => 'IIBAQuQ8AMEFeAOC',
        // 签发者
        'iss' => 'yylAdminVue',
        // 签发时间
        'iat' => time(),
        // 过期时间
        'exp' => time() + 1 * 24 * 60 * 60,
    ]
];
