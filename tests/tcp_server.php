<?php

namespace PHPrivoxy\Core;

require_once __DIR__ . '/../vendor/autoload.php';

use PHPrivoxy\Core\Tcp\TcpHandlerInterface;
use Workerman\Connection\TcpConnection;

class HelloWorld implements TcpHandlerInterface
{
    public function handle(TcpConnection $connection, ?ConnectionParameters $connectionParameters = null): void
    {
        $connection->send("HTTP/1.1 200 OK\r\ncontent-type: text/html;charset=UTF8\r\n\r\n" . 'Hello, world!');
        $connection->close();
    }
}

$handler = new HelloWorld();
new TcpServer($handler, 1, 8080, '0.0.0.0');
