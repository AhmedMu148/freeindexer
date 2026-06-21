<?php

namespace App\Filament\Resources\Payments\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;

class PaymentsTable
{
  public static function configure(Table $table): Table
  {
    return $table
      ->columns([
        TextColumn::make('id')
          ->label('#ID')
          ->searchable()
          ->formatStateUsing(fn($state) => '#' . $state),
        // TextColumn::make('plan.name')
        //   ->label('Plan Name')
        //   ->numeric(),
        TextColumn::make('txn')
          ->label('TXN')
          ->searchable(),
        TextColumn::make('amount')
          ->label('Amount')
          ->formatStateUsing(fn($state) => '$' . number_format($state, 2)),
        TextColumn::make('gateway.name')
          ->label('Gateway'),
        TextColumn::make('status')
          ->label('Status')
          ->badge()
          ->formatStateUsing(fn(string $state): string => match ($state) {
            '1' => 'Pending',
            '2' => 'Processing',
            '3' => 'Completed',
            '4' => 'Canceled',
            default => $state,
          })
          ->color(fn(string $state): string => match ($state) {
            '1' => 'warning',
            '2' => 'primary',
            '3' => 'success',
            '4' => 'danger',
            default => 'gray',
          }),
        TextColumn::make('updated_at')
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
