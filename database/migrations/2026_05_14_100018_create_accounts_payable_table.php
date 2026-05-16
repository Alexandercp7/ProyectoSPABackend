<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void {
        Schema::create('accounts_payable', function (Blueprint $table) {
            $table->id();
            $table->foreignId('contact_id')->nullable()->constrained()->nullOnDelete();
            $table->string('concepto');
            $table->decimal('monto', 10, 2);
            $table->decimal('monto_pagado', 10, 2)->default(0);
            $table->decimal('monto_pendiente', 10, 2);
            $table->date('fecha_vencimiento');
            $table->enum('estado', ['Pendiente','Parcial','Pagado'])->default('Pendiente');
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('accounts_payable'); }
};
