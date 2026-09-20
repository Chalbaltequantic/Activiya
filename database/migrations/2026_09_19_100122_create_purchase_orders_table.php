<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('purchase_orders', function (Blueprint $table) {
            $table->id();

            $table->foreignId('po_import_id')
                ->nullable()
                ->constrained('po_imports')
                ->nullOnDelete();

            $table->foreignId('po_template_id')
                ->nullable()
                ->constrained('po_templates')
                ->nullOnDelete();

            $table->unsignedBigInteger('created_by');

            $table->string('po_no')->nullable();
            $table->date('po_date')->nullable();
            $table->date('delivery_date')->nullable();
            $table->date('expiry_date')->nullable();

            $table->string('vendor_code')->nullable();
            $table->string('vendor_name')->nullable();
            $table->string('vendor_email')->nullable();

            $table->string('buyer_name')->nullable();

            $table->text('bill_to')->nullable();
            $table->text('ship_to')->nullable();

            $table->string('buyer_gstin')->nullable();
            $table->string('vendor_gstin')->nullable();

            $table->decimal('basic_amount', 18, 2)->nullable();
            $table->decimal('tax_amount', 18, 2)->nullable();
            $table->decimal('total_amount', 18, 2)->nullable();

            $table->string('currency', 10)->default('INR');

            $table->string('original_filename');
            $table->string('file_path');

            $table->longText('raw_text')->nullable();
            $table->json('extracted_json')->nullable();

            $table->string('processing_status', 30)->default('processed');
            $table->text('processing_remark')->nullable();

            $table->timestamps();

            $table->index('created_by');
            $table->index('po_no');
            $table->index('po_date');
            $table->index('processing_status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('purchase_orders');
    }
};