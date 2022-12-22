<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserHasVouchersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_has_vouchers', function (Blueprint $table) {
            $table->id();
            $table->integer('number_authorize_use')->default(1);
            $table->integer('number_times_use')->default(0);
            $table->timestamp('last_date_of_use')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_has_vouchers');
    }
}
