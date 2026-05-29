<?php

namespace RobersonFaria\DatabaseSchedule\Console\Commands;

use Illuminate\Console\Command;

class TestLongJobCommand extends Command
{
    protected $signature = 'schedule:test-long-job {seconds=10}';

    protected $description = 'Command that emits progressive output for manual scheduler testing.';

    public function handle()
    {
        $seconds = max(1, (int) $this->argument('seconds'));

        for ($second = 1; $second <= $seconds; $second++) {
            $this->line('Long job progress ' . $second . '/' . $seconds);
            sleep(1);
        }

        $this->info('Long job completed.');

        return 0;
    }
}
