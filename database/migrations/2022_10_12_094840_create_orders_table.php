<?php

use App\Enums\OrderTypeEnum;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnUpdate();
            $table->foreignId('voucher_id')->nullable()->constrained('vouchers')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId('plan_id')->nullable()->constrained('plans')->cascadeOnDelete()->cascadeOnUpdate();
            $table->smallInteger('total_items');
            $table->decimal('total_amount', 12);
            $table->decimal('total_amount_after_rebate_code', 12);
            $table->tinyInteger('status')->default(OrderTypeEnum::PENDING)->comment('1 : pending , 2 : pay_ok , 3 : pay_failed');
            $table->index(['user_id']);
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
        Schema::dropIfExists('orders')->disableForeignKeyConstraints();
    }
}
