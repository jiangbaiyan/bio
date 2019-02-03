<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateReserveTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bio', function (Blueprint $table) {
            $table->increments('id');
            $table->string('f_name', 32)->default('')->comment('家属姓名');
            $table->string('id_card', 32)->default('')->comment('家属身份证号码');
            $table->string('o_name', 32)->default('')->comment('操作员姓名');
            $table->string('organization', 64)->default('')->comment('操作员所在机构');
            $table->string('location', 64)->default('')->comment('预约地点');
            $table->string('face_data', 256)->default('')->comment('人脸数据');
            $table->string('finger_data', 1024)->default('')->comment('声纹数据');
            $table->string('voice_data', 1024)->default('')->comment('指纹数据');
            $table->unsignedTinyInteger('face_reserve_state')->default(0)->comment('人脸预约状态');
            $table->unsignedTinyInteger('finger_reserve_state')->default(0)->comment('指纹预约状态');
            $table->unsignedTinyInteger('voice_reserve_state')->default(0)->comment('声纹预约状态');
            $table->unsignedTinyInteger('face_compare_state')->default(0)->comment('人脸比对状态');
            $table->unsignedTinyInteger('finger_compare_state')->default(0)->comment('指纹比对状态');
            $table->unsignedTinyInteger('voice_compare_state')->default(0)->comment('声纹比对状态');
            $table->unsignedTinyInteger('total_state')->default(0)->comment('总状态:1-预约成功 2-预约失败 3-比对成功 4-比对失败');
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
        Schema::dropIfExists('bio');
    }
}
