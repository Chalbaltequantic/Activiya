<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('transport_rate_master', function (Blueprint $table) {
            $table->id();
            $table->string('lane_name', 100)->nullable();
            $table->string('from_code', 20)->nullable();
            $table->string('from_name', 50)->nullable();
            $table->string('origin_region', 25)->nullable();
            $table->string('to_name', 50)->nullable();
            $table->string('to_code', 20)->nullable();
            $table->string('destination_region', 25)->nullable();
            $table->string('vendor_code', 20)->nullable();
            $table->string('vendor_name', 50)->nullable();
            $table->string('mode', 20)->nullable();
            $table->decimal('chargeable_weight', 9, 2)->nullable();
            $table->decimal('cft_value', 9, 2)->nullable();
            $table->decimal('cft_weight', 9, 2)->nullable();
            $table->decimal('rate_logic', 9, 2)->nullable();
            $table->decimal('pickup_charges', 9, 2)->nullable();
            $table->decimal('delivery_charges', 9, 2)->nullable();
            $table->decimal('detention_rate', 9, 2)->nullable();
            $table->decimal('fov_on_base_rate', 9, 2)->nullable();
            $table->decimal('fuel_surcharge_on_base_rate', 9, 2)->nullable();
            $table->decimal('oda', 9, 2)->nullable();
            $table->decimal('oda_1', 9, 2)->nullable();
            $table->decimal('oda_2', 9, 2)->nullable();
            $table->decimal('minimum_chargeable_weight_case', 9, 2)->nullable();
            $table->date('validity_start_date')->nullable();
            $table->date('validity_end_date')->nullable();
            $table->decimal('docket_charges', 9, 2)->nullable();
            $table->string('custom', 100)->nullable();
            $table->string('custom_1', 100)->nullable();
            $table->string('custom_2', 100)->nullable();
            $table->string('custom_3', 100)->nullable();
            $table->string('custom_4', 100)->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();
            $table->index(['validity_start_date', 'validity_end_date'], 'trm_dates_idx');
        });
    }
    public function down(): void { Schema::dropIfExists('transport_rate_master'); }
};
