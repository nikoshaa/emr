<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPoliToObatTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('obat', function (Blueprint $table) {
            $table->unsignedBigInteger('poli_id')->nullable()->after('id');
            $table->foreign('poli_id')->references('id')->on('poli')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('obat', function (Blueprint $table) {
            $table->dropForeign(['poli_id']);
            $table->dropColumn('poli_id');
        });
    }
}