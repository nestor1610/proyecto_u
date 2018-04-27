<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDetalleIngresosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('detalle_ingresos', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('id_ingreso')->unsigned();
            $table->foreign('id_ingreso')->references('id')->on('ingresos')->onDelete('cascade');
            $table->integer('id_articulo')->unsigned();
            $table->foreign('id_articulo')->references('id')->on('articulos');
            $table->integer('cantidad');
            $table->decimal('precio', 11, 2);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('detalle_ingresos');
    }
}
