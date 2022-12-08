<?php

namespace Fomo\Facades;

/**
 * @method static \Fomo\Mail\Mail to(string|array $address)
 * @method static \Fomo\Mail\Mail replyTo(string|array $address)
 * @method static \Fomo\Mail\Mail cc(string|array $address)
 * @method static \Fomo\Mail\Mail bcc(string|array $address)
 * @method static \Fomo\Mail\Mail withFile(string|array $file)
 * @method static \Fomo\Mail\Mail subject(string $subject)
 * @method static \Fomo\Mail\Mail body(string $body)
 * @method static \Fomo\Mail\Mail altBody(string $altBody)
 * @method static void send()
 */
class Mail extends Facade
{
    protected static function getMainClass(): string
    {
        return 'mail';
    }
}