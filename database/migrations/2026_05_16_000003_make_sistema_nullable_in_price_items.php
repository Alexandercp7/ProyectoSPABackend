<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('price_items', function (Blueprint $table) {
            $table->string('sistema')->nullable()->default(null)->change();
        });
    }
    public function down(): void {
        Schema::table('price_items', function (Blueprint $table) {
            $table->string('sistema')->nullable(false)->default('')->change();
        });
    }
};
