<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void {
        Schema::create('work_order_checklists', function (Blueprint $table) {
            $table->id();
            $table->string('work_order_id');
            $table->foreign('work_order_id')->references('id')->on('work_orders')->cascadeOnDelete();
            $table->enum('tipo', ['inicial','trabajo']);
            $table->string('tarea');
            $table->string('responsable')->nullable();
            $table->boolean('completada')->default(false);
            $table->unsignedInteger('orden')->default(0);
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('work_order_checklists'); }
};
