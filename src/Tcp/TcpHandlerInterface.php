<?php

declare(strict_types=1);

namespace PHPrivoxy\Core\Tcp;

use PHPrivoxy\Core\ConnectionParameters;
use Workerman\Connection\TcpConnection;

/**
 * Handles a Workerman TCP connection.
 */
interface TcpHandlerInterface
{
    /**
     * Handles a Workerman TCP connection.
     *
     * May call other collaborating code.
     */
    public function handle(TcpConnection $connection, ?ConnectionParameters $connectionParameters = null): void;
}
