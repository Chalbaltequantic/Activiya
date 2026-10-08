<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('freight_bill_approval_histories', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('bill_data_id');
            $table->string('status', 20);
            $table->text('remark')->nullable();
            $table->unsignedBigInteger('action_by')->nullable();
            $table->timestamps();

            $table->index(['bill_data_id', 'id']);
            $table->index('status');
            $table->index('action_by');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('freight_bill_approval_histories');
    }
};