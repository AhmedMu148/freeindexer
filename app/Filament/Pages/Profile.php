<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Schemas\Schema;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class Profile extends Page
{
  use InteractsWithForms;

  protected string $view = 'filament.pages.profile';

  protected static ?string $title = 'Profile';

  protected static bool $shouldRegisterNavigation = false;

  public ?array $data = [];

  public function mount(): void
  {
    /** @var User $user */
    $user = Auth::user();

    $this->form->fill([
      'name' => $user->name,
      'email' => $user->email,
    ]);
  }

  public function form(Schema $schema): Schema
  {
    return $schema
      ->statePath('data')
      ->components([
        TextInput::make('name')
          ->label('Username')
          ->required()
          ->maxLength(255)
          ->helperText('Contact support to change this')
          ->disabled(),
        TextInput::make('email')
          ->email()
          ->required()
          ->maxLength(255)
          ->helperText('Contact support to change this')
          ->disabled(),
        TextInput::make('new_password')
          ->password()
          ->revealable()
          ->minLength(8)
          ->nullable()
          ->rules(['nullable', 'min:8', 'confirmed']),
        TextInput::make('new_password_confirmation')
          ->password()
          ->revealable()
          ->nullable(),
      ]);
  }

  public function save(): void
  {
    $data = $this->form->getState();

    $this->form->validate();

    /** @var User $user */
    $user = Auth::user();

    if (empty($data['new_password'])) {
      Notification::make()
        ->warning()
        ->title('No changes to update')
        ->body('Enter a new password to update your profile.')
        ->send();
      return;
    }

    if (Hash::check($data['new_password'], $user->password)) {
      Notification::make()
        ->danger()
        ->title('Invalid password')
        ->body('The new password must be different from your current password.')
        ->send();

      return;
    }

    $user->password = Hash::make($data['new_password']);
    $user->save();

    $this->form->fill([
      'name'                      => $user->name,
      'email'                     => $user->email,
      'new_password'              => null,
      'new_password_confirmation' => null,
    ]);

    Notification::make()
      ->success()
      ->title('Password updated')
      ->send();

    $this->redirect(request()->header('Referer'), navigate: true);
  }
}
