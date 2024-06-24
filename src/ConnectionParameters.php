<?php

declare(strict_types=1);

namespace PHPrivoxy\Core;

class ConnectionParameters
{
    private ?string $host;
    private ?int $port;
    private ?string $method;
    private ?string $httpVersion;
    private ?string $startBuffer;

    public function __construct(?string $host, ?int $port, ?string $method, ?string $httpVersion, ?string $startBuffer)
    {
        $this->setHost($host);
        $this->setPort($port);
        $this->setMethod($method);
        $this->setHttpVersion($httpVersion);
        $this->setStartBuffer($startBuffer);
    }

    public function getHost(): ?string
    {
        return $this->host;
    }

    public function getPort(): ?int
    {
        return $this->port;
    }

    public function getMethod(): ?string
    {
        return $this->method;
    }

    public function getHttpVersion(): ?string
    {
        return $this->httpVersion;
    }

    public function getStartBuffer(): ?string
    {
        return $this->startBuffer;
    }

    private function setHost(?string $host)
    {
        if (empty($host)) {
            $host = null;
        }
        $this->host = $host;
    }

    private function setPort(?int $port)
    {
        if (!empty($port) && 0 >= $port) {
            $port = null;
        }
        $this->port = $port;
    }

    private function setMethod(?string $method)
    {
        // TODO: validate method.
        $this->method = $method;
    }

    private function setHttpVersion(?string $httpVersion)
    {
        // TODO: validate HTTP version.
        $this->httpVersion = $httpVersion;
    }

    private function setStartBuffer(?string $startBuffer)
    {
        $this->startBuffer = $startBuffer;
    }
}
