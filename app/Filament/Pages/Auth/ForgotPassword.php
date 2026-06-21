<?php

namespace App\Filament\Pages\Auth;

use Filament\Auth\Pages\PasswordReset\RequestPasswordReset;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\ValidationException;
use App\Models\User;

class ForgotPassword extends RequestPasswordReset
{
  public function form(Schema $schema): Schema
  {
    return $schema->components([
      TextInput::make('email')
        ->label('Email or Username')
        ->required()
        ->autocomplete('username')
        ->dehydrateStateUsing(function ($state) {
          if (filter_var($state, FILTER_VALIDATE_EMAIL)) {
            return $state;
          }
          return User::where('name', $state)->value('email') ?? $state;
        }),
    ]);
  }

  public function submit(): void
  {
    $data   = $this->form->getState();
    $login  = $data['login'];
    $user   = filter_var($login, FILTER_VALIDATE_EMAIL)
      ? User::where('email', $login)->first()
      : User::where('name', $login)->first();
    if (! $user) {
      throw ValidationException::withMessages([
        'data.login' => __('We can’t find a user with that email or username.'),
      ]);
    }
    $status = Password::sendResetLink([
      'email' => $user->email,
    ]);
    if ($status !== Password::RESET_LINK_SENT) {
      throw ValidationException::withMessages([
        'data.login' => __($status),
      ]);
    }
    $this->notify(__('Password reset link sent.'));
  }
}
