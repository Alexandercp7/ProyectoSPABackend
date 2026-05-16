<?php

namespace App\Providers;

use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;
use App\Events\WorkOrderClosedEvent;
use App\Listeners\SendWorkOrderClosedNotification;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void {}

    public function boot(): void
    {
        Event::listen(WorkOrderClosedEvent::class, SendWorkOrderClosedNotification::class);
    }
}