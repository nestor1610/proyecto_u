CREATE TRIGGER `tr_updStockIngreso` AFTER INSERT ON `detalle_ingresos`
 FOR EACH ROW BEGIN
    UPDATE articulos SET stock = stock + NEW.cantidad
    WHERE articulos.id = NEW.id_articulo;
END