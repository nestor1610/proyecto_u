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
                <i class="fa fa-align-justify"></i> Ingresos
                <button v-on:click="mostrarDetalle()" type="button" class="btn btn-secondary">
                    <i class="icon-plus"></i>&nbsp;Nuevo
                </button>
            </div>
            <!-- Listado -->
            <template v-if="listado">
                <div class="card-body">
                    <div class="form-group row">
                        <div class="col-md-6">
                            <div class="input-group">
                                <select class="form-control col-md-3" v-model="criterio">
                                  <option value="tipo_comprobante">Tipo Comprobante</option>
                                  <option value="num_comprobante">Numero Comprobante</option>
                                  <option value="fecha_hora">Fecha-Hora</option>
                                </select>
                                <input v-on:keyup.enter="listarIngreso(1, buscar, criterio)" type="text" v-model="buscar" class="form-control" placeholder="Texto a buscar">
                                <button v-on:click="listarIngreso(1, buscar, criterio)" type="submit" class="btn btn-primary"><i class="fa fa-search"></i> Buscar</button>
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
                                    <th>Proveedor</th>
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
                                <tr v-for="ingreso in array_ingreso" :key="ingreso.id">
                                    <td>
                                        <button v-on:click="abrirModal('ingreso', 'actualizar', ingreso)" type="button" class="btn btn-success btn-sm">
                                          <i class="icon-eye"></i>
                                        </button>
                                        <template v-if="ingreso.estado == 'Registrado'">
                                            <button type="button" class="btn btn-danger btn-sm" v-on:click="desactivarIngreso(ingreso.id)">
                                                <i class="icon-trash"></i>
                                            </button>
                                        </template>
                                    </td>
                                    <td v-text="ingreso.usuario"></td>
                                    <td v-text="ingreso.nombre"></td>
                                    <td v-text="ingreso.tipo_comprobante"></td>
                                    <td v-text="ingreso.serie_comprobante"></td>
                                    <td v-text="ingreso.num_comprobante"></td>
                                    <td v-text="ingreso.fecha_hora"></td>
                                    <td v-text="ingreso.total"></td>
                                    <td v-text="ingreso.impuesto"></td>
                                    <td v-text="ingreso.estado"></td>
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
            <template v-else>
                <div class="card-body">
                    <div class="form-group row border">
                        <div class="col-md-9">
                            <div class="form-group">
                                <label for="">Proveedor(*)</label>
                                <v-select
                                    :on-search="listarProveedor"
                                    label="nombre"
                                    :options="array_proveedor"
                                    placeholder="Buscar Proveedores..."
                                    :onChange="getDatosProveedor"
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
                    </div>
                    <div class="form-group row border">
                        <div class="col-md-6">
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
                                <label>Precio <span style="color:red;" v-show="precio == 0">(*Ingrese)</span></label>
                                <input type="number" value="0" step="any" class="form-control" v-model="precio">
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
                                            <input v-model="detalle.precio" type="number" class="form-control">
                                        </td>
                                        <td>
                                            <input v-model="detalle.cantidad" type="number" class="form-control">
                                        </td>
                                        <td>
                                            {{ detalle.precio * detalle.cantidad }}
                                        </td>
                                    </tr>
                                    <tr style="background-color: #CEECF5;">
                                        <td colspan="4" align="right"><strong>Total Parcial:</strong></td>
                                        <td>$ {{ total_parcial = (total - total_impuesto).toFixed(2) }}</td>
                                    </tr>
                                    <tr style="background-color: #CEECF5;">
                                        <td colspan="4" align="right"><strong>Total Impuesto:</strong></td>
                                        <td>$ {{ total_impuesto = ( (total * impuesto) / (1 + impuesto) ).toFixed(2) }}</td>
                                    </tr>
                                    <tr style="background-color: #CEECF5;">
                                        <td colspan="4" align="right"><strong>Total Neto:</strong></td>
                                        <td>$ {{ total = calcularTotal }}</td>
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
                            <button type="button" class="btn btn-primary" v-on:click="registrarIngreso()">Registrar Compra</button>
                        </div>
                    </div>
                </div>
            </template>
            <!-- Fin Detalle -->
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
                    <button v-if="tipo_accion == 1" v-on:click="registrarIngreso()" type="button" class="btn btn-primary">Guardar</button>
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
    import vSelect from 'vue-select';
    export default {
        data (){
            return {
                ingreso_id : 0,
                id_proveedor : 0,
                nombre : '',
                tipo_comprobante : 'BOLETA',
                serie_comprobante : '',
                num_comprobante : '',
                impuesto : '12',
                total_impuesto : 0.00,
                total_parcial : 0.00,
                total : 0.00,
                array_ingreso : [],
                array_proveedor : [],
                array_detalle : [],
                array_articulo : [],
                id_articulo : 0,
                codigo : '',
                articulo : '',
                precio : 0,
                cantidad : 0,
                listado: 1, //Si visualizo el estado
                modal : 0,
                titulo_modal : '',
                tipo_accion : 0,
                error_ingreso : 0,
                error_msj_ing : 0,
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
                    
                    resultado = resultado + ( this.array_detalle[i].precio * this.array_detalle[i].cantidad );

                }

                return resultado;
            }
        },
        methods : {
            listarIngreso (page, buscar, criterio){
                let me = this;
                var url = '/ingreso?page=' + page + '&buscar=' + buscar + '&criterio=' + criterio;

                axios.get(url).then(function (response) {
                    var respuesta = response.data;
                    me.array_ingreso = respuesta.ingresos.data;
                    me.pagination = respuesta.pagination;
                }).catch(function (error) {
                    console.log(error);
                });
            },
            listarProveedor (search, loading){
                let me = this;

                loading(true)

                var url = '/proveedor/listar-proveedor?filtro=' + search;

                axios.get(url).then(function (response) {
                    q: search
                    me.array_proveedor = response.data;

                    loading(false)

                }).catch(function (error) {
                    console.log(error);
                });
            },
            getDatosProveedor (val_1){
                let me = this;
                me.loading = true;
                me.id_proveedor = val_1.id;

            },
            buscarArticulo (){
                let me = this;
                var url = '/articulo/buscar-articulo?filtro=' + me.codigo;

                axios.get(url).then(function (response) {
                    me.array_articulo = response.data;

                    if (me.array_articulo.length > 0)
                    {
                        me.articulo = me.array_articulo[0]['nombre'];
                        me.id_articulo = me.array_articulo[0]['id'];

                    } else{

                        me.articulo = 'No existe articulo';
                        me.id_articulo = 0;

                    }
                })
                .catch(function (error) {
                    console.log(error);
                });
            },
            cambiarPagina (page, buscar, criterio){
                let me = this;
                //actualiza la pagina actual
                me.pagination.current_page = page;
                //envia la peticion para visualizar la data de esa pagina
                me.listarIngreso(page, buscar, criterio);
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

                if (me.id_articulo == 0 || me.cantidad == 0 || me.precio == 0)
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

                        me.array_detalle.push({
                            id_articulo : me.id_articulo,
                            articulo : me.articulo,
                            cantidad : me.cantidad,
                            precio : me.precio
                        });

                        me.codigo = '';
                        me.id_articulo = 0;
                        me.articulo = '';
                        me.cantidad = 0;
                        me.precio = 0;
                        
                    }
                }

            },
            agregarDetalleModal (data = []){

            },
            listarArticulo (buscar, criterio){
                let me = this;
                var url = '/articulo/listarArticulo?buscar=' + buscar + '&criterio=' + criterio;

                axios.get(url).then(function (response) {
                    me.array_articulo = response.data.data;
                }).catch(function (error) {
                    console.log(error);
                });
            },
            registrarIngreso (){
                
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
                    me.listarIngreso(1, '', 'nombre');
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
                    'id': this.ingreso_id,
                    'idrol' :this.idrol
                }).then(function (){
                    me.cerrarModal();
                    me.listarIngreso(1, '', 'nombre');
                })
                .catch(function (){
                    console.log(error);
                });
            },
            desactivarIngreso (id){
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
                        me.listarIngreso(1, '', 'nombre');
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
                        me.listarIngreso(1, '', 'nombre');
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
                this.error_ingreso = 0;
                this.error_msj_ing =[];

                if (!this.nombre) this.error_msj_ing.push('El nombre de la ingreso no puede estar vacio');

                if (!this.usuario) this.error_msj_ing.push('El nombre de usuario no puede estar vacio');

                if (!this.password) this.error_msj_ing.push('El password del usuario no puede estar vacio');

                if (this.idrol == 0) this.error_msj_ing.push('Seleccione un rol para el usuario');

                if (this.error_msj_ing.length) this.error_ingreso = 1;

                return this.error_ingreso;
            },
            cerrarModal (){
                this.modal = 0;
                this.titulo_modal = '';
            },
            mostrarDetalle (){
                this.listado = 0;
            },
            ocultarDetalle (){
                this.listado = 1;
            },
            abrirModal (){
                this.modal = 1;
                this.titulo_modal = 'Seleccione uno o varios articulos'
            },
            limpiarBuscar (){
                this.buscar = '';
                this.criterio = 'num_comprobante';
                this.listarIngreso(1, this.buscar, this.criterio);
            }
        },
        mounted() {
            this.listarIngreso(1, this.buscar, this.criterio);
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