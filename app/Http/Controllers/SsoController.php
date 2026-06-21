<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SsoController extends Controller
{
    public function redirect(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login');
        }

        $ssoSecret = config('services.ticket_system.sso_secret') ?: 'default_sso_jwt_secret_here';
        $domain = rtrim(config('services.ticket_system.domain', ''), '/');

        if (empty($domain)) {
            abort(500, 'Ticket support domain is not configured.');
        }

        $baseUrl = (str_starts_with($domain, 'http://') || str_starts_with($domain, 'https://'))
            ? $domain
            : "https://{$domain}";

        $iat = time();
        $exp = $iat + 300; // exactly 5 minutes expiration
        $jti = (string) \Illuminate\Support\Str::uuid();

        $redirectUrl = $request->query('redirect_url') ?: '/tickets';

        $payload = [
            'name' => $user->name,
            'email' => $user->email,
            'iat' => $iat,
            'exp' => $exp,
            'jti' => $jti,
            'redirect_url' => $redirectUrl,
        ];

        $token = $this->encodeJwt($payload, $ssoSecret);

        return redirect("{$baseUrl}/auth/callback?token={$token}");
    }

    protected function encodeJwt(array $payload, string $secret): string
    {
        $header = json_encode(['alg' => 'HS256', 'typ' => 'JWT']);
        $payloadJson = json_encode($payload);

        $headerB64 = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($header));
        $payloadB64 = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($payloadJson));

        $signature = hash_hmac('sha256', $headerB64 . '.' . $payloadB64, $secret, true);
        $signatureB64 = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($signature));

        return $headerB64 . '.' . $payloadB64 . '.' . $signatureB64;
    }
}
