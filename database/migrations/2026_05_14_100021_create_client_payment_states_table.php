<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void {
        Schema::create('client_payment_states', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->constrained()->cascadeOnDelete();
            $table->string('work_order_id');
            $table->foreign('work_order_id')->references('id')->on('work_orders')->cascadeOnDelete();
            $table->enum('estado', ['Pendiente','Pagado','Vencido'])->default('Pendiente');
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('client_payment_states'); }
};
