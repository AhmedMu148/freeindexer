<?php

namespace App\Filament\Resources\Backlinks\Schemas;

use Filament\Forms;
use Filament\Schemas\Schema;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Components\Section;

class BacklinkForm
{
  public static function configure(Schema $schema): Schema
  {
    return $schema
      ->components([
        Section::make()
          ->schema([
            TextInput::make('project_name')
              ->required(),
            TextInput::make('qty')
              ->minValue(100)
              ->numeric()
              ->required(),
            Textarea::make('links')
              ->required(),
            Textarea::make('keywords')
              ->required(),
          ])
          ->columnSpanFull(),
      ])
      ->statePath('data');
  }
}
