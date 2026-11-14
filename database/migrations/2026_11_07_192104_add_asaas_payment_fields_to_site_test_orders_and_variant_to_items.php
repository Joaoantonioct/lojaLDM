<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private function indexExists(string $table, string $indexName): 
bool
{
    foreach (Schema::getIndexes($table) as $index) {
        if (($index['name'] ?? null) === $indexName) {
            return true;
        }
    }

    return false;
}

    public function up(): void
    {
        if (!Schema::hasColumn('site_test_orders', 'order_status')) {
            Schema::table('site_test_orders', function (Blueprint $table) {
                $table->string('order_status', 30)->default('pending_payment')->index();
            });
        }

        if (!Schema::hasColumn('site_test_orders', 'payment_status')) {
            Schema::table('site_test_orders', function (Blueprint $table) {
                $table->string('payment_status', 30)->default('pending')->index();
            });
        }

        if (!Schema::hasColumn('site_test_orders', 'payment_method')) {
            Schema::table('site_test_orders', function (Blueprint $table) {
                $table->string('payment_method', 20)->nullable();
            });
        }

        if (!Schema::hasColumn('site_test_orders', 'asaas_checkout_id')) {
            Schema::table('site_test_orders', function (Blueprint $table) {
                $table->string('asaas_checkout_id', 120)->nullable()->unique();
            });
        }

        if (!Schema::hasColumn('site_test_orders', 'asaas_payment_id')) {
            Schema::table('site_test_orders', function (Blueprint $table) {
                $table->string('asaas_payment_id', 120)->nullable()->unique();
            });
        }

        if (!Schema::hasColumn('site_test_orders', 'asaas_checkout_url')) {
            Schema::table('site_test_orders', function (Blueprint $table) {
                $table->text('asaas_checkout_url')->nullable();
            });
        }

        foreach ([
            'payment_due_at',
            'paid_at',
            'expires_at',
            'reservation_released_at',
            'pdv_released_at',
        ] as $column) {
            if (!Schema::hasColumn('site_test_orders', $column)) {
                Schema::table('site_test_orders', function (Blueprint $table) use ($column) {
                    $table->timestamp($column)->nullable();
                });
            }
        }

        if (!$this->indexExists('site_test_order_items', 'site_test_order_items_order_id_index')) {
            Schema::table('site_test_order_items', function (Blueprint $table) {
                $table->index('order_id', 'site_test_order_items_order_id_index');
            });
        }

        if ($this->indexExists('site_test_order_items', 'site_test_order_items_order_id_product_id_unique')) {
            Schema::table('site_test_order_items', function (Blueprint $table) {
                $table->dropUnique('site_test_order_items_order_id_product_id_unique');
            });
        }

        if (!Schema::hasColumn('site_test_order_items', 'variant_id')) {
            Schema::table('site_test_order_items', function (Blueprint $table) {
                $table->foreignId('variant_id')
                    ->nullable()
                    ->constrained('product_variants')
                    ->nullOnDelete();
            });
        }

        if (!$this->indexExists('site_test_order_items', 'site_order_items_order_product_variant_unique')) {
            Schema::table('site_test_order_items', function (Blueprint $table) {
                $table->unique(
                    ['order_id', 'product_id', 'variant_id'],
                    'site_order_items_order_product_variant_unique'
                );
            });
        }

        if (!Schema::hasTable('asaas_webhook_events')) {
            Schema::create('asaas_webhook_events', function (Blueprint $table) {
                $table->id();
                $table->string('event_id', 150)->unique();
                $table->string('event_type', 100);
                $table->string('asaas_payment_id', 120)->nullable();
                $table->string('processing_status', 30)->default('pending');
                $table->text('last_error')->nullable();
                $table->timestamp('received_at')->useCurrent();
                $table->timestamp('processed_at')->nullable();
                $table->timestamps();
                $table->index('processing_status');
                $table->index('asaas_payment_id');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('asaas_webhook_events');

        if ($this->indexExists('site_test_order_items', 'site_order_items_order_product_variant_unique')) {
            Schema::table('site_test_order_items', function (Blueprint $table) {
                $table->dropUnique('site_order_items_order_product_variant_unique');
            });
        }

        if (Schema::hasColumn('site_test_order_items', 'variant_id')) {
            Schema::table('site_test_order_items', function (Blueprint $table) {
                $table->dropForeign(['variant_id']);
                $table->dropColumn('variant_id');
            });
        }

        if ($this->indexExists('site_test_order_items', 'site_test_order_items_order_id_index')) {
            Schema::table('site_test_order_items', function (Blueprint $table) {
                $table->dropIndex('site_test_order_items_order_id_index');
            });
        }

        if (!$this->indexExists('site_test_order_items', 'site_test_order_items_order_id_product_id_unique')) {
            Schema::table('site_test_order_items', function (Blueprint $table) {
                $table->unique(['order_id', 'product_id']);
            });
        }

        Schema::table('site_test_orders', function (Blueprint $table) {
            foreach ([
                'order_status',
                'payment_status',
                'payment_method',
                'asaas_checkout_id',
                'asaas_payment_id',
                'asaas_checkout_url',
                'payment_due_at',
                'paid_at',
                'expires_at',
                'reservation_released_at',
                'pdv_released_at',
            ] as $column) {
                if (Schema::hasColumn('site_test_orders', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
