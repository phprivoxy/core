<?php

declare(strict_types=1);

namespace PHPrivoxy\Core;

use PHPrivoxy\Core\Tcp\TcpHandlerInterface;
use Workerman\Connection\TcpConnection;

class TcpServer extends AbstractServer
{
    private string $protocol = 'tcp';
    private TcpHandlerInterface $tcpHandler;

    public function __construct(
            TcpHandlerInterface $tcpHandler,
            ?int $processes = null,
            ?int $port = null,
            ?string $ip = null,
            ?string $workerName = null
    )
    {
        $this->tcpHandler = $tcpHandler;
        parent::__construct($processes, $port, $ip, $this->protocol, $workerName);
    }

    protected function init(ServerWorker $worker): void
    {
        $worker->onMessage = function (TcpConnection $connection, ?string $buffer) {
            $connectionParameters = $this->getConnectionParameters($buffer);
            $this->tcpHandler->handle($connection, $connectionParameters);
        };
    }

    private function getConnectionParameters(?string $buffer = null): ConnectionParameters
    {
        if (empty($buffer)) {
            throw new CoreException('Empty buffer.');
        }

        if (2 > substr_count($buffer, ' ')) {
            throw new CoreException('Incorrecty buffer content.');
        }

        list($method, $url, $httpVersion) = explode(' ', $buffer);
        $urlData = parse_url($url);

        $host = isset($urlData['host']) ? $urlData['host'] : null;
        $port = !isset($urlData['port']) ? 80 : $urlData['port'];

        return new ConnectionParameters($host, $port, $method, $httpVersion, $buffer);
    }
}
