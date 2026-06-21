<?php

namespace App\Filament\Resources\BgIndexers;

use App\Filament\Resources\BgIndexers\Pages\CreateBgIndexer;
use App\Filament\Resources\BgIndexers\Pages\EditBgIndexer;
use App\Filament\Resources\BgIndexers\Pages\ListBgIndexers;
use App\Filament\Resources\BgIndexers\Schemas\BgIndexerForm;
use App\Filament\Resources\BgIndexers\Tables\BgIndexersTable;
use App\Models\BgIndexer;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class BgIndexerResource extends Resource
{
  protected static ?string $model = BgIndexer::class;
  protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedFire;
  protected static ?int $navigationSort = 2;
  protected static string|\UnitEnum|null $navigationGroup = 'Services';
  protected static ?string $navigationLabel = 'BG Indexer';
  protected static ?string $modelLabel = 'BG Indexer';
  protected static ?string $pluralModelLabel = 'BG Indexers';
  protected static ?string $recordTitleAttribute = 'BgIndexer';

  public static function form(Schema $schema): Schema
  {
    return BgIndexerForm::configure($schema);
  }

  public static function table(Table $table): Table
  {
    return BgIndexersTable::configure($table);
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
      'index' => ListBgIndexers::route('/'),
      'create' => CreateBgIndexer::route('/create'),
      // 'edit' => EditBgIndexer::route('/{record}/edit'),
    ];
  }
}
