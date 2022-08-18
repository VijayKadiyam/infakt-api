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

Route::post('processTOIXML', 'ToiArticlesController@processTOIXML');

// ContactRequests
Route::resource('contact_requests', 'ContactRequestsController');


// UserStandard
Route::resource('user_standards', 'UserStandardsController');

// CareerRequests
Route::resource('career_requests', 'CareerRequestsController');

// Contents
Route::get('contents/masters', 'ContentsController@masters');
Route::resource('contents', 'ContentsController');

// Standards
Route::resource('standards', 'StandardsController');

// ContentSubjects
Route::resource('content_subjects', 'ContentSubjectsController');

// ContentMedias
Route::post('upload_content_mediapath', 'UploadsController@upload_content_mediapath');
Route::resource('content_medias', 'ContentMediasController');

// UserSections
Route::resource('user_sections', 'UserSectionsController');

// Sections
Route::resource('standards/{standard}/sections', 'SectionsController');
Route::resource('sections', 'SectionsController');

// Classcodes
Route::resource('classcodes', 'ClasscodesController');

// Assignments
Route::resource('assignments', 'AssignmentsController');

// Assignment Classcodes
Route::resource('assignment_classcodes', 'AssignmentClasscodesController');

// User Assignment 
Route::post('upload_user_assignment_documentpath', 'UploadsController@upload_user_assignment_documentpath');
Route::resource('user_assignments', 'UserAssignmentsController');

// Assignment Questions
Route::resource('assignment_questions', 'AssignmentQuestionsController');

// Assignment QuestionOptions
Route::resource('assignment_question_options', 'AssignmentQuestionOptionsController');

// UserClasscodes
Route::resource('user_classcodes', 'UserClasscodesController');

// Assignment Extensions
Route::resource('assignment_extensions', 'AssignmentExtensionsController');

// UserAssignmentSelectedAnswers
Route::post('upload_uasa_documentpath', 'UploadsController@upload_uasa_documentpath');
Route::resource('user_assignment_selected_answers', 'UserAssignmentSelectedAnswersController');

// bookmarks
Route::resource('bookmarks', 'BookmarksController');

// collection
Route::resource('collections', 'CollectionsController');

// Value 
Route::resource('values', 'ValuesController');
//  Value List
Route::get('value_lists/masters', 'ValueListsController@masters');
Route::post('values/{value}/value_lists_multiple', 'ValueListsController@storeMultiple');
Route::resource('values/{value}/value_lists', 'ValueListsController');
Route::resource('value_lists', 'ValueListsController');

// collection_contents
Route::resource('collection_contents', 'CollectionContentsController');
