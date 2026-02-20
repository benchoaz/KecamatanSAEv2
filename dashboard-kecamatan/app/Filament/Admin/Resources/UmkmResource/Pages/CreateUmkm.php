<?php

namespace App\Filament\Admin\Resources\UmkmResource\Pages;

use App\Filament\Admin\Resources\UmkmResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Str;

class CreateUmkm extends CreateRecord
{
    protected static string $resource = UmkmResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['source'] = 'admin';
        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
