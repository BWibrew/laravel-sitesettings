<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Builder as Schema;

class CreateSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        app(Schema::class)->create('settings', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('scope')->default('default');
            $table->unique(['name', 'scope']);
            $table->text('value')->nullable();
            $table->integer('updated_by')->unsigned()->nullable();
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
        app(Schema::class)->drop('settings');
    }
}
