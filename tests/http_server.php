<?php

namespace PHPrivoxy\Core;

require_once __DIR__ . '/../vendor/autoload.php';

use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use PHPrivoxy\Core\Http\ResponseHandlerInterface;
use Workerman\Connection\TcpConnection;
use Workerman\Psr7\Response;

class Psr7HelloWorld implements RequestHandlerInterface
{
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        return new Response(200, ['content-type' => 'text/html'], 'Hello, world!');
    }
}

class ResponseHandler implements ResponseHandlerInterface
{
    public function handle(ResponseInterface $response, TcpConnection $connection): void
    {
        $connection->send($response);
        $connection->close();
    }
}

$requestHandler = new Psr7HelloWorld();
$responseHandler = new ResponseHandler();
new HttpServer($requestHandler, $responseHandler, 1, 8080, '0.0.0.0');
