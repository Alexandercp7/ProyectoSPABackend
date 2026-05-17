<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PaymentsAgendaSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('payments_agenda')->insert([
            [
                'concepto'            => 'Renta mensual local',
                'tipo'                => 'Periódico',
                'categoria'           => 'Renta',
                'fecha_vencimiento'   => now()->addDays(5)->toDateString(),
                'monto_presupuestado' => 15000.00,
                'monto_pagado'        => 0.00,
                'comprobante_url'     => null,
                'notas'               => 'Pago mensual del local comercial.',
                'created_at'          => now(),
                'updated_at'          => now(),
            ],
            [
                'concepto'            => 'Suscripción software contabilidad',
                'tipo'                => 'Periódico',
                'categoria'           => 'Suscripciones',
                'fecha_vencimiento'   => now()->addDays(12)->toDateString(),
                'monto_presupuestado' => 2500.00,
                'monto_pagado'        => 0.00,
                'comprobante_url'     => null,
                'notas'               => 'Licencia anual de software de contabilidad.',
                'created_at'          => now(),
                'updated_at'          => now(),
            ],
        ]);
    }
}
