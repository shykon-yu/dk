<?php

namespace App\Observers;

use App\Models\Inbound;
use App\Models\User;
use Notification;
use App\Notifications\InboundCreateNotify;

class InboundObserver
{
    public function created(Inbound $inbound)
    {
        $admin = User::find(1);

        if ($admin) {
            Notification::send($admin, new InboundCreateNotify($inbound));
        }
    }

    public function updated(Inbound $inbound)
    {
        //
    }

    public function deleted(Inbound $inbound)
    {
        //
    }

    public function restored(Inbound $inbound)
    {
        //
    }

    public function forceDeleted(Inbound $inbound)
    {
        //
    }
}
