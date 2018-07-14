<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Ingreso extends Model
{
	protected $fillable = [
		'id_proveedor',
		'id_usuario',
		'tipo_comprobante',
		'serie_comprobante',
		'num_comprobante',
		'fecha_hora',
		'impuesto',
		'total',
		'estado'
	];

	public function usuario()
	{
		return $this->belongsTo('App\User', 'id_usuario');
	}

	public function proveedor()
	{
		return $this->belongsTo('App\Proveedor', 'id_proveedor');
	}

	public function detalleIngresos()
	{
		return $this->hasMany('App\DetalleIngreso', 'id_ingreso');
	}
}
