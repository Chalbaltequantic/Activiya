<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('load_unload_master', function (Blueprint $table) {
            $table->id();
            $table->string('from_code', 20);
            $table->string('from_name', 100)->nullable();
            $table->string('origin_region', 25)->nullable();
            $table->string('to_name', 100)->nullable();
            $table->string('to_code', 20);
            $table->string('destination_region', 25)->nullable();
            $table->string('vendor_code', 20);
            $table->string('vendor_name', 100)->nullable();
            $table->string('mode', 20);
            $table->string('truck_code', 20);
            $table->string('truck_description', 100)->nullable();
            $table->string('charge_type', 10);
            $table->string('rate_type', 20);
            $table->decimal('rate', 18, 4);
            $table->decimal('load_unload_limit', 18, 4)->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();
            $table->index(['mode', 'vendor_code', 'from_code', 'to_code'], 'lum_rate_lookup_idx');
            $table->index(['truck_code', 'charge_type', 'rate_type'], 'lum_charge_lookup_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('load_unload_master');
    }
};
