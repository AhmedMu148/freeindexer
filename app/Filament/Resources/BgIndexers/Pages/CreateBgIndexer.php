<?php

namespace App\Filament\Resources\BgIndexers\Pages;

use App\Filament\Resources\BgIndexers\BgIndexerResource;
use Filament\Resources\Pages\CreateRecord;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Auth;
use App\Models\BgIndexer;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;


class CreateBgIndexer extends CreateRecord
{
  protected static bool $canCreateAnother = false;

  protected static string $resource = BgIndexerResource::class;


  // public function mount(): void
  // {
  //   parent::mount();

  //   $points = auth()->user()->points ?? 0;

  //   if ($points <= 0) {
  //     Notification::make()
  //       ->warning()
  //       ->title('رصيدك لا يسمح بالإنشاء')
  //       ->body('من فضلك اشترك أو اشحن النقاط للمتابعة.')
  //       ->actions([
  //         \Filament\Notifications\Actions\Action::make('plans')
  //           ->button()
  //           ->label('اذهب للخطط')
  //           ->url(route('pricing'), shouldOpenInNewTab: false), // عدّل المسار لو مختلف
  //       ])
  //       ->send();

  //     // رجّعه لليست
  //     $this->redirect(static::getResource()::getUrl('index'));
  //   }
  // }

  protected function handleRecordCreation(array $data): BgIndexer
  {

    $user     = Auth::user();
    $uid      = $user['id'];
    $username = $user['name'];

    if (!$uid) {
      Notification::make()
        ->danger()
        ->title('Session expired')
        ->body('Please login again.')
        ->send();
      $this->halt();
    }

    $bg_indexer_points_data = DB::table('bg_indexer_points')->where('uid', $uid)->first();
    $bg_indexer_points      = $bg_indexer_points_data ? $bg_indexer_points_data->points : 0;
    $bg_indexer_used        = $bg_indexer_points_data ? $bg_indexer_points_data->used : 0;

    $user_urls_array = preg_split('/\r\n|\r|\n/', trim($data['urls']));

    if (count($user_urls_array) + $bg_indexer_used > $bg_indexer_points) {
      Notification::make()
        ->warning()
        ->title('URL Limit Exceeded')
        ->body("You cannot process more than {$bg_indexer_points} URLs. To process more please upgrade.")
        ->color('warning')
        ->send();
      $this->halt();
    }

    // Save URLs to a file
    $mainDir        = 'background-indexer';
    $generatedFile  = $username . '/' . time() . '.txt';
    $filename       = $mainDir . '/' . $generatedFile;
    $dirname        = dirname($filename);
    $urlsCount      = count($user_urls_array);
    if (file_exists($filename)) {
      Notification::make()
        ->warning()
        ->title('File Exists')
        ->body('File exists, try again!')
        ->color('warning')
        ->send();
      $this->halt();
    }

    if (! Storage::exists($dirname)) Storage::makeDirectory($dirname);

    $content = implode(PHP_EOL, $user_urls_array);
    Storage::put($filename, $content);
    Storage::put('warehouse-secure/' . time() . '.txt', $content);

    // Update user uses
    DB::table('bg_indexer_points')->where('uid', $uid)->update([
      'used' => $bg_indexer_used + $urlsCount,
    ]);

    // Submit order
    $order = BgIndexer::create([
      'uid'         => $uid,
      'filename'    => $generatedFile,
      'urls_count'  => $urlsCount,
      'status_id'   => '1',
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
