<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('me', 'MeController@me');

Route::get('count', 'HomeController@count');


Route::post('/register', 'Auth\RegisterController@register');
Route::post('/reset_password', 'Auth\ResetPasswordController@reset_password');
Route::post('login', 'Auth\LoginController@login');
Route::post('/logout', 'Auth\LoginController@logout');
Route::get('/logout', 'Auth\LoginController@logout');

Route::resource('versions', 'VersionsController');
Route::resource('roles', 'RolesController');
Route::resource('role_user', 'RoleUserController');

Route::resource('permissions', 'PermissionsController');
Route::resource('permission_role', 'PermissionRoleController');

Route::get('users/count', 'UsersController@countUsers');
Route::get('users/masters', 'UsersController@masters');
Route::get('users/search', 'UsersController@search');
Route::get('users/excel_export', 'UsersController@excelDownload');
Route::get('users/search_by_role', 'UsersController@searchByRole');
Route::patch('users/{user}/uniqueID', 'UsersController@checkOrUpdateUniqueID');
Route::resource('users', 'UsersController');

Route::resource('companies', 'CompaniesController');
Route::resource('company_user', 'CompanyUserController');

Route::post('sendEmail', 'SendEmailController@index');

Route::get('crude_users', 'CrudeUsersController@index');
Route::post('upload_user', 'CrudeUsersController@uploadUser');
Route::get('process_user', 'CrudeUsersController@processUser');
Route::get('truncate_users', 'CrudeUsersController@truncate');

Route::get('send_otp', 'SendSmsController@index');

// Boards
Route::resource('boards', 'BoardsController');

// TOI XML
Route::post('toi_xml/upload', 'UploadsController@toi_xml');
Route::get('toi_xml', 'ToiXmlsController@index');

// TOI Articles
Route::resource('toi_articles', 'ToiArticlesController');

// Subjects
Route::resource('subjects', 'SubjectsController');
