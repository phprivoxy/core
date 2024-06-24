<?php

declare(strict_types=1);

namespace PHPrivoxy\Core;

use Workerman\Connection\TcpConnection;

class PHPrivoxyCore
{
    private TcpConnectionHandlerInterface $handler;
    private ConnectionParameters $connectionParameters;

    public function __construct(TcpConnectionHandlerInterface $handler, TcpConnection $connection, ?string $buffer)
    {
        $this->handler = $handler;
        $this->connection = $connection;
        $this->init($buffer);
        $handler->handle($connection, $this->connectionParameters);
    }

    private function init(?string $buffer = null): void
    {
        if (empty($buffer)) {
            throw new PHPrivoxyCoreException('Empty buffer.');
        }

        if (2 > substr_count($buffer, ' ')) {
            throw new PHPrivoxyCoreException('Incorrecty buffer content.');
        }

        list($method, $url, $httpVersion) = explode(' ', $buffer);
        $urlData = parse_url($url);

        $host = isset($urlData['host']) ? $urlData['host'] : null;
        $port = !isset($urlData['port']) ? 80 : $urlData['port'];

        $this->connectionParameters = new ConnectionParameters($host, $port, $method, $httpVersion, $buffer);
    }
}
