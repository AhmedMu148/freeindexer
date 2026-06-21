<?php

namespace App\Filament\Resources\Tickets\RelationManagers;

use Filament\Actions\AssociateAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\DissociateAction;
use Filament\Actions\DissociateBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Forms;
use Filament\Forms\Form;

use Filament\Tables;
use Illuminate\Support\Facades\Auth;

class MessagesRelationManager extends RelationManager
{

  protected static string $relationship = 'messages';
  protected static ?string $title = 'المحادثة';
  protected static ?string $recordTitleAttribute = 'created_at';

  // protected static string $relationship = 'messages';

  public function form(Schema $schema): Schema
  {
    return $schema
      ->components([
        Forms\Components\Textarea::make('body')
          ->label('رسالتك')
          ->rows(5)
          ->required(),
        // Forms\Components\FileUpload::make('attachments')->multiple()->directory('tickets'),
      ]);
  }

  public function table(Table $table): Table
  {
    return $table
      ->recordTitleAttribute('created_at')
      ->columns([
        Tables\Columns\TextColumn::make('user.name')
          ->label('المُرسل')
          ->grow(false),

        Tables\Columns\TextColumn::make('body')
          ->label('النص')
          ->wrap()
          ->limit(200),

        Tables\Columns\TextColumn::make('created_at')
          ->since()
          ->label('الزمن'),
      ])
      ->filters([
        //
      ])
      ->recordActions([]) // بدون تعديل/حذف لرسائل المستخدم
      ->headerActions([
        CreateAction::make(),
        AssociateAction::make(),
        // Tables\Actions\CreateAction::make()->label('إرسال رسالة'),
      ])
      ->defaultSort('created_at', 'asc')
      ->recordActions([
        EditAction::make(),
        DissociateAction::make(),
        DeleteAction::make(),
      ])
      ->toolbarActions([
        BulkActionGroup::make([
          DissociateBulkAction::make(),
          DeleteBulkAction::make(),
        ]),
      ]);
  }

  protected function mutateFormDataBeforeCreate(array $data): array
  {
    $user     = Auth::user();
    $uid      = $user['id'];
    // $data['user_id'] = auth()->id();
    $data['user_id'] = $uid;
    return $data;
  }
}
