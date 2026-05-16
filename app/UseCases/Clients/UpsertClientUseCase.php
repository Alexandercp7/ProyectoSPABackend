<?php
namespace App\UseCases\Clients;
use App\Models\Client;

class UpsertClientUseCase
{
    public function execute(array $data, ?int $id = null): Client
    {
        if ($id) {
            $client = Client::findOrFail($id);
            $client->update($data);
            return $client->fresh();
        }
        return Client::create($data);
    }
}
