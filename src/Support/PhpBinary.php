<?php

namespace RobersonFaria\DatabaseSchedule\Support;

use Symfony\Component\Process\PhpExecutableFinder;

/**
 * Resolves the PHP CLI binary used to spawn artisan processes.
 *
 * PHP_BINARY is only reliable under the CLI SAPI: under FrankenPHP it is an
 * empty string, under php-fpm it points to the fpm binary. Using it from a web
 * request produces a command that silently fails in the background.
 */
class PhpBinary
{
    public static function resolve(): string
    {
        if ($configured = config('database-schedule.php_binary')) {
            return $configured;
        }

        if (in_array(PHP_SAPI, ['cli', 'phpdbg'], true) && PHP_BINARY !== '') {
            return PHP_BINARY;
        }

        return (new PhpExecutableFinder())->find(false) ?: 'php';
    }
}
