<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void {
        Schema::create('inventory_movements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('item_id')->constrained('inventory_items')->cascadeOnDelete();
            $table->enum('tipo', ['entrada','salida_ot','ajuste','entrada_inicial']);
            $table->integer('cantidad');
            $table->integer('stock_resultante');
            $table->string('work_order_id')->nullable();
            $table->foreign('work_order_id')->references('id')->on('work_orders')->nullOnDelete();
            $table->string('motivo')->nullable();
            $table->foreignId('usuario_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('created_at')->useCurrent();
        });
    }
    public function down(): void { Schema::dropIfExists('inventory_movements'); }
};
