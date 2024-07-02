<?php

declare(strict_types=1);

namespace PHPrivoxy\Core;

use Psr\Http\Server\RequestHandlerInterface;
use PHPrivoxy\Core\Http\ResponseHandlerInterface;
use Workerman\Protocols\Http;
use Workerman\Connection\TcpConnection;
use Workerman\Psr7\ServerRequest;
use \Exception;

class HttpServer extends AbstractServer
{
    private string $protocol = 'http';
    private RequestHandlerInterface $requestHandler;
    private ResponseHandlerInterface $responseHandler;

    public function __construct(
            RequestHandlerInterface $requestHandler,
            ResponseHandlerInterface $responseHandler,
            ?int $processes = null,
            ?int $port = null,
            ?string $ip = null,
            ?string $workerName = null
    )
    {
        $this->requestHandler = $requestHandler;
        $this->responseHandler = $responseHandler;
        parent::__construct($processes, $port, $ip, $this->protocol, $workerName);
    }

    protected function init(ServerWorker $worker): void
    {
        Http::requestClass(ServerRequest::class);
        $worker->onMessage = function (TcpConnection $connection, ServerRequest $request) {
            try {
                $response = $this->requestHandler->handle($request);
            } catch (Exception $e) {
                $connection->close();
                return;
            }
            $this->responseHandler->handle($response, $connection);
        };
    }
}
