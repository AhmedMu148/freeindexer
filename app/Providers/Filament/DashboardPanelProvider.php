<?php

namespace App\Providers\Filament;

use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages\Dashboard;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Filament\Navigation\NavigationItem;
use Filament\Actions\Action;
use App\Filament\Resources\Payments\PaymentResource;
use App\Filament\Pages\Auth\CustomLogin;
use App\Filament\Pages\Auth\ForgotPassword;
use App\Filament\Pages\Auth\CustomRegister;
use App\Filament\Pages\Profile;
use Illuminate\Support\Facades\Auth;
use Filament\Notifications\Notification;
use Filament\View\PanelsRenderHook;


class DashboardPanelProvider extends PanelProvider
{
  public function panel(Panel $panel): Panel
  {
    return $panel
      ->default()
      ->id('dashboard')
      ->path('dashboard')
      ->authGuard('web')
      ->brandName('Free Indexer')
      ->brandLogo(asset('assets/images/logo-dark.png'))
      ->brandLogo(fn() => view('filament.brand-logo'))
      ->brandLogoHeight('2rem')
      ->favicon(asset('assets/images/favicon.png'))
      ->topNavigation()
      ->login(CustomLogin::class)
      ->registration(CustomRegister::class)
      ->passwordReset(ForgotPassword::class)
      ->emailVerification(isRequired: false)
      ->emailChangeVerification()
      ->globalSearch(false)
      ->navigationItems([
        NavigationItem::make('Online Indexer')
          ->icon('heroicon-o-bolt')
          ->url(fn() => route('/'), shouldOpenInNewTab: false)
          ->isActiveWhen(fn(): bool => request()->routeIs('/'))
          ->group('Services')
          ->sort(1),
        NavigationItem::make('Plans & Pricing')
          ->icon('heroicon-o-exclamation-circle')
          ->url(fn() => route('pricing'), shouldOpenInNewTab: false)
          ->isActiveWhen(fn(): bool => request()->routeIs('/'))
          ->sort(1),
        NavigationItem::make('Download App')
          ->icon('heroicon-o-credit-card')
          ->url(fn() => route('buy-app'), shouldOpenInNewTab: false)
          ->isActiveWhen(fn(): bool => request()->routeIs('/'))
          ->sort(2),
      ])
      ->userMenuItems([
        'profile' => fn() => Action::make('profile')
          ->label('Edit Profile')
          ->url(Profile::getUrl())
          ->icon('heroicon-o-user'),
        'payments' => fn() => Action::make('payments')
          ->label('Payments')
          ->url(PaymentResource::getUrl())
          ->icon('heroicon-o-credit-card'),
        'tickets' => fn() => Action::make('tickets')
          ->label('Tickets')
          ->url(fn() => route('tickets.sso'))
          ->icon('heroicon-o-ticket'),
      ])
      ->colors([
        'primary' => Color::Orange,
      ])
      ->discoverResources(in: app_path('Filament/Resources'), for: 'App\Filament\Resources')
      ->discoverPages(in: app_path('Filament/Pages'), for: 'App\Filament\Pages')
      ->pages([
        Dashboard::class,
      ])
      ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\Filament\Widgets')
      ->middleware([
        EncryptCookies::class,
        AddQueuedCookiesToResponse::class,
        StartSession::class,
        AuthenticateSession::class,
        ShareErrorsFromSession::class,
        VerifyCsrfToken::class,
        SubstituteBindings::class,
        DisableBladeIconComponents::class,
        DispatchServingFilamentEvent::class,
      ])
      ->authMiddleware([
        Authenticate::class,
      ])
      ->renderHook(
        'panels::content.start',
        fn(): string => $this->getEmailVerificationAlert()
      )
      ->renderHook(
        'panels::content.start',
        fn(): string => $this->maybeShowVerificationLinkSentNotification()
      )
      ->renderHook(
        PanelsRenderHook::BODY_END,
        fn() => view('filament.footer'),
      );
  }

  protected function maybeShowVerificationLinkSentNotification(): string
  {
    if (session('status') !== 'verification-link-sent') {
      return '';
    }

    Notification::make()
      ->title('Verification email sent')
      ->body('We have sent a new verification link to your email address. Please check your inbox (and Spam/Junk).')
      ->success()
      ->send();

    return '';
  }

  protected function getEmailVerificationAlert(): string
  {
    $user = Auth::user();

    if (! $user || ! $user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail || $user->hasVerifiedEmail()) {
      return '';
    }

    $email      = e($user->email);
    $resendUrl  = route('filament.dashboard.auth.email-verification.resend');
    $csrf       = csrf_token();

    return <<<HTML
<div style="
    width: 100%;
    margin: 30px 0 16px 0;
    padding: 12px 14px;
    border: 1px solid #fde68a;
    background: #fffbeb;
    color: #92400e;
    border-radius: 12px;
    box-sizing: border-box;
">
    <div style="
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        flex-wrap: wrap;
    ">
        <div style="min-width: 240px;">
            <div style="font-weight: 700; font-size: 14px; margin-bottom: 4px;">
                Please verify your email address
            </div>
            <div style="font-size: 13px; line-height: 1.4;">
                We sent a verification link to <strong>{$email}</strong>.
                Please check your inbox (and Spam/Junk).
            </div>
        </div>

        <form method="POST" action="{$resendUrl}" style="margin: 0;">
            <input type="hidden" name="_token" value="{$csrf}">
            <button type="submit" style="
                appearance: none;
                border: 0;
                cursor: pointer;
                padding: 8px 12px;
                border-radius: 10px;
                background: #f59e0b;
                color: #111827;
                font-weight: 700;
                font-size: 13px;
                box-shadow: 0 1px 2px rgba(0,0,0,.08);
            ">
                Resend link
            </button>
        </form>
    </div>
</div>
HTML;
  }
}
