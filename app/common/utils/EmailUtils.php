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
            $setting = SettingService::info();
            $captcha = mt_rand(100000, 999999);
            $subject = $setting['system_name'] . '-验证码';
            $body = '您的验证码为：<b>' . $captcha . '</b>。';
            self::send($address, $subject, $body);
            CaptchaEmailCache::set($address, $captcha);
        }
    }

    /**
     * 发送邮件
     *
     * @param string $address 收件人
     * @param string $subject 标题
     * @param string $body    内容
     *
     * @return void
     */
    public static function send($address, $subject = '', $body = '')
    {
        $setting = SettingService::info();
        $mail = new PHPMailer(true); // 传递`true`会启用异常
        try {
            $mail->setLanguage('zh_cn', '/optional/path/to/language/directory/');
            // 配置
            $mail->SMTPDebug  = SMTP::DEBUG_OFF;             // 调试模式输出 
            $mail->isSMTP();                                 // 使用SMTP 
            $mail->Host       = $setting['email_host'];      // SMTP服务器 
            $mail->SMTPAuth   = true;                        // 允许 SMTP 认证 
            $mail->Username   = $setting['email_username'];  // SMTP 用户名  即邮箱的用户名 
            $mail->Password   = $setting['email_password'];  // SMTP 密码  部分邮箱是授权码(例如163邮箱) 
            $mail->SMTPSecure = $setting['email_secure'];    // 允许 tls 或者ssl协议 
            $mail->Port       = $setting['email_port'];      // 服务器端口 25 或者465 具体要看邮箱服务器支持 

            // 收件人
            $mail->setFrom($setting['email_setfrom'], $setting['email_setfrom']); // 发件人 
            $mail->addAddress($address, $address); // 收件人 
            // $mail->addAddress('address@example.com'); // 可添加多个收件人 
            $mail->addReplyTo($setting['email_setfrom'], $setting['email_setfrom']); // 回复的时候回复给哪个邮箱 建议和发件人一致 
            // $mail->addCC('cc@example.com'); // 抄送 
            // $mail->addBCC('bcc@example.com'); // 密送 

            // 附件 
            // $mail->addAttachment('../Attachment.zip'); // 添加附件 
            // $mail->addAttachment('../Attachment.jpg', 'AttachmentRename.jpg'); // 发送附件并且重命名 

            // 内容
            $mail->isHTML(true); // 是否以HTML文档格式发送  发送后客户端可直接显示对应HTML内容 
            $mail->Subject = $subject;
            $mail->Body    = $body;
            // $mail->AltBody = '如果邮件客户端不支持HTML则显示此内容';

            $mail->send();
        } catch (Exception $e) {
            exception('邮件发送失败: ' . $mail->ErrorInfo);
        }
    }
}
