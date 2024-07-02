<?php

declare(strict_types=1);

namespace PHPrivoxy\Core\Http;

use Psr\Http\Message\ResponseInterface;
use Workerman\Connection\TcpConnection;

/**
 * Handles a Workerman TCP connection.
 */
interface ResponseHandlerInterface
{
    /**
     * Handles a PSR7 ResponseInterface.
     *
     * May call other collaborating code.
     */
    public function handle(ResponseInterface $response, TcpConnection $connection): void;
}
