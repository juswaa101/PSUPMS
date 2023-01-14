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
            $table->string('program_title')->nullable();
            $table->string('project_title')->nullable();
            $table->string('activity_name')->nullable();
            $table->string('study_title')->nullable();
            $table->string('duration')->nullable();
            $table->longText('project_description')->nullable();
            $table->date('project_start_date');
            $table->date('project_end_date');
            $table->string('location')->nullable();
            $table->string('service_type')->nullable();
            $table->integer('participant_no')->nullable();
            $table->integer('training_no')->nullable();
            $table->string('responsible_person/department')->nullable();
            $table->foreignId('id')->references('id')->on('users')->onDelete('cascade');
            $table->string('template');
            $table->unsignedInteger('is_project_head');
            $table->integer('create_task_status')->unsigned();
            $table->integer('create_subtask_status')->unsigned();
            $table->integer('is_finished')->unsigned()->default(0);
            $table->decimal('budget_month')->nullable();
            $table->decimal('total_budget_released')->nullable();
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
