<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Venta extends Model
{
	protected $fillable = [
		'id_cliente',
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

	public function cliente()
	{
		return $this->belongsTo('App\Persona', 'id_cliente');
	}

	public function detalleVentas()
	{
		return $this->hasMany('App\DetalleVenta', 'id_venta');
	}
}
