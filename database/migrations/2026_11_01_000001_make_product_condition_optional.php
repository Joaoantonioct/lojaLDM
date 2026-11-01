<?php

use App\Models\Category;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('product', function (Blueprint $table): void {
            $table->string('condition', 10)->nullable()->default(null)->change();
        });

        Category::with('parent')->get()
            ->reject(fn (Category $category): bool => $category->isApparelCategory())
            ->each(function (Category $category): void {
                DB::table('product')
                    ->where('category_id', $category->id)
                    ->update(['condition' => null]);
            });
    }

    public function down(): void
    {
        DB::table('product')
            ->whereNull('condition')
            ->update(['condition' => 'novo']);

        Schema::table('product', function (Blueprint $table): void {
            $table->string('condition', 10)->default('novo')->nullable(false)->change();
        });
    }
};
