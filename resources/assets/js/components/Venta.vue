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
				<button v-on:click="mostrarDetalle()" type="button" class="btn btn-secondary">
					<i class="icon-plus"></i>&nbsp;Nuevo
				</button>
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
										<template v-if="venta.estado == 'Registrado'">
											<button type="button" class="btn btn-danger btn-sm" v-on:click="desactivarVenta(venta.id)">
												<i class="icon-trash"></i>
											</button>
										</template>
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
			<!-- Detalle -->
			<template v-else-if="listado == 0">
				<div class="card-body">
					<div class="form-group row border">
						<div class="col-md-9">
							<div class="form-group">
								<label for="">Cliente(*)</label>
								<v-select
									:on-search="listarCliente"
									label="nombre"
									:options="array_cliente"
									placeholder="Buscar Clientes..."
									:onChange="getDatosCliente"
								>
									
								</v-select>
							</div>
						</div>
						<div class="col-md-3">
							<label>Impuesto(*)</label>
							<input type="number" class="form-control" v-model="impuesto">
						</div>
						<div class="col-md-4">
							<div class="form-group">
								<label>Tipo Comprobante(*)</label>
								<select class="form-control" v-model="tipo_comprobante">
									<option v-bind:value="0">Seleccione</option>
									<option value="BOLETA">Boleta</option>
									<option value="FACTURA">Factura</option>
									<option value="TICKET">Ticket</option>
								</select>
							</div>
						</div>
						<div class="col-md-4">
							<div class="form-group">
								<label>Serie Comprobante</label>
								<input type="text" class="form-control" placeholder="000x" v-model="serie_comprobante">
							</div>
						</div>
						<div class="col-md-4">
							<div class="form-group">
								<label>Numero Comprobante(*)</label>
								<input type="text" class="form-control" placeholder="000x" v-model="num_comprobante">
							</div>
						</div>
						<div class="col-md-12">
							<div v-show="error_venta" class="form-group row div-error">
								<div class="text-center text-error">
									<div v-for="error in error_msj_ven" :key="error" v-text="error">

									</div>
								</div>
							</div>
						</div>
					</div>
					<div class="form-group row border">
						<div class="col-md-4">
							<div class="form-group">
								<label>Articulo <span style="color:red;" v-show="id_articulo == 0">(*Seleccione)</span></label>
								<div class="form-inline">
									<input type="text" class="form-control" v-model="codigo" v-on:keyup.enter="buscarArticulo" placeholder="Ingrese articulo">
									<button @click="abrirModal()" class="btn btn-primary">...</button>
									<input type="text" readonly class="form-control" v-model="articulo">
								</div>
							</div>
						</div>
						<div class="col-md-2">
							<div class="form-group">
								<label>Precio <span style="color:red;" v-show="precio_venta == 0">(*Ingrese)</span></label>
								<input type="number" value="0" step="any" class="form-control" v-model="precio_venta">
							</div>
						</div>
						<div class="col-md-2">
							<div class="form-group">
								<label>Cantidad <span style="color:red;" v-show="cantidad == 0">(*Ingrese)</span></label>
								<input type="number" value="0" class="form-control" v-model="cantidad">
							</div>
						</div>
						<div class="col-md-2">
							<div class="form-group">
								<label>Descuento</span></label>
								<input type="number" value="0" class="form-control" v-model="descuento">
							</div>
						</div>
						<div class="col-md-2">
							<div class="form-group">
								<button v-on:click="agregarDetalle()" class="btn btn-success form-control btnagregar">
									<i class="icon-plus"></i>
								</button>
							</div>
						</div>
					</div>
					<div class="form-group row border">
						<div class="table-responsive col-md-12">
							<table class="table table-bordered table-striped table-sm">
								<thead>
									<th>Opciones</th>
									<th>Articulo</th>
									<th>Precio</th>
									<th>Cantidad</th>
									<th>Descuento</th>
									<th>Subtotal</th>
								</thead>
								<tbody v-if="array_detalle.length">
									<tr v-for="(detalle, index) in array_detalle" :key="detalle.id">
										<td>
											<button @click="eliminarDetalle(index)" type="button" class="btn btn-danger btn-sm">
												<i class="icon-close"></i>
											</button>
										</td>
										<td v-text="detalle.articulo"></td>
										<td>
											<input v-model="detalle.precio_venta" type="number" class="form-control">
										</td>
										<td>
											<span style="color:red;" v-show="detalle.cantidad > detalle.stock">Stock: {{ detalle.stock }}</span>
											<input v-model="detalle.cantidad" type="number" class="form-control">
										</td>
										<td>
											<span style="color:red;" v-show="detalle.descuento > ( detalle.precio_venta * detalle.cantidad )">Descuento superior</span>
											<input v-model="detalle.descuento" type="number" class="form-control">
										</td>
										<td>
											{{ detalle.precio_venta * detalle.cantidad - detalle.descuento }}
										</td>
									</tr>
									<tr style="background-color: #CEECF5;">
										<td colspan="5" align="right"><strong>Total Parcial:</strong></td>
										<td>$ {{ total_parcial = (total - total_impuesto).toFixed(2) }}</td>
									</tr>
									<tr style="background-color: #CEECF5;">
										<td colspan="5" align="right"><strong>Total Impuesto:</strong></td>
										<td>$ {{ total_impuesto = ( (total * impuesto) / (1 + impuesto) ).toFixed(2) }}</td>
									</tr>
									<tr style="background-color: #CEECF5;">
										<td colspan="5" align="right"><strong>Total Neto:</strong></td>
										<td>$ {{ total = calcularTotal }}</td>
									</tr>
								</tbody>
								<tbody v-else>
									<tr>
										<td colspan="6">
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
							<button type="button" class="btn btn-primary" v-on:click="registrarVenta()">Registrar Venta</button>
						</div>
					</div>
				</div>
			</template>
			<!-- Fin Detalle -->
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
	<!--Inicio del modal agregar/actualizar-->
	<div :class="{'mostrar' : modal}" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" style="display: none;" aria-hidden="true">
		<div class="modal-dialog modal-primary modal-lg" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<h4 v-text="titulo_modal" class="modal-title"></h4>
					<button type="button" class="close" v-on:click="cerrarModal()" aria-label="Close">
					  <span aria-hidden="true">×</span>
					</button>
				</div>
				<div class="modal-body">
					<div class="form-group row">
						<div class="col-md-6">
							<div class="input-group">
								<select class="form-control col-md-3" v-model="criterio_a">
								  <option value="articulos.nombre">Nombre</option>
								  <option value="articulos.descripcion">Descripción</option>
								  <option value="categorias.nombre">Nombre Categoria</option>
								  <option value="articulos.codigo">Codigo Categoria</option>
								</select>
								<input v-on:keyup.enter="listarArticulo(buscar_a, criterio_a)" type="text" v-model="buscar_a" class="form-control" placeholder="Texto a buscar">
								<button v-on:click="listarArticulo(buscar_a, criterio_a)" type="submit" class="btn btn-primary"><i class="fa fa-search"></i> Buscar</button>
							</div>
						</div>
					</div>
					<div class="table-responsive">
						<table class="table table-bordered table-striped table-sm">
							<thead>
								<tr>
									<th>Opciones</th>
									<th>Código</th>
									<th>Nombre</th>
									<th>Categoría</th>
									<th>Precio Venta</th>
									<th>Stock</th>
									<th>Estado</th>
								</tr>
							</thead>
							<tbody>
								<tr v-for="articulo in array_articulo" :key="articulo.id">
									<td>
										<button v-on:click="agregarDetalleModal(articulo)" type="button" class="btn btn-success btn-sm">
										  <i class="icon-check"></i>
										</button>
									</td>
									<td v-text="articulo.codigo"></td>
									<td v-text="articulo.nombre"></td>
									<td v-text="articulo.nombre_categoria"></td>
									<td v-text="articulo.precio_venta"></td>
									<td v-text="articulo.stock"></td>
									<td>
										<div v-if="articulo.condicion">
											<span class="badge badge-success">Activo</span>
										</div>
										<div v-else>
											<span class="badge badge-danger">Desactivado</span>
										</div>
									</td>
								</tr>
							</tbody>
						</table>
					</div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-secondary" v-on:click="cerrarModal()">Cerrar</button>
				</div>
			</div>
			<!-- /.modal-content -->
		</div>
		<!-- /.modal-dialog -->
	</div>
	<!--Fin del modal-->
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
			listarCliente (search, loading){
				let me = this;

				loading(true)

				var url = '/cliente/listar-cliente?filtro=' + search;

				axios.get(url).then(function (response) {
					q: search
					me.array_cliente = response.data;

					loading(false)

				}).catch(function (error) {
					console.log(error);
				});
			},
			getDatosCliente (val_1){
				let me = this;
				me.loading = true;
				me.id_cliente = val_1.id;
			},
			buscarArticulo (){
				let me = this;
				var url = '/articulo/buscar-articulo-venta?filtro=' + me.codigo;

				axios.get(url).then(function (response) {
					me.array_articulo = response.data;

					if (me.array_articulo.length > 0)
					{
						me.articulo = me.array_articulo[0]['nombre'];
						me.id_articulo = me.array_articulo[0]['id'];
						me.precio_venta = me.array_articulo[0]['precio_venta'];
						me.stock = me.array_articulo[0]['stock'];

					} else{

						me.articulo = 'No existe articulo';
						me.id_articulo = 0;

					}
				})
				.catch(function (error) {
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
			encuentra (id_articulo){
				var sw = false;

				for (var i = 0; i < this.array_detalle.length; i++) {
					
					if (this.array_detalle[i].id_articulo == id_articulo) {
						
						sw = true;

					}

				}

				return sw;
			},
			eliminarDetalle (index){
				let me = this;
				me.array_detalle.splice(index, 1);
			},
			agregarDetalle (){
				let me = this;

				if (me.id_articulo == 0 || me.cantidad == 0 || me.precio_venta == 0)
				{
				}
				else {

					if ( me.encuentra(me.id_articulo) ) {
						swal({
							type: 'error',
							title: 'Error...',
							text: 'Ese articulo ya se encuentra agregado',
						})
					}
					else{

						if(me.cantidad > me.stock){
							swal({
								type: 'error',
								title: 'Error...',
								text: 'No hay stock disponible',
							})
						}
						else{
							me.array_detalle.push({
								id_articulo : me.id_articulo,
								articulo : me.articulo,
								cantidad : me.cantidad,
								precio_venta : me.precio_venta,
								descuento : me.descuento,
								stock : me.stock
							});

							me.codigo = '';
							me.id_articulo = 0;
							me.articulo = '';
							me.cantidad = 0;
							me.precio_venta = 0;
							me.descuento = 0;
							me.stock = 0;
						}
						
					}
				}

			},
			agregarDetalleModal (data = []){

				let me = this;

				if ( me.encuentra( data['id'] ) ) {
						swal({
							type: 'error',
							title: 'Error...',
							text: 'Ese articulo ya se encuentra agregado',
						})
					}
					else{

						me.array_detalle.push({
							id_articulo : data['id'],
							articulo : data['nombre'],
							cantidad : 1,
							precio_venta : data['precio_venta'],
							descuento : 0,
							stock : data['stock']
						});
						
					}
			},
			listarArticulo (buscar, criterio){
				let me = this;
				var url = '/articulo/listar-articulo-venta?buscar=' + buscar + '&criterio=' + criterio;

				axios.get(url).then(function (response) {
					me.array_articulo = response.data.data;
				}).catch(function (error) {
					console.log(error);
				});
			},
			registrarVenta (){
				
				if ( this.validarVenta() ) {
					return;
				}

				let me = this;

				axios.post('/venta/registrar', {

					'id_cliente': this.id_cliente,
					'tipo_comprobante': this.tipo_comprobante,
					'serie_comprobante': this.serie_comprobante,
					'num_comprobante': this.num_comprobante,
					'impuesto': this.impuesto,
					'total': this.total,
					'data': this.array_detalle

				}).then(function (response){

					me.listado = 1;
					me.listarVenta(1, '', 'num_comprobante');
					me.id_cliente = 0;
					me.tipo_comprobante = 'BOLETA';
					me.serie_comprobante = '';
					me.num_comprobante = '';
					me.impuesto = 0.12;
					me.total = 0.0;
					me.id_articulo = '';
					me.cantidad = 0;
					me.precio_venta = 0;
					me.codigo = 0;
					me.descuento = 0;
					me.stock = 0;
					me.array_detalle = [];
					window.open('http://127.0.0.1:8000/venta/pdf/' + response.data.id, '_blank');
				})
				.catch(function (error){
					console.log(error);
				});
			},
			validarVenta (){
				let me = this;
				this.error_venta = 0;
				this.error_msj_ven =[];
				var art;

				me.array_detalle.map( function (x) {
					if (x.cantidad > x.stock) {
						art = x.articulo + " con stock insuficiente";
						me.error_msj_ven.push(art);
					}
				});

				if ( !me.id_cliente ) me.error_msj_ven.push('Seleccione un cliente');

				if ( !me.tipo_comprobante ) me.error_msj_ven.push('Seleccione el comprobante');

				if ( !me.num_comprobante ) me.error_msj_ven.push('Ingrese el numero de comprobante');

				if ( !me.impuesto ) me.error_msj_ven.push('Ingrese el impuesto de compra');

				if ( me.array_detalle <= 0 ) me.error_msj_ven.push('Ingrese detalles');

				if (me.error_msj_ven.length) me.error_venta = 1;

				return me.error_venta;
			},
			cerrarModal (){
				this.modal = 0;
				this.titulo_modal = '';
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
			abrirModal (){
				this.array_articulo = [];
				this.modal = 1;
				this.titulo_modal = 'Seleccione uno o varios articulos'
			},
			desactivarVenta (id){
				swal({
				  title: '¿Estas seguro de anular esta venta?',
				  type: 'warning',
				  showCancelButton: true,
				  confirmButtonColor: '#3085d6',
				  cancelButtonColor: '#d33',
				  confirmButtonText: 'Aceptar',
				  cancelButtonText: 'Cancelar',
				  confirmButtonClass: 'btn btn-success',
				  cancelButtonClass: 'btn btn-danger',
				  buttonsStyling: false,
				  reverseButtons: true
				}).then((result) => {
				  if (result.value) {

					let me = this;

					axios.put('/venta/desactivar', {
						'id': id
					}).then(function (response){
						me.listarVenta(1, '', 'num_comprobante');
						swal(
						  'Anulada',
						  'La venta ha sido desactivada',
						  'success'
						)
					})
					.catch(function (error){
						console.log(error);
					});

				  } else if (
					// Read more about handling dismissals
					result.dismiss === swal.DismissReason.cancel
				  ) {
					
				  }
				})
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