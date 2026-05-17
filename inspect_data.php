$clients = App\Models\Client::withCount(["workOrders", "vehicles"])->get(["id", "nombre", "telefono", "correo"]);
echo "--- CLIENTS ---\n";
foreach($clients as $c) {
    echo "ID: " . $c->id . " | Name: " . $c->nombre . " | Tel: " . $c->telefono . " | Email: " . $c->correo . " | WOs: " . $c->work_orders_count . " | Vehs: " . $c->vehicles_count . "\n";
}

$workOrders = App\Models\WorkOrder::with(["client:id,nombre", "vehicle:id,placas,client_id"])->get(["id", "client_id", "vehicle_id", "status"]);
echo "\n--- WORK ORDERS ---\n";
foreach($workOrders as $wo) {
    $cN = $wo->client->nombre ?? "N/A";
    $vP = $wo->vehicle->placas ?? "N/A";
    $vC = $wo->vehicle->client_id ?? "N/A";
    echo "ID: " . $wo->id . " | CID: " . $wo->client_id . " | Client: " . $cN . " | VID: " . $wo->vehicle_id . " | V.Placas: " . $vP . " | V.CID: " . $vC . " | Status: " . $wo->status . "\n";
}

echo "\n--- DUPLICATES ---\n";
$dN = App\Models\Client::select("nombre")->groupBy("nombre")->havingRaw("COUNT(*) > 1")->pluck("nombre");
$dP = App\Models\Client::select("telefono")->whereNotNull("telefono")->where("telefono", "!=", "")->groupBy("telefono")->havingRaw("COUNT(*) > 1")->pluck("telefono");
echo "Duplicate Names: " . $dN->implode(", ") . "\n";
echo "Duplicate Phones: " . $dP->implode(", ") . "\n";
