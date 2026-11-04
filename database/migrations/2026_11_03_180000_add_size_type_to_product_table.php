<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('product', function (Blueprint $table): void {
            $table->string('size_type', 20)->nullable()->after('condition');
        });
    }

    public function down(): void
    {
        Schema::table('product', function (Blueprint $table): void {
            $table->dropColumn('size_type');
        });
    }
};
