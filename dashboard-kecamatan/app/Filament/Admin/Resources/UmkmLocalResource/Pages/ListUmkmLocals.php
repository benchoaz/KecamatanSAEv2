<?php

namespace App\Filament\Admin\Resources\UmkmLocalResource\Pages;

use App\Filament\Admin\Resources\UmkmLocalResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListUmkmLocals extends ListRecords
{
    protected static string $resource = UmkmLocalResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
