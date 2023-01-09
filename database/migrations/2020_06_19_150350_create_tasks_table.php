<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTasksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->references('id')->on('users');
            $table->foreignId('project_id')->references('project_id')->on('projects')->onDelete('cascade');
            $table->foreignId('board_id')->references('id')->on('boards')->onDelete('cascade');
            $table->string('name');
            $table->longText('description');
            $table->date('task_start_date')->nullable();
            $table->date('task_due_date')->nullable();
            $table->integer('privacy_status')->unsigned();
            $table->dateTime('created_at', 0);
            $table->dateTime('updated_at', 0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tasks');
    }
}
