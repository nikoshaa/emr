<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddEncryptedColumnsToOtpTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('otps', function (Blueprint $table) {
            //
            // otp
            $table->dropColumn([
                    'otp',
                ]);
            $table->text('otp_encrypted')->nullable()->after('otp'); // Store encrypted data
            $table->text('otp_encrypted_key')->nullable()->after('otp_encrypted'); // Store encrypted AES key for otp
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('otps', function (Blueprint $table) {
            $table->dropColumn([
                'otp_encrypted',
                'otp_encrypted_key',
            ]);
            $table->string('otp')->nullable()->after('email'); // Restore original otp column
        });
    }
}
