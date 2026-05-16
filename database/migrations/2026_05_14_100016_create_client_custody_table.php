<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void {
        Schema::create('client_custody', function (Blueprint $table) {
            $table->id();
            $table->string('work_order_id');
            $table->foreign('work_order_id')->references('id')->on('work_orders')->cascadeOnDelete();
            $table->foreignId('client_id')->constrained()->cascadeOnDelete();
            $table->string('item');
            $table->string('foto_url')->nullable();
            $table->string('responsable');
            $table->enum('estado', ['Resguardado','Entregado'])->default('Resguardado');
            $table->date('fecha_ingreso');
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('client_custody'); }
};
