<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateExceptionReportersTable extends Migration
{
    /**
     * 获取实际表名
     *
     * @return string
     */
    public function getFinalTable(): string
    {
        return config('admin.extensions.dcat_exception_reporter.table', 'exception_reporters');
    }

    public function getConnection()
    {
        return config('admin.database.connection') ? : config('database.default');
    }

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $table = $this->getFinalTable();
        Schema::create($table, function (Blueprint $table) {
            $table->increments('id');
            $table->string('type');
            $table->string('code');
            $table->string('message');
            $table->string('file');
            $table->integer('line');
            $table->text('trace');
            $table->string('method');
            $table->string('path');
            $table->text('query');
            $table->text('body');
            $table->text('cookies');
            $table->text('headers');
            $table->string('ip');
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
        $table = $this->getFinalTable();
        Schema::dropIfExists($table);
    }
}
