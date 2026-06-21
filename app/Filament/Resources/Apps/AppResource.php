<?php

namespace App\Filament\Resources\Apps;

use App\Filament\Resources\Apps\Pages\CreateApp;
use App\Filament\Resources\Apps\Pages\EditApp;
use App\Filament\Resources\Apps\Pages\ListApps;
use App\Filament\Resources\Apps\Schemas\AppForm;
use App\Filament\Resources\Apps\Tables\AppsTable;
use App\Models\App;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class AppResource extends Resource
{
  protected static ?string $model = App::class;
  protected static ?int $navigationSort = 10;
  protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedWindow;
  protected static ?string $modelLabel = 'App License Key';
  protected static ?string $pluralModelLabel = 'App License Key';
  protected static ?string $recordTitleAttribute = 'App License Key';
  protected static ?string $navigationLabel = 'App License Key';

  public static function form(Schema $schema): Schema
  {
    return AppForm::configure($schema);
  }

  public static function table(Table $table): Table
  {
    return AppsTable::configure($table);
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
      'index' => ListApps::route('/'),
      // 'create' => CreateApp::route('/create'),
      // 'edit' => EditApp::route('/{record}/edit'),
    ];
  }
}
