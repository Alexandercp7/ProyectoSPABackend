<?php
namespace Database\Seeders;
use App\Models\Client;
use App\Models\InventoryItem;
use App\Models\Vehicle;
use App\Models\WorkOrder;
use App\Models\User;
use Illuminate\Database\Seeder;

class DemoDataSeeder extends Seeder
{
    public function run(): void
    {
        $client1 = Client::firstOrCreate(['telefono' => '5551234567'], [
            'nombre'  => 'Juan Perez Garcia',
            'correo'  => 'juan@demo.com',
        ]);
        $client2 = Client::firstOrCreate(['telefono' => '5559876543'], [
            'nombre'  => 'Maria Lopez Torres',
            'correo'  => 'maria@demo.com',
        ]);

        $v1 = Vehicle::firstOrCreate(['placas' => 'ABC123'], [
            'client_id' => $client1->id,
            'marca' => 'Toyota', 'modelo' => 'Corolla', 'anio' => 2020,
            'kilometraje_actual' => 45000,
        ]);
        $v2 = Vehicle::firstOrCreate(['placas' => 'XYZ789'], [
            'client_id' => $client2->id,
            'marca' => 'Honda', 'modelo' => 'CRV', 'anio' => 2019,
            'kilometraje_actual' => 78000,
        ]);

        $wo1Exists = WorkOrder::where('id', 'WO-0001')->exists();
        if (!$wo1Exists) {
            $wo = WorkOrder::create([
                'id'           => 'WO-0001',
                'client_id'    => $client1->id,
                'vehicle_id'   => $v1->id,
                'status'       => 'En Proceso',
                'priority'     => 'Alta',
                'tipo_vehiculo'=> 'Auto',
                'problema'     => 'Falla en frenos traseros, ruido al frenar',
                'fecha_ingreso'=> now(),
            ]);
            $wo->timeline()->create(['descripcion' => 'Orden de trabajo creada (demo).']);
        }

        $items = [
            ['nombre'=>'Aceite Mobil 5W30','tipo'=>'Consumible','stock_actual'=>20,'stock_minimo'=>5,'precio'=>120,'precio_venta'=>180],
            ['nombre'=>'Filtro de aceite Toyota','tipo'=>'Parte en venta','stock_actual'=>8,'stock_minimo'=>3,'precio'=>85,'precio_venta'=>150],
            ['nombre'=>'Balatas Brembo delanteras','tipo'=>'Parte en venta','stock_actual'=>6,'stock_minimo'=>2,'precio'=>350,'precio_venta'=>600],
            ['nombre'=>'Multimetro digital','tipo'=>'Herramienta','stock_actual'=>2,'stock_minimo'=>1,'precio'=>800,'precio_venta'=>0],
        ];
        foreach ($items as $item) {
            InventoryItem::firstOrCreate(['nombre' => $item['nombre']], $item);
        }

        $this->command->info('Datos de demo creados.');
    }
}
