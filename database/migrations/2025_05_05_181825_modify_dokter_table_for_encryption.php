<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ModifyDokterTableForEncryption extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('dokter', function (Blueprint $table) {
            // Define columns to drop
            $columnsToDrop = [];
            if (Schema::hasColumn('dokter', 'nip')) {
                $columnsToDrop[] = 'nip';
            }
            if (Schema::hasColumn('dokter', 'nama')) {
                $columnsToDrop[] = 'nama';
            }
            if (Schema::hasColumn('dokter', 'no_hp')) {
                $columnsToDrop[] = 'no_hp';
            }
            if (Schema::hasColumn('dokter', 'alamat')) {
                $columnsToDrop[] = 'alamat';
            }

            // Drop the original columns if they exist
            if (!empty($columnsToDrop)) {
                $table->dropColumn($columnsToDrop);
            }

            // Add new encrypted and key columns
            // Adjust 'after' clauses based on your preferred column order
            if (!Schema::hasColumn('dokter', 'nip_encrypted')) {
                $table->text('nip_encrypted')->nullable()->after('id'); // Example position
            }
            if (!Schema::hasColumn('dokter', 'nip_key')) {
                $table->text('nip_key')->nullable()->after('nip_encrypted');
            }
            if (!Schema::hasColumn('dokter', 'nama_encrypted')) {
                $table->text('nama_encrypted')->nullable()->after('nip_key');
            }
            if (!Schema::hasColumn('dokter', 'nama_key')) {
                $table->text('nama_key')->nullable()->after('nama_encrypted');
            }
            if (!Schema::hasColumn('dokter', 'no_hp_encrypted')) {
                $table->text('no_hp_encrypted')->nullable()->after('nama_key');
            }
            if (!Schema::hasColumn('dokter', 'no_hp_key')) {
                $table->text('no_hp_key')->nullable()->after('no_hp_encrypted');
            }
            if (!Schema::hasColumn('dokter', 'alamat_encrypted')) {
                $table->text('alamat_encrypted')->nullable()->after('no_hp_key');
            }
            if (!Schema::hasColumn('dokter', 'alamat_key')) {
                $table->text('alamat_key')->nullable()->after('alamat_encrypted');
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
        Schema::table('dokter', function (Blueprint $table) {
            // Re-add original columns (adjust types/constraints as needed)
            if (!Schema::hasColumn('dokter', 'nip')) {
                $table->string('nip')->nullable()->after('id'); // Or original type/position
            }
            if (!Schema::hasColumn('dokter', 'nama')) {
                $table->string('nama')->nullable()->after('nip'); // Or original type/position
            }
            if (!Schema::hasColumn('dokter', 'no_hp')) {
                $table->string('no_hp')->nullable()->after('nama'); // Or original type/position
            }
            if (!Schema::hasColumn('dokter', 'alamat')) {
                $table->text('alamat')->nullable()->after('no_hp'); // Or original type/position
            }

            // Drop the encrypted and key columns
            $columnsToDrop = [];
            if (Schema::hasColumn('dokter', 'nip_encrypted')) $columnsToDrop[] = 'nip_encrypted';
            if (Schema::hasColumn('dokter', 'nip_key')) $columnsToDrop[] = 'nip_key';
            if (Schema::hasColumn('dokter', 'nama_encrypted')) $columnsToDrop[] = 'nama_encrypted';
            if (Schema::hasColumn('dokter', 'nama_key')) $columnsToDrop[] = 'nama_key';
            if (Schema::hasColumn('dokter', 'no_hp_encrypted')) $columnsToDrop[] = 'no_hp_encrypted';
            if (Schema::hasColumn('dokter', 'no_hp_key')) $columnsToDrop[] = 'no_hp_key';
            if (Schema::hasColumn('dokter', 'alamat_encrypted')) $columnsToDrop[] = 'alamat_encrypted';
            if (Schema::hasColumn('dokter', 'alamat_key')) $columnsToDrop[] = 'alamat_key';

            if (!empty($columnsToDrop)) {
                $table->dropColumn($columnsToDrop);
            }
        });
    }
}