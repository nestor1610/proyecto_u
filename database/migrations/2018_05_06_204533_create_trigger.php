<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTrigger extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::unprepared('
        CREATE TRIGGER `tr_updStockIngreso` AFTER INSERT ON `detalle_ingresos`
         FOR EACH ROW BEGIN
            UPDATE articulos SET stock = stock + NEW.cantidad
            WHERE articulos.id = NEW.id_articulo;
        END
        ');

        DB::unprepared('
        CREATE TRIGGER `tr_updStockIngresoAnular` AFTER UPDATE ON `ingresos`
         FOR EACH ROW BEGIN
            UPDATE articulos a
            JOIN detalle_ingresos di
            ON di.id_articulo = a.id
            AND di.id_ingreso = NEW.id
            set a.stock = a.stock - di.cantidad;
           END
        ');

        DB::unprepared('
        CREATE TRIGGER `tr_updStockVenta` AFTER INSERT ON `detalle_ventas`
         FOR EACH ROW BEGIN
            UPDATE articulos SET stock = stock - NEW.cantidad
            WHERE articulos.id = NEW.id_articulo;
        END
        ');

        DB::unprepared('
        CREATE TRIGGER `tr_updStockVentaAnular` AFTER UPDATE ON `ventas`
         FOR EACH ROW BEGIN
            UPDATE articulos a
            JOIN detalle_ventas dv
            ON dv.id_articulo = a.id
            AND dv.id_venta = NEW.id
            set a.stock = a.stock + dv.cantidad;
           END
        ');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::unprepared('DROP TRIGGER `tr_updStockIngreso`');
        DB::unprepared('DROP TRIGGER `tr_updStockIngresoAnular`');
        DB::unprepared('DROP TRIGGER `tr_updStockVenta`');
        DB::unprepared('DROP TRIGGER `tr_updStockVentaAnular`');
    }
}
