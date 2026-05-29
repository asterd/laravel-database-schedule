<?php

use Illuminate\Contracts\Console\Kernel;
use Illuminate\Support\Facades\DB;

require '/demo/vendor/autoload.php';

$app = require '/demo/bootstrap/app.php';
$app->make(Kernel::class)->bootstrap();

DB::table('schedules')->updateOrInsert(
    ['command' => 'schedule:test-job'],
    [
        'params' => json_encode([]),
        'options' => json_encode([]),
        'expression' => '* * * * *',
        'even_in_maintenance_mode' => true,
        'without_overlapping' => true,
        'without_overlapping_expires_at' => 5,
        'on_one_server' => false,
        'webhook_before' => null,
        'webhook_after' => null,
        'email_output' => null,
        'sendmail_error' => false,
        'sendmail_success' => false,
        'log_success' => true,
        'log_error' => true,
        'status' => true,
        'run_in_background' => false,
        'log_filename' => 'database-schedule-demo',
        'groups' => 'demo',
        'environments' => null,
        'created_at' => now(),
        'updated_at' => now(),
    ]
);

DB::table('schedules')->updateOrInsert(
    ['command' => 'schedule:test-long-job'],
    [
        'params' => json_encode([
            'seconds' => [
                'value' => '12',
                'type' => 'string',
            ],
        ]),
        'options' => json_encode([]),
        'expression' => '* * * * *',
        'even_in_maintenance_mode' => true,
        'without_overlapping' => true,
        'without_overlapping_expires_at' => 5,
        'on_one_server' => false,
        'webhook_before' => null,
        'webhook_after' => null,
        'email_output' => null,
        'sendmail_error' => false,
        'sendmail_success' => false,
        'log_success' => true,
        'log_error' => true,
        'status' => false,
        'run_in_background' => false,
        'log_filename' => 'database-schedule-long-demo',
        'groups' => 'demo',
        'environments' => null,
        'created_at' => now(),
        'updated_at' => now(),
    ]
);
