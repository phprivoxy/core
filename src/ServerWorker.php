<?php

namespace PHPrivoxy\Core;

use Workerman\Worker;
use Workerman\Timer;

class ServerWorker extends Worker
{
    use RootPath;

    private static string $logSubdirName = 'var/log';
    private static string $logFileName = 'phprivoxy.log';
    private static string $tmpSubdirName = 'var/tmp';
    private static ?string $logDirectory = null; // Absolute path to log files directory.
    private static ?string $tmpDirectory = null; // Absolute path to temporary files directory.

    public static function setLogDirectory(?string $path)
    {
        self::$logDirectory = $path;
    }

    public static function setTmpDirectory(?string $path)
    {
        self::$tmpDirectory = $path;
    }

    /**
     * Init.
     *
     * @return void
     */
    protected static function init()
    {
        $rootPath = self::getRootPath();
        if (empty(self::$logDirectory)) {
            self::$logDirectory = $rootPath . '/' . self::$logSubdirName;
        }
        if (empty(self::$tmpDirectory)) {
            self::$tmpDirectory = $rootPath . '/' . self::$tmpSubdirName;
        }

        \set_error_handler(function ($code, $msg, $file, $line) {
            Worker::safeEcho("$msg in file $file on line $line\n");
        });

        // Start file.
        $backtrace = \debug_backtrace();
        static::$_startFile = $backtrace[\count($backtrace) - 1]['file'];

        $unique_prefix = \str_replace('/', '_', static::$_startFile);

        // Pid file.
        if (empty(static::$pidFile)) {
            static::$pidFile = self::$tmpDirectory . '/' . $unique_prefix . '.pid';
        }
        self::checkFile(static::$pidFile);

        // Log file.
        if (empty(static::$logFile)) {
            static::$logFile = self::$logDirectory . '/' . self::$logFileName;
        }
        $log_file = (string) static::$logFile;
        self::checkFile(static::$logFile);

        if (!\is_file($log_file)) {
            \touch($log_file);
            \chmod($log_file, 0622);
        }

        // State.
        static::$_status = static::STATUS_STARTING;

        // For statistics.
        static::$_globalStatistics['start_timestamp'] = \time();

        // Process title.
        static::setProcessTitle(static::$processTitle . ': master process  start_file=' . static::$_startFile);

        // Init data for worker id.
        static::initId();

        // Timer init.
        Timer::init();
    }

    /**
     * Init All worker instances.
     *
     * @return void
     */
    protected static function initWorkers()
    {
        if (static::$_OS !== \OS_TYPE_LINUX) {
            return;
        }

        $rootPath = self::getRootPath();

        static::$_statisticsFile = static::$statusFile ? static::$statusFile : $rootPath . self::$tmpSubdirName . 'privoxy-' . posix_getpid() . '.status';

        foreach (static::$_workers as $worker) {
            // Worker name.
            if (empty($worker->name)) {
                $worker->name = 'none';
            }

            // Get unix user of the worker process.
            if (empty($worker->user)) {
                $worker->user = static::getCurrentUser();
            } else {
                if (\posix_getuid() !== 0 && $worker->user !== static::getCurrentUser()) {
                    static::log('Warning: You must have the root privileges to change uid and gid.');
                }
            }

            // Socket name.
            $worker->socket = $worker->getSocketName();

            // Status name.
            $worker->status = '<g> [OK] </g>';

            // Get column mapping for UI
            foreach (static::getUiColumns() as $column_name => $prop) {
                !isset($worker->{$prop}) && $worker->{$prop} = 'NNNN';
                $prop_length = \strlen((string) $worker->{$prop});
                $key = '_max' . \ucfirst(\strtolower($column_name)) . 'NameLength';
                static::$$key = \max(static::$$key, $prop_length);
            }

            // Listen.
            if (!$worker->reusePort) {
                $worker->listen();
            }
        }
    }
}
