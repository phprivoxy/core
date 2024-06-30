<?php

declare(strict_types=1);

namespace PHPrivoxy\Core;

use Workerman\Connection\TcpConnection;

class Server
{
    use RootPath;

    private int $processes;
    private string $ip;
    private int $port;
    private string $workerName;
    private TcpConnectionHandlerInterface $handler;

    public function __construct(
            TcpConnectionHandlerInterface $handler,
            int $processes = 1, // Number of workers processes.
            int $port = 8080, // PHPrivoxy port.
            string $ip = '0.0.0.0', // PHPrivoxy IP.
            string $workerName = 'PHPrivoxy'// Worker name.
    )
    {
        $this->setHandler($handler);
        $this->setProcesses($processes);
        $this->setPort($port);
        $this->setIP($ip);
        $this->setWorkerName($workerName);
        $this->run();
    }

    public function run(): void
    {
        $worker = new ServerWorker('tcp://' . $this->ip . ':' . $this->port);
        $worker->count = $this->processes;
        $worker->name = $this->workerName;

        $worker->onMessage = function (TcpConnection $connection, ?string $buffer) {
            $connectionParameters = $this->getConnectionParameters($buffer);
            $this->handler->handle($connection, $connectionParameters);
        };

        ServerWorker::runAll();
    }

    public static function setLogDirectory(?string $path) // Absolute path.
    {
        ServerWorker::setLogDirectory($path);
    }

    public static function setTmpDirectory(?string $path) // Absolute path.
    {
        ServerWorker::setTmpDirectory($path);
    }

    private function setHandler(TcpConnectionHandlerInterface $handler): void
    {
        $this->handler = $handler;
    }

    private function setProcesses(?int $processes): void
    {
        if (empty($processes) || 0 >= $processes) {
            throw new CoreException('Incorrect worker processes number.');
        }
        $this->processes = $processes;
    }

    private function setPort(?int $port): void
    {
        if (empty($port) || 0 >= $port) {
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
