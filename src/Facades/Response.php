<?php

namespace Fomo\Facades;

/**
 * @method static \Fomo\Response\Response withHeader(string $name , string $value)
 * @method static \Fomo\Response\Response withHeaders(array $headers)
 * @method static \Fomo\Response\Response withStatus(int $status)
 * @method static \Fomo\Response\Response withBody(string $body)
 * @method static \Fomo\Response\Response withoutHeader(string $name)
 * @method static \Fomo\Response\Response withoutHeaders(array $headers)
 * @method static \Fomo\Response\Response noContent()
 * @method static \Fomo\Response\Response html(string $data , int $status = 200)
 * @method static \Fomo\Response\Response plainText(string $data , int $status = 200)
 * @method static \Fomo\Response\Response json(array $data , int $status = 200)
 * @method static string|float|int|array|bool|null getHeader(string $name)
 * @method static array getHeaders()
 * @method static int getStatus()
 * @method static string|null getPhrase(int $status)
 * @method static string rawBody()
 */
class Response extends Facade
{
    protected static function getMainClass(): string
    {
        return 'response';
    }
}