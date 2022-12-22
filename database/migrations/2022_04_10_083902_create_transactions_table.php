<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->string('uuid')->unique();
            $table->foreignId('order_id')->index();
            $table->string('gateway_type')->index();
            $table->decimal('amount', 12, 0);
            $table->string('reference_code')->nullable();
            $table->string('authority')->nullable();
            $table->tinyInteger('status');
            $table->timestamp('payed_at')->nullable();
            $table->nullableTimestamps();
            $table->softDeletes();
            $table->index(['status', 'payed_at']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('transactions');
    }
}
