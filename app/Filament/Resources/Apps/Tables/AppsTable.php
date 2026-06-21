<?php

namespace App\Filament\Resources\Apps\Tables;

use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;

class AppsTable
{
  public static function configure(Table $table): Table
  {
    return $table
      ->columns([
        TextColumn::make('id')
          ->label('#ID')
          ->formatStateUsing(fn($state) => '#' . $state),
        TextColumn::make('payment_id')
          ->label('Payment ID')
          ->formatStateUsing(fn($state) => '#' . $state),
        TextColumn::make('key')
          ->label('License Key'),
        TextColumn::make('created_at')
          ->label('Date')
          ->since()
          ->tooltip(fn($record) => $record->updated_at?->format('Y-m-d H:i:s')),
      ])
      ->defaultSort('id', 'desc');
    // ->filters([
    //   //
    // ])
    // ->recordActions([
    //   // EditAction::make(),
    // ])
    // ->toolbarActions([
    //   // BulkActionGroup::make([
    //   //   DeleteBulkAction::make(),
    //   // ]),
    // ]);
  }
}
