<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        // Relax columns to TEXT so we can update values freely
        DB::statement("ALTER TABLE inventory_items MODIFY COLUMN tipo VARCHAR(50) NOT NULL");
        DB::statement("ALTER TABLE inventory_items MODIFY COLUMN estado VARCHAR(50) NOT NULL DEFAULT 'Bueno'");

        // Migrate existing data to new values
        DB::statement("UPDATE inventory_items SET tipo = CASE
            WHEN tipo = 'herramienta' THEN 'Herramienta'
            WHEN tipo = 'consumible'  THEN 'Consumible'
            WHEN tipo = 'equipo'      THEN 'Equipo'
            WHEN tipo = 'refaccion'   THEN 'Parte en venta'
            ELSE tipo END");

        DB::statement("UPDATE inventory_items SET estado = CASE
            WHEN estado = 'activo'   THEN 'Bueno'
            WHEN estado = 'inactivo' THEN 'Danado'
            ELSE estado END");

        // Apply the new enum constraints
        DB::statement("ALTER TABLE inventory_items
            MODIFY COLUMN tipo ENUM('Herramienta','Consumible','Equipo','Parte en venta') NOT NULL");

        DB::statement("ALTER TABLE inventory_items
            MODIFY COLUMN estado ENUM('Bueno','Regular','Danado') NOT NULL DEFAULT 'Bueno'");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE inventory_items MODIFY COLUMN tipo VARCHAR(50) NOT NULL");
        DB::statement("ALTER TABLE inventory_items MODIFY COLUMN estado VARCHAR(50) NOT NULL DEFAULT 'Bueno'");

        DB::statement("UPDATE inventory_items SET tipo = CASE
            WHEN tipo = 'Herramienta'    THEN 'herramienta'
            WHEN tipo = 'Consumible'     THEN 'consumible'
            WHEN tipo = 'Equipo'         THEN 'equipo'
            WHEN tipo = 'Parte en venta' THEN 'refaccion'
            ELSE tipo END");

        DB::statement("UPDATE inventory_items SET estado = CASE
            WHEN estado = 'Bueno'   THEN 'activo'
            WHEN estado = 'Danado'  THEN 'inactivo'
            WHEN estado = 'Regular' THEN 'activo'
            ELSE estado END");

        DB::statement("ALTER TABLE inventory_items
            MODIFY COLUMN tipo ENUM('refaccion','herramienta','equipo','consumible') NOT NULL");

        DB::statement("ALTER TABLE inventory_items
            MODIFY COLUMN estado ENUM('activo','inactivo') NOT NULL DEFAULT 'activo'");
    }
};
