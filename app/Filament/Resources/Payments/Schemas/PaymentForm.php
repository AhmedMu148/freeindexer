<?php

namespace App\Filament\Resources\Payments\Schemas;

use Filament\Forms;
use Filament\Schemas\Schema;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DatePicker;

class PaymentForm
{
  public static function configure(Schema $schema): Schema
  {
    return $schema
      ->components([
        Select::make('user_id')
          ->label('User')
          ->relationship('user', 'name')
          ->searchable()
          ->required(),
        TextInput::make('amount')
          ->label('Amount')
          ->required(),
        DatePicker::make('paid_at')
          ->label('Paid At')
          ->required(),
      ]);
  }
}
