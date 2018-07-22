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
        Schema::connection(AoQueue()->getConnectionName())
            ->create(AoQueue()->getTypesTableName(), function (Blueprint $table) {
                $table->increments('id');
                $table->tinyInteger('active')->default(1);
                $table->string('name');
                $table->string('class')->unique()->index();
                $table->string('work_days')->default('1,2,3,4,5,6,7');
                $table->tinyInteger('wake_up_hour')->default(0);
                $table->tinyInteger('sleep_hour')->default(24);
                $table->integer('lock_seconds')->default(0);
                $table->integer('ignore_seconds')->default(0);
                $table->integer('qt_min_instances')->default(0);
                $table->integer('qt_max_instances')->default(10);
                $table->integer('qt_works')->default(0);
                $table->integer('qt_errors')->default(0);
                $table->integer('qt_success')->default(0);
                $table->timestamp('selectable_at')->nullable();
                $table->timestamp('finished_at')->nullable();
                $table->timestamps();
            });

        Schema::connection(AoQueue()->getConnectionName())
            ->create(AoQueue()->getTasksTableName(), function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->tinyInteger('status')->unsigned()->default(\AoQueue\Constants\Status::WAITING)->index();
                $table->integer('type_id')->unsigned();
                $table->foreign('type_id')->references('id')->on(AoQueue()->getTypesTableName());
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
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection(AoQueue()->getConnectionName())->dropIfExists(AoQueue()->getTasksTableName());
        Schema::connection(AoQueue()->getConnectionName())->dropIfExists(AoQueue()->getTypesTableName());
    }
}
