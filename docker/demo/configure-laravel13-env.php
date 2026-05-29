<?php

$path = '/demo/.env';
$values = [
    'APP_ENV' => 'local',
    'APP_DEBUG' => 'true',
    'APP_URL' => 'http://localhost:8083',
    'DB_CONNECTION' => 'sqlite',
    'DB_DATABASE' => '/demo/database/database.sqlite',
    'CACHE_STORE' => 'file',
    'SESSION_DRIVER' => 'file',
    'QUEUE_CONNECTION' => 'sync',
    'SCHEDULE_DATABASE_CONNECTION' => 'sqlite',
    'SCHEDULE_RESTRICTED_ACCESS' => '0',
    'SCHEDULE_CACHE_ENABLE' => '1',
    'SCHEDULE_CACHE_DRIVER' => 'file',
    'SCHEDULE_CACHE_TTL' => '30',
    'SCHEDULE_WITHOUT_OVERLAPPING_EXPIRES_AT' => '5',
];

$contents = file_get_contents($path);

foreach ($values as $key => $value) {
    if (preg_match('/^' . preg_quote($key, '/') . '=/m', $contents)) {
        $contents = preg_replace('/^' . preg_quote($key, '/') . '=.*$/m', $key . '=' . $value, $contents);
        continue;
    }

    $contents .= PHP_EOL . $key . '=' . $value;
}

file_put_contents($path, $contents);
