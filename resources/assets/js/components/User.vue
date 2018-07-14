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
				<i class="fa fa-align-justify"></i> Usuarios
				<button v-on:click="abrirModal('persona', 'registrar')" type="button" class="btn btn-secondary">
					<i class="icon-plus"></i>&nbsp;Nuevo
				</button>
			</div>
			<div class="card-body">
				<div class="form-group row">
					<div class="col-md-6">
						<div class="input-group">
							<select class="form-control col-md-3" v-model="criterio">
								<option value="nombre">Nombre</option>
								<option value="num_documento">Documento</option>
								<option value="email">Email</option>
								<option value="telefono">Telefono</option>
							</select>
							<input v-on:keyup.enter="listarPersona(1, buscar, criterio)" type="text" v-model="buscar" class="form-control" placeholder="Texto a buscar">
							<button v-on:click="listarPersona(1, buscar, criterio)" type="submit" class="btn btn-primary"><i class="fa fa-search"></i> Buscar</button>
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
							<th>Tipo Documento</th>
							<th>Numero</th>
							<th>Direccion</th>
							<th>Telefono</th>
							<th>Email</th>
							<th>Usuario</th>
							<th>Rol</th>
						</tr>
					</thead>
					<tbody>
						<tr v-for="persona in array_persona" :key="persona.id">
							<td>
								<button v-on:click="abrirModal('persona', 'actualizar', persona)" type="button" class="btn btn-warning btn-sm">
								  <i class="icon-pencil"></i>
								</button>
								<template v-if="persona.condicion">
									<button type="button" class="btn btn-danger btn-sm" v-on:click="desactivarUsuario(persona.id)">
										<i class="icon-trash"></i>
									</button>
								</template>
								<template v-else>
									<button type="button" class="btn btn-info btn-sm" v-on:click="activarUsuario(persona.id)">
										<i class="icon-check"></i>
									</button>
								</template>
							</td>
							<td v-text="persona.nombre"></td>
							<td v-text="persona.tipo_documento"></td>
							<td v-text="persona.num_documento"></td>
							<td v-text="persona.direccion"></td>
							<td v-text="persona.telefono"></td>
							<td v-text="persona.email"></td>
							<td v-text="persona.usuario"></td>
							<td v-text="persona.rol"></td>
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
								<input type="text" v-model="nombre" class="form-control" placeholder="Nombre de la persona">
							</div>
						</div>
						<div class="form-group row">
							<label class="col-md-3 form-control-label" for="email-input">Tipo de documento</label>
							<div class="col-md-9">
								<select v-model="tipo_documento" class="form-control">
									<option value="CI">CI</option>
									<option value="RIF">RIF</option>
									<option value="PASS">PASS</option>
								</select>
							</div>
						</div>
						<div class="form-group row">
							<label class="col-md-3 form-control-label" for="text-input">Numero</label>
							<div class="col-md-9">
								<input type="text" v-model="num_documento" class="form-control" placeholder="Numero de documento">
							</div>
						</div>
						<div class="form-group row">
							<label class="col-md-3 form-control-label" for="text-input">Direccion</label>
							<div class="col-md-9">
								<input type="text" v-model="direccion" class="form-control" placeholder="Direccion">
							</div>
						</div>
						<div class="form-group row">
							<label class="col-md-3 form-control-label" for="text-input">Telefono</label>
							<div class="col-md-9">
								<input type="text" v-model="telefono" class="form-control" placeholder="telefono">
							</div>
						</div>
						<div class="form-group row">
							<label class="col-md-3 form-control-label" for="text-input">Email</label>
							<div class="col-md-9">
								<input type="email" v-model="email" class="form-control" placeholder="email">
							</div>
						</div>
						<div class="form-group row">
							<label class="col-md-3 form-control-label" for="text-input">Rol (*)</label>
							<div class="col-md-9">
								<select class="form-control" v-model="idrol">
									<option v-bind:value="0">Seleccione un rol</option>
									<option v-for="rol in array_rol" :key="rol.id" v-bind:value="rol.id" v-text="rol.nombre"></option>
								</select>
							</div>
						</div>
						<div class="form-group row">
							<label class="col-md-3 form-control-label" for="text-input">Usuario (*)</label>
							<div class="col-md-9">
								<input type="text" v-model="usuario" class="form-control" placeholder="Nombre de usuario">
							</div>
						</div>
						<div class="form-group row">
							<label class="col-md-3 form-control-label" for="text-input">password (*)</label>
							<div class="col-md-9">
								<input type="password" v-model="password" class="form-control" placeholder="Password de acceso">
							</div>
						</div>
						<div v-show="error_persona" class="form-group row div-error">
							<div class="text-center text-error">
								<div v-for="error in error_msj_per" :key="error" v-text="error"></div>
							</div>
						</div> 
					</form>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-secondary" v-on:click="cerrarModal()">Cerrar</button>
					<button v-if="tipo_accion == 1" v-on:click="registrarPersona()" type="button" class="btn btn-primary">Guardar</button>
					<button v-if="tipo_accion == 2" v-on:click="actualizarPersona()" type="button" class="btn btn-primary">Actualizar</button>
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
				persona_id : 0,
				idrol : 0,
				nombre : '',
				tipo_documento : 'CI',
				num_documento : '',
				direccion : '',
				telefono : '',
				email : '',
				usuario : '',
				password : '',
				array_persona : [],
				array_rol : [],
				modal : 0,
				titulo_modal : '',
				tipo_accion : 0,
				error_persona : 0,
				error_msj_per : 0,
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
			listarPersona (page, buscar, criterio){
				let me = this;
				var url = '/user?page=' + page + '&buscar=' + buscar + '&criterio=' + criterio;

				axios.get(url).then(function (response) {
					var respuesta = response.data;
					me.array_persona = respuesta.personas.data;
					me.pagination = respuesta.pagination;
				}).catch(function (error) {
					console.log(error);
				});
			},
			listarRol (){
				let me = this;
				var url = '/rol/listarRol';

				axios.get(url).then(function (response) {
					me.array_rol = response.data;
				}).catch(function (error) {
					console.log(error);
				});
			},
			cambiarPagina (page, buscar, criterio){
				let me = this;
				//actualiza la pagina actual
				me.pagination.current_page = page;
				//envia la peticion para visualizar la data de esa pagina
				me.listarPersona(page, buscar, criterio);
			},
			registrarPersona (){
				
				if (this.validarPersona()) {
					return;
				}

				let me = this;

				axios.post('/user/registrar', {
					'nombre': this.nombre,
					'tipo_documento': this.tipo_documento,
					'num_documento': this.num_documento,
					'direccion': this.direccion,
					'telefono': this.telefono,
					'email': this.email,
					'usuario': this.usuario,
					'password': this.password,
					'idrol': this.idrol
				}).then(function (){
					me.cerrarModal();
					me.listarPersona(1, '', 'nombre');
				})
				.catch(function (){
					console.log(error);
				});
			},
			actualizarPersona (){

				if (this.validarPersona()){
					return;
				}

				let me = this;

				axios.put('/user/actualizar', {
					'nombre': this.nombre,
					'tipo_documento': this.tipo_documento,
					'num_documento': this.num_documento,
					'direccion': this.direccion,
					'telefono': this.telefono,
					'email': this.email,
					'usuario': this.usuario,
					'password': this.password,
					'id': this.persona_id,
					'idrol' :this.idrol
				}).then(function (){
					me.cerrarModal();
					me.listarPersona(1, '', 'nombre');
				})
				.catch(function (){
					console.log(error);
				});
			},
			desactivarUsuario (id){
				swal({
				  title: '¿Estas seguro de desactivar este usuario?',
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

					axios.put('/user/desactivar', {
						'id': id
					}).then(function (){
						me.listarPersona(1, '', 'nombre');
						swal(
						  'Desactivada',
						  'El usuario ha sido desactivado',
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
			activarUsuario (id){
				swal({
				  title: '¿Estas seguro de activar este usuario?',
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

					axios.put('/user/activar', {
						'id': id
					}).then(function (){
						me.listarPersona(1, '', 'nombre');
						swal(
						  'Activada',
						  'El usuario ha sido activado',
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
			validarPersona (){
				this.error_persona = 0;
				this.error_msj_per =[];

				if (!this.nombre) this.error_msj_per.push('El nombre de la persona no puede estar vacio');

				if (!this.usuario) this.error_msj_per.push('El nombre de usuario no puede estar vacio');

				if (!this.password) this.error_msj_per.push('El password del usuario no puede estar vacio');

				if (this.idrol == 0) this.error_msj_per.push('Seleccione un rol para el usuario');

				if (this.error_msj_per.length) this.error_persona = 1;

				return this.error_persona;
			},
			cerrarModal (){
				this.modal = 0;
				this.titulo_modal = '';
				this.persona_id = 0;
				this.idrol = 0;
				this.nombre = '';
				this.tipo_documento = 'CI';
				this.num_documento = '';
				this.direccion = '';
				this.telefono = '';
				this.email = '';
				this.usuario = '';
				this.password = '';
				this.error_persona = 0;
			},
			abrirModal (modelo, accion, data = []){
				
				this.listarRol();

				switch (modelo){
					case "persona":
					{
						switch (accion){
							case "registrar":
							{
								this.modal = 1;
								this.titulo_modal = 'Registrar Usuario',
								this.nombre = '';
								this.tipo_documento = 'CI';
								this.num_documento = '';
								this.direccion = '';
								this.telefono = '';
								this.email = '';
								this.usuario = '';
								this.password = '';
								this.idrol = 0
								this.tipo_accion = 1;
								break;
							}
							case "actualizar":
							{
								//console.log(data);
								this.modal = 1;
								this.titulo_modal = 'Actualizar Usuario';
								this.tipo_accion = 2;
								this.persona_id = data['id'];
								this.nombre = data['nombre'];
								this.tipo_documento = data['tipo_documento'];
								this.num_documento = data['num_documento'];
								this.direccion = data['direccion'];
								this.telefono = data['telefono'];
								this.email = data['email'];
								this.usuario = data['usuario'];
								this.password = data['password'];
								this.idrol = data['idrol'];
								break;
							}
						}
					}
				}
			},
			limpiarBuscar (){
				this.buscar = '';
				this.criterio = 'nombre';
				this.listarPersona(1, this.buscar, this.criterio);
			}
		},
		mounted() {
			this.listarPersona(1, this.buscar, this.criterio);
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