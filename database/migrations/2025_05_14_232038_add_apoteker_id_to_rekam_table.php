<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddApotekerIdToRekamTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('rekam', function (Blueprint $table) {
            $table->unsignedBigInteger('apoteker_id')->nullable()->after('id');
            $table->foreign('apoteker_id')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('rekam', function (Blueprint $table) {
            $table->dropForeign(['apoteker_id']);
            $table->dropColumn('apoteker_id');
        });
    }
}