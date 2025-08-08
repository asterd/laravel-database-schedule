<?php

namespace RobersonFaria\DatabaseSchedule;

use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;

class DatabaseScheduleApplicationServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->authorization();
        // per Bootstrap 4.x (tu usi 4.6 / Cosmo)
        Paginator::useBootstrapFour();
    }

    protected function authorization()
    {
        $this->gate();
    }

    protected function gate()
    {
        Gate::define('viewDatabaseSchedule', function ($user) {
            return false;
        });
    }
}
