<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddEncryptionKeysToPasienTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('pasien', function (Blueprint $table) {
            // Store the patient's public RSA key (PEM format)
            $table->text('rsa_public_key')->nullable()->after('no_hp'); // Adjust 'after' as needed
            // Store the patient's private RSA key, encrypted using Laravel's default encryption
            $table->text('encrypted_rsa_private_key')->nullable()->after('rsa_public_key');
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
            $table->dropColumn(['rsa_public_key', 'encrypted_rsa_private_key']);
        });
    }
}