<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterAddForeignKeyToUserHasVouchersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('user_has_vouchers', function (Blueprint $table) {
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId('voucher_id')->constrained('vouchers')->cascadeOnDelete()->cascadeOnUpdate();
            $table->index(['user_id', 'voucher_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('user_has_vouchers', function (Blueprint $table) {
            $table->dropColumn(['user_id', 'voucher_id']);
        });
    }
}
