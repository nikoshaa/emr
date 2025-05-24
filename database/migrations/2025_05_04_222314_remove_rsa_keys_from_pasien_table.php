<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RemoveRsaKeysFromPasienTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('pasien', function (Blueprint $table) {
            // Drop the columns if they exist
            if (Schema::hasColumn('pasien', 'rsa_public_key')) {
                $table->dropColumn('rsa_public_key');
            }
            if (Schema::hasColumn('pasien', 'encrypted_rsa_private_key')) {
                $table->dropColumn('encrypted_rsa_private_key');
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
            // Re-add the columns if rolling back (optional, but good practice)
            // You might want to adjust the 'after' clause based on your original structure
             if (!Schema::hasColumn('pasien', 'rsa_public_key')) {
                 $table->text('rsa_public_key')->nullable()->after('no_hp');
             }
             if (!Schema::hasColumn('pasien', 'encrypted_rsa_private_key')) {
                 $table->text('encrypted_rsa_private_key')->nullable()->after('rsa_public_key');
             }
        });
    }
}