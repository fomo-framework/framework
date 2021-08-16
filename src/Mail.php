<?php

namespace Tower;

use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\PHPMailer;

class Mail
{
    protected static PHPMailer $instance;

    public static function setInstance()
    {
        $config = include configPath() . "mail.php";

        self::$instance = new PHPMailer(true);

        switch (env('MAIL_MAILER' , 'smtp')) {
            case 'smtp':
                self::$instance->isSMTP();
                if ($config['username'] != null && $config['password'] != null)
                    self::$instance->SMTPAuth = true;
                self::$instance->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                break;
            case 'mail':
                self::$instance->isMail();
                break;
            case 'sendmail':
                self::$instance->isSendmail();
                break;
            case 'qmail':
                self::$instance->isQmail();
                break;
        }

        self::$instance->Host = $config['host'];
        self::$instance->Username = $config['username'];
        self::$instance->Password = $config['password'];
        self::$instance->Port = $config['port'];

        try {
            self::$instance->setFrom(env('MAIL_FROM_ADDRESS', 'hello@example.com'), env('MAIL_FROM_NAME', 'Example'));
        } catch (Exception $e) {
            (new Log())->channel('mailer')->error($e->getMessage());
        }
    }

    public function to(string|array $address): self
    {
        if (is_string($address))
            try {
                self::$instance->addAddress($address);
            } catch (Exception $e) {
                (new Log())->channel('mailer')->error($e->getMessage());
            }
        else
            foreach ($address as $value){
                if (is_string($value))
                    try {
                        self::$instance->addAddress($value);
                    } catch (Exception $e) {
                        (new Log())->channel('mailer')->error($e->getMessage());
                    }

                if (is_array($value) && isset($value['address']) && isset($value['name']))
                    try {
                        self::$instance->addAddress($value['address'], $value['name']);
                    } catch (Exception $e) {
                        (new Log())->channel('mailer')->error($e->getMessage());
                    }
            }

        return $this;
    }

    public function replyTo(string|array $address): self
    {
        if (is_string($address))
            try {
                self::$instance->addReplyTo($address);
            } catch (Exception $e) {
                (new Log())->channel('mailer')->error($e->getMessage());
            }
        else
            foreach ($address as $value){
                if (is_string($value))
                    try {
                        self::$instance->addReplyTo($value);
                    } catch (Exception $e) {
                        (new Log())->channel('mailer')->error($e->getMessage());
                    }

                if (is_array($value) && isset($value['address']) && isset($value['name']))
                    try {
                        self::$instance->addReplyTo($value['address'], $value['name']);
                    } catch (Exception $e) {
                        (new Log())->channel('mailer')->error($e->getMessage());
                    }
            }

        return $this;
    }

    public function cc(string|array $address): self
    {
        if (is_string($address))
            try {
                self::$instance->addCC($address);
            } catch (Exception $e) {
                (new Log())->channel('mailer')->error($e->getMessage());
            }
        else
            foreach ($address as $value){
                if (is_string($value))
                    try {
                        self::$instance->addCC($value);
                    } catch (Exception $e) {
                        (new Log())->channel('mailer')->error($e->getMessage());
                    }

                if (is_array($value) && isset($value['address']) && isset($value['name']))
                    try {
                        self::$instance->addCC($value['address'], $value['name']);
                    } catch (Exception $e) {
                        (new Log())->channel('mailer')->error($e->getMessage());
                    }
            }

        return $this;
    }

    public function bcc(string|array $address): self
    {
        if (is_string($address))
            try {
                self::$instance->addBCC($address);
            } catch (Exception $e) {
                (new Log())->channel('mailer')->error($e->getMessage());
            }
        else
            foreach ($address as $value){
                if (is_string($value))
                    try {
                        self::$instance->addBCC($value);
                    } catch (Exception $e) {
                        (new Log())->channel('mailer')->error($e->getMessage());
                    }

                if (is_array($value) && isset($value['address']) && isset($value['name']))
                    try {
                        self::$instance->addBCC($value['address'], $value['name']);
                    } catch (Exception $e) {
                        (new Log())->channel('mailer')->error($e->getMessage());
                    }
            }

        return $this;
    }

    public function withFile(string|array $file): self
    {
        if (is_string($file))
            try {
                self::$instance->addAttachment($file);
            } catch (Exception $e) {
                (new Log())->channel('mailer')->error($e->getMessage());
            }
        else
            foreach ($file as $value)
                try {
                    self::$instance->addAttachment($value);
                } catch (Exception $e) {
                    (new Log())->channel('mailer')->error($e->getMessage());
                }

        return $this;
    }

    public function subject(string $subject): self
    {
        self::$instance->Subject = $subject;

        return $this;
    }

    public function body(string $body): self
    {
        self::$instance->Body = $body;

        return $this;
    }

    public function altBody(string $altBody): self
    {
        self::$instance->AltBody = $altBody;

        return $this;
    }

    public function send()
    {
        self::$instance->isHTML(true);

        try {
            self::$instance->send();
        } catch (Exception $e) {
            (new Log())->channel('mailer')->error($e->getMessage());
        }
    }
}