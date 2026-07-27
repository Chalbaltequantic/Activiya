<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement('DROP VIEW IF EXISTS digiwim_inventory_view');

        DB::statement("
            CREATE VIEW digiwim_inventory_view AS

            SELECT
                ledger.material_code,
                ledger.material_description,
                ledger.division,
                ledger.brand,
                ledger.sub_brand,
                ledger.uom,
                ledger.piece_per_box,
                ledger.mrp,
                ledger.weight,
                ledger.volume,
                ledger.storage_plant_code,
                ledger.storage_plant_name,
                ledger.storage_plant_location,
                ledger.batch_no,
                ledger.mfg_date,
                ledger.expiry_date,
                ledger.bin_no,

                SUM(COALESCE(ledger.inward_qty, 0))
                    AS total_inward_qty,

                SUM(COALESCE(ledger.outward_qty, 0))
                    AS total_outward_qty,

                (
                    SUM(COALESCE(ledger.inward_qty, 0))
                    -
                    SUM(COALESCE(ledger.outward_qty, 0))
                ) AS available_qty

            FROM
            (
                /*
                |--------------------------------------------------------------------------
                | INWARD = digi_wim
                |--------------------------------------------------------------------------
                */

                SELECT
                    dw.m_code COLLATE utf8mb4_general_ci
                        AS material_code,

                    dw.material_descriptions COLLATE utf8mb4_general_ci
                        AS material_description,

                    m.division COLLATE utf8mb4_general_ci
                        AS division,

                    m.brand COLLATE utf8mb4_general_ci
                        AS brand,

                    m.sub_brand COLLATE utf8mb4_general_ci
                        AS sub_brand,

                    m.uom COLLATE utf8mb4_general_ci
                        AS uom,

                    m.piece_per_box
                        AS piece_per_box,

                    CAST(NULL AS DECIMAL(12,2))
                        AS mrp,

                    m.gross_weight_kg
                        AS weight,

                    m.volume_cft
                        AS volume,

                    sp.plant_site_code COLLATE utf8mb4_general_ci
                        AS storage_plant_code,

                    sp.plant_site_name COLLATE utf8mb4_general_ci
                        AS storage_plant_name,

                    sp.city COLLATE utf8mb4_general_ci
                        AS storage_plant_location,

                    dw.batch_no COLLATE utf8mb4_general_ci
                        AS batch_no,

                    dw.mfg_date
                        AS mfg_date,

                    dw.expiry_date
                        AS expiry_date,

                    CAST(NULL AS CHAR CHARACTER SET utf8mb4)
                        COLLATE utf8mb4_general_ci
                        AS bin_no,

                    CAST(
                        COALESCE(dw.qty_units, 0)
                        AS DECIMAL(12,2)
                    ) AS inward_qty,

                    CAST(
                        0
                        AS DECIMAL(12,2)
                    ) AS outward_qty

                FROM digi_wim AS dw

                LEFT JOIN materials AS m
                    ON m.material_code COLLATE utf8mb4_general_ci
                    =
                    dw.m_code COLLATE utf8mb4_general_ci

                LEFT JOIN site_plants AS sp
                    ON sp.plant_site_code COLLATE utf8mb4_general_ci
                    =
                    dw.consignee_code COLLATE utf8mb4_general_ci


                UNION ALL


                /*
                |--------------------------------------------------------------------------
                | OUTWARD = digiwim_preloading
                |--------------------------------------------------------------------------
                */

                SELECT
                    pl.material_code COLLATE utf8mb4_general_ci
                        AS material_code,

                    pl.material_description COLLATE utf8mb4_general_ci
                        AS material_description,

                    m.division COLLATE utf8mb4_general_ci
                        AS division,

                    m.brand COLLATE utf8mb4_general_ci
                        AS brand,

                    m.sub_brand COLLATE utf8mb4_general_ci
                        AS sub_brand,

                    m.uom COLLATE utf8mb4_general_ci
                        AS uom,

                    m.piece_per_box
                        AS piece_per_box,

                    CAST(NULL AS DECIMAL(12,2))
                        AS mrp,

                    m.gross_weight_kg
                        AS weight,

                    m.volume_cft
                        AS volume,

                    sp.plant_site_code COLLATE utf8mb4_general_ci
                        AS storage_plant_code,

                    sp.plant_site_name COLLATE utf8mb4_general_ci
                        AS storage_plant_name,

                    sp.city COLLATE utf8mb4_general_ci
                        AS storage_plant_location,

                    pl.batch_no COLLATE utf8mb4_general_ci
                        AS batch_no,

                    pl.mfg_date
                        AS mfg_date,

                    pl.expiry_date
                        AS expiry_date,

                    pl.bin_no COLLATE utf8mb4_general_ci
                        AS bin_no,

                    CAST(
                        0
                        AS DECIMAL(12,2)
                    ) AS inward_qty,

                    CAST(
                        COALESCE(pl.qty, 0)
                        AS DECIMAL(12,2)
                    ) AS outward_qty

                FROM digiwim_preloading AS pl

                LEFT JOIN materials AS m
                    ON m.material_code COLLATE utf8mb4_general_ci
                    =
                    pl.material_code COLLATE utf8mb4_general_ci

                LEFT JOIN site_plants AS sp
                    ON sp.plant_site_code COLLATE utf8mb4_general_ci
                    =
                    pl.consignee_code COLLATE utf8mb4_general_ci

            ) AS ledger

            GROUP BY
                ledger.material_code,
                ledger.material_description,
                ledger.division,
                ledger.brand,
                ledger.sub_brand,
                ledger.uom,
                ledger.piece_per_box,
                ledger.mrp,
                ledger.weight,
                ledger.volume,
                ledger.storage_plant_code,
                ledger.storage_plant_name,
                ledger.storage_plant_location,
                ledger.batch_no,
                ledger.mfg_date,
                ledger.expiry_date,
                ledger.bin_no
        ");
    }

    public function down(): void
    {
        DB::statement('DROP VIEW IF EXISTS digiwim_inventory_view');
    }
};