<?php

namespace App\Filament\Resources\Tickets\Schemas;

use Filament\Schemas\Schema;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Components\Section;
use Filament\Resources\Pages\EditRecord;
use Filament\Schemas\Components\View;

class TicketForm
{
  public static function configure(Schema $schema): Schema
  {
    return $schema
      ->components([
        Section::make()
          ->schema([

            TextInput::make('subject')
              ->label('Subject')
              ->required()
              ->maxLength(255)
              ->readOnly(fn($livewire) => $livewire instanceof EditRecord)
              ->dehydrated(fn($state, $livewire) => ! ($livewire instanceof EditRecord))
              ->disabled(fn($livewire) => $livewire instanceof EditRecord),

            // Select::make('status')
            //   ->label('Status')
            //   ->visible(fn(string $operation) => $operation === 'edit')
            //   ->options([
            //     'awaiting-agent'  => 'Open',
            //     'responded'       => 'Open',
            //     'solved'          => 'Open',
            //     'open'            => 'Open',
            //     'closed'          => 'Closed'
            //   ])
            //   ->default('open')
            //   ->required(),

            Select::make('status')
              ->label('Status')
              ->visible(fn(string $operation) => $operation === 'edit')
              ->options([
                'open'   => 'Open',
                'closed' => 'Closed',
              ])
              ->formatStateUsing(
                fn($state) => in_array($state, ['awaiting-agent', 'responded', 'solved', 'open'], true)
                  ? 'open'
                  : 'closed'
              )
              ->dehydrateStateUsing(fn($state) => $state)
              ->required(),

            View::make('filament.tickets.partials.chat')
              ->visible(fn(string $operation) => $operation === 'edit')
              ->columnSpanFull()
              ->extraAttributes(['class' => 'max-h-96 overflow-y-auto rounded-lg bg-white'])
              ->viewData(fn($livewire) => [
                'chat' => $livewire->chat ?? [],
              ]),

            Textarea::make('message')
              ->label('Message')
              ->rows(3)
              ->columnSpanFull(),
          ])
          ->columnSpanFull()
      ]);
  }
}
