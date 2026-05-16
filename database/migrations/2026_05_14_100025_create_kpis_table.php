<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void {
        Schema::create('kpis', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->text('descripcion')->nullable();
            $table->string('role_responsable');
            $table->integer('meta')->default(100);
            $table->enum('periodo', ['semanal','mensual','trimestral','anual'])->default('mensual');
            $table->date('fecha_inicio');
            $table->date('fecha_fin');
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('kpis'); }
};
