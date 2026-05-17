<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void {
        if (DB::getDriverName() === 'sqlite') {
            $this->rebuildActivitiesTable([
                'Pendiente',
                'En Progreso',
                'Completada',
                'Cancelada',
            ], [
                'estado' => [
                    'Pendiente' => 'Pendiente',
                    'En progreso' => 'En Progreso',
                    'Completada' => 'Completada',
                    'Cancelada' => 'Cancelada',
                ],
            ]);

            return;
        }

        DB::statement("UPDATE activities SET estado = 'En Progreso' WHERE estado = 'En progreso'");
        DB::statement("ALTER TABLE activities MODIFY COLUMN estado ENUM('Pendiente','En Progreso','Completada','Cancelada') NOT NULL DEFAULT 'Pendiente'");
    }
    public function down(): void {
        if (DB::getDriverName() === 'sqlite') {
            $this->rebuildActivitiesTable([
                'Pendiente',
                'En progreso',
                'Completada',
                'Cancelada',
            ], [
                'estado' => [
                    'Pendiente' => 'Pendiente',
                    'En Progreso' => 'En progreso',
                    'Completada' => 'Completada',
                    'Cancelada' => 'Cancelada',
                ],
            ]);

            return;
        }

        DB::statement("UPDATE activities SET estado = 'En progreso' WHERE estado = 'En Progreso'");
        DB::statement("ALTER TABLE activities MODIFY COLUMN estado ENUM('Pendiente','En progreso','Completada','Cancelada') NOT NULL DEFAULT 'Pendiente'");
    }

    private function rebuildActivitiesTable(array $allowedEstados, array $mappings): void
    {
        $temporaryTable = 'activities_new';

        Schema::disableForeignKeyConstraints();

        Schema::create($temporaryTable, function (Blueprint $table) use ($allowedEstados) {
            $table->id();
            $table->string('titulo');
            $table->text('descripcion')->nullable();
            $table->foreignId('asignado_a_id')->nullable()->constrained('users')->nullOnDelete();
            $table->date('fecha_limite')->nullable();
            $table->enum('prioridad', ['Alta', 'Media', 'Baja'])->default('Media');
            $table->string('etiqueta')->nullable();
            $table->enum('estado', $allowedEstados)->default('Pendiente');
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        $estadoCases = collect($mappings['estado'])
            ->map(fn (string $replacement, string $source) => "WHEN estado = '" . str_replace("'", "''", $source) . "' THEN '" . str_replace("'", "''", $replacement) . "'")
            ->implode(' ');

        DB::statement("INSERT INTO {$temporaryTable} (id, titulo, descripcion, asignado_a_id, fecha_limite, prioridad, etiqueta, estado, created_by, created_at, updated_at) SELECT id, titulo, descripcion, asignado_a_id, fecha_limite, prioridad, etiqueta, CASE {$estadoCases} ELSE estado END, created_by, created_at, updated_at FROM activities");

        Schema::drop('activities');
        Schema::rename($temporaryTable, 'activities');

        Schema::enableForeignKeyConstraints();
    }
};
