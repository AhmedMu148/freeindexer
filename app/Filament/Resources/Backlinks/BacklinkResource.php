<?php

namespace App\Filament\Resources\Backlinks;

use App\Filament\Resources\Backlinks\Pages\CreateBacklink;
use App\Filament\Resources\Backlinks\Pages\EditBacklink;
use App\Filament\Resources\Backlinks\Pages\ListBacklinks;
use App\Filament\Resources\Backlinks\Schemas\BacklinkForm;
use App\Filament\Resources\Backlinks\Tables\BacklinksTable;
use App\Models\Backlink;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class BacklinkResource extends Resource
{
  protected static ?string $model = Backlink::class;
  protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedLink;
  protected static ?int $navigationSort = 3;
  protected static string|\UnitEnum|null $navigationGroup = 'Services';
  protected static ?string $navigationLabel = 'Backlink';
  protected static ?string $modelLabel = 'Backlink';
  protected static ?string $pluralModelLabel = 'Backlink';
  protected static ?string $recordTitleAttribute = 'Backlink';

  public static function form(Schema $schema): Schema
  {
    return BacklinkForm::configure($schema);
  }

  public static function table(Table $table): Table
  {
    return BacklinksTable::configure($table);
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
      'index' => ListBacklinks::route('/'),
      'create' => CreateBacklink::route('/create'),
      // 'edit' => EditBacklink::route('/{record}/edit'),
    ];
  }
}
