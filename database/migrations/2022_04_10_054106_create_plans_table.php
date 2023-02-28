<?php

use App\Enums\PlanTypeEnum;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePlansTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('plans', function (Blueprint $table) {
            $table->id();
            $table->string('title', 256);
            $table->text('description', 256)->nullable();
            $table->decimal('amount', 12, 0, true);
            $table->decimal('rebate', 12, 0, true)->nullable();
            $table->boolean('percentage')->default(true)->nullable();
            $table->unsignedSmallInteger('daily_download_limit_count');
            $table->unsignedSmallInteger('daily_free_download_limit_count');
            $table->tinyInteger('type')->default(PlanTypeEnum::MONTHLY);
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
        Schema::dropIfExists('plans');
    }
}
