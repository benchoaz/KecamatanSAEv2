<?php

namespace App\Filament\Resources\WorkDirectoryResource\Pages;

use App\Filament\Resources\WorkDirectoryResource;
use Filament\Resources\Pages\CreateRecord;

class CreateWorkDirectory extends CreateRecord
{
    protected static string $resource = WorkDirectoryResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
