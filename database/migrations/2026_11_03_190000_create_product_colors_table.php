<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_colors', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('product_id')->constrained('product')->cascadeOnDelete();
            $table->string('name', 50);
            $table->string('image');
            $table->timestamps();

            $table->unique(['product_id', 'name']);
        });

        Schema::table('product_variants', function (Blueprint $table): void {
            $table->foreignId('color_id')
                ->nullable()
                ->after('product_id')
                ->constrained('product_colors')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('product_variants', function (Blueprint $table): void {
            $table->dropConstrainedForeignId('color_id');
        });

        Schema::dropIfExists('product_colors');
    }
};
