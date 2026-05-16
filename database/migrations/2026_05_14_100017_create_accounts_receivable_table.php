<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void {
        Schema::create('accounts_receivable', function (Blueprint $table) {
            $table->id();
            $table->string('work_order_id')->unique();
            $table->foreign('work_order_id')->references('id')->on('work_orders')->cascadeOnDelete();
            $table->foreignId('client_id')->constrained()->restrictOnDelete();
            $table->decimal('monto', 10, 2);
            $table->decimal('monto_recibido', 10, 2)->default(0);
            $table->decimal('monto_pendiente', 10, 2);
            $table->date('fecha_emision');
            $table->date('fecha_vencimiento');
            $table->enum('estado', ['Pendiente','Parcial','Pagado','Vencido'])->default('Pendiente');
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('accounts_receivable'); }
};
