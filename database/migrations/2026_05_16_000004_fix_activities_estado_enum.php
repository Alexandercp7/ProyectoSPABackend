<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
return new class extends Migration {
    public function up(): void {
        DB::statement("UPDATE activities SET estado = 'En Progreso' WHERE estado = 'En progreso'");
        DB::statement("ALTER TABLE activities MODIFY COLUMN estado ENUM('Pendiente','En Progreso','Completada','Cancelada') NOT NULL DEFAULT 'Pendiente'");
    }
    public function down(): void {
        DB::statement("UPDATE activities SET estado = 'En progreso' WHERE estado = 'En Progreso'");
        DB::statement("ALTER TABLE activities MODIFY COLUMN estado ENUM('Pendiente','En progreso','Completada','Cancelada') NOT NULL DEFAULT 'Pendiente'");
    }
};
