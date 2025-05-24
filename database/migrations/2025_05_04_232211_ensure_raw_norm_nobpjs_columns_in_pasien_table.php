<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class EnsureRawNormNobpjsColumnsInPasienTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('pasien', function (Blueprint $table) {
            // 1. Add back original columns if they don't exist
            // Adjust type/constraints/position based on your original definition
            if (!Schema::hasColumn('pasien', 'no_rm')) {
                // Assuming unique string, nullable. Adjust 'after' as needed.
                $table->string('no_rm')->unique()->nullable()->after('id');
            }
            if (!Schema::hasColumn('pasien', 'no_bpjs')) {
                 // Assuming unique string, nullable. Adjust 'after' as needed.
                $table->string('no_bpjs')->unique()->nullable()->after('no_rm');
            }

            // 2. Remove corresponding encrypted/key columns if they exist
            $columnsToDrop = [];
            if (Schema::hasColumn('pasien', 'no_rm_encrypted')) {
                $columnsToDrop[] = 'no_rm_encrypted';
            }
            if (Schema::hasColumn('pasien', 'no_rm_key')) {
                $columnsToDrop[] = 'no_rm_key';
            }
             if (Schema::hasColumn('pasien', 'no_bpjs_encrypted')) {
                $columnsToDrop[] = 'no_bpjs_encrypted';
            }
            if (Schema::hasColumn('pasien', 'no_bpjs_key')) {
                $columnsToDrop[] = 'no_bpjs_key';
            }

            if (!empty($columnsToDrop)) {
                $table->dropColumn($columnsToDrop);
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
            // Reversing this is complex as it depends on the previous state.
            // Option 1: Re-add encrypted columns (best effort)
             if (!Schema::hasColumn('pasien', 'no_rm_encrypted')) {
                 $table->text('no_rm_encrypted')->nullable()->after('no_rm');
             }
             if (!Schema::hasColumn('pasien', 'no_rm_key')) {
                 $table->text('no_rm_key')->nullable()->after('no_rm_encrypted');
             }
             if (!Schema::hasColumn('pasien', 'no_bpjs_encrypted')) {
                 $table->text('no_bpjs_encrypted')->nullable()->after('no_bpjs');
             }
             if (!Schema::hasColumn('pasien', 'no_bpjs_key')) {
                 $table->text('no_bpjs_key')->nullable()->after('no_bpjs_encrypted');
             }

            // Option 2: Drop the original columns if they were added by 'up'
            // This assumes they didn't exist before running 'up'.
            // Be cautious with this part of rollback logic.
            // if (Schema::hasColumn('pasien', 'no_rm')) {
            //     $table->dropUnique(['no_rm']); // Drop unique constraint first if added
            //     $table->dropColumn('no_rm');
            // }
            // if (Schema::hasColumn('pasien', 'no_bpjs')) {
            //     $table->dropUnique(['no_bpjs']); // Drop unique constraint first if added
            //     $table->dropColumn('no_bpjs');
            // }
        });
    }
}