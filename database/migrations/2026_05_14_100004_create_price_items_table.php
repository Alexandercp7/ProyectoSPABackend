<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void {
        Schema::create('price_items', function (Blueprint $table) {
            $table->id();
            $table->enum('categoria', ['automotriz','torno']);
            $table->string('sistema');
            $table->string('familia');
            $table->string('concepto');
            $table->string('tamano')->nullable();
            $table->string('diametro')->nullable();
            $table->decimal('precio_auto', 8, 2)->default(0);
            $table->decimal('precio_camioneta', 8, 2)->default(0);
            $table->decimal('precio_camion', 8, 2)->default(0);
            $table->decimal('precio', 8, 2)->default(0);
            $table->boolean('activo')->default(true);
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('price_items'); }
};
