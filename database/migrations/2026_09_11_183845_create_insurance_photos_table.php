<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('insurance_photos', function (Blueprint $table) {

            $table->id();
            $table->unsignedBigInteger('insurance_id');
            $table->string('photo_path');
            $table->string('original_name')->nullable();
            $table->unsignedBigInteger('uploaded_by')->nullable();
            $table->timestamps();

            $table->foreign('insurance_id')
                ->references('id')
                ->on('insurance')
                ->onDelete('cascade');

            $table->index('insurance_id');
        });
    }


    public function down(): void
    {
        Schema::dropIfExists('insurance_photos');
    }
};