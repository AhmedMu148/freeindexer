<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\CmsApiToken;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AuthenticateCmsApiToken
{
    public function handle(Request $request, Closure $next): Response
    {
        $token = $this->resolveToken($request);

        if (empty($token)) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }

        // 1. Check fallback static env token
        $fallbackToken = config('services.cms.api_token');
        if (filled($fallbackToken) && $token === $fallbackToken) {
            $request->attributes->set('cms_token_record', new CmsApiToken([
                'name' => 'Env Static Token',
                'abilities' => ['cms.all'],
                'is_active' => true,
            ]));
            return $next($request);
        }

        // 2. Check Database Token
        $hash = hash('sha256', $token);
        $tokenRecord = CmsApiToken::where('token_hash', $hash)->first();

        if (!$tokenRecord ||
            !$tokenRecord->is_active ||
            !is_null($tokenRecord->revoked_at) ||
            ($tokenRecord->expires_at && $tokenRecord->expires_at->isPast())
        ) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }

        // Success - stamp last_used details
        $tokenRecord->update([
            'last_used_at' => now(),
            'last_used_ip' => $request->ip(),
            'last_used_user_agent' => substr($request->userAgent(), 0, 255),
        ]);

        $request->attributes->set('cms_token_record', $tokenRecord);

        return $next($request);
    }

    protected function resolveToken(Request $request): ?string
    {
        if ($request->hasHeader('X-CMS-Token')) {
            return $request->header('X-CMS-Token');
        }

        $bearer = $request->bearerToken();
        if ($bearer) {
            return $bearer;
        }

        return null;
    }
}
