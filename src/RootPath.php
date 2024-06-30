<?php

declare(strict_types=1);

namespace PHPrivoxy\Core;

trait RootPath
{
    protected static function getRootPath(): string
    {
        $dir = str_replace('\\', '/', __DIR__);
        $seps = ['/vendor/', '/src/'];
        foreach ($seps as $sep) {
            if (false !== ($pos = mb_strrpos($dir, $sep))) {
                return mb_substr($dir, 0, $pos);
            }
        }

        return dirname(dirname($dir));
    }

    protected static function checkFile(string $file): void
    {
        $dir = dirname($file);
        if (!@is_dir($dir)) {
            @mkdir($dir, 0755, true);
        }
    }
}
