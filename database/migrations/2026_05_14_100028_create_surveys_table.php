<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void {
        Schema::create('surveys', function (Blueprint $table) {
            $table->id();
            $table->string('work_order_id');
            $table->foreign('work_order_id')->references('id')->on('work_orders')->cascadeOnDelete();
            $table->string('cliente');
            $table->tinyInteger('satisfaccion_general');
            $table->tinyInteger('calidad_trabajo');
            $table->tinyInteger('trato_recibido');
            $table->boolean('recomendacion')->default(false);
            $table->text('comentarios')->nullable();
            $table->string('token')->unique();
            $table->date('fecha');
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('surveys'); }
};
