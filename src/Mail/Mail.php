<?php

namespace Fomo\Mail;

use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\PHPMailer;
use Fomo\Log\Log;

class Mail
{
    protected static self $instance;
    protected PHPMailer $PHPMailer;

    public static function setInstance(): void
    {
        if (!isset(self::$instance)){
            self::$instance = new self();
        }
    }

    public static function getInstance(): Mail
    {
        return self::$instance;
    }

    public function configPHPMailer(): void
    {
        $this->PHPMailer = new PHPMailer(true);
        
        switch (env('MAIL_MAILER' , 'smtp')) {
            case 'smtp':
                $this->PHPMailer->isSMTP();
                if (config('mail.username') != null && config('mail.password') != null)
                    $this->PHPMailer->SMTPAuth = true;
                $this->PHPMailer->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                break;
            case 'mail':
                $this->PHPMailer->isMail();
                break;
            case 'sendmail':
                $this->PHPMailer->isSendmail();
                break;
            case 'qmail':
                $this->PHPMailer->isQmail();
                break;
        }

        $this->PHPMailer->Host = config('mail.host');
        $this->PHPMailer->Username = config('mail.username');
        $this->PHPMailer->Password = config('mail.password');
        $this->PHPMailer->Port = config('mail.port');

        try {
            $this->PHPMailer->setFrom(env('MAIL_FROM_ADDRESS', 'hello@example.com'), env('MAIL_FROM_NAME', 'Example'));
        } catch (Exception $e) {
            Log::channel('mailer')->error($e->getMessage());
        }
    }

    public function to(string|array $address): self
    {
        if (is_string($address)){
            try {
                $this->PHPMailer->addAddress($address);
            } catch (Exception $e) {
                Log::channel('mailer')->error($e->getMessage());
            }
        } else{
            foreach ($address as $value){
                if (is_string($value)){
                    try {
                        $this->PHPMailer->addAddress($value);
                    } catch (Exception $e) {
                        Log::channel('mailer')->error($e->getMessage());
                    }
                }

                if (is_array($value) && isset($value['address']) && isset($value['name'])){
                    try {
                        $this->PHPMailer->addAddress($value['address'], $value['name']);
                    } catch (Exception $e) {
                        Log::channel('mailer')->error($e->getMessage());
                    }
                }
            }
        }

        return $this;
    }

    public function replyTo(string|array $address): self
    {
        if (is_string($address)){
            try {
                $this->PHPMailer->addReplyTo($address);
            } catch (Exception $e) {
                Log::channel('mailer')->error($e->getMessage());
            }
        } else{
            foreach ($address as $value){
                if (is_string($value)){
                    try {
                        $this->PHPMailer->addReplyTo($value);
                    } catch (Exception $e) {
                        Log::channel('mailer')->error($e->getMessage());
                    }
                }

                if (is_array($value) && isset($value['address']) && isset($value['name'])){
                    try {
                        $this->PHPMailer->addReplyTo($value['address'], $value['name']);
                    } catch (Exception $e) {
                        Log::channel('mailer')->error($e->getMessage());
                    }
                }
            }
        }

        return $this;
    }

    public function cc(string|array $address): self
    {
        if (is_string($address)){
            try {
                $this->PHPMailer->addCC($address);
            } catch (Exception $e) {
                Log::channel('mailer')->error($e->getMessage());
            }
        } else{
            foreach ($address as $value){
                if (is_string($value)){
                    try {
                        $this->PHPMailer->addCC($value);
                    } catch (Exception $e) {
                        Log::channel('mailer')->error($e->getMessage());
                    }
                }

                if (is_array($value) && isset($value['address']) && isset($value['name'])){
                    try {
                        $this->PHPMailer->addCC($value['address'], $value['name']);
                    } catch (Exception $e) {
                        Log::channel('mailer')->error($e->getMessage());
                    }
                }
            }
        }

        return $this;
    }

    public function bcc(string|array $address): self
    {
        if (is_string($address)){
            try {
                $this->PHPMailer->addBCC($address);
            } catch (Exception $e) {
                Log::channel('mailer')->error($e->getMessage());
            }
        } else{
            foreach ($address as $value){
                if (is_string($value)){
                    try {
                        $this->PHPMailer->addBCC($value);
                    } catch (Exception $e) {
                        Log::channel('mailer')->error($e->getMessage());
                    }
                }

                if (is_array($value) && isset($value['address']) && isset($value['name'])){
                    try {
                        $this->PHPMailer->addBCC($value['address'], $value['name']);
                    } catch (Exception $e) {
                        Log::channel('mailer')->error($e->getMessage());
                    }
                }
            }
        }

        return $this;
    }

    public function withFile(string|array $file): self
    {
        if (is_string($file)){
            try {
                $this->PHPMailer->addAttachment($file);
            } catch (Exception $e) {
                Log::channel('mailer')->error($e->getMessage());
            }
        } else{
            foreach ($file as $value){
                try {
                    $this->PHPMailer->addAttachment($value);
                } catch (Exception $e) {
                    Log::channel('mailer')->error($e->getMessage());
                }
            }
        }

        return $this;
    }

    public function subject(string $subject): self
    {
        $this->PHPMailer->Subject = $subject;

        return $this;
    }

    public function body(string $body): self
    {
        $this->PHPMailer->Body = $body;

        return $this;
    }

    public function altBody(string $altBody): self
    {
        $this->PHPMailer->AltBody = $altBody;

        return $this;
    }

    public function send(): void
    {
        $this->PHPMailer->isHTML(true);

        try {
            $this->PHPMailer->send();
        } catch (Exception $e) {
            Log::channel('mailer')->error($e->getMessage());
        }
    }
}