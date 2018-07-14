<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DetalleVenta extends Model
{
	protected $table = 'detalle_ventas';
	protected $fillable = [
		'id_venta',
		'id_articulo',
		'cantidad',
		'precio',
		'descuento'
	];
	public $timestamps = false;

	public function articulo()
	{
		return $this->belongsTo('App\Articulo', 'id_articulo');
	}

	public function ingreso()
	{
		return $this->belongsTo('App\ingreso', 'id_venta');
	}
}
