<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void {
        DB::statement("ALTER TABLE payments_agenda MODIFY COLUMN tipo ENUM('Periódico', 'No Periódico') NOT NULL");
    }

    public function down(): void {
        DB::statement("ALTER TABLE payments_agenda MODIFY COLUMN tipo ENUM('ingreso', 'egreso') NOT NULL");
    }
};
