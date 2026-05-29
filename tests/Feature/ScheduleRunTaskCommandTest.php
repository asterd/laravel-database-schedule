<?php

namespace RobersonFaria\DatabaseSchedule\Tests\Feature;

use RobersonFaria\DatabaseSchedule\Models\Schedule;
use RobersonFaria\DatabaseSchedule\Tests\TestCase;

class ScheduleRunTaskCommandTest extends TestCase
{
    public function testRunTaskCommandStreamsOutputIntoHistoryAndLogFile()
    {
        $task = Schedule::query()->create([
            'command' => 'custom',
            'command_custom' => PHP_BINARY . ' -r "echo \'Hello the test worked.\' . PHP_EOL;"',
            'log_filename' => 'manual-run-test',
        ]);

        $this->artisan('database-schedule:run-task', ['schedule' => $task->id])
            ->assertExitCode(0);

        $history = $task->histories()->first();

        $this->assertNotNull($history);
        $this->assertStringContainsString('Started at', $history->output);
        $this->assertStringContainsString('Hello the test worked.', $history->output);
        $this->assertStringContainsString('Finished at', $history->output);
        $this->assertStringContainsString(
            'Hello the test worked.',
            file_get_contents(storage_path('logs/manual-run-test.log'))
        );
    }
}
