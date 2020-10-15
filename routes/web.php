<?php

use Illuminate\Support\Facades\Route;

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

Route::get('/', function () {
    return redirect('admin');
});

Route::group(['prefix' => 'admin'], function () {
    Voyager::routes();
});

Auth::routes();

// Route::get('/home', 'HomeController@index')->name('home');

/*
|--------------------------------------------------------------------------
| Rutas Documentos----------------->
|--------------------------------------------------------------------------

 */
Route::put('documentos/{id}/inactivar', 'DocumentosController@inactivar')->name('documentos.inactivar');
Route::put('documentos/{id}/activar', 'DocumentosController@activar')->name('documentos.activar');
Route::put('documentos/{id}/restore', 'DocumentosController@restore')->name('documentos.restore');
Route::group(['middleware' => ['activity']], function () {
    Route::apiResources(
        [
            'documentos' => 'DocumentosController',
        ]
    );
});
/*
|--------------------------------------------------------------------------
| Rutas Documentos----------------->
|--------------------------------------------------------------------------

 */
