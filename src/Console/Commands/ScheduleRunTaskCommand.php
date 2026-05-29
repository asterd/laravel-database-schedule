<?php

namespace RobersonFaria\DatabaseSchedule\Console\Commands;

use Illuminate\Console\Command;
use RobersonFaria\DatabaseSchedule\Models\ScheduleHistory;
use Symfony\Component\Process\Process;

class ScheduleRunTaskCommand extends Command
{
    protected $signature = 'database-schedule:run-task {schedule} {history?}';

    protected $description = 'Runs one database scheduled task and streams the output into history.';

    public function handle()
    {
        $model = config('database-schedule.model');
        $task = $model::query()->findOrFail($this->argument('schedule'));
        $history = $this->resolveHistory($task);

        $this->appendOutput($history, 'Started at ' . now()->toDateTimeString() . PHP_EOL);
        $this->appendLogFile($task, 'Started at ' . now()->toDateTimeString() . PHP_EOL);

        $process = $this->makeProcess($task);
        $process->setTimeout(null);
        $process->setIdleTimeout(null);

        $process->run(function ($type, $buffer) use ($history, $task) {
            $this->appendOutput($history, $buffer);
            $this->appendLogFile($task, $buffer);
            $this->output->write($buffer);
        });

        $finishLine = PHP_EOL . 'Finished at ' . now()->toDateTimeString()
            . ' with exit code ' . $process->getExitCode() . PHP_EOL;
        $this->appendOutput($history, $finishLine);
        $this->appendLogFile($task, $finishLine);

        if (!$process->isSuccessful()) {
            return $process->getExitCode() ?: 1;
        }

        return 0;
    }

    private function resolveHistory($task): ScheduleHistory
    {
        if ($this->argument('history')) {
            return ScheduleHistory::query()->findOrFail($this->argument('history'));
        }

        return $task->histories()->create([
            'command' => $this->displayCommand($task),
            'params' => $task->getArguments(),
            'options' => $task->getOptions(),
            'output' => '',
        ]);
    }

    private function makeProcess($task): Process
    {
        if ($task->command === 'custom') {
            return Process::fromShellCommandline($task->command_custom, base_path());
        }

        return new Process(array_merge(
            [PHP_BINARY, base_path('artisan'), $task->command],
            $this->normalizeParameters($task->getCommandParameters())
        ), base_path());
    }

    private function normalizeParameters(array $parameters): array
    {
        $normalized = [];

        foreach ($parameters as $key => $value) {
            if (is_int($key)) {
                $normalized[] = (string) $value;
                continue;
            }

            if ($value === null || $value === '') {
                $normalized[] = (string) $key;
                continue;
            }

            $normalized[] = $key . '=' . $value;
        }

        return $normalized;
    }

    private function appendOutput(ScheduleHistory $history, string $output): void
    {
        $history->refresh();
        $history->output = ($history->output ?? '') . $output;
        $history->save();
    }

    private function appendLogFile($task, string $output): void
    {
        if (!$task->log_filename) {
            return;
        }

        file_put_contents(storage_path('logs/' . $task->log_filename . '.log'), $output, FILE_APPEND);
    }

    private function displayCommand($task): string
    {
        return $task->command === 'custom' ? $task->command_custom : $task->command;
    }
}
