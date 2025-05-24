<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddEncryptedColumnsToPasienTable extends Migration
{
    /**
     * List of columns to encrypt (excluding id, timestamps, etc.)
     */
    private $encryptedColumns = [
        "no_rm", "nama", "tmp_lahir", "tgl_lahir", "jk", "alamat_lengkap",
        "kelurahan", "kecamatan", "kabupaten", "kodepos", "agama", "status_menikah",
        "pendidikan", "pekerjaan", "kewarganegaraan", "no_hp", "cara_bayar",
        "no_bpjs", "alergi"
        // Add any other columns you need encrypted here
    ];

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('pasien', function (Blueprint $table) {
            foreach ($this->encryptedColumns as $column) {
                // Add column for encrypted data
                $table->text($column . '_encrypted')->nullable()->after($column);
                // Add column for the encrypted AES key used for the data
                $table->text($column . '_key')->nullable()->after($column . '_encrypted');
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
            $columnsToDrop = [];
            foreach ($this->encryptedColumns as $column) {
                $columnsToDrop[] = $column . '_encrypted';
                $columnsToDrop[] = $column . '_key';
            }
            $table->dropColumn($columnsToDrop);
        });
    }
}