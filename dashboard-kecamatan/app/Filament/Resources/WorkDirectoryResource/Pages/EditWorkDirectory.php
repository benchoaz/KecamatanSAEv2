<?php

namespace App\Filament\Resources\WorkDirectoryResource\Pages;

use App\Filament\Resources\WorkDirectoryResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditWorkDirectory extends EditRecord
{
    protected static string $resource = WorkDirectoryResource::class;

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
