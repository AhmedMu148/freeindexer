<?php

namespace App\Filament\Resources\Backlinks\Pages;

use Filament\Resources\Pages\CreateRecord;
use App\Filament\Resources\Backlinks\BacklinkResource;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Auth;
use App\Models\Backlink;
use Illuminate\Support\Facades\DB;

class CreateBacklink extends CreateRecord
{
  protected static bool $canCreateAnother = false;

  protected static string $resource = BacklinkResource::class;

  protected static ?string $title = 'Create Backlink Campaign';

  protected function handleRecordCreation(array $data): Backlink
  {

    $user     = Auth::user();
    $uid      = $user['id'];

    if (!$uid) {
      Notification::make()
        ->danger()
        ->title('Session expired')
        ->body('Please login again.')
        ->send();
      $this->halt();
    }

    $backlink_points_data = DB::table('backlinks_points')->where('uid', $uid)->first();
    $backlink_points      = $backlink_points_data ? $backlink_points_data->points : 0;
    $backlink_used        = $backlink_points_data ? $backlink_points_data->used : 0;

    $qty = $data['qty'];

    if ($qty + $backlink_used > $backlink_points) {
      Notification::make()
        ->warning()
        ->title('Quantity Limit Exceeded')
        ->body("You cannot process more than {$backlink_points} Quantity. To process more please upgrade.")
        ->color('warning')
        ->send();
      $this->halt();
    }

    // Update user uses
    DB::table('backlinks_points')->where('uid', $uid)->update([
      'used' => $qty + $backlink_used,
    ]);

    $project_name = $data['project_name'];
    $links        = $data['links'];
    $keywords     = $data['keywords'];
    $qty          = $data['qty'];

    // Submit order
    $order = Backlink::create([
      'uid'           => $uid,
      'ses_id'        => '',
      'project_name'  => $project_name,
      'links'         => $links,
      'keywords'      => $keywords,
      'qty'           => $qty,
      'status_id'     => 1
    ]);

    if ($order) {
      Notification::make()
        ->success()
        ->title('Order Submitted Successfully')
        ->body("Done! Your Order ID: #{$order->id}")
        ->send();
    }

    return $order;
  }

  /**
   * Redirect to applications list after creation for better UX
   */
  protected function getRedirectUrl(): string
  {
    return $this->getResource()::getUrl('index');
  }
}
