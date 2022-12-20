<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('task_progress', function (Blueprint $table) {
            $table->id();
            $table->foreignId('board_id')->references('id')->on('boards')->onDelete('cascade');
            $table->foreignId('task_id')->references('id')->on('tasks')->onDelete('cascade');
            $table->unsignedInteger('total_subtask')->default(0);
            $table->unsignedBigInteger('total_subtask_done')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('task_progress');
    }
};
