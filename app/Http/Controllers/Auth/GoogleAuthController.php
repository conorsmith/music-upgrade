<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Session;

class GoogleAuthController extends Controller
{
    private $client;

    public function __construct(\Google_Client $client)
    {
        $this->client = $client;
    }

    public function trigger()
    {
        return redirect($this->client->createAuthUrl());
    }

    public function callback()
    {
        if (Request::has('code')) {
            $accessToken = $this->client->authenticate(Request::get('code'));
            Session::put('google.access_token', $accessToken);
        }

        return redirect(env('GOOGLE_CALLBACK_ACTUAL_ROOT', "") . "/dashboard");
    }
}
