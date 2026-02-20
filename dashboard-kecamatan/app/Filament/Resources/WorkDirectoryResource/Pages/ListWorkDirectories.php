<?php

namespace App\Filament\Resources\WorkDirectoryResource\Pages;

use App\Filament\Resources\WorkDirectoryResource;
use Filament\Resources\Pages\ListRecords;

class ListWorkDirectories extends ListRecords
{
    protected static string $resource = WorkDirectoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            \Filament\Actions\CreateAction::make(),
        ];
    }
}
