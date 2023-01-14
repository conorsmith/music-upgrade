<?php

namespace App;

use Carbon\Carbon;

class Clock
{
    public function mondayThisWeek()
    {
        return Carbon::now("Europe/Dublin")->dayOfWeek === Carbon::MONDAY
            ? new Carbon("today", "Europe/Dublin")
            : new Carbon("last Monday", "Europe/Dublin");
    }

    public function now()
    {
        return Carbon::now("Europe/Dublin");
    }
}
