<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class DropOriginalPasienColumns extends Migration
{
    /**
     * List of original columns to drop
     */
    private $originalColumns = [
        "no_rm", "nama", "tmp_lahir", "tgl_lahir", "jk", "alamat_lengkap",
        "kelurahan", "kecamatan", "kabupaten", "kodepos", "agama", "status_menikah",
        "pendidikan", "pekerjaan", "kewarganegaraan", "no_hp", "cara_bayar",
        "no_bpjs", "alergi"
        // Add general_uncent here if it wasn't dropped in a previous migration
        // "general_uncent"
    ];

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('pasien', function (Blueprint $table) {
            // Check if columns exist before dropping to avoid errors on re-run
            $columnsToDrop = [];
            foreach ($this->originalColumns as $column) {
                if (Schema::hasColumn('pasien', $column)) {
                    $columnsToDrop[] = $column;
                }
            }
            if (!empty($columnsToDrop)) {
                $table->dropColumn($columnsToDrop);
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
        Schema::table('pasien', function (Blueprint $table) {
            // Re-add columns if rolling back.
            // You might need to adjust types and nullability based on original schema.
            // This is best-effort for rollback; restoring data requires backups.
            foreach ($this->originalColumns as $column) {
                 if (!Schema::hasColumn('pasien', $column)) {
                     // Guessing common types, adjust as needed!
                     if (in_array($column, ['tgl_lahir'])) {
                         $table->date($column)->nullable();
                     } elseif (in_array($column, ['alergi', 'alamat_lengkap'])) {
                         $table->text($column)->nullable();
                     } else {
                         $table->string($column)->nullable();
                     }
                 }
            }
        });
    }
}