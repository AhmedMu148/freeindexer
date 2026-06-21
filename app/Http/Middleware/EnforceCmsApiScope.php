<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnforceCmsApiScope
{
    public function handle(Request $request, Closure $next, string $ability): Response
    {
        $tokenRecord = $request->attributes->get('cms_token_record');

        if (!$tokenRecord) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }

        $abilities = $tokenRecord->abilities ?? [];

        if (in_array('cms.all', $abilities) || in_array($ability, $abilities)) {
            return $next($request);
        }

        return response()->json(['message' => 'Forbidden.'], 403);
    }
}
