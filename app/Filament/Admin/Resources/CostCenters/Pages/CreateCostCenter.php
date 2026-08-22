<?php

namespace App\Filament\Admin\Resources\CostCenters\Pages;

use App\Filament\Admin\Resources\CostCenters\CostCenterResource;
use Filament\Resources\Pages\CreateRecord;

class CreateCostCenter extends CreateRecord
{
    protected static string $resource = CostCenterResource::class;
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['company_id'] = auth()->user()->company_id;

        return $data;
    }
}
