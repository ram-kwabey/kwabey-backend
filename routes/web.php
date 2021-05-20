<?php

use Illuminate\Support\Facades\Auth;
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
    return view('auth.login');
});

Auth::routes(['verify' => true]);

Route::get('/home', 'HomeController@index')->name('home');

Route::prefix('admin')->namespace('Admin')->name('admin.')->middleware(['web','auth'])->group(function(){
    Route::get('users','AdminController@index')->name('users');
    //axios requests
    Route::get('users-list','AdminController@users_list')->name('users-list');
    Route::post('users-list','AdminController@store')->name('store-users-List');
    Route::get('getRoles','AdminController@getRoles')->name('getRoles');
    Route::delete('user/{id}','AdminController@deleteUser')->name('delete_user');
    Route::put('updateUser/{user}','AdminController@updateUser')->name('updateUser');
    Route::get('getAllUsers','AdminController@getAllUsers')->name('getAllUsers');
    Route::post('/restore_user/{id}','AdminController@restore_user');
    
    //Roles and permission controller
    Route::get('roles','RolesController@index')->name('roles');
    Route::post('user-role','RolesController@store')->name('store-user-role');
    Route::get('permissions','RolesController@show_permissions')->name('permissions');
    Route::post('user-permission','RolesController@store_permission')->name('store-user-permission');
    Route::get('getSetters','AdminController@getSetters')->name('getSetters');
    Route::get('permissions','RolesController@getPermissions')->name('get_permissions');
    Route::get('checkedPermissions','RolesController@checkedPermissions')->name('checkedPermissions');
    Route::post('updatePermissions','RolesController@updatePermissions')->name('updatePermissions');

    
});

Route::prefix('admin')->namespace('Admin')->name('admin.')->middleware(['web','auth'])->group(function(){
    
    Route::get('edit-profile', 'UserController@editUserProfile');
    Route::put('user/update/{id}', 'UserController@update');
    Route::post('handleUsersCsv','UserController@handleUsersCsv');
    
});


Route::prefix('admin')->namespace('Admin')->name('admin.')->middleware(['web','auth'])->group(function(){
    //axios requests
    Route::get('usersCount','DashboardController@getUsersCount')->name('usersCount');
    Route::get('getAllLeads','DashboardController@getAllLeads')->name('getAllLeads');
    Route::get('clientsCount','DashboardController@getClientsCount')->name('clientsCount');
    Route::get('getAllTask','DashboardController@getAllTask')->name('getAllTask');
});

Route::prefix('admin')->namespace('Admin')->name('admin.')->middleware(['web','auth'])->group(function(){
    //axios requests
    Route::get('template-list','EmailTemplateController@template_list')->name('temaplate-list');
    Route::post('store-template','EmailTemplateController@store')->name('store-template');
    Route::post('updateTemplate/{id}','EmailTemplateController@update')->name('updateTemplate');
});

Route::prefix('admin')->namespace('Admin')->name('admin.')->middleware(['web','auth'])->group(function(){
    //axios requests
    Route::post('store-figure','FigureController@createUpdateFigure')->name('store-figure');
    Route::get('figure-list','FigureController@index')->name('figure-list');
    Route::delete('delete-figure/{id}','FigureController@destroy')->name('delete-figure');
    Route::post('restore-figure/{id}','FigureController@restoreUser')->name('restore-figure');
});


Route::group(['middleware' => ['web','auth']], function () {

    Route::get('/', function () {
        return redirect('home');

    });
    Route::get('/{any}', 'HomeController@index')->where('any', '.*');

});
