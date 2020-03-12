<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCmsFieldConfigurationTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cms_field_configuration', function (Blueprint $table) {
            $table->increments('id');
            $table->string('table_name')->nullable();
            $table->string('field_name')->nullable();
            $table->string('field_type')->default('textfield');
            $table->string('foreign_table')->nullable();
            $table->string('foreign_field')->nullable();
            $table->boolean('mandatory')->default(0);
            $table->boolean('visible')->default(0);
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
        Schema::dropIfExists('cms_field_configuration');
    }
}
