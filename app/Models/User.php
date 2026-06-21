<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Models\PymPayment;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;

// class User extends Authenticatable implements FilamentUser, MustVerifyEmail
class User extends Authenticatable implements MustVerifyEmail, FilamentUser
{
  /** @use HasFactory<\Database\Factories\UserFactory> */
  use HasFactory, Notifiable;

  /**
   * The attributes that are mass assignable.
   *
   * @var list<string>
   */
  protected $fillable = [
    'name',
    'email',
    'status_id',
    'password',
  ];

  public function canAccessPanel(Panel $panel): bool
  {
    // You can add your custom logic here, e.g., checking for an admin role
    return true;
  }


  /**
   * The attributes that should be hidden for serialization.
   *
   * @var list<string>
   */
  protected $hidden = [
    'password',
    'remember_token',
  ];

  /**
   * Get the attributes that should be cast.
   *
   * @return array<string, string>
   */
  protected function casts(): array
  {
    return [
      'email_verified_at' => 'datetime',
      'password' => 'hashed',
    ];
  }

  public function payments()
  {
    return $this->hasMany(PymPayment::class, 'uid');
  }

  public function sendEmailVerificationNotification()
  {
    $this->notify(new \App\Notifications\VerifyEmail);
  }
}
