<?php

use App\Enums\AccessTypeEnum;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserHasfilesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_has_files', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId('file_id')->constrained('files')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId('voucher_id')->nullable();
            $table->decimal('amount', 12, 0, true);
            $table->decimal('amount_after_voucher_code', 12);
            $table->tinyInteger('access')->default(AccessTypeEnum::AdminHaveAdded);
            $table->timestamp('bought_at');
            $table->index(['user_id', 'file_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_has_files');
    }
}
