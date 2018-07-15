<template>
	<main class="main">
	<!-- Breadcrumb -->
	<ol class="breadcrumb">
		<li class="breadcrumb-item">Home</li>
		<li class="breadcrumb-item"><a href="#">Admin</a></li>
		<li class="breadcrumb-item active">Dashboard</li>
	</ol>
	<div class="container-fluid">
		<!-- Ejemplo de tabla Listado -->
		<div class="card">
			<div class="card-header">
				<i class="fa fa-align-justify"></i> Ventas
			</div>
			<!-- Listado -->
			<template v-if="listado == 1">
				<div class="card-body">
					<div class="form-group row">
						<div class="col-md-6">
							<div class="input-group">
								<select class="form-control col-md-3" v-model="criterio">
									<option value="tipo_comprobante">Tipo Comprobante</option>
									<option value="num_comprobante">Numero Comprobante</option>
									<option value="fecha_hora">Fecha-Hora</option>
								</select>
								<input v-on:keyup.enter="listarVenta(1, buscar, criterio)" type="text" v-model="buscar" class="form-control" placeholder="Texto a buscar">
								<button v-on:click="listarVenta(1, buscar, criterio)" type="submit" class="btn btn-primary"><i class="fa fa-search"></i> Buscar</button>
								<button v-on:click="limpiarBuscar()" type="submit" class="btn btn-primary">
									<i class="icon-trash"></i> Limpiar
								</button>
							</div>
						</div>
					</div>
					<div class="table-responsive">
						<table class="table table-bordered table-striped table-sm">
							<thead>
								<tr>
									<th>Opciones</th>
									<th>Usuario</th>
									<th>Cliente</th>
									<th>Tipo Comprobante</th>
									<th>Serie Comprobante</th>
									<th>Numero Comprobante</th>
									<th>Fecha Hora</th>
									<th>Total</th>
									<th>Impuesto</th>
									<th>Estado</th>
								</tr>
							</thead>
							<tbody>
								<tr v-for="venta in array_venta" :key="venta.id">
									<td>
										<button v-on:click="verVenta(venta.id)" type="button" class="btn btn-success btn-sm">
										  <i class="icon-eye"></i>
										</button>
										<button v-on:click="pdfVenta(venta.id)" type="button" class="btn btn-info btn-sm">
										  <i class="icon-doc"></i>
										</button>
									</td>
									<td v-text="venta.usuario"></td>
									<td v-text="venta.nombre"></td>
									<td v-text="venta.tipo_comprobante"></td>
									<td v-text="venta.serie_comprobante"></td>
									<td v-text="venta.num_comprobante"></td>
									<td v-text="venta.fecha_hora"></td>
									<td v-text="venta.total"></td>
									<td v-text="venta.impuesto"></td>
									<td v-text="venta.estado"></td>
								</tr>
							</tbody>
						</table>
					</div>
					<nav>
						<ul class="pagination">
							<li class="page-item" v-if="pagination.current_page > 1">
								<a class="page-link" href="#" v-on:click.prevent="cambiarPagina(pagination.current_page - 1, buscar, criterio)">Ant</a>
							</li>
							<li class="page-item" v-for="page in pageNumber" :key="page" v-bind:class="[page == isActived ? 'active' : '']">
								<a class="page-link" href="#" v-on:click.prevent="cambiarPagina(page, buscar, criterio)" v-text="page"></a>
							</li>
							<li class="page-item" v-if="pagination.current_page < pagination.last_page">
								<a class="page-link" href="#" v-on:click.prevent="cambiarPagina(pagination.current_page + 1, buscar, criterio)">Sig</a>
							</li>
						</ul>
					</nav>
				</div>
			</template>
			<!-- Fin Listado -->
			<!-- Ver venta -->
			<template v-else-if="listado == 2">
				<div class="card-body">
					<div class="form-group row border">
						<div class="col-md-9">
							<div class="form-group">
								<label for="">Proveedor</label>
								<p v-text="cliente"></p>
							</div>
						</div>
						<div class="col-md-3">
							<label>Impuesto(*)</label>
							<p v-text="impuesto"></p>
						</div>
						<div class="col-md-4">
							<div class="form-group">
								<label>Tipo Comprobante</label>
								<p v-text="tipo_comprobante"></p>
							</div>
						</div>
						<div class="col-md-4">
							<div class="form-group">
								<label>Serie Comprobante</label>
								<p v-text="serie_comprobante"></p>
							</div>
						</div>
						<div class="col-md-4">
							<div class="form-group">
								<label>Numero Comprobante(*)</label>
								<p v-text="num_comprobante"></p>
							</div>
						</div>
					</div>
					<div class="form-group row border">
						<div class="table-responsive col-md-12">
							<table class="table table-bordered table-striped table-sm">
								<thead>
									<th>Articulo</th>
									<th>Precio</th>
									<th>Cantidad</th>
									<th>Descuento</th>
									<th>Subtotal</th>
								</thead>
								<tbody v-if="array_detalle.length">
									<tr v-for="detalle in array_detalle" :key="detalle.id">
										<td v-text="detalle.articulo"></td>
										<td v-text="detalle.precio"></td>
										<td v-text="detalle.cantidad"></td>
										<td v-text="detalle.descuento"></td>
										<td>
											{{ detalle.precio * detalle.cantidad - detalle.descuento }}
										</td>
									</tr>
									<tr style="background-color: #CEECF5;">
										<td colspan="4" align="right"><strong>Total Parcial:</strong></td>
										<td>$ {{ total_parcial = (total - total_impuesto).toFixed(2) }}</td>
									</tr>
									<tr style="background-color: #CEECF5;">
										<td colspan="4" align="right"><strong>Total Impuesto:</strong></td>
										<td>$ {{ total_impuesto = ( (total * impuesto) ).toFixed(2) }}</td>
									</tr>
									<tr style="background-color: #CEECF5;">
										<td colspan="4" align="right"><strong>Total Neto:</strong></td>
										<td>$ {{ total }}</td>
									</tr>
								</tbody>
								<tbody v-else>
									<tr>
										<td colspan="5">
											NO hay articulos agregados
										</td>
									</tr>
								</tbody>
							</table>
						</div>
					</div>
					<div class="form-group row">
						<div class="col-md-12">
							<button type="button" v-on:click="ocultarDetalle()" class="btn btn-secondary">Cerrar</button>
						</div>
					</div>
				</div>
			</template>
			<!-- Fin ver venta -->
		</div>
		<!-- Fin ejemplo de tabla Listado -->
	</div>
	</main>
</template>

<script>
	import vSelect from 'vue-select';
	export default {
		data (){
			return {
				venta_id : 0,
				id_cliente : 0,
				cliente : '',
				tipo_comprobante : 'BOLETA',
				serie_comprobante : '',
				num_comprobante : '',
				impuesto : 0.12,
				total_impuesto : 0.00,
				total_parcial : 0.00,
				total : 0.00,
				array_venta : [],
				array_cliente : [],
				array_detalle : [],
				array_articulo : [],
				id_articulo : 0,
				codigo : '',
				articulo : '',
				precio_venta : 0,
				stock : 0,
				cantidad : 0,
				descuento : 0.00,
				listado: 1, //Si visualizo el estado
				modal : 0,
				titulo_modal : '',
				tipo_accion : 0,
				error_venta : 0,
				error_msj_ven : 0,
				pagination : {
					'total' : 0,
					'current_page' :0,
					'per_page' : 0,
					'last_page' : 0,
					'from' : 0,
					'to' : 0
				},
				offset : 3,
				criterio : 'num_comprobante',
				buscar : '',
				criterio_a : 'articulos.nombre',
				buscar_a : ''
			}
		},
		components :{
			vSelect
		},
		computed : {
			isActived: function (){
				return this.pagination.current_page;
			},
			pageNumber: function (){

				if (!this.pagination.to){
					return [];
				}

				var from = this.pagination.current_page - this.offset;

				if (from < 1) from = 1;

				var to = from + (this.offset * 2);

				if (to >= this.pagination.last_page) to = this.pagination.last_page;

				var page_array = [];

				while (from <= to) {
					page_array.push(from);
					from++;
				}

				return page_array;
			},
			calcularTotal: function (){
				var resultado = 0;

				for (var i = 0; i < this.array_detalle.length; i++) {
					
					resultado = resultado + ( this.array_detalle[i].precio_venta * this.array_detalle[i].cantidad - this.array_detalle[i].descuento );

				}

				return resultado;
			}
		},
		methods : {
			listarVenta (page, buscar, criterio){
				let me = this;
				var url = '/venta?page=' + page + '&buscar=' + buscar + '&criterio=' + criterio;

				axios.get(url).then(function (response) {
					var respuesta = response.data;
					me.array_venta = respuesta.ventas.data;
					me.pagination = respuesta.pagination;
				}).catch(function (error) {
					console.log(error);
				});
			},
			pdfVenta (id){
				window.open('http://127.0.0.1:8000/venta/pdf/' + id, '_blank');
			},
			cambiarPagina (page, buscar, criterio){
				let me = this;
				//actualiza la pagina actual
				me.pagination.current_page = page;
				//envia la peticion para visualizar la data de esa pagina
				me.listarVenta(page, buscar, criterio);
			},
			mostrarDetalle (){
				let me = this;
				this.listado = 0;

				me.id_cliente = 0;
				me.tipo_comprobante = 'BOLETA';
				me.serie_comprobante = '';
				me.num_comprobante = '';
				me.impuesto = 0.12;
				me.total = 0.0;
				me.id_articulo = '';
				me.cantidad = 0;
				me.precio_venta = 0;
				me.array_detalle = [];
			},
			ocultarDetalle (){
				this.listado = 1;
			},
			verVenta (id){
				let me = this;
				me.listado = 2;


				//obtener los datos del venta
				var array_ingreso_t = [];
				var url = '/venta/obtener-cabecera?id=' + id;

				axios.get(url).then(function (response) {
					array_ingreso_t = response.data;

					me.cliente = array_ingreso_t[0]['nombre'];
					me.tipo_comprobante = array_ingreso_t[0]['tipo_comprobante'];
					me.serie_comprobante = array_ingreso_t[0]['serie_comprobante'];
					me.num_comprobante = array_ingreso_t[0]['num_comprobante'];
					me.impuesto = array_ingreso_t[0]['impuesto'];
					me.total = array_ingreso_t[0]['total'];
				}).catch(function (error) {
					console.log(error);
				});
				//obtener los  datos de los detalles
				var url_d = '/venta/obtener-detalles?id=' + id;

				axios.get(url_d).then(function (response) {
					me.array_detalle = response.data;
				}).catch(function (error) {
					console.log(error);
				});
			},
			limpiarBuscar (){
				this.buscar = '';
				this.criterio = 'num_comprobante';
				this.listarVenta(1, this.buscar, this.criterio);
			}
		},
		mounted() {
			this.listarVenta(1, this.buscar, this.criterio);
		}
	}
</script>
<style>
	.modal-content{
		width: 100% !important;
		position: absolute !important;
	}
	.mostrar{
		display: list-item !important;
		opacity: 1 !important;
		position: absolute !important;
		background-color: #3c29297a !important;
	}
	.div-error{
		display: flex;
		justify-content: center;
	}
	.text-error{
		color: red;
		font-weight: bold;
	}
	@media (min-width: 600px) {
		.btnagregar {
			margin-top: 2rem;
		}
	}
</style>