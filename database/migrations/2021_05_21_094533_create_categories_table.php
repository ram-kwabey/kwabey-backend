<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCategoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('categories', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('figure_id');
            $table->string('type');
            $table->string('name');
            $table->Text('description');
            $table->string('slug');
            $table->string('code');
            $table->string('sequence')->nullable();
            $table->string('image')->nullable();
            $table->tinyInteger('status')->default(1)->comment('1:Active, 0:Deactive');
            $table->foreign('figure_id')->references('id')->on('figures')->onDelete('cascade');   
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
        Schema::dropIfExists('categories');
    }
}
