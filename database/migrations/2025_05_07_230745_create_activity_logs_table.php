<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateActivityLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('activity_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable(); // Or string if you store non-integer IDs
            $table->string('ip_address')->nullable();
            $table->text('url')->nullable();
            $table->string('method', 10)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('request_body')->nullable();
            $table->integer('response_status_code')->nullable();
            // $table->longText('response_body')->nullable(); // Consider storage implications
            $table->text('action_description')->nullable(); // For custom logs
            $table->integer('duration_ms')->nullable(); // Request duration in milliseconds
            $table->timestamps();

            // $table->index('user_id');
            // $table->index('url');
            // $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('activity_logs');
    }
}