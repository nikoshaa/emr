<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Make sure the class name matches the filename
class RemoveOriginalRekamFieldsFromRekamTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Check if the columns exist before trying to drop them
        if (Schema::hasColumns('rekam', ['keluhan', 'pemeriksaan', 'diagnosa', 'tindakan'])) {
            Schema::table('rekam', function (Blueprint $table) {
                // Remove the original, unencrypted columns
                $table->dropColumn([
                    'keluhan',
                    'pemeriksaan',
                    'diagnosa',
                    'tindakan'
                ]);
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('rekam', function (Blueprint $table) {
            // Add the original columns back only if they don't already exist
            if (!Schema::hasColumn('rekam', 'keluhan')) {
                $table->text('keluhan')->after('pasien_id'); // Adjust 'after' based on original position
            }
            if (!Schema::hasColumn('rekam', 'pemeriksaan')) {
                $table->text('pemeriksaan')->after('keluhan');
            }
            if (!Schema::hasColumn('rekam', 'diagnosa')) {
                $table->text('diagnosa')->after('pemeriksaan');
            }
            if (!Schema::hasColumn('rekam', 'tindakan')) {
                $table->text('tindakan')->after('diagnosa');
            }
        });
    }
}