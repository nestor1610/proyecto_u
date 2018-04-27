<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Ingreso;
use App\DetalleIngreso;

class IngresoController extends Controller
{
     /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if (!$request->ajax()) return redirect('/');

        $buscar = $request->buscar;
        $criterio = $request->criterio;

        if ($buscar == '')
            $ingresos =  Ingreso::join('personas', 'ingresos.id_proveedor', '=', 'personas.id')
        	->join('users', 'ingresos.id_usuario', '=', 'users.id')
        	->select('ingresos.id', 'ingresos.tipo_comprobante', 'ingresos.serie_comprobante',
        		'ingresos.num_comprobante', 'ingresos.fecha_hora', 'ingresos.impuesto',
        		'ingresos.total', 'ingresos.estado', 'personas.nombre', 'users.usuario')
        	->orderBy('ingresos.id', 'desc')->paginate(5);
        else
            $ingresos =  Ingreso::join('personas', 'ingresos.id_proveedor', '=', 'personas.id')
        	->join('users', 'ingresos.id_usuario', '=', 'users.id')
        	->select('ingresos.id', 'ingresos.tipo_comprobante', 'ingresos.serie_comprobante',
        		'ingresos.num_comprobante', 'ingresos.fecha_hora', 'ingresos.impuesto',
        		'ingresos.total', 'ingresos.estado', 'personas.nombre', 'users.usuario')
        	->where('ingresos.'.$criterio, 'like', '%'. $buscar . '%')
        	->orderBy('ingresos.id', 'desc')->paginate(5);

        return [
            'pagination' => [
                'total' => $ingresos->total(),
                'current_page' => $ingresos->currentPage(),
                'per_page' => $ingresos->perPage(),
                'last_page' => $ingresos->lastPage(),
                'from' => $ingresos->firstItem(),
                'to' => $ingresos->lastItem()
            ],
            'ingresos' => $ingresos
        ];
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if (!$request->ajax()) return redirect('/');

        try {
        	
        	DB::beginTransaction();

        	$my_Time = Carbon::now('America/Caracas');

        	$ingreso = new Ingreso();
        	$ingreso->id_proveedor = $request->id_proveedor;
        	$ingreso->id_usuario = \Auth::user()->id;
        	$ingreso->tipo_comprobante = $request->tipo_comprobante;
        	$ingreso->serie_comprobante = $request->serie_comprobante;
        	$ingreso->num_comprobante = $request->num_comprobante;
        	$ingreso->fecha_hora = $my_Time->toDateString();
        	$ingreso->impuesto = $request->impuesto;
        	$ingreso->total = $request->total;
        	$ingreso->estado = 'Registrado';
	        $ingreso->save();

	        $detalles = $request->data;//Array de detalles

	        foreach ($detalles as $key => $detalle) {
	        	
	        	$detalle = new DetalleIngreso();
	        	$detalle->id_ingreso = $ingreso->id;
	        	$detalle->id_articulo = $detalle['id_articulo'];
	        	$detalle->cantidad = $detalle['cantidad'];
	        	$detalle->precio = $detalle['precio'];
	        	$detalle->save();

	        }

            DB::commit();

        } catch (Exception $e) {

        	DB::rollBack();
        	
        }
        
    }

    public function desactivar(Request $request)
    {
        if (!$request->ajax()) return redirect('/');

        $ingreso = Ingreso::findOrFail($request->id);
        $ingreso->estado = 'Anulado';
        $ingreso->save();
    }
}
