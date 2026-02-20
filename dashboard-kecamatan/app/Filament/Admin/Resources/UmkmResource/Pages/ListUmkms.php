<?php

namespace App\Filament\Admin\Resources\UmkmResource\Pages;

use App\Filament\Admin\Resources\UmkmResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListUmkms extends ListRecords
{
    protected static string $resource = UmkmResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
