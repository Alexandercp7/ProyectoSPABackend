<?php
namespace Database\Seeders;

use App\Models\PriceItem;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PriceItemsSeeder extends Seeder
{
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        PriceItem::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $automotriz = [
            // ── Mecánica General ─────────────────────────────────────────────
            [1,  'Mecánica General', 'Eléctrico',   'Bobinas',     'Cambio de bobinas',                                      null, null, 400,   600,   800,   0],
            [2,  'Mecánica General', 'Eléctrico',   'Marchas',     'Desmontaje y montaje de marcha',                          null, null, 800,   1000,  1200,  0],
            [3,  'Mecánica General', 'Eléctrico',   'Alternador',  'Desmontaje y montaje de alternador',                      null, null, 800,   1000,  1200,  0],
            [4,  'Mecánica General', 'Eléctrico',   'Bandas',      'Cambio de banda de alternador',                           null, null, 500,   600,   700,   0],
            [41, 'Mecánica General', 'Transmisión', 'Cajas',       'Desmontaje y montaje caja transmisión',                   null, null, 2800,  3400,  4000,  0],
            [42, 'Mecánica General', 'Transmisión', 'Diferencial', 'Cambio de diferencial',                                   null, null, 3500,  4000,  4500,  0],
            [43, 'Mecánica General', 'Transmisión', 'Soportes',    'Cambio de 1 soporte de caja',                             null, null, 500,   600,   700,   0],

            // ── Mecánica Rápida ───────────────────────────────────────────────
            [100,'Mecánica Rápida', 'Balatas',      '',            'Cambio de par de balatas delanteras o traseras (frenos disco)', null, null, 500, 600, 700, 0],
            [101,'Mecánica Rápida', 'Balatas',      '',            'Cambio de par de balatas traseras (frenos tambor)',              null, null, 600, 700, 800, 0],
            [102,'Mecánica Rápida', 'Discos',       '',            'Cambio de disco delantero o trasero — precio por pieza',         null, null, 300, 400, 500, 0],
            [103,'Mecánica Rápida', 'Tambores',     '',            'Cambio de tambor trasero — precio por pieza',                    null, null, 400, 500, 600, 0],
            [104,'Mecánica Rápida', 'Rectificación','',            'Rectificación de 1 disco o tambor (sin desmontaje)',             null, null, 300, 350, 400, 0],
            [105,'Mecánica Rápida', 'Rectificación','',            'Rectificación de 1 disco o tambor (con desmontaje)',             null, null, 600, 700, 800, 0],
            [106,'Mecánica Rápida', 'Cilindro',     '',            'Cambio de cilindro de freno — precio por pieza',                 null, null, 600, 700, 800, 0],
            [107,'Mecánica Rápida', 'Pistones',     '',            'Revisión, engrasado o cambio de pistones y retenes (1 lado)',    null, null, 600, 800, 1000, 0],
            [108,'Mecánica Rápida', 'Bombas',       '',            'Cambio de bomba de frenos',                                      null, null, 800, 900, 1000, 0],
            [109,'Mecánica Rápida', 'Purgado',      '',            'Purgado de frenos',                                              null, null, 500, 600, 700, 0],
            [110,'Mecánica Rápida', 'Limpieza',     '',            'Servicio de limpieza y ajuste de par de frenos disco',           null, null, 500, 600, 700, 0],
            [111,'Mecánica Rápida', 'Ajuste',       '',            'Servicio de limpieza y ajuste de par de frenos tambor',          null, null, 600, 700, 800, 0],
            [112,'Mecánica Rápida', 'Clutch',       '',            'Cambio de clutch',                                               null, null, 2800, 3400, 4000, 0],
            [113,'Mecánica Rápida', 'Amortiguador', '',            'Cambio de base de amortiguador (con resorte)',                   null, null, 600, 700, 800, 0],
            [114,'Mecánica Rápida', 'Amortiguador', '',            'Cambio de amortiguador (con resorte, tipo ensamblado)',          null, null, 600, 700, 800, 0],
            [115,'Mecánica Rápida', 'Amortiguador', '',            'Cambio de amortiguador (sin resorte — precio x pieza)',          null, null, 400, 500, 600, 0],
            [116,'Mecánica Rápida', 'Bases',        '',            'Cambiar base + amortiguador — precio por lado',                  null, null, 800, 900, 1100, 0],
            [117,'Mecánica Rápida', 'Suspensión',   '',            'Revisión física y diagnóstico preliminar de suspensión',         null, null, 200, 300, 400, 0],
            [118,'Mecánica Rápida', 'Suspensión',   '',            'Cambio de brazo Pickman o codo',                                 null, null, 0,   500, 600, 0],
            [119,'Mecánica Rápida', 'Suspensión',   '',            'Cambio de brazo auxiliar',                                       null, null, 0,   400, 500, 0],
            [120,'Mecánica Rápida', 'Suspensión',   '',            'Cambio de varilla de dirección o bieleta — precio x pieza',     null, null, 300, 400, 500, 0],
            [121,'Mecánica Rápida', 'Suspensión',   '',            'Cambio de terminal de dirección — precio por pieza',            null, null, 300, 400, 500, 0],
            [122,'Mecánica Rápida', 'Suspensión',   '',            'Cambio de horquilla completa inferior (solo adelante)',          null, null, 500, 600, 700, 0],
            [123,'Mecánica Rápida', 'Suspensión',   '',            'Cambio de horquilla completa superior (solo adelante)',          null, null, 500, 600, 700, 0],
            [124,'Mecánica Rápida', 'Suspensión',   '',            'Cambio de rótula de horquilla inferior (suspensión delantera)', null, null, 600, 700, 800, 0],
            [125,'Mecánica Rápida', 'Suspensión',   '',            'Cambio de rótula de horquilla superior (suspensión delantera)', null, null, 600, 700, 800, 0],
            [126,'Mecánica Rápida', 'Suspensión',   '',            'Cambio de buje grande, buje cilindro o rótula — x pieza',      null, null, 100, 100, 100, 0],
            [127,'Mecánica Rápida', 'Suspensión',   '',            'Cambio de articulaciones o tornillos estabilizadores',          null, null, 400, 500, 600, 0],
            [128,'Mecánica Rápida', 'Suspensión',   '',            'Cambio del par de gomas de barra estabilizadora',               null, null, 600, 700, 800, 0],
            [129,'Mecánica Rápida', 'Suspensión',   '',            'Desmontaje y cambio de bujes de puente delantero',              null, null, 0,   2500, 3000, 0],
            [130,'Mecánica Rápida', 'Suspensión',   '',            'Cambio de buje de eje trasero',                                  null, null, 1800, 2200, 2400, 0],
            [131,'Mecánica Rápida', 'Dirección',    '',            'Cambio de caja de dirección',                                    null, null, 1000, 1200, 1400, 0],
            [132,'Mecánica Rápida', 'Dirección',    '',            'Cambio de gomas de caja de dirección o cremallera',             null, null, 800, 1000, 1200, 0],
            [133,'Mecánica Rápida', 'Dirección',    '',            'Servicio de alineación manual del eje delantero',               null, null, 400, 450, 500, 0],
            [134,'Mecánica Rápida', 'Ruedas',       '',            'Cambio de maza completa',                                        null, null, 500, 600, 700, 0],
            [135,'Mecánica Rápida', 'Ruedas',       '',            'Cambio de balero homocinético',                                  null, null, 600, 700, 800, 0],
            [136,'Mecánica Rápida', 'Ruedas',       '',            'Cambio de balero doble delantero o trasero',                     null, null, 700, 800, 900, 0],
            [137,'Mecánica Rápida', 'Ruedas',       '',            'Cambio de balero cónico delantero o trasero',                   null, null, 600, 900, 1200, 0],
            [138,'Mecánica Rápida', 'Ruedas',       '',            'Engrasado de una flecha y sus 2 baleros homocinéticos',         null, null, 800, 900, 1000, 0],

            // ── Afinación y Otros ─────────────────────────────────────────────
            [200,'Afinación y Otros','Rescates',    'Rescates',    'Salida a sitio alrededor de 2 cuadras del taller',              null, null, 200, 200, 200, 0],
            [201,'Afinación y Otros','Rescates',    'Rescates',    'Salida a sitio dentro de colonia Colosio',                      null, null, 400, 400, 400, 0],
            [202,'Afinación y Otros','Rescates',    'Rescates',    'Salida a sitio dentro de zona urbana',                          null, null, 600, 600, 600, 0],
            [203,'Afinación y Otros','Afinaciones', 'Motor',       'Afinación menor (cambio de aceite de motor y filtro)',          null, null, 300, 350, 400, 0],
            [204,'Afinación y Otros','Afinaciones', 'Motor',       'Cambio de bujías',                                              null, null, 400, 600, 800, 0],
            [205,'Afinación y Otros','Afinaciones', 'Motor',       'Afinación mayor (cambio de aceite, filtros y bujías)',          null, null, 800, 1000, 1200, 0],
            [206,'Afinación y Otros','Afinaciones', 'Cajas',       'Cambio de aceite de caja estándar',                            null, null, 500, 600, 700, 0],
            [207,'Afinación y Otros','Afinaciones', 'Cajas',       'Cambio de aceite de diferencial',                               null, null, 500, 600, 700, 0],
            [208,'Afinación y Otros','Afinaciones', 'Cajas',       'Cambio de aceite de caja automática',                          null, null, 900, 1100, 1300, 0],
            [209,'Afinación y Otros','Diagnósticos','Diagnóstico', 'Diagnóstico con escáner Nivel 1 (solo lectura o borrado)',      null, null, 200, 200, 200, 0],
            [210,'Afinación y Otros','Diagnósticos','Diagnóstico', 'Diagnóstico con escáner Nivel 2 (diagnóstico preliminar)',      null, null, 500, 600, 800, 0],
            [211,'Afinación y Otros','Diagnósticos','Diagnóstico', 'Diagnóstico con escáner Nivel 3 (lectura datos en tiempo real)',null, null, 800, 900, 1000, 0],
        ];

        $torno = [
            [300,'Servicio de Torno','Rectificado','Torno','Pequeño / Hasta 50mm',   'Pequeño',  'Hasta 50mm',  0, 0, 0, 180],
            [301,'Servicio de Torno','Rectificado','Torno','Mediano / Hasta 100mm',  'Mediano',  'Hasta 100mm', 0, 0, 0, 350],
            [302,'Servicio de Torno','Rectificado','Torno','Grande / Hasta 200mm',   'Grande',   'Hasta 200mm', 0, 0, 0, 600],
            [303,'Servicio de Torno','Rectificado','Torno','Muy Grande / +200mm',    'Muy Grande','Más de 200mm',0, 0, 0, 1000],
        ];

        $rows = [];

        foreach ($automotriz as $r) {
            [$id, $catPrincipal, $sistema, $familia, $concepto, $tamano, $diametro, $pa, $pc, $pca, $precio] = $r;
            $rows[] = [
                'id'                => $id,
                'categoria'         => 'automotriz',
                'categoria_principal'=> $catPrincipal,
                'sistema'           => $sistema,
                'familia'           => $familia,
                'concepto'          => $concepto,
                'tamano'            => $tamano,
                'diametro'          => $diametro,
                'precio_auto'       => $pa,
                'precio_camioneta'  => $pc,
                'precio_camion'     => $pca,
                'precio'            => $precio,
                'activo'            => true,
                'created_at'        => now(),
                'updated_at'        => now(),
            ];
        }

        foreach ($torno as $r) {
            [$id, $catPrincipal, $sistema, $familia, $concepto, $tamano, $diametro, $pa, $pc, $pca, $precio] = $r;
            $rows[] = [
                'id'                => $id,
                'categoria'         => 'torno',
                'categoria_principal'=> $catPrincipal,
                'sistema'           => $sistema,
                'familia'           => $familia,
                'concepto'          => $concepto,
                'tamano'            => $tamano,
                'diametro'          => $diametro,
                'precio_auto'       => $pa,
                'precio_camioneta'  => $pc,
                'precio_camion'     => $pca,
                'precio'            => $precio,
                'activo'            => true,
                'created_at'        => now(),
                'updated_at'        => now(),
            ];
        }

        DB::table('price_items')->insert($rows);
    }
}
