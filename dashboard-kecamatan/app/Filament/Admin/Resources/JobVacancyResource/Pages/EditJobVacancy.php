<?php

namespace App\Filament\Admin\Resources\JobVacancyResource\Pages;

use App\Filament\Admin\Resources\JobVacancyResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditJobVacancy extends EditRecord
{
    protected static string $resource = JobVacancyResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
