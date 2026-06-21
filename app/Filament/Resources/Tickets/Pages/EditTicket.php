<?php

namespace App\Filament\Resources\Tickets\Pages;

use App\Filament\Resources\Tickets\TicketResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Actions\Action;
use Filament\Actions\CancelAction;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Contracts\Support\Htmlable;
use Filament\Forms\Form;
use App\Mail\TemplatedEmail;
use Illuminate\Support\Facades\Mail;

class EditTicket extends EditRecord
{
  protected static string $resource = TicketResource::class;

  public array $chat = [];

  public function getTitle(): string|Htmlable
  {
    return 'Ticket ID: #' . $this->record->getKey();
  }

  public function getBreadcrumbs(): array
  {
    return [
      // 'url' => 'Tickets',
      static::getResource()::getUrl('index') => 'Tickets',
      null => '#' . $this->record->getKey(),
    ];
  }

  protected function getHeaderActions(): array
  {
    return [
      // ViewAction::make(),
      // DeleteAction::make(),
    ];
  }

  protected function getSaveFormAction(): Action
  {
    return Action::make('save')
      ->label('Add Reply')
      ->submit('save');
  }

  protected function handleRecordUpdate(Model $record, array $data): Model
  {
    $user               = Auth::user();
    $uid                = $user['id'];
    $email              = $user['email'];
    $username           = $user['name'];
    $ticketID           = $record->id;
    $x                  = [];
    $x['ticket_id']     = $ticketID;
    $x['uid']           = $uid;
    $x['body']          = $data['message'];
    $x['attachments']   = null;
    $x['created_at']    = now();
    $x['updated_at']    = now();
    DB::table('ticket_messages')->insert($x);

    // $ticketUrl = url("/dashboard/tickets/{$ticketID}/edit");
    // Mail::to($email)->send(
    //   new TemplatedEmail(
    //     template: 'ticket-update',
    //     subjectLine: 'Ticket update successfully',
    //     data: [
    //       'ticketId'            => $ticketID,
    //       'lastMessageSnippet'  => $data['message'],
    //       'ticketUrl'           => $ticketUrl,
    //     ]
    //   )
    // );

    $record->update($data);
    return $record;
  }

  protected function fillForm(): void
  {
    $user     = Auth::user();
    $uid      = $user->id;
    $username = $user->username;
    $msgs = DB::table('ticket_messages')
      ->where('ticket_id', $this->record->id)
      ->select('uid', 'body', 'created_at')
      ->orderBy('id')
      ->get();

    $this->chat = $msgs->map(function ($m) use ($uid, $username) {
      $c = Carbon::parse($m->created_at);
      return [
        'me'   => (int)$m->uid === (int)$uid,
        'name' => (int)$m->uid === (int)$uid ? $username : 'Support',
        'body' => (string)$m->body,
        'date' => $c->isoFormat('Y-MM-DD'),
        'time' => $c->format('h:i A'),
      ];
    })->all();
    $this->form->fill([
      'subject'  => $this->record->subject,
      'priority' => $this->record->priority,
      'status'   => $this->record->status,
      'message'  => '',
    ]);
  }

  protected function getRedirectUrl(): string
  {
    return $this->getResource()::getUrl('edit', ['record' => $this->record]);
  }
}
