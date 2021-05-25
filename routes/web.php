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

    //Category
    Route::post('store-category','CategoryController@createUpdateCategory')->name('store-category');
    Route::get('category-list','CategoryController@index')->name('category-list');
    Route::delete('delete-category/{id}','CategoryController@destroy')->name('delete-category');
    Route::post('restore-category/{id}','CategoryController@restoreCategory')->name('restore-category');
    Route::get('get-category','CategoryController@geCategoryData')->name('get-category');

    //Sub-Category
    Route::post('store-subcategory','SubCategoryController@createUpdateSubCategory')->name('store-subcategory');
    Route::get('sub-category-list','SubCategoryController@index')->name('subcategory-list');
    Route::delete('delete-subcategory/{id}','SubCategoryController@destroy')->name('delete-subcategory');
    Route::post('restore-subcategory/{id}','SubCategoryController@restoreSubCategory')->name('restore-subcategory');

    //axios requests
    Route::post('store-figure','FigureController@createUpdateFigure')->name('store-figure');
    Route::get('figure-list','FigureController@index')->name('figure-list');
    Route::delete('delete-figure/{id}','FigureController@destroy')->name('delete-figure');
    Route::post('restore-figure/{id}','FigureController@restoreUser')->name('restore-figure');
    Route::get('get-figure','FigureController@geFigureData')->name('get-figure');

    //Size
    Route::post('store-size','SizeController@createUpdateSize')->name('store-size');
    Route::get('size-list','SizeController@index')->name('size-list');
    Route::delete('delete-size/{id}','SizeController@destroy')->name('delete-size');
    Route::post('restore-size/{id}','SizeController@restoreUser')->name('restore-size');
    
    //Color
    Route::post('store-color','ColorController@createUpdateColor')->name('store-color');
    Route::get('color-list','ColorController@index')->name('color-list');
    Route::delete('delete-color/{id}','ColorController@destroy')->name('delete-color');
    Route::post('restore-color/{id}','ColorController@restoreColor')->name('restore-color');
    
    //Material
    Route::post('store-material','MaterialController@createUpdateMaterial')->name('store-material');
    Route::get('material-list','MaterialController@index')->name('material-list');
    Route::delete('delete-material/{id}','MaterialController@destroy')->name('delete-material');
    Route::post('restore-material/{id}','MaterialController@restoreUser')->name('restore-material');

    //Fit
    Route::post('store-fit','FitController@createUpdateFit')->name('store-fit');
    Route::get('fit-list','FitController@index')->name('fit-list');
    Route::delete('delete-fit/{id}','FitController@destroy')->name('delete-fit');
    Route::post('restore-fit/{id}','FitController@restoreUser')->name('restore-fit');

});


Route::group(['middleware' => ['web','auth']], function () {

    Route::get('/', function () {
        return redirect('home');

    });
    Route::get('/{any}', 'HomeController@index')->where('any', '.*');

});
