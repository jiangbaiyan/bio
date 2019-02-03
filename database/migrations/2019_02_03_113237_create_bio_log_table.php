<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBioLogTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bio_log', function (Blueprint $table) {
            $table->increments('id');
            $table->string('f_name', 32)->default('')->comment('家属姓名');
            $table->string('id_card', 32)->default('')->comment('家属身份证号码');
            $table->string('o_name', 32)->default('')->comment('操作员姓名');
            $table->string('organization', 64)->default('')->comment('操作员所在机构');
            $table->string('operation',64)->default('')->comment('操作');
            $table->string('remark',256)->default('')->comment('备注');
            $table->unsignedTinyInteger('state')->default(0)->comment('是否成功');
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
        Schema::dropIfExists('bio_log');
    }
}
