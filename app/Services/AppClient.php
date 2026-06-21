<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AppClient
{
  public function createKeyRow(int|string $uid, string $paymentId, string $key): string
  {
    DB::table('app')->insert([
      'uid'        => $uid,
      'payment_id' => $paymentId,
      'key'        => $key,
      'status_id'  => 1,
      'created_at' => now(),
      'updated_at' => now(),
    ]);

    return $key;
  }

  public function sendKeyApi(User|int|string $userOrId, string $key): bool
  {
    $user = $userOrId instanceof User ? $userOrId : User::find($userOrId);
    if (!$user) {
      Log::warning('sendKeyApi: user not found', ['userOrId' => $userOrId]);
      return false;
    }

    $payload = [
      'action'     => 'app',
      'name'       => $user->name ?? 'guest',
      'email'      => $user->email,
      'userApiKey' => $key,
    ];

    $apiUrl = config('services.freeindexer.url');

    $ch = curl_init($apiUrl);
    curl_setopt_array($ch, [
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_POST           => true,
      CURLOPT_POSTFIELDS     => http_build_query($payload),
      CURLOPT_HTTPHEADER     => [
        'Accept: application/json',
        'X-API-Key: replace-me',
      ],
      CURLOPT_TIMEOUT        => 10,
    ]);

    $resp = curl_exec($ch);
    $http = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $err  = curl_error($ch);
    curl_close($ch);

    if ($resp === false || $http < 200 || $http >= 300) {
      Log::error('FreeIndexer API failed', [
        'status' => $http,
        'error'  => $err,
        'body'   => $resp,
      ]);
      return false;
    }

    return true;
  }
}
