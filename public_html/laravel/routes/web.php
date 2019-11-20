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

Route::get('/', function () {
    return view('welcome');
});
Auth::routes(['register' => true,'verify'=>true]);

Route::middleware(['auth','verified'])->group(function () {
	Route::get( '/home', 'HomeController@index' )->name( 'home' );
	Route::get('/searches/list','SearchesController@list')->name('searches.list');
	Route::get('/searches/{id}/delete','SearchesController@delete')->name('searches.search.delete');
	Route::get('/searches/{id}/view','SearchesController@view')->name('searches.search.view');
    Route::post('/searches/{id}/update','SearchesController@update')->name('searches.search.update');
});
Route::get('/searches/{id}/unsubscribe', 'SearchesController@unsubscribe')->name('searches.search.unsubscribe')->middleware('signed');
Route::get('/searches/{id}/view/public', 'SearchesController@view')->name('searches.search.view.public')->middleware('signed');
Route::post('/searches/add','SearchesController@add')->name('searches.add');
Route::get('/city/{city}/avgprice','SearchesController@getAvgCityPrice')->name('city.avgprice');
Route::get('/city/{id}/neighborhoods','SearchesController@cityNeighborhoods')->name('city.neighborhoods');
// required paramters in query string: ?city=
Route::get('/city/search','SearchesController@citySearch')->name('city.search');
Route::get('/city/{id}','SearchesController@city')->name('city.view');
