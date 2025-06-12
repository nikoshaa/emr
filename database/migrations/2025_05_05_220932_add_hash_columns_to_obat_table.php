<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Log; // Added for logging in down()

class AddHashColumnsToObatTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('obat', function (Blueprint $table) {
            // 1. Drop original columns if they exist (as value is in _encrypted)
            //    This assumes the previous migration might have left them or reverted them.
            $columnsToDrop = [];
            if (Schema::hasColumn('obat', 'kd_obat')) $columnsToDrop[] = 'kd_obat';
            if (Schema::hasColumn('obat', 'nama')) $columnsToDrop[] = 'nama';
            if (!empty($columnsToDrop)) {
                $table->dropColumn($columnsToDrop);
            }

            // 2. Add the dedicated hash columns with unique constraints
            //    Place them logically, e.g., after 'id'
            $afterColumn = 'id'; // Example position
            if (!Schema::hasColumn('obat', 'kd_obat_hash')) {
                $table->string('kd_obat_hash', 64)->nullable()->unique()->after($afterColumn);
            }
            if (!Schema::hasColumn('obat', 'nama_hash')) {
                $table->string('nama_hash', 64)->nullable()->unique()->after('kd_obat_hash');
            }

            // 3. Ensure encrypted and key columns exist (adjust 'after' as needed)
            $afterHashColumns = 'nama_hash'; // Place encrypted after hash columns
            if (!Schema::hasColumn('obat', 'kd_obat_encrypted')) {
                $table->text('kd_obat_encrypted')->nullable()->after($afterHashColumns);
            }
            if (!Schema::hasColumn('obat', 'kd_obat_key')) {
                $table->text('kd_obat_key')->nullable()->after('kd_obat_encrypted');
            }
            if (!Schema::hasColumn('obat', 'nama_encrypted')) {
                $table->text('nama_encrypted')->nullable()->after('kd_obat_key');
            }
            if (!Schema::hasColumn('obat', 'nama_key')) {
                $table->text('nama_key')->nullable()->after('nama_encrypted');
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('obat', function (Blueprint $table) {
            // 1. Drop the hash columns
            $hashColumnsToDrop = [];
            if (Schema::hasColumn('obat', 'kd_obat_hash')) $hashColumnsToDrop[] = 'kd_obat_hash';
            if (Schema::hasColumn('obat', 'nama_hash')) $hashColumnsToDrop[] = 'nama_hash';

            // Need to drop unique constraints before columns if DB requires it
            foreach ($hashColumnsToDrop as $col) {
                 try {
                    // Construct index name (Laravel default: table_column_unique)
                    $indexName = $table->getTable() . '_' . $col . '_unique';
                    $table->dropUnique($indexName);
                 } catch (\Exception $e) {
                    Log::warning("Could not drop unique index on {$col} during migration rollback: " . $e->getMessage());
                 }
            }
            if (!empty($hashColumnsToDrop)) {
                $table->dropColumn($hashColumnsToDrop);
            }


            // 2. Re-add the original columns (optional, depends on desired rollback state)
            //    If re-added, they would typically hold NULL as the value is in _encrypted.
            if (!Schema::hasColumn('obat', 'kd_obat')) {
                 // Add back with original type (assuming string, adjust if different)
                 // Place it appropriately, e.g., after id
                $table->string('kd_obat')->nullable()->after('id');
            }
             if (!Schema::hasColumn('obat', 'nama')) {
                $table->string('nama')->nullable()->after('kd_obat');
            }

            // Note: Keep the _encrypted and _key columns as the model expects them.
            // Dropping them would require reverting the model changes too.
        });
    }
}