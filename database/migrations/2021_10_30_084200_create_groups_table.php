<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGroupsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('groups', function (Blueprint $table) {
            $table->id();
            $table->foreignId('telegram_user_id');
            $table->integer('status')->default(1)->comment('1 = в группе, 2 = вышел из группы, 3 = должен оплатить');
            $table->integer('group_id');
            $table->integer('pay')->default(0);
            $table->date('pay_day');
            $table->integer('join_status')->default(1)->comment('1 = первый заход, 2 = 2или больше вход');
            $table->date('join')->default(\Carbon\Carbon::now());
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
        Schema::dropIfExists('groups');
    }
}
