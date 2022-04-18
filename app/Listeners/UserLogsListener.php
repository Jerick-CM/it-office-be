<?php

namespace App\Listeners;


use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use App\Models\AdminUsersLogs;
use App\Events\UserLogsEvent;
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
     * @param  \App\Events\UserLogsEvent  $event
     * @return void
     */
    public function handle(UserLogsEvent $event)
    {

        AdminUsersLogs::create([
            'user_id' => $event->user_id,
            'type' => $event->type,
            'meta' => $event->meta,
        ]);
    }
}
