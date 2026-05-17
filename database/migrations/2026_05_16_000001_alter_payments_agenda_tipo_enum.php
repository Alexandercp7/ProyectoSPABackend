<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        if (DB::getDriverName() === 'sqlite') {
            $this->rebuildPaymentsAgendaTable(['Periódico', 'No Periódico'], [
                'ingreso' => 'Periódico',
                'egreso' => 'No Periódico',
            ]);

            return;
        }

        DB::statement("ALTER TABLE payments_agenda MODIFY COLUMN tipo ENUM('Periódico', 'No Periódico') NOT NULL");
    }

    public function down(): void {
        if (DB::getDriverName() === 'sqlite') {
            $this->rebuildPaymentsAgendaTable(['ingreso', 'egreso'], [
                'Periódico' => 'ingreso',
                'No Periódico' => 'egreso',
            ]);

            return;
        }

        DB::statement("ALTER TABLE payments_agenda MODIFY COLUMN tipo ENUM('ingreso', 'egreso') NOT NULL");
    }

    private function rebuildPaymentsAgendaTable(array $allowedTipos, array $tipoMap): void
    {
        $temporaryTable = 'payments_agenda_new';

        Schema::disableForeignKeyConstraints();

        Schema::create($temporaryTable, function (Blueprint $table) use ($allowedTipos) {
            $table->id();
            $table->string('concepto');
            $table->enum('tipo', $allowedTipos);
            $table->string('categoria');
            $table->date('fecha_vencimiento');
            $table->decimal('monto_presupuestado', 10, 2);
            $table->decimal('monto_pagado', 10, 2)->default(0);
            $table->string('comprobante_url')->nullable();
            $table->text('notas')->nullable();
            $table->timestamps();
        });

        $cases = collect($tipoMap)
            ->map(fn (string $replacement, string $source) => "WHEN tipo = '" . str_replace("'", "''", $source) . "' THEN '" . str_replace("'", "''", $replacement) . "'")
            ->implode(' ');

        DB::statement("INSERT INTO {$temporaryTable} (id, concepto, tipo, categoria, fecha_vencimiento, monto_presupuestado, monto_pagado, comprobante_url, notas, created_at, updated_at) SELECT id, concepto, CASE {$cases} ELSE tipo END, categoria, fecha_vencimiento, monto_presupuestado, monto_pagado, comprobante_url, notas, created_at, updated_at FROM payments_agenda");

        Schema::drop('payments_agenda');
        Schema::rename($temporaryTable, 'payments_agenda');

        Schema::enableForeignKeyConstraints();
    }
};
