<?php

namespace App\Filament\Resources\Tickets\Pages;

use App\Filament\Resources\Tickets\TicketResource;
use App\Models\Ticket;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\DB;

// class CreateTicket extends CreateRecord
// {
//     protected static string $resource = TicketResource::class;
// }


class CreateTicket extends CreateRecord
{

  protected static bool $canCreateAnother = false;
  protected static string $resource = TicketResource::class;
  protected ?string $firstMessage = null;

  protected function handleRecordCreation(array $data): Ticket
  {
    $user       = Auth::user();
    $uid        = $user['id'];
    $username   = $user['name'];
    if (!$uid) {
      Notification::make()
        ->danger()
        ->title('Session expired')
        ->body('Please login again.')
        ->send();
      $this->halt();
    }

    $message            = $data['message'];
    $data['uid']        = $uid;
    $ticket             = Ticket::create($data);

    $x                  = [];
    $x['ticket_id']     = $ticket->id;
    $x['uid']           = $uid;
    $x['body']          = $message;
    $x['attachments']   = null;
    $x['created_at']    = now();
    $x['updated_at']    = now();
    DB::table('ticket_messages')->insert($x);

    return $ticket;
  }

  protected function afterCreate(): void
  {
    $user     = Auth::user();
    $uid      = $user['id'];
    if ($this->firstMessage) {
      $this->record->messages()->create([
        // 'user_id' => auth()->id(),
        'user_id' => $uid,
        'body'    => $this->firstMessage,
      ]);
    }
  }

  protected function getRedirectUrl(): string
  {
    return $this->getResource()::getUrl('edit', ['record' => $this->record]);
  }
}
