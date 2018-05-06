CREATE TRIGGER `tr_updStockIngresoAnular` AFTER UPDATE ON `ingresos`
 FOR EACH ROW BEGIN
	UPDATE articulos a
    JOIN detalle_ingresos di
    ON di.id_articulo = a.id
    AND di.id_ingreso = NEW.id
    set a.stock = a.stock - di.cantidad;
   END