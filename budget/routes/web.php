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
    return view('auth/login');
});

Route::group(['middleware' => 'auth'], function() {
    Route::get('/home', 'HomeController@index')->name('home');
    Route::post('/home' , 'HomeController@bulletinBoadStore')->name('home');

    Route::resource('/SearchPage', 'SearchPageController', ['only' => ['index', 'update', 'destroy']]); 
    Route::get('/SearchPage/updateCartOrderRequestNum', 'SearchPageController@updateCartOrderRequestNum');
    Route::get('/SearchPage/updateFavorite', 'FavoriteController@updateFavorite');
    Route::get('/SearchPage/createFavoriteFolder', 'FavoriteController@createFavoriteFolder');
    Route::resource('/OrderRequest', 'OrderRequestController',['only' => ['index', 'update', 'destroy']]);
    Route::get('/OrderRequest/updateListData', 'OrderRequestController@updateListData');
    Route::get('/OrderRequest/updateFavorite', 'FavoriteController@updateFavorite');
    Route::get('/OrderRequest/createFavoriteFolder', 'FavoriteController@createFavoriteFolder');
    
    /*Route::get('/SearchPage/cartnumadd', 'SearchPageController@updateCartNumber');*/
    //Route::resource('/SearchPage', 'SearchPageController');
    //Route::post('/SearchPage', 'SearchPageController@folderAdd');
    //Route::get('/SearchPage','SearchPageController@treeFavorite')->name('SearchPage.treeFavorite');
    Route::resource('/User','UserController');
    Route::resource('/Supplier','SupplierController');
    Route::resource('/Maker','MakerController');
    Route::resource('/Budget','BudgetController');

});

Auth::routes();

