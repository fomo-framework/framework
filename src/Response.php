<?php

namespace Tower;

use Workerman\Protocols\Http\Response as WorkerResponse;

class Response extends WorkerResponse
{
    public const HTTP_CONTINUE = 100;
    public const HTTP_SWITCHING_PROTOCOLS = 101;
    public const HTTP_PROCESSING = 102;            // RFC2518
    public const HTTP_EARLY_HINTS = 103;           // RFC8297
    public const HTTP_OK = 200;
    public const HTTP_CREATED = 201;
    public const HTTP_ACCEPTED = 202;
    public const HTTP_NON_AUTHORITATIVE_INFORMATION = 203;
    public const HTTP_NO_CONTENT = 204;
    public const HTTP_RESET_CONTENT = 205;
    public const HTTP_PARTIAL_CONTENT = 206;
    public const HTTP_MULTI_STATUS = 207;          // RFC4918
    public const HTTP_ALREADY_REPORTED = 208;      // RFC5842
    public const HTTP_IM_USED = 226;               // RFC3229
    public const HTTP_MULTIPLE_CHOICES = 300;
    public const HTTP_MOVED_PERMANENTLY = 301;
    public const HTTP_FOUND = 302;
    public const HTTP_SEE_OTHER = 303;
    public const HTTP_NOT_MODIFIED = 304;
    public const HTTP_USE_PROXY = 305;
    public const HTTP_RESERVED = 306;
    public const HTTP_TEMPORARY_REDIRECT = 307;
    public const HTTP_PERMANENTLY_REDIRECT = 308;  // RFC7238
    public const HTTP_BAD_REQUEST = 400;
    public const HTTP_UNAUTHORIZED = 401;
    public const HTTP_PAYMENT_REQUIRED = 402;
    public const HTTP_FORBIDDEN = 403;
    public const HTTP_NOT_FOUND = 404;
    public const HTTP_METHOD_NOT_ALLOWED = 405;
    public const HTTP_NOT_ACCEPTABLE = 406;
    public const HTTP_PROXY_AUTHENTICATION_REQUIRED = 407;
    public const HTTP_REQUEST_TIMEOUT = 408;
    public const HTTP_CONFLICT = 409;
    public const HTTP_GONE = 410;
    public const HTTP_LENGTH_REQUIRED = 411;
    public const HTTP_PRECONDITION_FAILED = 412;
    public const HTTP_REQUEST_ENTITY_TOO_LARGE = 413;
    public const HTTP_REQUEST_URI_TOO_LONG = 414;
    public const HTTP_UNSUPPORTED_MEDIA_TYPE = 415;
    public const HTTP_REQUESTED_RANGE_NOT_SATISFIABLE = 416;
    public const HTTP_EXPECTATION_FAILED = 417;
    public const HTTP_I_AM_A_TEAPOT = 418;                                               // RFC2324
    public const HTTP_MISDIRECTED_REQUEST = 421;                                         // RFC7540
    public const HTTP_UNPROCESSABLE_ENTITY = 422;                                        // RFC4918
    public const HTTP_LOCKED = 423;                                                      // RFC4918
    public const HTTP_FAILED_DEPENDENCY = 424;                                           // RFC4918
    public const HTTP_TOO_EARLY = 425;                                                   // RFC-ietf-httpbis-replay-04
    public const HTTP_UPGRADE_REQUIRED = 426;                                            // RFC2817
    public const HTTP_PRECONDITION_REQUIRED = 428;                                       // RFC6585
    public const HTTP_TOO_MANY_REQUESTS = 429;                                           // RFC6585
    public const HTTP_REQUEST_HEADER_FIELDS_TOO_LARGE = 431;                             // RFC6585
    public const HTTP_UNAVAILABLE_FOR_LEGAL_REASONS = 451;
    public const HTTP_INTERNAL_SERVER_ERROR = 500;
    public const HTTP_NOT_IMPLEMENTED = 501;
    public const HTTP_BAD_GATEWAY = 502;
    public const HTTP_SERVICE_UNAVAILABLE = 503;
    public const HTTP_GATEWAY_TIMEOUT = 504;
    public const HTTP_VERSION_NOT_SUPPORTED = 505;
    public const HTTP_VARIANT_ALSO_NEGOTIATES_EXPERIMENTAL = 506;                        // RFC2295
    public const HTTP_INSUFFICIENT_STORAGE = 507;                                        // RFC4918
    public const HTTP_LOOP_DETECTED = 508;                                               // RFC5842
    public const HTTP_NOT_EXTENDED = 510;                                                // RFC2774
    public const HTTP_NETWORK_AUTHENTICATION_REQUIRED = 511;


    protected function createHeadForFile($file_info)
    {
        $file = $file_info['file'];
        $reason = $this->_reason ? $this->_reason : static::$_phrases[$this->_status];
        $head = "HTTP/{$this->_version} {$this->_status} $reason\r\n";
        $headers = $this->_header;
        if (!isset($headers['Server'])) {
            $head .= "Server: tower\r\n";
        }
        foreach ($headers as $name => $value) {
            if (\is_array($value)) {
                foreach ($value as $item) {
                    $head .= "$name: $item\r\n";
                }
                continue;
            }
            $head .= "$name: $value\r\n";
        }

        if (!isset($headers['Connection'])) {
            $head .= "Connection: keep-alive\r\n";
        }

        $file_info = \pathinfo($file);
        $extension = isset($file_info['extension']) ? $file_info['extension'] : '';
        $base_name = isset($file_info['basename']) ? $file_info['basename'] : 'unknown';
        if (!isset($headers['Content-Type'])) {
            if (isset(self::$_mimeTypeMap[$extension])) {
                $head .= "Content-Type: " . self::$_mimeTypeMap[$extension] . "\r\n";
            } else {
                $head .= "Content-Type: application/octet-stream\r\n";
            }
        }

        if (!isset($headers['Content-Disposition']) && !isset(self::$_mimeTypeMap[$extension])) {
            $head .= "Content-Disposition: attachment; filename=\"$base_name\"\r\n";
        }

        if (!isset($headers['Last-Modified'])) {
            if ($mtime = \filemtime($file)) {
                $head .= 'Last-Modified: '.\date('D, d M Y H:i:s', $mtime) . ' ' . \date_default_timezone_get() ."\r\n";
            }
        }

        return "{$head}\r\n";
    }


    public function __toString()
    {
        if (isset($this->file)) {
            return $this->createHeadForFile($this->file);
        }

        $reason = $this->_reason ? $this->_reason : static::$_phrases[$this->_status];
        $body_len = \strlen($this->_body);
        if (empty($this->_header)) {
            return "HTTP/{$this->_version} {$this->_status} $reason\r\nServer: tower\r\nContent-Type: text/html;charset=utf-8\r\nContent-Length: $body_len\r\nConnection: keep-alive\r\n\r\n{$this->_body}";
        }

        $head = "HTTP/{$this->_version} {$this->_status} $reason\r\n";
        $headers = $this->_header;
        if (!isset($headers['Server'])) {
            $head .= "Server: tower\r\n";
        }
        foreach ($headers as $name => $value) {
            if (\is_array($value)) {
                foreach ($value as $item) {
                    $head .= "$name: $item\r\n";
                }
                continue;
            }
            $head .= "$name: $value\r\n";
        }

        if (!isset($headers['Connection'])) {
            $head .= "Connection: keep-alive\r\n";
        }

        if (!isset($headers['Content-Type'])) {
            $head .= "Content-Type: text/html;charset=utf-8\r\n";
        } else if ($headers['Content-Type'] === 'text/event-stream') {
            return $head . $this->_body;
        }

        if (!isset($headers['Transfer-Encoding'])) {
            $head .= "Content-Length: $body_len\r\n\r\n";
        } else {
            return "$head\r\n".dechex($body_len)."\r\n{$this->_body}\r\n";
        }

        // The whole http package
        return $head . $this->_body;
    }

}
