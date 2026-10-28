<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('slab_master', function (Blueprint $table) {
            $table->id();
            $table->string('from_code', 20)->nullable();
            $table->string('from_name', 255)->nullable();
            $table->string('origin_region', 255)->nullable();
            $table->string('to_name', 255)->nullable();
            $table->string('to_code', 20)->nullable();
            $table->string('destination_region', 255)->nullable();
            $table->string('vendor_code', 20)->nullable();
            $table->string('vendor_name', 255)->nullable();
            $table->string('mode', 20)->nullable();
            $table->string('truck_code', 20)->nullable();
            $table->string('truck_description', 255)->nullable();
            $table->decimal('slab_1', 18, 4)->nullable();
            $table->decimal('slab_2', 18, 4)->nullable();
            $table->decimal('slab_3', 18, 4)->nullable();
            $table->decimal('slab_4', 18, 4)->nullable();
            $table->decimal('slab_5', 18, 4)->nullable();
            $table->decimal('slab_6', 18, 4)->nullable();
            $table->decimal('slab_7', 18, 4)->nullable();
            $table->decimal('slab_8', 18, 4)->nullable();
            $table->decimal('slab_9', 18, 4)->nullable();
            $table->decimal('slab_10', 18, 4)->nullable();
            $table->decimal('slab_1_rate', 18, 4)->nullable();
            $table->decimal('slab_2_rate', 18, 4)->nullable();
            $table->decimal('slab_3_rate', 18, 4)->nullable();
            $table->decimal('slab_4_rate', 18, 4)->nullable();
            $table->decimal('slab_5_rate', 18, 4)->nullable();
            $table->decimal('slab_6_rate', 18, 4)->nullable();
            $table->decimal('slab_7_rate', 18, 4)->nullable();
            $table->decimal('slab_8_rate', 18, 4)->nullable();
            $table->decimal('slab_9_rate', 18, 4)->nullable();
            $table->decimal('slab_10_rate', 18, 4)->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();
            $table->index(['mode', 'vendor_code', 'from_code', 'to_code'], 'slab_master_lookup_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('slab_master');
    }
};
