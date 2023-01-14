<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

class ShowNewWeeksAlbumsForm extends Controller
{
    public function __invoke()
    {
        return view("weeks-albums");
    }
}
