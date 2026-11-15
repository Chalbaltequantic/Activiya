<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
 public function up(): void {Schema::create('freight_ocr_results',function(Blueprint $t){$t->id();$t->foreignId('batch_id')->constrained('freight_ocr_batches')->cascadeOnDelete();$t->unsignedBigInteger('billdata_id')->index();$t->string('status',30)->default('queued');$t->text('remarks')->nullable();$t->json('reasons')->nullable();$t->json('extracted')->nullable();$t->timestamps();$t->unique(['batch_id','billdata_id']);});}
 public function down(): void {Schema::dropIfExists('freight_ocr_results');}
};
