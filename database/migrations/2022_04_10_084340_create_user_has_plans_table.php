<?php

use App\Enums\PlanStatusEnum;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserHasPlansTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_has_plans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId('plan_id')->constrained('plans')->cascadeOnDelete()->cascadeOnUpdate();
            $table->decimal('amount', 12, 0, true);
            $table->timestamp('activation_at');
            $table->timestamp('expired_at');
            $table->tinyInteger('access')->default(1);
            $table->timestamp('bought_at');
            $table->tinyInteger('status')->default(PlanStatusEnum::ACTIVE);
            $table->index(['user_id', 'plan_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_has_plans');
    }
}
