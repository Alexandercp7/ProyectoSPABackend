<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void {
        Schema::create('inventory_items', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->enum('tipo', ['refaccion','herramienta','equipo','consumible']);
            $table->enum('estado', ['activo','inactivo'])->default('activo');
            $table->foreignId('responsable_id')->nullable()->constrained('users')->nullOnDelete();
            $table->integer('stock_actual')->default(0);
            $table->integer('stock_minimo')->default(0);
            $table->decimal('precio', 10, 2)->default(0);
            $table->decimal('precio_venta', 10, 2)->default(0);
            $table->unsignedBigInteger('linked_part_id')->nullable();
            $table->foreign('linked_part_id')->references('id')->on('inventory_items')->nullOnDelete();
            $table->string('foto_url')->nullable();
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('inventory_items'); }
};
