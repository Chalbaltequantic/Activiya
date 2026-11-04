<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bill_data_upload', function (Blueprint $table) {
            $table->decimal('chargeable_weight', 12, 2)->nullable();
            $table->decimal('actual_gross_weight', 12, 2)->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('bill_data_upload', function (Blueprint $table) {
            $table->dropColumn([
                'chargeable_weight',
                'actual_gross_weight'
            ]);
        });
    }
};