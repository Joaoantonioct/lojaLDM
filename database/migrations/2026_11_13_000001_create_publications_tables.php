<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('publications', function (Blueprint $table): void {
            $table->id();
            $table->uuid('event_id')->unique();
            $table->char('payload_hash', 64);
            $table->string('title', 160);
            $table->text('description')->nullable();
            $table->string('image_path', 255);
            $table->timestamps();
        });

        Schema::create('publication_items', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('publication_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->constrained('product')->restrictOnDelete();
            $table->foreignId('variant_id')->nullable()->constrained('product_variants')->restrictOnDelete();
            $table->string('sku', 100);
            $table->string('item_name', 180);
            $table->unsignedTinyInteger('position');
            $table->timestamps();
            $table->unique(['publication_id', 'sku']);
            $table->index('sku');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('publication_items');
        Schema::dropIfExists('publications');
    }
};
