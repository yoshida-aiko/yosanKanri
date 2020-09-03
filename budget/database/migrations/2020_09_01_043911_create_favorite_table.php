<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFavoriteTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('favorites', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedInteger('VersionNo')->default(0);
            $table->bigInteger('UserId')->unsigned();
            $table->integer('ParentId')->default(-1);
            $table->unsignedInteger('ItemClass');
            $table->bigInteger('ItemId')->unsigned();
            $table->string('FolderName',20)->nullable();
            $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP'));

            //論理削除
            $table->softDeletes();

            $table->foreign('UserId')
            	->references('id')
            	->on('users');
            	
            $table->foreign('ItemId')
            	->references('id')
            	->on('items');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('favorites');
    }
}
