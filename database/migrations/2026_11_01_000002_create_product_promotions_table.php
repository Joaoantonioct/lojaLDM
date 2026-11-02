<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_promotions', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('product_id')->constrained('product')->cascadeOnDelete();
            $table->string('external_id', 40);
            $table->unsignedSmallInteger('discount_basis_points');
            $table->dateTime('starts_at');
            $table->dateTime('ends_at');
            $table->timestamps();
            $table->unique(['product_id', 'external_id']);
            $table->index(['product_id', 'starts_at', 'ends_at']);
        });

        Schema::create('pdv_sync_receipts', function (Blueprint $table): void {
            $table->uuid('event_id')->primary();
            $table->string('event_type', 40);
            $table->timestamp('received_at')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pdv_sync_receipts');
        Schema::dropIfExists('product_promotions');
    }
};
