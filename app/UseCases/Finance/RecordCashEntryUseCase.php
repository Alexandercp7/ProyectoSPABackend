<?php
namespace App\UseCases\Finance;
use App\Models\DailyCashEntry;

class RecordCashEntryUseCase
{
    public function execute(array $data, int $userId): DailyCashEntry
    {
        return DailyCashEntry::create(array_merge($data, ['usuario_id' => $userId]));
    }
}
