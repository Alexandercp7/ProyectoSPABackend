<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void {
        Schema::create('work_orders', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('client_id')->constrained()->restrictOnDelete();
            $table->foreignId('vehicle_id')->constrained()->restrictOnDelete();
            $table->foreignId('tecnico_id')->nullable()->constrained('users')->nullOnDelete();
            $table->enum('status', ['Agendado','En Espera','En Proceso','Terminado','En Garantia','Rezagado','Entregado'])->default('Agendado');
            $table->enum('priority', ['Alta','Media','Baja'])->default('Media');
            $table->enum('tipo_vehiculo', ['Auto','Camioneta','Camion']);
            $table->text('problema');
            $table->text('diagnostico')->nullable();
            $table->dateTime('fecha_ingreso');
            $table->date('fecha_programada')->nullable();
            $table->boolean('cargo_generado')->default(false);
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('work_orders'); }
};
