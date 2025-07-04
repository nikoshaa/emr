<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIsDecryptedToRekamTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('rekam', function (Blueprint $table) {
            $table->boolean('is_decrypted')->default(false)->after('tindakan_file'); // Adjust 'after' as needed
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('rekam', function (Blueprint $table) {
            $table->dropColumn('is_decrypted');
        });
    }
}