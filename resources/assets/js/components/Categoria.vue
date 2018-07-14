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
				<i class="fa fa-align-justify"></i> Categorías
				<button v-on:click="abrirModal('categoria', 'registrar')" type="button" class="btn btn-secondary">
					<i class="icon-plus"></i>&nbsp;Nuevo
				</button>
			</div>
			<div class="card-body">
				<div class="form-group row">
					<div class="col-md-6">
						<div class="input-group">
							<select class="form-control col-md-3" v-model="criterio">
								<option value="nombre">Nombre</option>
								<option value="descripcion">Descripción</option>
							</select>
							<input v-on:keyup.enter="listarCategoria(1, buscar, criterio)" type="text" v-model="buscar" class="form-control" placeholder="Texto a buscar">
							<button v-on:click="listarCategoria(1, buscar, criterio)" type="submit" class="btn btn-primary"><i class="fa fa-search"></i> Buscar</button>
							<button v-on:click="limpiarBuscar()" type="submit" class="btn btn-primary">
								<i class="icon-trash"></i> Limpiar
							</button>
						</div>
					</div>
				</div>
				<table class="table table-bordered table-striped table-sm">
					<thead>
						<tr>
							<th>Opciones</th>
							<th>Nombre</th>
							<th>Descripción</th>
							<th>Estado</th>
						</tr>
					</thead>
					<tbody>
						<tr v-for="categoria in array_categoria" :key="categoria.id">
							<td>
								<button v-on:click="abrirModal('categoria', 'actualizar', categoria)" type="button" class="btn btn-warning btn-sm">
								  <i class="icon-pencil"></i>
								</button> &nbsp;
								<template v-if="categoria.condicion">
									<button type="button" class="btn btn-danger btn-sm" v-on:click="desactivarCategoria(categoria.id)">
										<i class="icon-trash"></i>
									</button>
								</template>
								<template v-else="categoria.condicion">
									<button type="button" class="btn btn-info btn-sm" v-on:click="activarCategoria(categoria.id)">
										<i class="icon-check"></i>
									</button>
								</template>
							</td>
							<td v-text="categoria.nombre"></td>
							<td v-text="categoria.descripcion"></td>
							<td>
								<div v-if="categoria.condicion">
									<span class="badge badge-success">Activo</span>
								</div>
								<div v-else>
									<span class="badge badge-danger">Desactivado</span>
								</div>
							</td>
						</tr>
					</tbody>
				</table>
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
					<form action="" method="post" enctype="multipart/form-data" class="form-horizontal">
						<div class="form-group row">
							<label class="col-md-3 form-control-label" for="text-input">Nombre</label>
							<div class="col-md-9">
								<input type="text" v-model="nombre" class="form-control" placeholder="Nombre de categoría">
							</div>
						</div>
						<div class="form-group row">
							<label class="col-md-3 form-control-label" for="email-input">Descripción</label>
							<div class="col-md-9">
								<input type="text" v-model="descripcion" class="form-control" placeholder="Ingrese descripcion">
							</div>
						</div>
						<div v-show="error_categoria" class="form-group row div-error">
							<div class="text-center text-error">
								<div v-for="error in error_msj_cat" :key="error" v-text="error"></div>
							</div>
						</div> 
					</form>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-secondary" v-on:click="cerrarModal()">Cerrar</button>
					<button v-if="tipo_accion == 1" v-on:click="registrarCategoria()" type="button" class="btn btn-primary">Guardar</button>
					<button v-if="tipo_accion == 2" v-on:click="actualizarCategoria()" type="button" class="btn btn-primary">Actualizar</button>
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
	export default {
		data (){
			return {
				categoria_id : 0,
				nombre : '',
				descripcion : '',
				array_categoria : [],
				modal : 0,
				titulo_modal : '',
				tipo_accion : 0,
				error_categoria : 0,
				error_msj_cat : 0,
				pagination : {
					'total' : 0,
					'current_page' :0,
					'per_page' : 0,
					'last_page' : 0,
					'from' : 0,
					'to' : 0
				},
				offset : 3,
				criterio : 'nombre',
				buscar : ''
			}
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
			}
		},
		methods : {
			listarCategoria (page, buscar, criterio){
				let me = this;
				var url = '/categoria?page=' + page + '&buscar=' + buscar + '&criterio=' + criterio;

				axios.get(url).then(function (response) {
					var respuesta = response.data;
					me.array_categoria = respuesta.categorias.data;
					me.pagination = respuesta.pagination;
				}).catch(function (error) {
					console.log(error);
				});
			},
			cambiarPagina (page, buscar, criterio){
				let me = this;
				//actualiza la pagina actual
				me.pagination.current_page = page;
				//envia la peticion para visualizar la data de esa pagina
				me.listarCategoria(page, buscar, criterio);
			},
			registrarCategoria (){
				
				if (this.validarCategoria()) {
					return;
				}

				let me = this;

				axios.post('/categoria/registrar', {
					'nombre': this.nombre,
					'descripcion': this.descripcion
				}).then(function (){
					me.cerrarModal();
					me.listarCategoria(1, '', 'nombre');
				})
				.catch(function (){
					console.log(error);
				});
			},
			actualizarCategoria (){

				if (this.validarCategoria()){
					return;
				}

				let me = this;

				axios.put('/categoria/actualizar', {
					'nombre': this.nombre,
					'descripcion': this.descripcion,
					'id': this.categoria_id
				}).then(function (){
					me.cerrarModal();
					me.listarCategoria(1, '', 'nombre');
				})
				.catch(function (){
					console.log(error);
				});
			},
			desactivarCategoria (categoria_id){
				swal({
				  title: '¿Estas seguro de desactivar esta categoria?',
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

					axios.put('/categoria/desactivar', {
						'id': categoria_id
					}).then(function (){
						me.listarCategoria(1, '', 'nombre');
						swal(
						  'Desactivada',
						  'La categoria ha sido desactivada',
						  'success'
						)
					})
					.catch(function (){
						console.log(error);
					});

				  } else if (
					// Read more about handling dismissals
					result.dismiss === swal.DismissReason.cancel
				  ) {
					
				  }
				})
			},
			activarCategoria (categoria_id){
				swal({
				  title: '¿Estas seguro activar esta categoria?',
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

					axios.put('/categoria/activar', {
						'id': categoria_id
					}).then(function (){
						me.listarCategoria(1, '', 'nombre');
						swal(
						  'Activada',
						  'La categoria ha sido activada',
						  'success'
						)
					})
					.catch(function (){
						console.log(error);
					});

				  } else if (
					// Read more about handling dismissals
					result.dismiss === swal.DismissReason.cancel
				  ) {
					
				  }
				})
			},
			validarCategoria (){
				this.error_categoria = 0;
				this.error_msj_cat =[];

				if (!this.nombre) this.error_msj_cat.push('El nombre de la categoria no puede estar vacio');

				if (this.error_msj_cat.length) this.error_categoria = 1;

				return this.error_categoria;
			},
			cerrarModal (){
				this.modal = 0;
				this.titulo_modal = '';
				this.categoria_id = 0;
				this.nombre = '';
				this.descripcion = '';
			},
			abrirModal (modelo, accion, data = []){
				switch (modelo){
					case "categoria":
					{
						switch (accion){
							case "registrar":
							{
								this.modal = 1;
								this.titulo_modal = 'Registrar Categoria',
								this.nombre = '';
								this.descripcion = '';
								this.tipo_accion = 1;
								break;
							}
							case "actualizar":
							{
								//console.log(data);
								this.modal = 1;
								this.titulo_modal = 'Actualizar categoria';
								this.tipo_accion = 2;
								this.categoria_id = data['id'];
								this.nombre = data['nombre'];
								this.descripcion = data['descripcion'];
								break;
							}
						}
					}
				}
			},
			limpiarBuscar (){
				this.buscar = '';
				this.criterio = 'nombre';
				this.listarCategoria(1, this.buscar, this.criterio);
			}
		},
		mounted() {
			this.listarCategoria(1, this.buscar, this.criterio);
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
</style>