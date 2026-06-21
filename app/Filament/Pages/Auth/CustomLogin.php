<?php

namespace App\Filament\Pages\Auth;

use Filament\Auth\Pages\Login;
use Filament\Auth\Http\Responses\Contracts\LoginResponse;
use Filament\Schemas\Schema;
use Filament\Forms\Components\TextInput;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Filament\Actions\Action;

class CustomLogin extends Login
{
  // You can set a custom view here if needed
  // protected static string $view = 'auth.login';
  protected string $view = 'filament.pages.auth.login';

  public function mount(): void
  {
    parent::mount();
    $redirect = request()->query('redirect');
    if ($redirect) {
      session()->put('url.intended', $redirect);
    }
  }

  public function form(Schema $schema): Schema
  {
    return $schema
      ->components([

        TextInput::make('login')
          ->label('Email or Username')
          ->required(),

        // TextInput::make('username')
        //   ->label('Username')
        //   ->required()
        //   ->autocomplete('username'),
        // $this->getEmailFormComponent(),
        $this->getPasswordFormComponent(),
        $this->getRememberFormComponent(),
        // Add any extra custom fields here if needed
        // $this->TextInput::make('username')->required(),
      ]);
  }

  public function authenticate(): ?LoginResponse
  {
    $data   = $this->form->getState();
    $login  = $data['login'];
    $field  = filter_var($login, FILTER_VALIDATE_EMAIL)
      ? 'email'
      : 'name';
    if (! Auth::attempt([
      $field => $login,
      'password' => $data['password'],
    ], $data['remember'] ?? false)) {
      throw ValidationException::withMessages([
        'data.login' => __('auth.failed'),
      ]);
    }
    session()->regenerate();

    return app(LoginResponse::class);
  }

  public function getAuthenticateFormAction(): Action
  {
    return Action::make('authenticate')
      ->label(__('filament-panels::pages/auth/login.form.actions.authenticate.label'))
      ->submit('authenticate')
      ->color('primary')
      ->extraAttributes([
        'class' => 'w-full',
      ]);
  }
}
