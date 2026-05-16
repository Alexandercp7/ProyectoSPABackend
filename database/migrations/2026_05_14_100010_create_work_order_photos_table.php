<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void {
        Schema::create('work_order_photos', function (Blueprint $table) {
            $table->id();
            $table->string('work_order_id');
            $table->foreign('work_order_id')->references('id')->on('work_orders')->cascadeOnDelete();
            $table->string('url');
            $table->string('storage_path');
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('work_order_photos'); }
};
