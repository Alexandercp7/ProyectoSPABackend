<?php
namespace App\UseCases\Inventory;
use App\Models\InventoryItem;

class CreateInventoryItemUseCase
{
    public function execute(array $data, int $userId): InventoryItem
    {
        $item = InventoryItem::create($data);

        $item->movements()->create([
            'tipo'            => 'entrada_inicial',
            'cantidad'        => $item->stock_actual,
            'stock_resultante'=> $item->stock_actual,
            'motivo'          => 'Stock inicial',
            'usuario_id'      => $userId,
        ]);

        return $item;
    }
}
