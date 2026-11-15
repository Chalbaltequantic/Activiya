<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
 public function up(): void { Schema::create('freight_ocr_batches',function(Blueprint $t){$t->id();$t->unsignedBigInteger('created_by');$t->unsignedInteger('total')->default(0);$t->string('status',30)->default('queued');$t->timestamps();}); }
 public function down(): void {Schema::dropIfExists('freight_ocr_batches');}
};
