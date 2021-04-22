<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;

class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return string|null
     */

    // protected $except = [
    //     '/notification/post_to_midtrans',
    // ];


    protected function redirectTo($request)
    {
        if (! $request->expectsJson()) {
            return route('login');
        }
    }

    // protected function inExceptArray($request)
    // {
    //     foreach ($this->except as $except) {
    //         if ($except !== '/') {
    //             $except = trim($except, '/');
    //         }

    //         if ($request->is($except)) {
    //             return true;
    //         }
    //     }

    //     return false;
    // }
}
