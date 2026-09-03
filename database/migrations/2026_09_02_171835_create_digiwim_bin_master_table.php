<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('digiwim_bin_master', function (Blueprint $table) {

            $table->id();

            $table->string('plant_code', 15);
            $table->string('plant_name', 50);

            $table->string('bin_no', 20);
            $table->string('bin_type', 20)->nullable();

            $table->enum('bin_status', ['Active', 'Inactive'])
                  ->default('Active');

            $table->string('storage_location', 50)->nullable();
            $table->string('storage_section', 25)->nullable();
            $table->string('bin_location', 50)->nullable();

            // Dimensions in inches
            $table->decimal('bin_length', 9, 2)->nullable();
            $table->decimal('bin_width', 9, 2)->nullable();
            $table->decimal('bin_height', 9, 2)->nullable();

            // Calculated volume
            $table->decimal('bin_volume_cft_cap', 9, 2)->nullable();
            $table->decimal('bin_volume_cft_cap_2', 9, 2)->nullable();

            // Weight
            $table->decimal('bin_weight_kg_cap', 9, 2)->nullable();
            $table->decimal('bin_weight_kg_cap_2', 9, 2)->nullable();

            // Custom fields
            $table->string('custom1', 50)->nullable();
            $table->string('custom2', 50)->nullable();
            $table->string('custom3', 50)->nullable();
            $table->string('custom4', 50)->nullable();
            $table->string('custom5', 50)->nullable();

            $table->unsignedBigInteger('created_by')->nullable();

            $table->timestamps();

            $table->index('plant_code');
            $table->index('bin_no');

            // Same BIN number should not repeat within same plant
            $table->unique(
                ['plant_code', 'bin_no'],
                'unique_plant_bin'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('digiwim_bin_master');
    }
};