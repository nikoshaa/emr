<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddGeneralUncentToPasienTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('pasien', function (Blueprint $table) {
            // Add the column, make it nullable if it's not always required
            // Adjust 'after' to place it where you want in the table structure
            if (!Schema::hasColumn('pasien', 'general_uncent')) {
                $table->longText('general_uncent')->nullable()->after('alergi_key'); // Example: place after 'alergi'
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
            // Drop the column if it exists
             if (Schema::hasColumn('pasien', 'general_uncent')) {
                $table->dropColumn('general_uncent');
            }
        });
    }
}