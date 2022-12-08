<?php

namespace Fomo\Mail;

use Fomo\Facades\Log;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\PHPMailer;

class Mail extends PHPMailer
{
    public function __construct($exceptions = true)
    {
        parent::__construct($exceptions);
    }

    public function to(string|array $address): self
    {
        if (is_string($address)){
            try {
                $this->addAddress($address);
            } catch (Exception $e) {
                Log::channel('mailer')->error($e->getMessage());
            }
        } else{
            foreach ($address as $value){
                if (is_string($value)){
                    try {
                        $this->addAddress($value);
                    } catch (Exception $e) {
                        Log::channel('mailer')->error($e->getMessage());
                    }
                }

                if (is_array($value) && isset($value['address']) && isset($value['name'])){
                    try {
                        $this->addAddress($value['address'], $value['name']);
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
                $this->addReplyTo($address);
            } catch (Exception $e) {
                Log::channel('mailer')->error($e->getMessage());
            }
        } else{
            foreach ($address as $value){
                if (is_string($value)){
                    try {
                        $this->addReplyTo($value);
                    } catch (Exception $e) {
                        Log::channel('mailer')->error($e->getMessage());
                    }
                }

                if (is_array($value) && isset($value['address']) && isset($value['name'])){
                    try {
                        $this->addReplyTo($value['address'], $value['name']);
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
                $this->addCC($address);
            } catch (Exception $e) {
                Log::channel('mailer')->error($e->getMessage());
            }
        } else{
            foreach ($address as $value){
                if (is_string($value)){
                    try {
                        $this->addCC($value);
                    } catch (Exception $e) {
                        Log::channel('mailer')->error($e->getMessage());
                    }
                }

                if (is_array($value) && isset($value['address']) && isset($value['name'])){
                    try {
                        $this->addCC($value['address'], $value['name']);
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
                $this->addBCC($address);
            } catch (Exception $e) {
                Log::channel('mailer')->error($e->getMessage());
            }
        } else{
            foreach ($address as $value){
                if (is_string($value)){
                    try {
                        $this->addBCC($value);
                    } catch (Exception $e) {
                        Log::channel('mailer')->error($e->getMessage());
                    }
                }

                if (is_array($value) && isset($value['address']) && isset($value['name'])){
                    try {
                        $this->addBCC($value['address'], $value['name']);
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
                $this->addAttachment($file);
            } catch (Exception $e) {
                Log::channel('mailer')->error($e->getMessage());
            }
        } else{
            foreach ($file as $value){
                try {
                    $this->addAttachment($value);
                } catch (Exception $e) {
                    Log::channel('mailer')->error($e->getMessage());
                }
            }
        }

        return $this;
    }

    public function subject(string $subject): self
    {
        $this->Subject = $subject;

        return $this;
    }

    public function body(string $body): self
    {
        $this->Body = $body;

        return $this;
    }

    public function altBody(string $altBody): self
    {
        $this->AltBody = $altBody;

        return $this;
    }

    public function send(): void
    {
        $this->isHTML(true);

        try {
            $this->send();
        } catch (Exception $e) {
            Log::channel('mailer')->error($e->getMessage());
        }
    }
}