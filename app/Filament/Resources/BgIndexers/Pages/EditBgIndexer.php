<?php

namespace App\Filament\Resources\BgIndexers\Pages;

use App\Filament\Resources\BgIndexers\BgIndexerResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditBgIndexer extends EditRecord
{
    protected static string $resource = BgIndexerResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
