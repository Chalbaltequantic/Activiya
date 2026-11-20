
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('freight_validation_histories')) {
            throw new \RuntimeException(
                'The freight_validation_histories table does not exist.'
            );
        }

        Schema::table('freight_validation_histories', function (Blueprint $table) {

            if (!Schema::hasColumn('freight_validation_histories', 'remarks')) {
                $table->text('remarks')->nullable();
            }

            if (!Schema::hasColumn('freight_validation_histories', 'old_values')) {
                $table->json('old_values')->nullable();
            }

            if (!Schema::hasColumn('freight_validation_histories', 'new_values')) {
                $table->json('new_values')->nullable();
            }

            if (!Schema::hasColumn('freight_validation_histories', 'action_by')) {
                $table->unsignedBigInteger('action_by')->nullable();
            }

            if (!Schema::hasColumn('freight_validation_histories', 'action_by_role')) {
                $table->string('action_by_role', 50)->nullable();
            }
        });
    }

    public function down(): void
    {
        // Intentionally do not drop columns automatically.
        // Preserve freight validation history records.
    }
};
