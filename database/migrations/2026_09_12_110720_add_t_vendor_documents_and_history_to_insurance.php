<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('insurance', function (Blueprint $table) {
            $table->string('cof_file')->nullable()->after('pod_lr_copy');
            $table->string('fir_police_report_file')->nullable()->after('cof_file');
            $table->string('fire_report_file')->nullable()->after('fir_police_report_file');
            $table->string('fir_closure_report_file')->nullable()->after('fire_report_file');
        });

        Schema::create('insurance_file_histories', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('insurance_id');
            $table->string('document_type', 50);
            $table->string('file_path')->nullable();
            $table->string('original_name')->nullable();
            $table->string('action', 30);
            $table->unsignedBigInteger('uploaded_by')->nullable();
            $table->timestamps();

            $table->foreign('insurance_id')
                ->references('id')
                ->on('insurance')
                ->onDelete('cascade');

            $table->index(['insurance_id', 'document_type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('insurance_file_histories');

        Schema::table('insurance', function (Blueprint $table) {
            $table->dropColumn([
                'cof_file',
                'fir_police_report_file',
                'fire_report_file',
                'fir_closure_report_file'
            ]);
        });
    }
};