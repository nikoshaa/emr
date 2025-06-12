<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMessageEncryptionColumns extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('chats', function (Blueprint $table) {
            // Add columns for encrypted message
            $table->text('message_encrypted')->nullable();
            $table->text('message_key')->nullable();
            $table->string('message_hash', 64)->nullable();
            // drop column message
            $table->dropColumn('message');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('chats', function (Blueprint $table) {
            $table->dropColumn('message_encrypted');
            $table->dropColumn('message_key');
            $table->dropColumn('message_hash');
            $table->text('message')->nullable();
        });
    }
}