<?php

namespace RobersonFaria\DatabaseSchedule\Tests\Feature;

use RobersonFaria\DatabaseSchedule\Tests\TestCase;

class ScheduleCacheCommandTest extends TestCase
{
    public function testPackageCacheCommandUsesPackageNamespace()
    {
        $this->artisan('database-schedule:clear-cache')
            ->expectsOutput('Scheduling cache cleared.')
            ->assertExitCode(0);
    }
}
