<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('insurance', function (Blueprint $table) {

            $table->id();

            $table->date('loss_date')->nullable();

            $table->string('nature_of_claim', 50)->nullable();

            $table->string('from_location_code', 15)->nullable();
            $table->string('from_location', 50)->nullable();

            $table->string('to_location_code', 15)->nullable();
            $table->string('to_location', 50)->nullable();

            $table->string('invoice_no', 20)->nullable();
            $table->date('invoice_date')->nullable();

            $table->string('transporter_name', 25)->nullable();

            $table->string('lr_no', 25)->nullable();
            $table->date('lr_date')->nullable();

            $table->decimal('damage_value', 15, 2)->default(0);
            $table->decimal('shortage_value', 15, 2)->default(0);
            $table->decimal('total_value', 15, 2)->default(0);
            $table->string('invoice_file')->nullable();
            $table->string('pod_lr_copy')->nullable();

            $table->unsignedBigInteger('created_by')->nullable();

            $table->timestamps();

            /*  Indexes*/

            $table->index('from_location_code');
            $table->index('to_location_code');
            $table->index('invoice_no');
            $table->index('lr_no');
            $table->index('loss_date');
        });
    }


    public function down(): void
    {
        Schema::dropIfExists('insurance');
    }
};