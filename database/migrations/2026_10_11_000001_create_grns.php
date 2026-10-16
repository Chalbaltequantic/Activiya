<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('grns', function (Blueprint $table) {
            $table->id();
            $table->string('client_code', 80)->index();
            $table->string('grn_number', 120)->nullable()->index();
            $table->date('grn_date')->nullable();
            $table->string('po_number', 120)->nullable()->index();
            $table->string('supplier_name')->nullable();
            $table->string('supplier_code', 100)->nullable();
            $table->string('supplier_gstin', 20)->nullable();
            $table->string('buyer_name')->nullable();
            $table->string('buyer_gstin', 20)->nullable();
            $table->text('delivery_address')->nullable();
            $table->string('invoice_number', 120)->nullable();
            $table->string('delivery_number', 120)->nullable();
            $table->decimal('challan_qty', 16, 3)->nullable();
            $table->decimal('received_qty', 16, 3)->nullable();
            $table->decimal('accepted_qty', 16, 3)->nullable();
            $table->string('original_name');
            $table->string('file_path');
            $table->string('file_sha256', 64)->index();
            $table->string('status', 40)->default('review_required');
            $table->json('raw_result')->nullable();
            $table->text('error_message')->nullable();
            $table->foreignId('uploaded_by')->nullable()->index();
            $table->timestamps();
        });

        Schema::create('grn_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('grn_id')->constrained()->cascadeOnDelete();
            $table->unsignedInteger('line_no');
            $table->string('item_code', 120)->nullable()->index();
            $table->string('ean', 120)->nullable()->index();
            $table->text('description')->nullable();
            $table->string('uom', 40)->nullable();
            $table->decimal('challan_qty', 16, 3)->nullable();
            $table->decimal('received_qty', 16, 3)->nullable();
            $table->decimal('accepted_qty', 16, 3)->nullable();
            $table->decimal('mrp', 16, 2)->nullable();
            $table->json('extra')->nullable();
            $table->timestamps();
        });

        Schema::create('grn_po_matches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('grn_id')->constrained()->cascadeOnDelete();
            $table->unsignedBigInteger('po_id')->nullable()->index();
            $table->string('po_number', 120)->index();
            $table->string('status', 40)->default('pending');
            $table->json('summary')->nullable();
            $table->timestamps();
            $table->unique(['grn_id', 'po_id']);
        });

        Schema::create('grn_reconciliation_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('match_id')
                ->constrained('grn_po_matches')
                ->cascadeOnDelete();
            $table->string('item_key', 150)->nullable();
            $table->string('description')->nullable();
            $table->decimal('po_qty', 16, 3)->nullable();
            $table->decimal('received_qty', 16, 3)->nullable();
            $table->decimal('accepted_qty', 16, 3)->nullable();
            $table->decimal('difference_qty', 16, 3)->nullable();
            $table->string('status', 40);
            $table->json('details')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('grn_reconciliation_items');
        Schema::dropIfExists('grn_po_matches');
        Schema::dropIfExists('grn_items');
        Schema::dropIfExists('grns');
    }
};
