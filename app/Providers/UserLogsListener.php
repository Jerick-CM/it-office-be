<?php

namespace App\Providers;

use App\Providers\UserLogsEvent;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class UserLogsListener
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  \App\Providers\UserLogsEvent  $event
     * @return void
     */
    public function handle(UserLogsEvent $event)
    {
        //
    }
}
