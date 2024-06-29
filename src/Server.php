<?php

declare(strict_types=1);

namespace PHPrivoxy\Core;

use Workerman\Worker;
use Workerman\Connection\TcpConnection;

class Server
{
    private string $workerName = 'PHPrivoxy'; // Worker name.
    private int $processes; // Number of workers processes.
    private string $ip; // PHPrivoxy IP.
    private int $port; // PHPrivoxy port.
    private TcpConnectionHandlerInterface $handler;

    public function __construct(
            TcpConnectionHandlerInterface $handler,
            int $processes = 1, // Default number of workers processes.
            int $port = 8080, // Default PHPrivoxy port.
            string $ip = '0.0.0.0',
            ?string $name = null
    )
    {
        $this->setHandler($handler);
        $this->setProcesses($processes);
        $this->setPort($port);
        $this->setIP($ip);
        $this->setWorkerName($name);
        $this->run();
    }

    private function run(): void
    {
        $worker = new Worker('tcp://' . $this->ip . ':' . $this->port);
        $worker->count = $this->processes;
        $worker->name = $this->workerName;

        $worker->onMessage = function (TcpConnection $connection, ?string $buffer) {
            $connectionParameters = $this->getConnectionParameters($buffer);
            $this->handler->handle($connection, $connectionParameters);
        };

        Worker::runAll();
    }

    private function setHandler(TcpConnectionHandlerInterface $handler): void
    {
        $this->handler = $handler;
    }

    private function setProcesses(int $processes): void
    {
        if (0 >= $processes) {
            throw new CoreException('Incorrect worker processes number.');
        }
        $this->processes = $processes;
    }

    private function setPort(int $port): void
    {
        if (0 >= $port) {
            throw new CoreException('Incorrect worker port.');
        }
        $this->port = $port;
    }

    private function setIP(?string $ip): void
    {
        // TODO: IP correctness checking.
        if (empty($ip)) {
            throw new CoreException('Incorrect worker host.');
        }
        $this->ip = $ip;
    }

    private function setWorkerName(?string $name): void
    {
        if (!empty($name)) {
            $this->workerName = $name;
        }
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
