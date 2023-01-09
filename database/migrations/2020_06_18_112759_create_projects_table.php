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
        Schema::create('projects', function (Blueprint $table) {
            $table->id('project_id');
            $table->uuid('uuid')->unique();
            $table->string('project_title');
            $table->longText('project_description');
            $table->date('project_start_date');
            $table->date('project_end_date');
            $table->foreignId('id')->references('id')->on('users')->onDelete('cascade');
            $table->string('template');
            $table->unsignedInteger('is_project_head');
            $table->integer('create_task_status')->unsigned();
            $table->integer('create_subtask_status')->unsigned();
            $table->integer('is_finished')->unsigned()->default(0);
            $table->softDeletes();
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
        Schema::dropIfExists('projects');
    }
};
