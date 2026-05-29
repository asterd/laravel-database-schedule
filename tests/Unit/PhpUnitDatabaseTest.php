<?php

namespace RobersonFaria\DatabaseSchedule\Tests\Unit;

use RobersonFaria\DatabaseSchedule\Models\Schedule;
use RobersonFaria\DatabaseSchedule\Tests\TestCase;

class PhpUnitDatabaseTest extends TestCase
{
    public function testDatabaseAndFactoryWorks()
    {
        Schedule::query()->create(['command' => 'first']);
        Schedule::query()->create(['command' => 'second']);

        $this->assertEquals(2, Schedule::all()->count());
    }
}
