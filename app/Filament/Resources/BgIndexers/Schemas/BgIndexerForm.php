<?php

namespace App\Filament\Resources\BgIndexers\Schemas;

use Filament\Forms;
use Filament\Schemas\Schema;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Components\Section;

class BgIndexerForm
{

  public static function configure(Schema $schema): Schema
  {
    return $schema
      ->components([
        Section::make()
          ->schema([
            Textarea::make('urls')
              ->label('Links: (one per line)')
              ->required()
              ->rows(10)
              ->placeholder('Enter all links here with line break between each link...')
              ->dehydrated(),
          ])
          ->columnSpanFull(),
      ])
      ->statePath('data');
  }
}
