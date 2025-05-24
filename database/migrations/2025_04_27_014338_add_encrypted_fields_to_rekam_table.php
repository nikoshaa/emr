<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddEncryptedFieldsToRekamTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('rekam', function (Blueprint $table) {
            // Add columns for encrypted data and their corresponding encrypted AES keys
            // We'll store them as TEXT, assuming base64 encoded strings.

            // Keluhan
            $table->text('encrypted_keluhan')->nullable()->after('keluhan'); // Store encrypted data
            $table->text('encrypted_keluhan_aes_key')->nullable()->after('encrypted_keluhan'); // Store encrypted AES key for keluhan

            // Pemeriksaan
            $table->text('encrypted_pemeriksaan')->nullable()->after('pemeriksaan');
            $table->text('encrypted_pemeriksaan_aes_key')->nullable()->after('encrypted_pemeriksaan');

            // Diagnosa
            $table->text('encrypted_diagnosa')->nullable()->after('diagnosa');
            $table->text('encrypted_diagnosa_aes_key')->nullable()->after('encrypted_diagnosa');

            // Tindakan
            $table->text('encrypted_tindakan')->nullable()->after('tindakan');
            $table->text('encrypted_tindakan_aes_key')->nullable()->after('encrypted_tindakan');

            // Optional: Nullify original columns after migrating data (do this in a separate step/migration)
            // $table->text('keluhan')->nullable()->change();
            // $table->text('pemeriksaan')->nullable()->change();
            // $table->text('diagnosa')->nullable()->change();
            // $table->text('tindakan')->nullable()->change();

            // Add an index for faster lookups if needed, e.g., on status or pasien_id
            // $table->index('status');
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
            $table->dropColumn([
                'encrypted_keluhan', 'encrypted_keluhan_aes_key',
                'encrypted_pemeriksaan', 'encrypted_pemeriksaan_aes_key',
                'encrypted_diagnosa', 'encrypted_diagnosa_aes_key',
                'encrypted_tindakan', 'encrypted_tindakan_aes_key'
            ]);

             // Optional: Restore original columns if they were changed
            // $table->text('keluhan')->nullable(false)->change(); // Adjust nullability as per original schema
            // ... restore other columns ...
        });
    }
}