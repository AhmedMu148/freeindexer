<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Filament\Pages\Auth\LogoutResponse;
use Filament\Auth\Http\Responses\Contracts\LogoutResponse as LogoutResponseContract;

class AppServiceProvider extends ServiceProvider
{
  /**
   * Register any application services.
   */
  public function register(): void
  {
    $this->app->bind(LogoutResponseContract::class, LogoutResponse::class);

    // $this->app->bind(Responsable::class, FilamentLoginResponse::class);
  }

  /**
   * Bootstrap any application services.
   */
  public function boot(): void
  {
    \Illuminate\Support\Facades\Event::listen(
      \Illuminate\Auth\Events\Logout::class,
      function (\Illuminate\Auth\Events\Logout $event) {
        $user = $event->user;
        if ($user && $user->email) {
          try {
            $apiKey = config('services.ticket_system.api_key');
            $apiSecret = config('services.ticket_system.api_secret');
            
            $url = config('services.ticket_system.url');
            if (empty($url)) {
              $domain = rtrim(config('services.ticket_system.domain', ''), '/');
              if (filled($domain)) {
                $url = (str_starts_with($domain, 'http://') || str_starts_with($domain, 'https://'))
                  ? $domain
                  : "https://{$domain}";
              }
            }

            if (filled($url)) {
              $logoutUrl = rtrim($url, '/') . '/api/auth/logout';

              \Illuminate\Support\Facades\Http::withHeaders([
                'X-API-Key'    => $apiKey,
                'X-API-Secret' => $apiSecret,
                'Accept'       => 'application/json',
                'Content-Type' => 'application/json',
              ])->post($logoutUrl, [
                'email' => $user->email,
              ]);
            }
          } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('SSO Logout Sync Failed: ' . $e->getMessage());
          }
        }
      }
    );
  }
}
