<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Verified;

class SetStatusAfterEmailVerified
{
  public function handle(Verified $event): void
  {
    /** @var \App\Models\User $user */
    $user = $event->user;

    $UNVERIFIED_STATUS_ID = 1;
    $VERIFIED_STATUS_ID   = 2;

    if ((int) $user->status_id === $UNVERIFIED_STATUS_ID) {
      $user->status_id = $VERIFIED_STATUS_ID;
      $user->save();
    }
  }
}
