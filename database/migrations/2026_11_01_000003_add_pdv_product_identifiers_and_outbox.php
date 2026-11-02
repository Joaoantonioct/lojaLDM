<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('product', function (Blueprint $table): void {
            $table->string('barcode', 13)->nullable()->unique();
        });

        Schema::create('pdv_identifiers', function (Blueprint $table): void {
            $table->id();
            $table->string('sku', 100)->unique();
            $table->string('barcode', 13)->unique();
            $table->foreignId('product_id')->nullable()->unique()->constrained('product')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('pdv_identifier_sequences', function (Blueprint $table): void {
            $table->unsignedTinyInteger('id')->primary();
            $table->unsignedBigInteger('next_sku');
            $table->unsignedBigInteger('next_barcode');
            $table->timestamps();
        });

        DB::table('pdv_identifier_sequences')->insert([
            'id' => 1,
            'next_sku' => 1,
            'next_barcode' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        Schema::create('pdv_product_outbox', function (Blueprint $table): void {
            $table->uuid('event_id')->primary();
            $table->foreignId('product_id')->constrained('product')->cascadeOnDelete();
            $table->timestamp('acknowledged_at')->nullable();
            $table->timestamps();
            $table->unique('product_id');
            $table->index('acknowledged_at');
        });

        Schema::create('pdv_product_receipts', function (Blueprint $table): void {
            $table->uuid('event_id')->primary();
            $table->foreignId('product_id')->nullable()->constrained('product')->nullOnDelete();
            $table->char('payload_hash', 64);
            $table->timestamp('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pdv_product_receipts');
        Schema::dropIfExists('pdv_product_outbox');
        Schema::dropIfExists('pdv_identifier_sequences');
        Schema::dropIfExists('pdv_identifiers');

        Schema::table('product', function (Blueprint $table): void {
            $table->dropUnique(['barcode']);
            $table->dropColumn('barcode');
        });
    }
};
