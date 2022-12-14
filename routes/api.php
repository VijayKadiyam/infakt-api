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
Route::get('users/send_mail', 'UsersController@SendMail');
Route::get('users/send_mail_all', 'UsersController@SendMailAll');
Route::get('users/excel_export', 'UsersController@excelDownload');
Route::get('users/search_by_role', 'UsersController@searchByRole');
Route::patch('users/{user}/uniqueID', 'UsersController@checkOrUpdateUniqueID');
Route::post('upload_user_image', 'UploadsController@uploadUserImage');
Route::resource('users', 'UsersController');

Route::get('companies/send_mail', 'CompaniesController@SendMail');
Route::get('companies/masters', 'CompaniesController@masters');
Route::resource('companies', 'CompaniesController');
Route::resource('company_user', 'CompanyUserController');

Route::post('sendEmail', 'SendEmailController@index');

// Crude User
Route::get('crude_users', 'CrudeUsersController@index');
Route::post('upload_user', 'CrudeUsersController@uploadUsers');
Route::get('process_user', 'CrudeUsersController@processUsers');
Route::get('truncate_users', 'CrudeUsersController@truncate');

// Crude Teacher
Route::get('crude_teachers', 'CrudeTeachersController@index');
Route::post('upload_teacher', 'CrudeTeachersController@uploadTeachers');
Route::get('process_teacher', 'CrudeTeachersController@processTeachers');
Route::get('truncate_teachers', 'CrudeTeachersController@truncate');

// Crude Student
Route::get('crude_students', 'CrudeStudentsController@index');
Route::post('upload_student', 'CrudeStudentsController@uploadStudents');
Route::get('process_student', 'CrudeStudentsController@processStudents');
Route::get('truncate_students', 'CrudeStudentsController@truncate');

Route::get('send_otp', 'SendSmsController@index');

// Boards
Route::resource('boards', 'BoardsController');

// TOI XML
Route::post('toi_xml/upload', 'UploadsController@toi_xml');
Route::get('toi_xml', 'ToiXmlsController@index');

// TOI Articles
Route::resource('toi_articles', 'ToiArticlesController');

// Subjects
Route::post('upload_subject_imagepath', 'UploadsController@upload_subject_imagepath');
Route::resource('subjects', 'SubjectsController');

Route::post('processTOIXML', 'ToiArticlesController@processTOIXML');

// ContactRequests
Route::resource('contact_requests', 'ContactRequestsController');


// UserStandard
Route::resource('user_standards', 'UserStandardsController');

// CareerRequests
Route::post('upload_career_attachment', 'UploadsController@upload_career_attachment');
Route::resource('career_requests', 'CareerRequestsController');


// Search Mother [TOI + ET] Article
Route::get('contents/search_mother_articles', 'ContentsController@search_mother_articles');
// Contents
Route::get('contents/masters', 'ContentsController@masters');
Route::get('contents/assigned_to_read_articles', 'ContentsController@assigned_to_read_articles');
Route::resource('contents', 'ContentsController');
Route::post('upload_content_featured_image', 'UploadsController@uploadContentFeaturedImage');
Route::post('upload_editor_image', 'UploadsController@uploadEditorImage');

// Standards
Route::get('standards/masters', 'StandardsController@masters');
Route::resource('standards', 'StandardsController');
Route::post('standards/checkUnique', 'StandardsController@checkUnique');

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
Route::resource('sections/{section}/classcodes', 'ClasscodesController');
Route::resource('classcodes', 'ClasscodesController');

// Assignments
Route::get('assignments/type_overview', 'AssignmentsController@type_overview');
Route::get('assignments/assignment_wise_performance_overview', 'AssignmentsController@assignment_wise_performance_overview');
Route::get('assignments/student_wise_performance_overview', 'AssignmentsController@student_wise_performance_overview');
Route::resource('assignments', 'AssignmentsController');
Route::post('upload_assignment_document', 'UploadsController@uploadAssignmentDocument');

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

// Content Reads
Route::resource('content_reads', 'ContentReadsController');

// Dashboards
Route::get('masters', 'DashboardsController@masters');
Route::get('getSubTypes', 'DashboardsController@getSubTypes');
Route::get('adminDashboard', 'DashboardsController@adminDashboard');
Route::get('teacherDashboard', 'DashboardsController@teacherDashboard');
Route::get('studentDashboard', 'DashboardsController@studentDashboard');
// notifications 
Route::post('notifications/mark_all_read', 'NotificationsController@mark_all_read');
Route::post('notifications/clear_all', 'NotificationsController@clear_all');
Route::post('notifications/clear', 'NotificationsController@clear');
Route::resource('notifications', 'NotificationsController');

// Bookmark Classcodes 
Route::resource('bookmark_classcodes', 'BookmarkClasscodesController');

// Collection Classcodes 
Route::resource('collection_classcodes', 'CollectionClasscodesController');

// Content Classcodes 
Route::resource('content_classcodes', 'ContentClasscodesController');


// ET XML
Route::post('et_xml/upload', 'UploadsController@et_xml');
Route::get('et_xml', 'EtXmlsController@index');
Route::post('processETXML', 'EtXmlsController@processETXML');
// ET Articles
Route::resource('et_articles', 'EtArticlesController');

// Content Metadata
Route::resource('content_metadatas', 'ContentMetadatasController');

// grades
Route::resource('grades', 'GradesController');

// Content Description
Route::resource('content_descriptions', 'ContentDescriptionsController');

// About Us
Route::resource('about_us', 'AboutUsController');

// Company Boards
Route::resource('company_boards', 'CompanyBoardsController');

// toi_xml_imap
Route::get('toi_xmls/emails', 'ToiXmlsController@toi_xml_imap');

// et_xml_imap
Route::get('et_xmls/emails', 'EtXmlsController@et_xml_imap');

// Content Description
Route::resource('user_timestamps', 'UserTimestampsController');
// SUPERADMIN Dashboard
Route::get('superadminDashboard', 'SuperAdminDashboardsController@superadminDashboard');

// Dashboard (Content Based (Grade, Board, Subject and school))
Route::get('content_based_counts', 'SuperAdminDashboardsController@contentBasedCount');
// School Wise Overview
Route::get('school_wise_overview', 'AdminDashboardsController@SchoolWiseOverview');
// Classcode Overview
Route::get('classcode_wise_overview', 'DashboardsController@ClasscodeWiseOverview');
// Top 10 School based on no of assignment
Route::get('top_school_based_on_assignments', 'DashboardsController@topSchoolBasedOnAssignments');
// User Timestamps
Route::resource('user_timestamps', 'UserTimestampsController');

// searches
Route::resource('searches', 'SearchesController');

// Teacher Overview

// features
Route::resource('features', 'FeaturesController');

// Student Overview
Route::get('student_wise_overview', 'StudentDashboardsController@StudentWiseOverview');
Route::get('teacher_wise_overview', 'TeacherDashboardsController@TeacherWiseOverview');

Route::get('contentMetadataWise', 'DashboardsController@contentMetadataWise');

// jobs
Route::resource('jobs', 'JobsController');

// content_grades
Route::resource('content_grades', 'ContentGradesController');

//content_info_boards
Route::resource('content_info_boards', 'ContentInfoBoardsController');
// content_boards
Route::resource('content_boards', 'ContentBoardsController');

// content_schools
Route::resource('content_schools', 'ContentSchoolsController');
Route::get('getMyStudents', 'UsersController@getMyStudents');
//User_subjects
Route::resource('user_subjects', 'UserSubjectsController');
// categories
Route::resource('categories', 'CategoriesController');
// content_categories
Route::resource('content_categories', 'ContentCategoriesController');

// Most Popular Article
Route::get('most_popular_articles', 'ContentsController@mostContentRead');

// User Assignment Timing
Route::resource('user_assignment_timings', 'UserAssignmentTimingsController');

// Task Wise Overview
Route::get('task_wise_overview', 'AdminDashboardsController@TaskWiseOverview');

// Subject Wise Overview
Route::get('subject_wise_overview', 'AdminDashboardsController@subjectWiseOverview');

// Assignment Wise Performance
Route::get('assignment_wise_performance', 'AdminDashboardsController@assignmentWisePerformance');

// Epaper Bookmakrs 
Route::resource('epaper_bookmarks', 'EpaperBookmarksController');

// Epaper Collection
Route::resource('epaper_collections', 'EpaperCollectionsController');

// Collection Epaper 
Route::resource('collection_epapers', 'CollectionEpapersController');

// Dictionary 
Route::resource('dictionaries', 'DictionariesController');
