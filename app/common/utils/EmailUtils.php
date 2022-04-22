<?php
// +----------------------------------------------------------------------
// | yylAdmin 前后分离，简单轻量，免费开源，开箱即用，极简后台管理系统
// +----------------------------------------------------------------------
// | Copyright https://gitee.com/skyselang All rights reserved
// +----------------------------------------------------------------------
// | Gitee: https://gitee.com/skyselang/yylAdmin
// +----------------------------------------------------------------------

// 邮件 https://gitee.com/skyselang/PHPMailer
namespace app\common\utils;

use app\common\cache\utils\CaptchaEmailCache;
use app\common\service\admin\SettingService;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

class EmailUtils
{
    /**
     * 发送邮件验证码
     *
     * @param string $address 收件人邮箱
     *
     * @return void
     */
    public static function captcha($address)
    {
        $captcha = CaptchaEmailCache::get($address);
        if (empty($captcha)) {
            $captcha = mt_rand(100000, 999999);
            $subject = '邮箱验证码';
            $body = '您的验证码为：<b>' . $captcha . '</b>。';
            self::send($address, $subject, $body);
            CaptchaEmailCache::set($address, $captcha);
        }
    }

    /**
     * 发送邮件
     *
     * @param string $address 收件人
     * @param string $subject 主题
     * @param string $body    内容
     *
     * @return void
     */
    public static function send($address, $subject = '', $body = '')
    {
        $setting = SettingService::info();
        $mail = new PHPMailer(true); // 传递`true`会启用异常
        try {
            $address = explode(',', $address);
            $email_setfrom = $setting['email_setfrom'] ?: ($setting['system_name'] ?: $setting['email_username']);

            // 语言
            $mail->setLanguage('zh_cn', '/optional/path/to/language/directory/');

            // 配置
            $mail->SMTPDebug  = SMTP::DEBUG_OFF;             // 调试模式输出 
            $mail->isSMTP();                                 // 使用 SMTP 
            $mail->SMTPAuth   = true;                        // 允许 SMTP 认证 
            $mail->Host       = $setting['email_host'];      // SMTP 服务器 
            $mail->SMTPSecure = $setting['email_secure'];    // 允许 ssl 或者 tls 协议 
            $mail->Port       = $setting['email_port'];      // 服务器端口 465 或者 25 具体要看邮箱服务器支持 
            $mail->Username   = $setting['email_username'];  // SMTP 用户名 即邮箱地址
            $mail->Password   = $setting['email_password'];  // SMTP 密码 部分邮箱是授权码(例如 QQ 邮箱) 

            // 收件人
            $mail->setFrom($setting['email_username'], $email_setfrom); // 发件人 
            foreach ($address as $val) {
                $mail->addAddress($val); //收件人 可添加多个
            }
            $mail->addReplyTo($setting['email_username'], $email_setfrom); // 回复的时候回复给哪个邮箱 建议和发件人一致 
            // $mail->addCC('cc@example.com'); // 抄送 
            // $mail->addBCC('bcc@example.com'); // 密送 

            // 附件 
            // $mail->addAttachment('../Attachment.zip'); // 添加附件 
            // $mail->addAttachment('../Attachment.jpg', 'AttachmentRename.jpg'); // 发送附件并且重命名 

            // 内容
            $mail->isHTML(true); // 是否以HTML文档格式发送 发送后客户端可直接显示对应 HTML 内容 
            $mail->Subject = $subject; // 主题
            $mail->Body    = $body; // $mail->isHTML(true), HTML 内容 
            $mail->AltBody = $body; // 如果邮件客户端不支持 HTML 则显示此内容

            $mail->send();
        } catch (Exception $e) {
            exception('邮件发送失败: ' . $mail->ErrorInfo);
        }
    }
}
