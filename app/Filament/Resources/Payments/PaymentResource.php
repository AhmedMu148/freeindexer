<?php

namespace App\Filament\Resources\Payments;

use App\Filament\Resources\Payments\Pages\CreatePayment;
use App\Filament\Resources\Payments\Pages\EditPayment;
use App\Filament\Resources\Payments\Pages\ListPayments;
use App\Filament\Resources\Payments\Schemas\PaymentForm;
use App\Filament\Resources\Payments\Tables\PaymentsTable;
use App\Models\Payment;
use App\Models\PymPayment;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class PaymentResource extends Resource
{
  protected static ?string $model = PymPayment::class;

  protected static ?int $navigationSort = 6;

  // protected static ?string $navigationIcon = 'heroicon-o-lifebuoy';
  protected static ?string $navigationLabel = 'Payments';

  protected static ?string $modelLabel = 'Payments';

  protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedShoppingCart;

  public static function shouldRegisterNavigation(): bool
  {
    return false;
  }

  public static function form(Schema $schema): Schema
  {
    return PaymentForm::configure($schema);
  }

  public static function table(Table $table): Table
  {
    return PaymentsTable::configure($table);
  }

  public static function getRelations(): array
  {
    return [
      //
    ];
  }

  public static function getEloquentQuery(): Builder
  {
    return parent::getEloquentQuery()
      ->where('uid', Auth::id());
  }

  public static function getPages(): array
  {
    return [
      'index' => ListPayments::route('/'),
      // 'create' => CreatePayment::route('/create'),
      // 'edit' => EditPayment::route('/{record}/edit'),
    ];
  }
}
