<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void {
        Schema::create('daily_cash_entries', function (Blueprint $table) {
            $table->id();
            $table->date('fecha');
            $table->string('concepto');
            $table->enum('tipo', ['Ingreso','Egreso']);
            $table->decimal('monto', 10, 2);
            $table->enum('metodo_pago', ['Efectivo','Transferencia','Tarjeta','Cheque']);
            $table->string('referencia')->nullable();
            $table->foreignId('accounts_receivable_id')->nullable()->constrained('accounts_receivable')->nullOnDelete();
            $table->foreignId('usuario_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('daily_cash_entries'); }
};
