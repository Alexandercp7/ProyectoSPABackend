<?php
namespace Database\Seeders;

use App\Models\AccountsPayable;
use App\Models\AccountsReceivable;
use App\Models\Client;
use App\Models\Contact;
use App\Models\DailyCashEntry;
use App\Models\Vehicle;
use App\Models\WorkOrder;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class FinanceSeeder extends Seeder
{
    public function run(): void
    {
        $client1 = Client::firstOrCreate(['telefono' => '5551234567'], [
            'nombre' => 'Juan Perez Garcia', 'correo' => 'juan@demo.com',
        ]);
        $client2 = Client::firstOrCreate(['telefono' => '5559876543'], [
            'nombre' => 'Maria Lopez Torres', 'correo' => 'maria@demo.com',
        ]);
        $client3 = Client::firstOrCreate(['telefono' => '5554561230'], [
            'nombre' => 'Carlos Ramirez Vega', 'correo' => 'carlos@demo.com',
        ]);
        $client4 = Client::firstOrCreate(['telefono' => '5558889990'], [
            'nombre' => 'Ana Flores Mendez', 'correo' => 'ana@demo.com',
        ]);

        $v1 = Vehicle::firstOrCreate(['placas' => 'ABC123'], [
            'client_id' => $client1->id, 'marca' => 'Toyota', 'modelo' => 'Corolla',
            'anio' => 2020, 'kilometraje_actual' => 45000,
        ]);
        $v2 = Vehicle::firstOrCreate(['placas' => 'XYZ789'], [
            'client_id' => $client2->id, 'marca' => 'Honda', 'modelo' => 'CRV',
            'anio' => 2019, 'kilometraje_actual' => 78000,
        ]);
        $v3 = Vehicle::firstOrCreate(['placas' => 'DEF456'], [
            'client_id' => $client3->id, 'marca' => 'Nissan', 'modelo' => 'Sentra',
            'anio' => 2021, 'kilometraje_actual' => 32000,
        ]);
        $v4 = Vehicle::firstOrCreate(['placas' => 'GHI321'], [
            'client_id' => $client4->id, 'marca' => 'Volkswagen', 'modelo' => 'Jetta',
            'anio' => 2022, 'kilometraje_actual' => 18000,
        ]);

        $this->seedAccountsReceivable($client1, $client2, $client3, $client4, $v1, $v2, $v3, $v4);
        $this->seedAccountsPayable();
        $this->seedDailyCashEntries();

        $this->command->info('Datos financieros de demo creados.');
    }

    private function seedAccountsReceivable($c1, $c2, $c3, $c4, $v1, $v2, $v3, $v4): void
    {
        $workOrders = [
            [
                'id' => 'WO-FIN-01',
                'client' => $c1, 'vehicle' => $v1,
                'problema' => 'Cambio de aceite y filtros completo',
                'fecha_ingreso' => Carbon::now()->subDays(35),
                'monto' => 850.00, 'monto_recibido' => 850.00,
                'estado' => 'Pagado',
                'vence_dias' => -5,
            ],
            [
                'id' => 'WO-FIN-02',
                'client' => $c2, 'vehicle' => $v2,
                'problema' => 'Reparacion de sistema de frenos traseros',
                'fecha_ingreso' => Carbon::now()->subDays(20),
                'monto' => 2400.00, 'monto_recibido' => 1200.00,
                'estado' => 'Parcial',
                'vence_dias' => 10,
            ],
            [
                'id' => 'WO-FIN-03',
                'client' => $c3, 'vehicle' => $v3,
                'problema' => 'Afinacion general y revision completa',
                'fecha_ingreso' => Carbon::now()->subDays(50),
                'monto' => 1800.00, 'monto_recibido' => 0.00,
                'estado' => 'Vencido',
                'vence_dias' => -15,
            ],
            [
                'id' => 'WO-FIN-04',
                'client' => $c1, 'vehicle' => $v1,
                'problema' => 'Cambio de 4 llantas y balanceo',
                'fecha_ingreso' => Carbon::now()->subDays(5),
                'monto' => 3200.00, 'monto_recibido' => 0.00,
                'estado' => 'Pendiente',
                'vence_dias' => 25,
            ],
            [
                'id' => 'WO-FIN-05',
                'client' => $c4, 'vehicle' => $v4,
                'problema' => 'Diagnostico electronico y reparacion ECU',
                'fecha_ingreso' => Carbon::now()->subDays(25),
                'monto' => 4500.00, 'monto_recibido' => 4500.00,
                'estado' => 'Pagado',
                'vence_dias' => -10,
            ],
            [
                'id' => 'WO-FIN-06',
                'client' => $c2, 'vehicle' => $v2,
                'problema' => 'Cambio de suspension delantera',
                'fecha_ingreso' => Carbon::now()->subDays(8),
                'monto' => 5800.00, 'monto_recibido' => 2900.00,
                'estado' => 'Parcial',
                'vence_dias' => 22,
            ],
        ];

        foreach ($workOrders as $wo) {
            if (!WorkOrder::where('id', $wo['id'])->exists()) {
                WorkOrder::create([
                    'id'            => $wo['id'],
                    'client_id'     => $wo['client']->id,
                    'vehicle_id'    => $wo['vehicle']->id,
                    'status'        => 'Entregado',
                    'priority'      => 'Media',
                    'tipo_vehiculo' => 'Auto',
                    'problema'      => $wo['problema'],
                    'fecha_ingreso' => $wo['fecha_ingreso'],
                ]);
            }

            $fechaEmision = $wo['fecha_ingreso']->toDateString();
            $fechaVencimiento = Carbon::now()->addDays($wo['vence_dias'])->toDateString();

            AccountsReceivable::firstOrCreate(
                ['work_order_id' => $wo['id']],
                [
                    'client_id'         => $wo['client']->id,
                    'monto'             => $wo['monto'],
                    'monto_recibido'    => $wo['monto_recibido'],
                    'monto_pendiente'   => $wo['monto'] - $wo['monto_recibido'],
                    'fecha_emision'     => $fechaEmision,
                    'fecha_vencimiento' => $fechaVencimiento,
                    'estado'            => $wo['estado'],
                ]
            );
        }
    }

    private function seedAccountsPayable(): void
    {
        $p1 = Contact::firstOrCreate(['telefono' => '5552223331'], [
            'nombre' => 'AutoPiezas del Norte SA', 'correo' => 'ventas@autopiezas.demo',
        ]);
        $p2 = Contact::firstOrCreate(['telefono' => '5554445552'], [
            'nombre' => 'RefacSA de CV', 'correo' => 'contacto@refacsa.demo',
        ]);
        $p3 = Contact::firstOrCreate(['telefono' => '5556667773'], [
            'nombre' => 'Herramientas Industriales MX', 'correo' => 'ventas@herramientas.demo',
        ]);

        $payables = [
            [
                'contact_id'       => $p1->id,
                'concepto'         => 'Compra de refacciones mayo 2026',
                'monto'            => 4500.00,
                'monto_pagado'     => 4500.00,
                'fecha_vencimiento'=> Carbon::now()->subDays(5)->toDateString(),
                'estado'           => 'Pagado',
            ],
            [
                'contact_id'       => $p2->id,
                'concepto'         => 'Filtros, lubricantes y consumibles',
                'monto'            => 2800.00,
                'monto_pagado'     => 1400.00,
                'fecha_vencimiento'=> Carbon::now()->addDays(10)->toDateString(),
                'estado'           => 'Parcial',
            ],
            [
                'contact_id'       => $p3->id,
                'concepto'         => 'Equipo de diagnostico electronico',
                'monto'            => 8500.00,
                'monto_pagado'     => 0.00,
                'fecha_vencimiento'=> Carbon::now()->addDays(20)->toDateString(),
                'estado'           => 'Pendiente',
            ],
            [
                'contact_id'       => $p1->id,
                'concepto'         => 'Balatas, discos y componentes frenos',
                'monto'            => 3200.00,
                'monto_pagado'     => 0.00,
                'fecha_vencimiento'=> Carbon::now()->subDays(8)->toDateString(),
                'estado'           => 'Pendiente',
            ],
            [
                'contact_id'       => null,
                'concepto'         => 'Renta mensual local taller',
                'monto'            => 6000.00,
                'monto_pagado'     => 6000.00,
                'fecha_vencimiento'=> Carbon::now()->subDays(1)->toDateString(),
                'estado'           => 'Pagado',
            ],
            [
                'contact_id'       => $p2->id,
                'concepto'         => 'Aceites y fluidos junio 2026',
                'monto'            => 1950.00,
                'monto_pagado'     => 0.00,
                'fecha_vencimiento'=> Carbon::now()->addDays(5)->toDateString(),
                'estado'           => 'Pendiente',
            ],
        ];

        foreach ($payables as $p) {
            AccountsPayable::firstOrCreate(
                ['concepto' => $p['concepto']],
                [
                    'contact_id'        => $p['contact_id'],
                    'monto'             => $p['monto'],
                    'monto_pagado'      => $p['monto_pagado'],
                    'monto_pendiente'   => $p['monto'] - $p['monto_pagado'],
                    'fecha_vencimiento' => $p['fecha_vencimiento'],
                    'estado'            => $p['estado'],
                ]
            );
        }
    }

    private function seedDailyCashEntries(): void
    {
        // Data per month (monthsAgo => [ingresos, egresos])
        $monthsData = [
            5 => [42000, 18000],
            4 => [38000, 15500],
            3 => [51000, 21000],
            2 => [48000, 19500],
            1 => [55000, 22000],
            0 => [63000, 25000],
        ];

        $conceptosIngreso = ['Cobro servicios taller', 'Pago OT cliente', 'Ingreso por servicio', 'Cobro reparacion'];
        $conceptosEgreso  = ['Pago proveedor refacciones', 'Gastos operativos', 'Compra materiales', 'Pago servicios'];
        $metodos          = ['Efectivo', 'Transferencia', 'Tarjeta'];

        foreach ($monthsData as $monthsAgo => $data) {
            $inicio = Carbon::now()->subMonths($monthsAgo)->startOfMonth();
            $fin    = $inicio->copy()->endOfMonth();

            $yaExiste = DailyCashEntry::whereBetween('fecha', [
                $inicio->toDateString(),
                $fin->toDateString(),
            ])->exists();

            if ($yaExiste) continue;

            [$totalIngresos, $totalEgresos] = $data;

            // 4 entradas de ingreso distribuidas en el mes
            $montoIngreso = $totalIngresos / 4;
            for ($i = 0; $i < 4; $i++) {
                DailyCashEntry::create([
                    'fecha'      => $inicio->copy()->addDays($i * 6 + 1)->toDateString(),
                    'concepto'   => $conceptosIngreso[$i],
                    'tipo'       => 'Ingreso',
                    'monto'      => $montoIngreso,
                    'metodo_pago'=> $metodos[$i % 3],
                ]);
            }

            // 3 entradas de egreso distribuidas en el mes
            $montoEgreso = $totalEgresos / 3;
            for ($i = 0; $i < 3; $i++) {
                DailyCashEntry::create([
                    'fecha'      => $inicio->copy()->addDays($i * 8 + 4)->toDateString(),
                    'concepto'   => $conceptosEgreso[$i],
                    'tipo'       => 'Egreso',
                    'monto'      => $montoEgreso,
                    'metodo_pago'=> $metodos[$i % 3],
                ]);
            }
        }
    }
}
