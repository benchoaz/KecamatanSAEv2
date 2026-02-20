<?php

namespace App\Filament\Admin\Resources\PelayananFaqResource\Pages;

use App\Filament\Admin\Resources\PelayananFaqResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPelayananFaq extends EditRecord
{
    protected static string $resource = PelayananFaqResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
