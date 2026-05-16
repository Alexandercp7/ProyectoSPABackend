<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void {
        Schema::create('work_order_services', function (Blueprint $table) {
            $table->id();
            $table->string('work_order_id');
            $table->foreign('work_order_id')->references('id')->on('work_orders')->cascadeOnDelete();
            $table->foreignId('price_item_id')->constrained()->restrictOnDelete();
            $table->string('nombre');
            $table->decimal('precio_auto', 8, 2)->default(0);
            $table->decimal('precio_camioneta', 8, 2)->default(0);
            $table->decimal('precio_camion', 8, 2)->default(0);
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('work_order_services'); }
};
