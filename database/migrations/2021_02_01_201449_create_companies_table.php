<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCompaniesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('companies', function (Blueprint $table) {

            $table->id();
            $table->string('inn', 10)->nullable();
            $table->string('kpp', 10)->nullable();
            $table->unsignedSmallInteger('active')->nullable();
            $table->string('address', 127)->nullable();
            $table->string('name', 127)->nullable();
            $table->string('ogrn', 15)->nullable();
            $table->unsignedInteger('maintrainer');

            $table->foreign('maintrainer')
                ->references('id')
                ->on('maintrainers')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('companies');
    }
}
