<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (DB::getDriverName() === 'sqlite') {
            $this->rebuildInventoryItemsTable(
                ['Herramienta', 'Consumible', 'Equipo', 'Parte en venta'],
                ['Bueno', 'Regular', 'Danado'],
                [
                    'tipo' => [
                        'herramienta' => 'Herramienta',
                        'consumible' => 'Consumible',
                        'equipo' => 'Equipo',
                        'refaccion' => 'Parte en venta',
                    ],
                    'estado' => [
                        'activo' => 'Bueno',
                        'inactivo' => 'Danado',
                    ],
                ]
            );

            return;
        }

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
        if (DB::getDriverName() === 'sqlite') {
            $this->rebuildInventoryItemsTable(
                ['refaccion', 'herramienta', 'equipo', 'consumible'],
                ['activo', 'inactivo'],
                [
                    'tipo' => [
                        'Herramienta' => 'herramienta',
                        'Consumible' => 'consumible',
                        'Equipo' => 'equipo',
                        'Parte en venta' => 'refaccion',
                    ],
                    'estado' => [
                        'Bueno' => 'activo',
                        'Regular' => 'activo',
                        'Danado' => 'inactivo',
                    ],
                ]
            );

            return;
        }

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

    private function rebuildInventoryItemsTable(array $allowedTipos, array $allowedEstados, array $mappings): void
    {
        $temporaryTable = 'inventory_items_new';

        Schema::disableForeignKeyConstraints();

        Schema::create($temporaryTable, function (Blueprint $table) use ($allowedTipos, $allowedEstados) {
            $table->id();
            $table->string('nombre');
            $table->enum('tipo', $allowedTipos);
            $table->enum('estado', $allowedEstados)->default($allowedEstados[0]);
            $table->foreignId('responsable_id')->nullable()->constrained('users')->nullOnDelete();
            $table->integer('stock_actual')->default(0);
            $table->integer('stock_minimo')->default(0);
            $table->decimal('precio', 10, 2)->default(0);
            $table->decimal('precio_venta', 10, 2)->default(0);
            $table->unsignedBigInteger('linked_part_id')->nullable();
            $table->foreign('linked_part_id')->references('id')->on('inventory_items')->nullOnDelete();
            $table->string('foto_url')->nullable();
            $table->timestamps();
        });

        $tipoCases = collect($mappings['tipo'])
            ->map(fn (string $replacement, string $source) => "WHEN tipo = '" . str_replace("'", "''", $source) . "' THEN '" . str_replace("'", "''", $replacement) . "'")
            ->implode(' ');

        $estadoCases = collect($mappings['estado'])
            ->map(fn (string $replacement, string $source) => "WHEN estado = '" . str_replace("'", "''", $source) . "' THEN '" . str_replace("'", "''", $replacement) . "'")
            ->implode(' ');

        DB::statement("INSERT INTO {$temporaryTable} (id, nombre, tipo, estado, responsable_id, stock_actual, stock_minimo, precio, precio_venta, linked_part_id, foto_url, created_at, updated_at) SELECT id, nombre, CASE {$tipoCases} ELSE tipo END, CASE {$estadoCases} ELSE estado END, responsable_id, stock_actual, stock_minimo, precio, precio_venta, linked_part_id, foto_url, created_at, updated_at FROM inventory_items");

        Schema::drop('inventory_items');
        Schema::rename($temporaryTable, 'inventory_items');

        Schema::enableForeignKeyConstraints();
    }
};
