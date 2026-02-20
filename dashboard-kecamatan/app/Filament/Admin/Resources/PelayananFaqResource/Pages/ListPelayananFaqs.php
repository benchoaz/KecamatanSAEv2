<?php

namespace App\Filament\Admin\Resources\PelayananFaqResource\Pages;

use App\Filament\Admin\Resources\PelayananFaqResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPelayananFaqs extends ListRecords
{
    protected static string $resource = PelayananFaqResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
