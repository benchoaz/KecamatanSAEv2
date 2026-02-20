<?php

namespace App\Filament\Admin\Resources\UmkmResource\Pages;

use App\Filament\Admin\Resources\UmkmResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditUmkm extends EditRecord
{
    protected static string $resource = UmkmResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
