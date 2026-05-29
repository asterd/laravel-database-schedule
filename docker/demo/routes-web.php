<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return response(<<<'HTML'
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Laravel Database Schedule Demo</title>
    <style>
        body { font-family: ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif; margin: 0; color: #172033; background: #f4f6f8; }
        main { max-width: 960px; margin: 0 auto; padding: 48px 24px; }
        h1 { font-size: 32px; margin: 0 0 12px; }
        p { line-height: 1.5; }
        .panel { background: #fff; border: 1px solid #d9e0e7; border-radius: 8px; padding: 24px; }
        .actions { display: flex; gap: 12px; flex-wrap: wrap; margin-top: 24px; }
        a { color: #0f5f8f; font-weight: 600; }
        .button { background: #153f5c; color: #fff; padding: 10px 14px; border-radius: 6px; text-decoration: none; }
        code { background: #e9eef3; padding: 2px 5px; border-radius: 4px; }
    </style>
</head>
<body>
<main>
    <div class="panel">
        <h1>Laravel 13 scheduler demo</h1>
        <p>This container runs a Laravel 13 application with <code>robersonfaria/laravel-database-schedule</code> installed from the local package source.</p>
        <p>An active demo schedule runs <code>schedule:test-job</code> every minute. Open the scheduler UI to edit it, run it manually, and inspect execution history.</p>
        <div class="actions">
            <a class="button" href="/schedule">Open scheduler UI</a>
            <a href="/schedule/1">Open demo history</a>
        </div>
    </div>
</main>
</body>
</html>
HTML);
});
