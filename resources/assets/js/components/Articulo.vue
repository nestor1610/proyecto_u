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
                <i class="fa fa-align-justify"></i> Articulos
                <button v-on:click="abrirModal('articulo', 'registrar')" type="button" class="btn btn-secondary">
                    <i class="icon-plus"></i>&nbsp;Nuevo
                </button>
                <button v-on:click="cargarPdf()" type="button" class="btn btn-info">
                    <i class="icon-doc"></i>&nbsp;Reporte
                </button>
            </div>
            <div class="card-body">
                <div class="form-group row">
                    <div class="col-md-6">
                        <div class="input-group">
                            <select class="form-control col-md-3" v-model="criterio">
                              <option value="articulos.nombre">Nombre</option>
                              <option value="articulos.descripcion">Descripción</option>
                              <option value="categorias.nombre">Nombre Categoria</option>
                            </select>
                            <input v-on:keyup.enter="listarArticulo(1, buscar, criterio)" type="text" v-model="buscar" class="form-control" placeholder="Texto a buscar">
                            <button v-on:click="listarArticulo(1, buscar, criterio)" type="submit" class="btn btn-primary"><i class="fa fa-search"></i> Buscar</button>
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
                            <th>Código</th>
                            <th>Nombre</th>
                            <th>Categoría</th>
                            <th>Precio Venta</th>
                            <th>Stock</th>
                            <th>Descripción</th>
                            <th>Estado</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="articulo in array_articulo" :key="articulo.id">
                            <td>
                                <button v-on:click="abrirModal('articulo', 'actualizar', articulo)" type="button" class="btn btn-warning btn-sm">
                                  <i class="icon-pencil"></i>
                                </button> &nbsp;
                                <template v-if="articulo.condicion">
                                    <button type="button" class="btn btn-danger btn-sm" v-on:click="desactivarArticulo(articulo.id)">
                                        <i class="icon-trash"></i>
                                    </button>
                                </template>
                                <template v-else="articulo.condicion">
                                    <button type="button" class="btn btn-info btn-sm" v-on:click="activarArticulo(articulo.id)">
                                        <i class="icon-check"></i>
                                    </button>
                                </template>
                            </td>
                            <td v-text="articulo.codigo"></td>
                            <td v-text="articulo.nombre"></td>
                            <td v-text="articulo.nombre_categoria"></td>
                            <td v-text="articulo.precio_venta"></td>
                            <td v-text="articulo.stock"></td>
                            <td v-text="articulo.descripcion"></td>
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
    <div class="modal fade" tabindex="-1" v-bind:class="{'mostrar' : modal}" role="dialog" aria-labelledby="myModalLabel" style="display: none;" aria-hidden="true">
        <div class="modal-dialog modal-primary modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" v-text="titulo_modal"></h4>
                    <button type="button" class="close" @click="cerrarModal()" aria-label="Close">
                      <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form action="" method="post" enctype="multipart/form-data" class="form-horizontal">
                        <div class="form-group row">
                            <label class="col-md-3 form-control-label" for="text-input">Categoría</label>
                            <div class="col-md-9">
                                <select class="form-control" v-model="id_categoria">
                                    <option value="0" disabled>Seleccione</option>
                                    <option v-for="categoria in array_categoria" :key="categoria.id" :value="categoria.id" v-text="categoria.nombre"></option>
                                </select>                                        
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-md-3 form-control-label" for="text-input">Código</label>
                            <div class="col-md-9">
                                <input type="text" v-model="codigo" class="form-control" placeholder="Código de barras"> 
                                <barcode v-bind:value="codigo" v-bind:options="{ format : 'EAN-13'}">
                                    Generando codigo de barras.
                                </barcode>                                 
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-md-3 form-control-label" for="text-input">Nombre</label>
                            <div class="col-md-9">
                                <input type="text" v-model="nombre" class="form-control" placeholder="Nombre de artículo">                                        
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-md-3 form-control-label" for="text-input">Precio Venta</label>
                            <div class="col-md-9">
                                <input type="number" v-model="precio" class="form-control" placeholder="">                                        
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-md-3 form-control-label" for="text-input">Stock</label>
                            <div class="col-md-9">
                                <input type="number" v-model="stock" class="form-control" placeholder="">                                        
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-md-3 form-control-label" for="email-input">Descripción</label>
                            <div class="col-md-9">
                                <input type="email" v-model="descripcion" class="form-control" placeholder="Ingrese descripción">
                            </div>
                        </div>
                        <div v-show="error_articulo" class="form-group row div-error">
                            <div class="text-center text-error">
                                <div v-for="error in error_msj_art" :key="error" v-text="error">

                                </div>
                            </div>
                        </div>

                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" @click="cerrarModal()">Cerrar</button>
                    <button type="button" v-if="tipo_accion==1" class="btn btn-primary" @click="registrarArticulo()">Guardar</button>
                    <button type="button" v-if="tipo_accion==2" class="btn btn-primary" @click="actualizarArticulo()">Actualizar</button>
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
    import VueBarcode from 'vue-barcode';

    export default {
        data (){
            return {
                id_categoria : 0,
                id_articulo : 0,
                nombre_categoria : '',
                codigo : '',
                nombre : '',
                precio : 0,
                stock : 0,
                descripcion : '',
                array_categoria : [],
                array_articulo : [],
                modal : 0,
                titulo_modal : '',
                tipo_accion : 0,
                error_articulo : 0,
                error_msj_art : 0,
                pagination : {
                    'total' : 0,
                    'current_page' :0,
                    'per_page' : 0,
                    'last_page' : 0,
                    'from' : 0,
                    'to' : 0
                },
                offset : 3,
                criterio : 'articulos.nombre',
                buscar : ''
            }
        },
        components: {
            'barcode': VueBarcode
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
            listarArticulo (page, buscar, criterio){
                let me = this;
                var url = '/articulo?page=' + page + '&buscar=' + buscar + '&criterio=' + criterio;

                axios.get(url).then(function (response) {
                    var respuesta = response.data;
                    me.array_articulo = respuesta.articulos.data;
                    me.pagination = respuesta.pagination;
                }).catch(function (error) {
                    console.log(error);
                });
            },
            cargarPdf() {
                window.open('http://127.0.0.1:8000/articulo/listarPdf', '_blank');
            },
            listarCategoria (){
                let me = this;

                axios.get('/articulo/listar-categorias').then(function (response) {
                    me.array_categoria = response.data;
                }).catch(function (error) {
                    console.log(error);
                });
            },
            cambiarPagina (page, buscar, criterio){
                let me = this;
                //actualiza la pagina actual
                me.pagination.current_page = page;
                //envia la peticion para visualizar la data de esa pagina
                me.listarArticulo(page, buscar, criterio);
            },
            registrarArticulo (){
                
                if (this.validarArticulo()) {
                    return;
                }

                let me = this;

                axios.post('/articulo/registrar', {
                    'id_categoria' : this.id_categoria,
                    'codigo' : this.codigo,
                    'nombre' : this.nombre,
                    'stock' : this.stock,
                    'precio_venta' : this.precio,
                    'descripcion' : this.descripcion
                }).then(function (response){
                    me.cerrarModal();
                    me.listarArticulo(1, '', 'nombre');
                })
                .catch(function (error){
                    console.log(error);
                });
            },
            actualizarArticulo (){

                if (this.validarArticulo()){
                    return;
                }

                let me = this;

                axios.put('/articulo/actualizar', {
                    'id_categoria' : this.id_categoria,
                    'codigo' : this.codigo,
                    'nombre' : this.nombre,
                    'stock' : this.stock,
                    'precio_venta' : this.precio,
                    'descripcion' : this.descripcion,
                    'id': this.id_articulo
                }).then(function (response){
                    me.cerrarModal();
                    me.listarArticulo(1, '', 'nombre');
                })
                .catch(function (error){
                    console.log(error);
                });
            },
            desactivarArticulo (id_articulo){
                swal({
                  title: '¿Estas seguro de desactivar este articulo?',
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

                    axios.put('/articulo/desactivar', {
                        'id': id_articulo
                    }).then(function (response){
                        me.listarArticulo(1, '', 'nombre');
                        swal(
                          'Desactivada',
                          'El articulo ha sido desactivado',
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
            activarArticulo (id_articulo){
                swal({
                  title: '¿Estas seguro activar este articulo?',
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

                    axios.put('/articulo/activar', {
                        'id': id_articulo
                    }).then(function (response){
                        me.listarArticulo(1, '', 'nombre');
                        swal(
                          'Activada',
                          'El articulo ha sido activado',
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
            validarArticulo (){
                this.error_articulo = 0;
                this.error_msj_art =[];

                if (!this.id_categoria) this.error_msj_art.push('Seleccione una categoria');

                if (!this.nombre) this.error_msj_art.push('El nombre del articulo no puede estar vacio');

                if (!this.stock) this.error_msj_art.push('El stock del articulo no puede estar vacio');

                if (!this.precio) this.error_msj_art.push('El precio de venta del articulo debe ser un numero y no puede estar vacio');

                if (this.error_msj_art.length) this.error_articulo = 1;

                return this.error_articulo;
            },
            cerrarModal (){
                this.modal = 0;
                this.titulo_modal = '';
                this.id_categoria = 0;
                this.id_articulo = 0;
                this.nombre_categoria = 0;
                this.codigo = '';
                this.nombre = '';
                this.precio = 0;
                this.stock = 0;
                this.descripcion = '';
                this.array_categoria = [];
                this.error_articulo = 0;
                this.error_msj_art = [];
            },
            abrirModal (modelo, accion, data = []){
                switch (modelo){
                    case "articulo":
                    {
                        switch (accion){
                            case "registrar":
                            {
                                this.modal = 1;
                                this.titulo_modal = 'Registrar Articulo',
                                this.listarCategoria();
                                this.id_categoria = 0;
                                this.codigo = '';
                                this.nombre = '';
                                this.precio = 0;
                                this.stock = 0;
                                this.descripcion = '';
                                this.tipo_accion = 1;
                                break;
                            }
                            case "actualizar":
                            {
                                //console.log(data);
                                this.modal = 1;
                                this.titulo_modal = 'Actualizar Articulo';
                                this.tipo_accion = 2;
                                this.listarCategoria();
                                this.id_categoria = data['id_categoria'];
                                this.id_articulo = data['id'];
                                this.codigo = data['codigo']
                                this.nombre = data['nombre']
                                this.precio = data['precio_venta'];
                                this.stock = data['stock'];
                                this.descripcion = data['descripcion']
                                break;
                            }
                        }
                    }
                }
            },
            limpiarBuscar (){
                this.buscar = '';
                this.criterio = 'articulos.nombre';
                this.listarArticulo(1, this.buscar, this.criterio);
            }
        },
        mounted() {
            this.listarArticulo(1, this.buscar, this.criterio);
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