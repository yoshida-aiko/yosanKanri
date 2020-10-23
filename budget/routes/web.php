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
    Route::get('/SearchPage/checkOrderRequest', 'SearchPageController@checkOrderRequest');
    Route::get('/SearchPage/cartAddProcess', 'SearchPageController@cartAddProcess');
    Route::get('/SearchPage/favoriteAddProcess', 'SearchPageController@favoriteAddProcess');
    Route::get('/SearchPage/getData_Favorite', 'SearchPageController@getData_Favorite');

    Route::resource('/OrderRequest', 'OrderRequestController',['only' => ['index', 'update', 'destroy']]);
    Route::get('/OrderRequest/updateListPrice', 'OrderRequestController@updateListPrice');
    Route::get('/OrderRequest/moveToCart', 'OrderRequestController@moveToCart');
    Route::get('/OrderRequest/updateFavorite', 'FavoriteController@updateFavorite');
    Route::get('/OrderRequest/createFavoriteFolder', 'FavoriteController@createFavoriteFolder');
    Route::get('/OrderRequest/newProductStore', 'OrderRequestController@newProductStore');
    Route::get('/OrderRequest/orderRequest', 'OrderRequestController@orderRequest');
    Route::get('/OrderRequest/getData_Favorite', 'SearchPageController@getData_Favorite');

    Route::resource('/Order', 'OrderController',['only' => ['index', 'update', 'destroy']]);
    Route::get('/Order/updateListPrice', 'OrderController@updateListPrice');
    Route::get('/Order/updateSupplier', 'OrderController@updateSupplier');
    Route::get('/Order/updateOrderRequestGiveBudget', 'OrderController@updateOrderRequestGiveBudget');
    Route::get('/Order/orderExec', 'OrderController@orderExec');
    Route::get('/Order/createPDF', 'OrderController@createPDF');
    Route::resource('/Delivery', 'DeliveryController', ['only' => ['index', 'update', 'destroy']]); 
    Route::get('/Delivery/insertDelivery', 'DeliveryController@insertDelivery');
    
    Route::resource('/BudgetStatus', 'BudgetStatusController', ['only' => ['index']]); 
    Route::get('/BudgetStatus/getDetail', 'BudgetStatusController@getDetail');
    Route::get('/BudgetStatus/balanceAdjustment', 'BudgetStatusController@balanceAdjustment');
    Route::get('/BudgetStatus/outputCSV', 'BudgetStatusController@outputCSV');

    Route::resource('/Purchase', 'PurchaseController', ['only' => ['index']]); 
    Route::get('/Purchase/outputCSV', 'PurchaseController@outputCSV');
    Route::get('/Purchase/insertOrderRequest', 'PurchaseController@insertOrderRequest');

    Route::resource('/User','UserController');
    Route::get('/Order/passwordHash','OrderController@passwordHash');
    Route::resource('/Supplier','SupplierController');
    Route::resource('/Maker','MakerController');
    Route::resource('/Budget','BudgetController');
    Route::resource('/Condition', 'ConditionController');
    Route::post('/Condition', 'ConditionController@judge');
    
    Route::get('pdf','PDFController@index');
    Route::get('lang/{lang}', ['as'=>'lang.switch', 'uses'=>'LanguageController@switchLang']);
    
});

Auth::routes();

