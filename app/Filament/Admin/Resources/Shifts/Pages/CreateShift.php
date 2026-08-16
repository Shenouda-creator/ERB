<?php

namespace App\Filament\Admin\Resources\Shifts\Pages;

use App\Filament\Admin\Resources\Shifts\ShiftResource;
use Filament\Resources\Pages\CreateRecord;

class CreateShift extends CreateRecord
{
    protected static string $resource = ShiftResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['company_id'] = auth()->user()->company_id;

        return $data;
    }
}
