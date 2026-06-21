<?php

namespace App\Filament\Pages\Auth;

use Filament\Auth\Pages\Register;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class CustomRegister extends Register
{

  protected string $view = 'filament.pages.auth.register';

  public function form(Schema $schema): Schema
  {
    return $schema->components([
      TextInput::make('name')
        ->label('Username')
        ->required()
        ->maxLength(50)
        ->minLength(3)
        ->rules([
          'alpha_dash',
          'not_regex:/^\d+$/',
        ])
        ->dehydrateStateUsing(fn($state) => trim($state))
        ->unique(table: 'users', column: 'name'),
      TextInput::make('email')
        ->label('Email address')
        ->required()
        ->email()
        ->maxLength(255)
        ->unique(table: 'users', column: 'email'),
      $this->getPasswordFormComponent(),
      $this->getPasswordConfirmationFormComponent(),
    ]);
  }

  protected function getRedirectUrl(): string
  {
    return $this->getPanel()->getUrl();
  }
}
