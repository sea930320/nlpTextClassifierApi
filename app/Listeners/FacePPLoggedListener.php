<?php

namespace App\Listeners;

use App\Events\FacePPLogged;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class FacePPLoggedListener
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
     * @param  FacePPLogged  $event
     * @return void
     */
    public function handle(FacePPLogged $event)
    {
        //
    }
}
