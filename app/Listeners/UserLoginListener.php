<?php

namespace App\Listeners;

use App\Events\JwtLogin;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class UserLoginListener
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
     * @param  JwtLogin  $event
     * @return void
     */
    public function handle(JwtLogin $event)
    {
        //
    }
}
