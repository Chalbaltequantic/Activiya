<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('vendor_pods', function (Blueprint $table) {
            $table->id();
            $table->string('vendor_code', 50)->index();
            $table->string('invoice_no', 100);
            $table->string('lr_no', 100);
            $table->string('lr_no_key', 100)->unique(); // normalized, globally unique, retained on soft delete
            $table->string('pod_front_path');
            $table->string('pod_back_path');
            $table->string('other_document_path')->nullable();
            $table->unsignedBigInteger('uploaded_by');
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->unsignedBigInteger('deleted_by')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->index(['vendor_code', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vendor_pods');
    }
};
