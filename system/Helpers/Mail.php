<?php

namespace System\Helpers;

use PHPMailer\PHPMailer\PHPMailer;
//use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

/**
 * Description of sendMail
 *
 * @author thiago
 * @param $to
 * @param $subject
 * @param $body
 * @param $reply
 * @param string $name
 * @param array $mailConfig
 * @throws phpmailerException
 */
class Mail {

    private $to, $subject, $message, $from, $name, $mailConfig;

    function __construct($to, $subject, $message, $from, $name = '') {
        $this->to = $to;
        $this->subject = $subject;
        $this->message = $message;
        $this->from = $from;
        $this->name = $name;
        $this->mailConfig = [
            'php_mailer_host' => MAIL_CONFIG['php_mailer_host'],
            'php_mailer_username' => MAIL_CONFIG['php_mailer_username'],
            'php_mailer_password' => MAIL_CONFIG['php_mailer_password']
        ];
    }

    public function sendMail() {
        if (empty($this->mailConfig)) {
            $this->mailConfig = MAIL_CONFIG;
        }
        if (!empty($this->mailConfig['php_mailer_host']) || empty($this->mailConfig['php_mailer_username']) || empty($$this->mailConfig['php_mailer_password'])) {
            $mail = new PHPMailer(true); // true Habilita as Exceptions
            try {
//                $mail->SMTPDebug = SMTP::DEBUG_SERVER;
                $mail->IsSMTP();
                $mail->SMTPAuth = true;
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                $mail->IsHTML(true);
                $mail->CharSet = "UTF-8";

                $mail->Host = $this->mailConfig['php_mailer_host']; //seu servidor SMTP
                $mail->Username = $this->mailConfig['php_mailer_username']; // usuário de SMTP
                $mail->Password = $this->mailConfig['php_mailer_password']; // senha de SMTP
                $mail->Port = 587;

                $mail->setFrom($this->from, $this->name);
                $mail->addAddress($this->to, $this->name);
                $mail->Subject = $this->subject; // Assunto da mensagem
                $mail->Body = $this->message;

                $mail->Send();
                return true;
            } catch (Exception $ex) {
                echo $ex->getMessage();
            }
        } else {
            return true;
        }
    }

}
