<?php

namespace App\Filament\Admin\Resources\UmkmLocalResource\Pages;

use App\Filament\Admin\Resources\UmkmLocalResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditUmkmLocal extends EditRecord
{
    protected static string $resource = UmkmLocalResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
