<?php

namespace App\Filament\Resources\WorkLogResource\Pages;

use App\Filament\Resources\WorkLogResource;
use App\Models\WorkLog;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model as ModelBase;

class CreateWorkLog extends CreateRecord
{
    protected static string $resource = WorkLogResource::class;

    protected function handleRecordCreation(array $data): ModelBase
    {
        $items = $data['items'] ?? [];
        unset($data['items']);

        $createdLogs = [];
        foreach ($items as $item) {
            $worklog = WorkLog::create([
                'buyer_id'      => $data['buyer_id'],
                'description'   => $data['description'],
                'start'         => $item['start'],
                'end'           => $item['end'] ?? null,
                'unit_amount'   => $item['unit_amount'] ?? null,
                ]);
            $createdLogs[] = $worklog;
        }
        return $createdLogs[0];
    }

    /**
     * Custom success message to indicate multiple records were created
     */
    protected function getCreatedNotificationTitle(): ?string
    {
        $items = $this->data['items'] ?? [];
        $count = count($items);

        if ($count > 1) {
            return $count." ".__('worklogs.worklogs_has_been_created');
        }

        return parent::getCreatedNotificationTitle();
    }
}
