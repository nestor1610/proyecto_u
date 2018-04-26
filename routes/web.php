<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

/* Login */

/*
	El primer grupo de rutas para el Middleware guest/invitado
	Los que no sean autenticado
*/
Route::group( ['middleware' => ['guest'] ], function(){

	Route::get('/', 'Auth\LoginController@showLoginForm');
	Route::post('login', 'Auth\LoginController@login')
		->name('login');

});

/*
	El segundo grupo de rutas para el Middleware auth/autenticado
	Los que se han autenticado
*/
Route::group( ['middleware' => ['auth'] ], function(){

	Route::post('logout', 'Auth\LoginController@logout')
		->name('logout');

	Route::get('/main', function () {
	    return view('contenido.contenido');
	})->name('main');

	/*
		El tercer grupo de rutas para el Middleware Almacenero
	*/
	Route::group( ['middleware' => ['Almacenero'] ], function(){

		/* Categorias */
		Route::get('/categoria', 'CategoriaController@index');
		Route::post('/categoria/registrar', 'CategoriaController@store');
		Route::put('/categoria/actualizar', 'CategoriaController@update');
		Route::put('/categoria/desactivar', 'CategoriaController@desactivar');
		Route::put('/categoria/activar', 'CategoriaController@activar');

		/* Articulos */
		Route::get('articulo', 'ArticuloController@index');
		Route::post('/articulo/registrar', 'ArticuloController@store');
		Route::put('/articulo/actualizar', 'ArticuloController@update');
		Route::put('/articulo/desactivar', 'ArticuloController@desactivar');
		Route::put('/articulo/activar', 'ArticuloController@activar');
		Route::get('articulo/listar-categorias', 'ArticuloController@listarCategorias');

		/* Proveedores */
		Route::get('proveedor', 'ProveedorController@index');
		Route::post('/proveedor/registrar', 'ProveedorController@store');
		Route::put('/proveedor/actualizar', 'ProveedorController@update');

	});

	/*
		El cuarto grupo de rutas para el Middleware Vendedor
	*/
	Route::group( ['middleware' => ['Vendedor'] ], function(){

		/* Clientes */
		Route::get('cliente', 'ClienteController@index');
		Route::post('/cliente/registrar', 'ClienteController@store');
		Route::put('/cliente/actualizar', 'ClienteController@update');

	});

	/*
		El quinto grupo de rutas para el Middleware Administrador
	*/
	Route::group( ['middleware' => ['Administrador'] ], function(){

		/* Categorias */
		Route::get('/categoria', 'CategoriaController@index');
		Route::post('/categoria/registrar', 'CategoriaController@store');
		Route::put('/categoria/actualizar', 'CategoriaController@update');
		Route::put('/categoria/desactivar', 'CategoriaController@desactivar');
		Route::put('/categoria/activar', 'CategoriaController@activar');

		/* Articulos */
		Route::get('articulo', 'ArticuloController@index');
		Route::post('/articulo/registrar', 'ArticuloController@store');
		Route::put('/articulo/actualizar', 'ArticuloController@update');
		Route::put('/articulo/desactivar', 'ArticuloController@desactivar');
		Route::put('/articulo/activar', 'ArticuloController@activar');
		Route::get('articulo/listar-categorias', 'ArticuloController@listarCategorias');

		/* Proveedores */
		Route::get('proveedor', 'ProveedorController@index');
		Route::post('/proveedor/registrar', 'ProveedorController@store');
		Route::put('/proveedor/actualizar', 'ProveedorController@update');

		/* Clientes */
		Route::get('cliente', 'ClienteController@index');
		Route::post('/cliente/registrar', 'ClienteController@store');
		Route::put('/cliente/actualizar', 'ClienteController@update');

		/* Roles */
		Route::get('rol', 'RolController@index');
		Route::get('rol/listarRol', 'RolController@listarRol');

		/* Usuarios */
		Route::get('user', 'UserController@index');
		Route::post('/user/registrar', 'UserController@store');
		Route::put('/user/actualizar', 'UserController@update');
		Route::put('/user/desactivar', 'UserController@desactivar');
		Route::put('/user/activar', 'UserController@activar');
		
	});

});

//Route::get('/home', 'HomeController@index')->name('home');
