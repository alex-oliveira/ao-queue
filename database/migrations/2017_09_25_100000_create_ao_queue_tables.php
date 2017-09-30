<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAoQueueTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ao_queue__flags', function (Blueprint $table) {
            $table->tinyInteger('id', false, true);
            $table->primary('id');
            $table->string('name');
            $table->string('description')->nullable();
        });

        Schema::create('ao_queue__types', function (Blueprint $table) {
            $table->increments('id');
            $table->tinyInteger('active')->default(1);
            $table->string('name');
            $table->string('class')->unique()->index();
            $table->string('description')->nullable();
            $table->string('work_days')->default('1,2,3,4,5,6,7');
            $table->tinyInteger('wake_up_hour')->default(0);
            $table->tinyInteger('sleep_hour')->default(24);
            $table->integer('relax_seconds')->default(0);
            $table->integer('qt_min_instances')->default(0);
            $table->integer('qt_max_instances')->default(10);
            $table->integer('qt_works')->default(0);
            $table->integer('qt_errors')->default(0);
            $table->integer('qt_success')->default(0);
            $table->timestamps();
        });

        Schema::create('ao_queue__tasks', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->tinyInteger('flag_id')->unsigned()->default(\AoQueue\Constants\Flag::WAITING)->index();
            $table->foreign('flag_id')->references('id')->on('ao_queue__flags');
            $table->integer('type_id')->unsigned();
            $table->foreign('type_id')->references('id')->on('ao_queue__types');
            $table->string('worker_unique')->nullable()->unique();
            $table->string('group_unique')->nullable()->index();
            $table->string('reference_id')->index();
            $table->text('data')->nullable();
            $table->timestamp('selectable_at')->nullable();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('finished_at')->nullable();
            $table->timestamps();
            $table->timestamp('deleted_at')->nullable();
        });

        Schema::create('ao_queue__logs', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('task_id')->unsigned();
            $table->foreign('task_id')->references('id')->on('ao_queue__tasks');
            $table->string('type');
            $table->string('message');
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
        Schema::dropIfExists('ao_queue__logs');
        Schema::dropIfExists('ao_queue__tasks');
        Schema::dropIfExists('ao_queue__types');
        Schema::dropIfExists('ao_queue__flags');
    }
}
