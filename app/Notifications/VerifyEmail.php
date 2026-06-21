<?php

namespace App\Notifications;

use Illuminate\Auth\Notifications\VerifyEmail as BaseVerifyEmail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class VerifyEmail extends BaseVerifyEmail implements ShouldQueue
{
  use Queueable;

  public function toMail($notifiable)
  {
    $verificationUrl = $this->verificationUrl($notifiable);
    return (new MailMessage)
      ->subject('Email Verification - Free Indexer')
      ->greeting('Welcome to Free Indexer!')
      ->line('Please click the button below to verify your email address.')
      ->action('Verify Email', $verificationUrl)
      ->line('This verification link will expire in 60 minutes.')
      ->line('Thank you for using Free Indexer!');
  }
}
