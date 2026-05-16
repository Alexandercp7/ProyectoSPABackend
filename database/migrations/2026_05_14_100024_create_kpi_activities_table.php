<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void {
        Schema::create('kpi_activities', function (Blueprint $table) {
            $table->id();
            $table->string('titulo');
            $table->text('descripcion')->nullable();
            $table->string('role_asignado');
            $table->enum('status', ['Pendiente','En progreso','Completada'])->default('Pendiente');
            $table->enum('prioridad', ['Alta','Media','Baja'])->default('Media');
            $table->foreignId('empleado_id')->nullable()->constrained('employees')->nullOnDelete();
            $table->date('fecha_vencimiento')->nullable();
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('kpi_activities'); }
};
