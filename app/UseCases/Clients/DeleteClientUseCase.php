<?php
namespace App\UseCases\Clients;
use App\Exceptions\ClientHasActiveOrdersException;
use App\Models\Client;

class DeleteClientUseCase
{
    public function execute(int $id): void
    {
        $client = Client::with('workOrders')->findOrFail($id);

        $active = $client->workOrders->filter(fn($wo) =>
            !in_array($wo->status, ['Terminado','Entregado'])
        );

        if ($active->isNotEmpty()) {
            throw new ClientHasActiveOrdersException(
                "El cliente tiene {$active->count()} orden(es) de trabajo activa(s)."
            );
        }

        $client->delete();
    }
}
