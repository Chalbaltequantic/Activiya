<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('purchase_order_items', function (Blueprint $table) {
            $table->id();

            $table->foreignId('purchase_order_id')
                ->constrained('purchase_orders')
                ->cascadeOnDelete();

            $table->unsignedInteger('line_no')->nullable();

            $table->string('item_code')->nullable();
            $table->text('description')->nullable();

            $table->string('hsn_code')->nullable();
            $table->string('ean')->nullable();

            $table->decimal('quantity', 18, 3)->nullable();
            $table->string('uom', 50)->nullable();

            $table->decimal('mrp', 18, 2)->nullable();
            $table->decimal('unit_cost', 18, 2)->nullable();

            $table->decimal('cgst_percent', 8, 3)->nullable();
            $table->decimal('sgst_percent', 8, 3)->nullable();
            $table->decimal('igst_percent', 8, 3)->nullable();
            $table->decimal('cess_percent', 8, 3)->nullable();

            $table->decimal('total_amount', 18, 2)->nullable();

            $table->json('raw_data')->nullable();

            $table->timestamps();

            $table->index('purchase_order_id');
            $table->index('item_code');
            $table->index('hsn_code');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('purchase_order_items');
    }
};