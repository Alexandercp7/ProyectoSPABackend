<?php
namespace Database\Seeders;

use App\Models\Employee;
use App\Models\Kpi;
use App\Models\KpiActivity;
use Illuminate\Database\Seeder;

class KpisSeeder extends Seeder
{
    public function run(): void
    {
        // Employees por rol para asignar actividades
        $empleados = Employee::all()->groupBy('role');

        // KPIs por rol
        $kpis = [
            [
                'nombre'           => 'Eficiencia Administrativa',
                'descripcion'      => 'Cumplimiento de procesos administrativos y reportes a tiempo',
                'role_responsable' => 'Líder Admin',
                'meta'             => 95,
                'periodo'          => 'mensual',
                'fecha_inicio'     => '2026-05-01',
                'fecha_fin'        => '2026-05-31',
            ],
            [
                'nombre'           => 'Crecimiento de Ingresos',
                'descripcion'      => 'Incremento mensual en ingresos totales del taller',
                'role_responsable' => 'Director General',
                'meta'             => 90,
                'periodo'          => 'mensual',
                'fecha_inicio'     => '2026-05-01',
                'fecha_fin'        => '2026-05-31',
            ],
            [
                'nombre'           => 'Tasa de Cierre de Ventas',
                'descripcion'      => 'Porcentaje de cotizaciones convertidas a servicios contratados',
                'role_responsable' => 'Asesor de Servicio',
                'meta'             => 80,
                'periodo'          => 'mensual',
                'fecha_inicio'     => '2026-05-01',
                'fecha_fin'        => '2026-05-31',
            ],
            [
                'nombre'           => 'Seguridad en Taller',
                'descripcion'      => 'Cero incidentes de seguridad durante el período',
                'role_responsable' => 'Líder Técnico',
                'meta'             => 100,
                'periodo'          => 'mensual',
                'fecha_inicio'     => '2026-05-01',
                'fecha_fin'        => '2026-05-31',
            ],
            [
                'nombre'           => 'Calidad de Reparaciones',
                'descripcion'      => 'Trabajos entregados sin retorno por garantía',
                'role_responsable' => 'Técnico Automotriz',
                'meta'             => 98,
                'periodo'          => 'mensual',
                'fecha_inicio'     => '2026-05-01',
                'fecha_fin'        => '2026-05-31',
            ],
            [
                'nombre'           => 'Satisfacción del Cliente',
                'descripcion'      => 'Calificación promedio en encuestas post-servicio',
                'role_responsable' => 'Personal de Apoyo',
                'meta'             => 90,
                'periodo'          => 'mensual',
                'fecha_inicio'     => '2026-05-01',
                'fecha_fin'        => '2026-05-31',
            ],
        ];

        foreach ($kpis as $kpiData) {
            Kpi::firstOrCreate(
                ['nombre' => $kpiData['nombre'], 'role_responsable' => $kpiData['role_responsable']],
                $kpiData
            );
        }

        // Actividades por rol con variedad de estados para que el progreso se vea interesante
        $actividades = [
            // Líder Admin — 4 de 6 completadas → ~67%
            ['titulo' => 'Actualizar expedientes de empleados', 'descripcion' => 'Revisar y completar documentación de todos los empleados activos', 'role_asignado' => 'Líder Admin', 'status' => 'Completada', 'prioridad' => 'Alta', 'fecha_vencimiento' => '2026-05-05'],
            ['titulo' => 'Generar reporte mensual de asistencia', 'descripcion' => 'Consolidar registros de asistencia del mes de mayo', 'role_asignado' => 'Líder Admin', 'status' => 'Completada', 'prioridad' => 'Alta', 'fecha_vencimiento' => '2026-05-07'],
            ['titulo' => 'Pago a proveedores', 'descripcion' => 'Gestionar pagos pendientes con proveedores de refacciones', 'role_asignado' => 'Líder Admin', 'status' => 'Completada', 'prioridad' => 'Media', 'fecha_vencimiento' => '2026-05-10'],
            ['titulo' => 'Facturación quincenal', 'descripcion' => 'Emitir facturas correspondientes a la primera quincena', 'role_asignado' => 'Líder Admin', 'status' => 'Completada', 'prioridad' => 'Alta', 'fecha_vencimiento' => '2026-05-15'],
            ['titulo' => 'Auditoría de inventario', 'descripcion' => 'Verificar existencias físicas contra sistema', 'role_asignado' => 'Líder Admin', 'status' => 'En progreso', 'prioridad' => 'Media', 'fecha_vencimiento' => '2026-05-20'],
            ['titulo' => 'Preparar cierre mensual', 'descripcion' => 'Consolidar información financiera del mes para informe a dirección', 'role_asignado' => 'Líder Admin', 'status' => 'Pendiente', 'prioridad' => 'Alta', 'fecha_vencimiento' => '2026-05-31'],

            // Director General — 3 de 5 completadas → 60%
            ['titulo' => 'Revisión de metas Q2', 'descripcion' => 'Analizar avance de indicadores del segundo trimestre', 'role_asignado' => 'Director General', 'status' => 'Completada', 'prioridad' => 'Alta', 'fecha_vencimiento' => '2026-05-05'],
            ['titulo' => 'Reunión con socios', 'descripcion' => 'Presentación de resultados financieros del mes de abril', 'role_asignado' => 'Director General', 'status' => 'Completada', 'prioridad' => 'Alta', 'fecha_vencimiento' => '2026-05-08'],
            ['titulo' => 'Negociación contrato proveedor Bosch', 'descripcion' => 'Renovar contrato de suministro de herramientas diagnósticas', 'role_asignado' => 'Director General', 'status' => 'Completada', 'prioridad' => 'Media', 'fecha_vencimiento' => '2026-05-12'],
            ['titulo' => 'Plan de expansión sucursal norte', 'descripcion' => 'Definir cronograma y presupuesto para apertura de segunda unidad', 'role_asignado' => 'Director General', 'status' => 'En progreso', 'prioridad' => 'Alta', 'fecha_vencimiento' => '2026-05-25'],
            ['titulo' => 'Evaluación de desempeño del equipo', 'descripcion' => 'Aplicar evaluación semestral a mandos medios', 'role_asignado' => 'Director General', 'status' => 'Pendiente', 'prioridad' => 'Media', 'fecha_vencimiento' => '2026-05-30'],

            // Asesor de Servicio — 3 de 5 completadas → 60%
            ['titulo' => 'Seguimiento clientes recurrentes', 'descripcion' => 'Llamar a 20 clientes para recordatorio de servicio preventivo', 'role_asignado' => 'Asesor de Servicio', 'status' => 'Completada', 'prioridad' => 'Media', 'fecha_vencimiento' => '2026-05-06'],
            ['titulo' => 'Cotizaciones pendientes Toyota', 'descripcion' => 'Enviar presupuestos a 5 clientes con vehículos Toyota en espera', 'role_asignado' => 'Asesor de Servicio', 'status' => 'Completada', 'prioridad' => 'Alta', 'fecha_vencimiento' => '2026-05-08'],
            ['titulo' => 'Encuestas de satisfacción abril', 'descripcion' => 'Tabular resultados de satisfacción del mes pasado', 'role_asignado' => 'Asesor de Servicio', 'status' => 'Completada', 'prioridad' => 'Media', 'fecha_vencimiento' => '2026-05-10'],
            ['titulo' => 'Campaña de mantenimiento preventivo', 'descripcion' => 'Diseñar oferta especial de paquete de mantenimiento para clientes nuevos', 'role_asignado' => 'Asesor de Servicio', 'status' => 'En progreso', 'prioridad' => 'Alta', 'fecha_vencimiento' => '2026-05-22'],
            ['titulo' => 'Actualizar catálogo de servicios', 'descripcion' => 'Revisar y actualizar lista de precios con los nuevos servicios disponibles', 'role_asignado' => 'Asesor de Servicio', 'status' => 'Pendiente', 'prioridad' => 'Baja', 'fecha_vencimiento' => '2026-05-28'],

            // Líder Técnico — 5 de 6 completadas → 83%
            ['titulo' => 'Inspección mensual de elevadores', 'descripcion' => 'Verificar estado y calibración de los 3 elevadores hidráulicos', 'role_asignado' => 'Líder Técnico', 'status' => 'Completada', 'prioridad' => 'Alta', 'fecha_vencimiento' => '2026-05-03'],
            ['titulo' => 'Verificación extintores', 'descripcion' => 'Revisar vigencia y condición de extintores en área de taller', 'role_asignado' => 'Líder Técnico', 'status' => 'Completada', 'prioridad' => 'Alta', 'fecha_vencimiento' => '2026-05-05'],
            ['titulo' => 'Capacitación ADAS a técnicos', 'descripcion' => 'Impartir sesión sobre sistemas avanzados de asistencia al conductor', 'role_asignado' => 'Líder Técnico', 'status' => 'Completada', 'prioridad' => 'Media', 'fecha_vencimiento' => '2026-05-10'],
            ['titulo' => 'Calibración scanner diagnóstico', 'descripcion' => 'Actualizar software y calibrar escáner Launch X431 Pro', 'role_asignado' => 'Líder Técnico', 'status' => 'Completada', 'prioridad' => 'Media', 'fecha_vencimiento' => '2026-05-14'],
            ['titulo' => 'Revisión EPP del personal', 'descripcion' => 'Verificar uso correcto de equipo de protección por todos los técnicos', 'role_asignado' => 'Líder Técnico', 'status' => 'Completada', 'prioridad' => 'Alta', 'fecha_vencimiento' => '2026-05-17'],
            ['titulo' => 'Protocolo de manejo de residuos peligrosos', 'descripcion' => 'Actualizar procedimiento de disposición de aceites y líquidos usados', 'role_asignado' => 'Líder Técnico', 'status' => 'En progreso', 'prioridad' => 'Media', 'fecha_vencimiento' => '2026-05-28'],

            // Técnico Automotriz — 4 de 6 completadas → 67%
            ['titulo' => 'Servicio 5000km Honda Civic LMN-456', 'descripcion' => 'Cambio de aceite, filtros y revisión de frenos', 'role_asignado' => 'Técnico Automotriz', 'status' => 'Completada', 'prioridad' => 'Media', 'fecha_vencimiento' => '2026-05-04'],
            ['titulo' => 'Reparación caja de cambios VW Jetta', 'descripcion' => 'Diagnóstico y reparación de falla en 3er velocidad', 'role_asignado' => 'Técnico Automotriz', 'status' => 'Completada', 'prioridad' => 'Alta', 'fecha_vencimiento' => '2026-05-08'],
            ['titulo' => 'Alineación y balanceo Nissan Sentra', 'descripcion' => 'Ajuste de alineación y balanceo de 4 llantas', 'role_asignado' => 'Técnico Automotriz', 'status' => 'Completada', 'prioridad' => 'Baja', 'fecha_vencimiento' => '2026-05-09'],
            ['titulo' => 'Cambio de clutch Mazda 3 2018', 'descripcion' => 'Reemplazar disco, plato y cojinete de embrague', 'role_asignado' => 'Técnico Automotriz', 'status' => 'Completada', 'prioridad' => 'Alta', 'fecha_vencimiento' => '2026-05-13'],
            ['titulo' => 'Diagnóstico eléctrico Toyota RAV4', 'descripcion' => 'Falla en módulo de aire acondicionado, lectura de códigos y reparación', 'role_asignado' => 'Técnico Automotriz', 'status' => 'En progreso', 'prioridad' => 'Alta', 'fecha_vencimiento' => '2026-05-20'],
            ['titulo' => 'Mantenimiento Ford F-150 flotilla', 'descripcion' => 'Revisión de 3 unidades de empresa cliente — servicio mayor', 'role_asignado' => 'Técnico Automotriz', 'status' => 'Pendiente', 'prioridad' => 'Media', 'fecha_vencimiento' => '2026-05-25'],

            // Personal de Apoyo — 3 de 5 completadas → 60%
            ['titulo' => 'Limpieza área de recepción', 'descripcion' => 'Aseo profundo de sala de espera y mostrador de servicio', 'role_asignado' => 'Personal de Apoyo', 'status' => 'Completada', 'prioridad' => 'Media', 'fecha_vencimiento' => '2026-05-02'],
            ['titulo' => 'Acomodo de refacciones almacén B', 'descripcion' => 'Organizar y etiquetar existencias recién llegadas en almacén B', 'role_asignado' => 'Personal de Apoyo', 'status' => 'Completada', 'prioridad' => 'Media', 'fecha_vencimiento' => '2026-05-06'],
            ['titulo' => 'Traslado vehículos área de entrega', 'descripcion' => 'Mover 8 vehículos terminados al área de lavado y entrega', 'role_asignado' => 'Personal de Apoyo', 'status' => 'Completada', 'prioridad' => 'Baja', 'fecha_vencimiento' => '2026-05-09'],
            ['titulo' => 'Reabastecimiento de insumos limpieza', 'descripcion' => 'Compra de detergentes, desengrasantes y materiales de aseo', 'role_asignado' => 'Personal de Apoyo', 'status' => 'En progreso', 'prioridad' => 'Baja', 'fecha_vencimiento' => '2026-05-18'],
            ['titulo' => 'Apoyo en lavado de unidades', 'descripcion' => 'Asistir en lavado de 12 vehículos programados esta semana', 'role_asignado' => 'Personal de Apoyo', 'status' => 'Pendiente', 'prioridad' => 'Media', 'fecha_vencimiento' => '2026-05-23'],
        ];

        foreach ($actividades as $actData) {
            $role = $actData['role_asignado'];
            $empleadoId = null;
            if (isset($empleados[$role]) && $empleados[$role]->isNotEmpty()) {
                $empleadoId = $empleados[$role]->first()->id;
            }

            KpiActivity::firstOrCreate(
                ['titulo' => $actData['titulo'], 'role_asignado' => $role],
                array_merge($actData, ['empleado_id' => $empleadoId])
            );
        }

        $this->command->info('KPIs y actividades creados correctamente.');
    }
}
