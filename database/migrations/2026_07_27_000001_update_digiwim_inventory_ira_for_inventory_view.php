<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasColumn('digiwim_inventory_ira', 'digiwim_preloading_id')) {
            DB::statement('ALTER TABLE digiwim_inventory_ira MODIFY digiwim_preloading_id BIGINT UNSIGNED NULL');
        }

        Schema::table('digiwim_inventory_ira', function (Blueprint $table) {
            if (!Schema::hasColumn('digiwim_inventory_ira', 'inventory_key')) {
                $table->char('inventory_key', 64)->nullable()->after('digiwim_preloading_id');
            }
            if (!Schema::hasColumn('digiwim_inventory_ira', 'material_code')) {
                $table->string('material_code', 100)->nullable()->after('inventory_key');
            }
            if (!Schema::hasColumn('digiwim_inventory_ira', 'material_description')) {
                $table->string('material_description', 500)->nullable()->after('material_code');
            }
            if (!Schema::hasColumn('digiwim_inventory_ira', 'division')) {
                $table->string('division', 150)->nullable()->after('material_description');
            }
            if (!Schema::hasColumn('digiwim_inventory_ira', 'brand')) {
                $table->string('brand', 150)->nullable()->after('division');
            }
            if (!Schema::hasColumn('digiwim_inventory_ira', 'sub_brand')) {
                $table->string('sub_brand', 150)->nullable()->after('brand');
            }
            if (!Schema::hasColumn('digiwim_inventory_ira', 'uom')) {
                $table->string('uom', 50)->nullable()->after('sub_brand');
            }
            if (!Schema::hasColumn('digiwim_inventory_ira', 'piece_per_box')) {
                $table->decimal('piece_per_box', 18, 3)->nullable()->after('uom');
            }
            if (!Schema::hasColumn('digiwim_inventory_ira', 'mrp')) {
                $table->decimal('mrp', 18, 2)->nullable()->after('piece_per_box');
            }
            if (!Schema::hasColumn('digiwim_inventory_ira', 'weight')) {
                $table->decimal('weight', 18, 3)->nullable()->after('mrp');
            }
            if (!Schema::hasColumn('digiwim_inventory_ira', 'volume')) {
                $table->decimal('volume', 18, 3)->nullable()->after('weight');
            }
            if (!Schema::hasColumn('digiwim_inventory_ira', 'storage_plant_code')) {
                $table->string('storage_plant_code', 100)->nullable()->after('volume');
            }
            if (!Schema::hasColumn('digiwim_inventory_ira', 'storage_plant_name')) {
                $table->string('storage_plant_name', 255)->nullable()->after('storage_plant_code');
            }
            if (!Schema::hasColumn('digiwim_inventory_ira', 'storage_plant_location')) {
                $table->string('storage_plant_location', 255)->nullable()->after('storage_plant_name');
            }
            if (!Schema::hasColumn('digiwim_inventory_ira', 'batch_no')) {
                $table->string('batch_no', 150)->nullable()->after('storage_plant_location');
            }
            if (!Schema::hasColumn('digiwim_inventory_ira', 'mfg_date')) {
                $table->date('mfg_date')->nullable()->after('batch_no');
            }
            if (!Schema::hasColumn('digiwim_inventory_ira', 'expiry_date')) {
                $table->date('expiry_date')->nullable()->after('mfg_date');
            }
            if (!Schema::hasColumn('digiwim_inventory_ira', 'inventory_qty')) {
                $table->decimal('inventory_qty', 35, 3)->nullable()->after('expiry_date');
            }
        });

        if (!$this->indexExists('digiwim_inventory_ira', 'digiwim_inventory_ira_inventory_key_unique')) {
            Schema::table('digiwim_inventory_ira', function (Blueprint $table) {
                $table->unique('inventory_key', 'digiwim_inventory_ira_inventory_key_unique');
            });
        }
    }

    public function down(): void
    {
        if ($this->indexExists('digiwim_inventory_ira', 'digiwim_inventory_ira_inventory_key_unique')) {
            Schema::table('digiwim_inventory_ira', function (Blueprint $table) {
                $table->dropUnique('digiwim_inventory_ira_inventory_key_unique');
            });
        }

        Schema::table('digiwim_inventory_ira', function (Blueprint $table) {
            foreach ([
                'inventory_key','material_code','material_description','division','brand','sub_brand','uom',
                'piece_per_box','mrp','weight','volume','storage_plant_code','storage_plant_name',
                'storage_plant_location','batch_no','mfg_date','expiry_date','inventory_qty'
            ] as $column) {
                if (Schema::hasColumn('digiwim_inventory_ira', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }

    private function indexExists(string $table, string $index): bool
    {
        return DB::table('information_schema.statistics')
            ->where('table_schema', DB::getDatabaseName())
            ->where('table_name', $table)
            ->where('index_name', $index)
            ->exists();
    }
};
