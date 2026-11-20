<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('freight_validation_histories', function (Blueprint $table) {
            $table->id();

            // Related freight record
            $table->unsignedBigInteger('billdata_id')->index();

            // Action performed
            $table->string('action', 50)->nullable();

            // Status before and after action
            $table->string('old_status', 50)->nullable();
            $table->string('new_status', 50)->nullable();

            // Submission and return flags
            $table->boolean('old_submit')->nullable();
            $table->boolean('new_submit')->nullable();

            $table->boolean('old_f_return')->nullable();
            $table->boolean('new_f_return')->nullable();

            // Validation remarks
            $table->text('remark')->nullable();

            // User performing the action
            $table->unsignedBigInteger('created_by')->nullable()->index();

            // Optional additional information
            $table->json('old_data')->nullable();
            $table->json('new_data')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('freight_validation_histories');
    }
};
