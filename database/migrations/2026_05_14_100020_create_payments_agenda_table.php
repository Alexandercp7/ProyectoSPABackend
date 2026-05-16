<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void {
        Schema::create('payments_agenda', function (Blueprint $table) {
            $table->id();
            $table->string('concepto');
            $table->enum('tipo', ['ingreso','egreso']);
            $table->string('categoria');
            $table->date('fecha_vencimiento');
            $table->decimal('monto_presupuestado', 10, 2);
            $table->decimal('monto_pagado', 10, 2)->default(0);
            $table->string('comprobante_url')->nullable();
            $table->text('notas')->nullable();
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('payments_agenda'); }
};
