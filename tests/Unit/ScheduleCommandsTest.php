<?php

namespace RobersonFaria\DatabaseSchedule\Tests\Unit;

use Mockery;
use RobersonFaria\DatabaseSchedule\Models\Schedule;
use RobersonFaria\DatabaseSchedule\Tests\TestCase;

class ScheduleCommandsTest extends TestCase
{
    private $event;

    protected function setUp(): void
    {
        parent::setUp();
        $this->event = $this->mock(\Illuminate\Console\Scheduling\Event::class, function (Mockery\MockInterface $mock) {
            $mock->shouldReceive('cron')
                ->once()
                ->with('* * * * *');

            $mock->shouldReceive('storeOutput')
                ->once();

            $mock->shouldReceive('timezone')
                ->once()
                ->with('UTC');

            $mock->shouldReceive('onSuccess')
                ->once();

            $mock->shouldReceive('onFailure')
                ->once();

            $mock->shouldReceive('after')
                ->once();
        });
    }

    public function testRunInspireCommand()
    {
        Schedule::query()->create([
            'command' => 'inspire'
        ]);


        $this->mock(\Illuminate\Console\Scheduling\Schedule::class, function (Mockery\MockInterface $mock) {
            $mock->shouldReceive('command')
                ->once()
                ->with('inspire', [])
                ->andReturn($this->event);
        });

        $scheduleService = app(\RobersonFaria\DatabaseSchedule\Console\Scheduling\Schedule::class);
        $scheduleService->execute();
    }

    public function testRunInspireWithArguments()
    {
        Schedule::query()->create([
            'command' => 'inspire',
            'params' => [
                "test" => ["value" => "1", "type" => "string"]
            ]
        ]);

        $this->mock(\Illuminate\Console\Scheduling\Schedule::class, function (Mockery\MockInterface $mock) {
            $mock->shouldReceive('command')
                ->once()
                ->with('inspire', ['1'])
                ->andReturn($this->event);
        });

        $scheduleService = app(\RobersonFaria\DatabaseSchedule\Console\Scheduling\Schedule::class);
        $scheduleService->execute();
    }

    public function testRunInspireWithOptionalArguments()
    {
        Schedule::query()->create([
            'command' => 'inspire',
            'params' => [
                "test" => ["value" => null, "type" => "string"]
            ]
        ]);

        $this->mock(\Illuminate\Console\Scheduling\Schedule::class, function (Mockery\MockInterface $mock) {
            $mock->shouldReceive('command')
                ->once()
                ->with('inspire', [])
                ->andReturn($this->event);
        });

        $scheduleService = app(\RobersonFaria\DatabaseSchedule\Console\Scheduling\Schedule::class);
        $scheduleService->execute();
    }

    public function testRunInspireWithOptionsBoolean()
    {
        Schedule::query()->create([
            'command' => 'inspire',
            'options' => [
                "argDisabledTrue" => "on"
            ]
        ]);

        $this->mock(\Illuminate\Console\Scheduling\Schedule::class, function (Mockery\MockInterface $mock) {
            $mock->shouldReceive('command')
                ->once()
                ->with('inspire', ['--argDisabledTrue'])
                ->andReturn($this->event);
        });

        $scheduleService = app(\RobersonFaria\DatabaseSchedule\Console\Scheduling\Schedule::class);
        $scheduleService->execute();
    }

    public function testArgumentsAndBooleanOptionsAreMergedWithoutLosingOptions()
    {
        Schedule::query()->create([
            'command' => 'inspire',
            'params' => [
                "test" => ["value" => "1", "type" => "string"]
            ],
            'options' => [
                "force" => "on"
            ]
        ]);

        $this->mock(\Illuminate\Console\Scheduling\Schedule::class, function (Mockery\MockInterface $mock) {
            $mock->shouldReceive('command')
                ->once()
                ->with('inspire', ['1', '--force'])
                ->andReturn($this->event);
        });

        $scheduleService = app(\RobersonFaria\DatabaseSchedule\Console\Scheduling\Schedule::class);
        $scheduleService->execute();
    }

    public function testZeroStringArgumentIsPreserved()
    {
        Schedule::query()->create([
            'command' => 'inspire',
            'params' => [
                "test" => ["value" => "0", "type" => "string"]
            ]
        ]);

        $this->mock(\Illuminate\Console\Scheduling\Schedule::class, function (Mockery\MockInterface $mock) {
            $mock->shouldReceive('command')
                ->once()
                ->with('inspire', ['0'])
                ->andReturn($this->event);
        });

        $scheduleService = app(\RobersonFaria\DatabaseSchedule\Console\Scheduling\Schedule::class);
        $scheduleService->execute();
    }

    public function testWithoutOverlappingUsesConfiguredExpiration()
    {
        Schedule::query()->create([
            'command' => 'inspire',
            'without_overlapping' => true,
            'without_overlapping_expires_at' => 30,
        ]);

        $this->event->shouldReceive('withoutOverlapping')
            ->once()
            ->with(30);

        $this->mock(\Illuminate\Console\Scheduling\Schedule::class, function (Mockery\MockInterface $mock) {
            $mock->shouldReceive('command')
                ->once()
                ->with('inspire', [])
                ->andReturn($this->event);
        });

        $scheduleService = app(\RobersonFaria\DatabaseSchedule\Console\Scheduling\Schedule::class);
        $scheduleService->execute();
    }
}
