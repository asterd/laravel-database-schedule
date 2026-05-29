<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class DemoScheduleServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Gate::define('viewDatabaseSchedule', function ($user = null) {
            return true;
        });
    }
}
