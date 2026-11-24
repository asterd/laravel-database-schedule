<?php

namespace RobersonFaria\DatabaseSchedule\Tests\Unit;

use RobersonFaria\DatabaseSchedule\Support\PhpBinary;
use RobersonFaria\DatabaseSchedule\Tests\TestCase;

class PhpBinaryTest extends TestCase
{
    public function test_uses_configured_binary_when_set()
    {
        config(['database-schedule.php_binary' => '/opt/custom/php']);

        $this->assertSame('/opt/custom/php', PhpBinary::resolve());
    }

    public function test_uses_php_binary_under_cli()
    {
        config(['database-schedule.php_binary' => null]);

        $this->assertSame(PHP_BINARY, PhpBinary::resolve());
    }

    public function test_resolved_binary_is_executable()
    {
        config(['database-schedule.php_binary' => null]);

        $this->assertTrue(is_executable(PhpBinary::resolve()));
    }
}
