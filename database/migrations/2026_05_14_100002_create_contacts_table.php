<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void {
        Schema::create('contacts', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->string('rfc')->nullable();
            $table->string('empresa')->nullable();
            $table->string('telefono');
            $table->string('correo')->nullable();
            $table->unsignedInteger('dias_pago')->default(30);
            $table->decimal('limite_credito', 10, 2)->default(0);
            $table->text('politica_descuentos')->nullable();
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('contacts'); }
};
