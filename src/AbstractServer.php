<?php

declare(strict_types=1);

namespace PHPrivoxy\Core;

abstract class AbstractServer
{
    use RootPath;

    private string $protocol = 'tcp'; // PHPrivoxy protocol.
    private int $processes = 1;  // Number of workers processes.
    private string $ip = '0.0.0.0'; // PHPrivoxy IP.
    private int $port = 0; // PHPrivoxy port.
    private string $workerName = 'PHPrivoxy'; // Worker name.

    public function __construct(
            ?int $processes = null,
            ?int $port = null,
            ?string $ip = null,
            ?string $protocol = null,
            ?string $workerName = null
    )
    {
        $this->setProcesses($processes);
        $this->setPort($port);
        $this->setIP($ip);
        $this->setProtocol($protocol);
        $this->setWorkerName($workerName);
        $this->run();
    }

    abstract protected function init(ServerWorker $worker): void;

    /*
     * Main process - start Workerman workers.
     */
    public function run(): void
    {
        $worker = new ServerWorker($this->protocol . '://' . $this->ip . ':' . $this->port);
        $worker->count = $this->processes;
        $worker->name = $this->workerName;
        $this->init($worker);
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

    private function setProcesses(?int $processes): void
    {
        if (empty($processes) || 0 >= $processes) {
            return;
        }
        $this->processes = $processes;
    }

    private function setPort(?int $port): void
    {
        if (empty($port) || 0 > $port) {
            return;
        }
        $this->port = $port;
    }

    private function setIP(?string $ip): void
    {
        // TODO: IP correctness checking.
        if (empty($ip)) {
            return;
        }
        $this->ip = $ip;
    }

    private function setProtocol(?string $protocol): void
    {
        // TODO: Protocol correctness checking.
        if (empty($protocol)) {
            return;
        }
        $this->protocol = $protocol;
    }

    private function setWorkerName(?string $name): void
    {
        if (empty($name)) {
            return;
        }
        $this->workerName = $name;
    }
}
