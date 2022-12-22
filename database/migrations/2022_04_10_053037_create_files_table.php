<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFilesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('files', function (Blueprint $table) {
            $table->id();
            $table->string('title', 256);
            $table->text('description')->nullable();
            $table->boolean('sale_as_single')->default(false);
            $table->decimal('amount', 12, 0, true);
            $table->decimal('rebate', 12, 0, true)->nullable();
            $table->boolean('percentage')->nullable();
            $table->unsignedInteger('download_count')->default(0);
            $table->string('link', 1024)->nullable();
            $table->foreignId('category_id')->index()
            ->constrained('categories')->cascadeOnDelete()->cascadeOnUpdate();
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
        Schema::dropIfExists('files');
    }
}
