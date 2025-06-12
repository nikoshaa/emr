<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFilesToRekamTable extends Migration
{
    public function up()
    {
        Schema::table('rekam', function (Blueprint $table) {
            $table->string('keluhan_file')->nullable()->after('encrypted_keluhan_aes_key');
            $table->string('pemeriksaan_file')->nullable()->after('encrypted_pemeriksaan_aes_key');
            $table->string('diagnosa_file')->nullable()->after('encrypted_diagnosa_aes_key');
            $table->string('tindakan_file')->nullable()->after('encrypted_tindakan_aes_key');
        });
    }

    public function down()
    {
        Schema::table('rekam', function (Blueprint $table) {
            $table->dropColumn(['keluhan_file', 'pemeriksaan_file', 'diagnosa_file', 'tindakan_file']);
        });
    }
}