<?php

namespace App\Http\Middleware;

use App\Http\Controllers\Api\ApiBaseController;
use Closure;
use Illuminate\Auth\Middleware\Authenticate as Middleware;

class CustomAuthenticateMiddleware extends Middleware
{
    /**
     * Handle an unauthenticated user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  array  $guards
     * @return void
     *
     * @throws \Illuminate\Http\Exceptions\HttpResponseException
     */
    protected function unauthenticated($request, array $guards)
    {
        $controller = new ApiBaseController();
        throw new \Illuminate\Http\Exceptions\HttpResponseException(
            $controller->sendUnauthorized('You must login first')
        );
    }
}