<?php

namespace Tower;

use SimpleXMLElement;
use Workerman\Protocols\Http\Response as WorkerResponse;

class Response extends WorkerResponse
{
    public function json(array $data, int $status = 200): self
    {
        $this->_status = $status;
        $this->_body = json_encode($data , JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_IGNORE);
        $this->_header['Content-Type'] = 'application/json';

        return $this;
    }

    public function noContent(): self
    {
        $this->_status = 204;

        return $this;
    }

    public function xml(mixed $xml , int $status = 200): self
    {
        if ($xml instanceof SimpleXMLElement)
            $xml = $xml->asXML();

        $this->_status = $status;
        $this->_body = $xml;
        $this->_header['Content-Type'] = 'text/xml';

        return $this;
    }

    public function jsonp(mixed $data, string $callbackName = 'callback' , int $status = 200): self
    {
        if (!is_scalar($data) && null !== $data)
            $data = json_encode($data);

        $this->_body = "$callbackName($data)";
        $this->_status = $status;
        $this->_header['Content-Type'] = 'application/json';

        return $this;
    }
}
