<?php
namespace App\Jobs;
use App\Models\InventoryItem;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class LowStockAlertJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable;

    public function __construct(public InventoryItem $item) {}

    public function handle(): void
    {
        Log::warning("Stock bajo: {$this->item->nombre} (actual: {$this->item->stock_actual}, minimo: {$this->item->stock_minimo})");
    }
}
