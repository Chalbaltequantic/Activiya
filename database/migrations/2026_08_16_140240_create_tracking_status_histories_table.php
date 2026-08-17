<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tracking_status_histories', function (Blueprint $table) {

            $table->id();

            $table->unsignedBigInteger('tracking_id');

            $table->string('indent_no', 20)->nullable();

            $table->string('shipment_status', 20)->nullable();

            $table->string('transit_status', 20)->nullable();

            $table->integer('distance_covered')->nullable();
			$table->integer('distance_to_cover')->nullable();

            $table->string('current_location', 100)->nullable();

            $table->string('tracking_link', 100)->nullable();

            $table->string('driver_number', 20)->nullable();


            /*
             * Who updated the tracking
             */

            $table->unsignedBigInteger('updated_by')->nullable();

            $table->string('updated_by_type', 50)
                ->default('Vendor');


            /*
             * When tracking was updated
             */

            $table->dateTime('status_updated_at')->nullable();


            $table->timestamps();


            /*
             * Useful indexes
             */

            $table->index('tracking_id');

            $table->index('indent_no');

            $table->index('shipment_status');

            $table->index('updated_by');

            $table->index('status_updated_at');
        });
    }


    public function down(): void
    {
        Schema::dropIfExists('tracking_status_histories');
    }
};