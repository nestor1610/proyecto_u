<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DetalleIngreso extends Model
{
	protected $table = 'detalle_ingresos';
	protected $fillable = [
		'id_ingreso',
		'id_articulo',
		'cantidad',
		'precio'
	];
	public $timestamps = false;

	public function articulo()
	{
		return $this->belongsTo('App\Articulo', 'id_articulo');
	}

	public function ingreso()
	{
		return $this->belongsTo('App\ingreso', 'id_ingreso');
	}
}
