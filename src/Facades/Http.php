<?php

namespace Fomo\Facades;

/**
 * @method static \Fomo\Http\Http withHeaders(array $headers)
 * @method static \Fomo\Http\Http withOptions(array $options)
 * @method static \Fomo\Http\Http attach(array|string $name, string $contents = '', string $filename = null, array $headers = [])
 * @method static \Fomo\Http\Http withToken(string $token , string $type = 'Bearer')
 * @method static \Fomo\Http\Http withBasicAuth(string $username, string $password)
 * @method static \Fomo\Http\Http withDigestAuth(string $username, string $password)
 * @method static \Fomo\Http\Http withUserAgent(string $userAgent)
 * @method static \Fomo\Http\Http withCookies(array $cookies, string $domain)
 * @method static \Fomo\Http\Http withoutRedirecting()
 * @method static \Fomo\Http\Http withoutVerifying()
 * @method static \Fomo\Http\Http timeout(int $seconds)
 * @method static \Fomo\Http\Http asBody(string $contentType)
 * @method static \Fomo\Http\Http asJson()
 * @method static \Fomo\Http\Http asForm()
 * @method static \Fomo\Http\Http asMultipart()
 * @method static \Fomo\Http\Http bodyFormat(string $format)
 * @method static \Fomo\Http\Http contentType(string $contentType)
 * @method static \Fomo\Http\Http accept(string $contentType = 'application/json')
 * @method static \Fomo\Http\Response head(string $url , array $query = [])
 * @method static \Fomo\Http\Response get(string $url , array $query = [])
 * @method static \Fomo\Http\Response post(string $url , array $body = [] , string $contentType = 'application/json')
 * @method static \Fomo\Http\Response patch(string $url , array $body = [] , string $contentType = 'application/json')
 * @method static \Fomo\Http\Response put(string $url , array $body = [] , string $contentType = 'application/json')
 * @method static \Fomo\Http\Response delete(string $url , array $body = [] , string $contentType = 'application/json')
 */
class Http extends Facade
{
    protected static function getMainClass(): string
    {
        return 'http';
    }
}