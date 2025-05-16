<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_equivalents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->foreignId('equivalent_id')->constrained('products')->onDelete('cascade');
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->unique(['product_id', 'equivalent_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_equivalents');
    }
};
