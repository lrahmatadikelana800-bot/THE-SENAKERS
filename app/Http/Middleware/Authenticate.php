<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Request;

class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     */
    protected function redirectTo(Request $request): ?string
    {
        // For API and web requests in this project, do not attempt to redirect to a login route.
        // Always return null so unauthenticated requests receive JSON 401 responses.
        return null;
    }
}
