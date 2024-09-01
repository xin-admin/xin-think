<?php
/*
 *  +----------------------------------------------------------------------
 *  | XinAdmin [ A Full stack framework ]
 *  +----------------------------------------------------------------------
 *  | Copyright (c) 2023~2024 http://xinadmin.cn All rights reserved.
 *  +----------------------------------------------------------------------
 *  | Apache License ( http://www.apache.org/licenses/LICENSE-2.0 )
 *  +----------------------------------------------------------------------
 *  | Author: 小刘同学 <2302563948@qq.com>
 *  +----------------------------------------------------------------------
 */

namespace app\common\library\sms\driver;

use app\common\library\sms\Driver;
use app\common\model\VerificationCodeModel;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\PHPMailer;

class Mail extends Driver
{
    public function sendCode($sendNo): bool|string
    {
        try {
            $code = rand(1000, 9999);
            $mail = self::getService();
            $mail->setFrom(get_setting('mail.username'));  // 发件人
            $mail->addAddress($sendNo);  // 收件人
            $mail->isHTML(false);
            $mail->Subject = '【XinAdmin】邮箱验证码';
            $mail->Body = "你的邮箱验证码是\" {$code} \"，请在10分钟内使用，请勿泄露与他人！";
            $mail->AltBody = "你的邮箱验证码是{$code}，请在10分钟内使用，请勿泄露与他人！";
            $mail->send();
            $model = new VerificationCodeModel();
            $model->save([
                'type' => 'mail',
                'code' => $code,
                'status' => 1,
                'interval' => 300,
                'data' => $sendNo,
            ]);
            return true;
        } catch (Exception|\Exception $exception) {
            return $exception->getMessage();
        }
    }

    public function verify($sendNo, $code): bool|string
    {
        $model = new VerificationCodeModel();
        // 获取最新的一条验证码数据
        $verCode = $model->where('data', $sendNo)->where('type', 'mail')->order('id', 'desc')->findOrEmpty();
        if ($verCode->isEmpty()) {
            return '未查询到验证码';
        }
        if ($verCode['status'] != 1) {
            return '验证码错误';
        }
        if ($verCode['code'] != $code) {
            return '验证码错误';
        }
        if ($verCode['interval'] + $verCode->getData('create_time') < time()) {
            return '验证码已过期';
        }
        return true;
    }

    /**
     * 获取服务
     */
    protected function getService(): PHPMailer
    {
        $mail = new PHPMailer(true);
        $mail->CharSet = get_setting('mail.char');                     // 设定邮件编码
        $mail->SMTPDebug = 0;                        // 调试模式输出
        $mail->isSMTP();                             // 使用SMTP
        $mail->Host = get_setting('mail.smtp');                // SMTP服务器
        $mail->SMTPAuth = true;                      // 允许 SMTP 认证
        $mail->Username = get_setting('mail.username');                // SMTP 用户名  即邮箱的用户名
        $mail->Password = get_setting('mail.password');             // SMTP 密码  部分邮箱是授权码(例如163邮箱)
        $mail->SMTPSecure = get_setting('mail.SMTPSecure');                    // 允许 TLS 或者ssl协议
        $mail->Port = get_setting('mail.Port');                            // 服务器端口 25 或者465 具体要看邮箱服务器支持
        return $mail;
    }
}
