<?php

namespace App\Filament\Admin\Resources\DesaResource\Pages;

use App\Filament\Admin\Resources\DesaResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListDesas extends ListRecords
{
    protected static string $resource = DesaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
