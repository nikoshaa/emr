<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddUserIdToDokterTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('dokter', function (Blueprint $table) {
            $table->unsignedBigInteger('user_id')->after('id');  // menambah kolom user_id
            $table->foreign('user_id')->references('id')->on('users');  // membuat foreign key ke tabel users
        });
    }

    public function down()
    {
        Schema::table('dokter', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropColumn('user_id');
        });
    }
}
