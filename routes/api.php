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
Route::post('/reset_password','Auth\ResetPasswordController@reset_password');
Route::post('login', 'Auth\LoginController@login');
Route::post('/logout','Auth\LoginController@logout');
Route::get('/logout','Auth\LoginController@logout');

Route::resource('roles', 'RolesController');
Route::resource('role_user', 'RoleUserController');

Route::resource('leave_patterns', 'LeavePatternsController');
Route::resource('holidays', 'HolidaysController');
Route::resource('permissions', 'PermissionsController');
Route::resource('permission_role', 'PermissionRoleController');

Route::resource('users', 'UsersController');
Route::patch('users/{user}/uniqueID', 'UsersController@checkOrUpdateUniqueID');

Route::resource('companies', 'CompaniesController');
Route::resource('company_user', 'CompanyUserController');
Route::resource('company_states', 'CompanyStatesController');
Route::resource('break_types', 'BreakTypesController');
Route::resource('allowance_types', 'AllowanceTypesController');
Route::resource('transport_modes', 'TransportModesController');
Route::resource('travelling_ways', 'TravellingWaysController');
Route::resource('feedbacks', 'FeedbacksController');
Route::resource('plans', 'PlansController');
Route::resource('reasons', 'ReasonsController');
Route::resource('plans/{plan}/plan_actual', 'PlanActualController');
Route::resource('plans/{plan}/plan_travelling_details', 'PlanTravellingDetailsController');
Route::resource('companies/{company}/company_designations', 'CompanyDesignationsController');
Route::resource('company_states/{company_state}/company_state_branches', 'CompanyStateBranchesController');
Route::resource('company_states/{company_state}/company_state_holidays', 'CompanyStateHolidaysController');

Route::resource('company_leave_pattern', 'CompanyLeavePatternController');
Route::resource('company_leaves', 'CompanyLeavesController');
Route::resource('leave_types', 'LeaveTypesController');

Route::resource('user_attendances', 'UserAttendancesController');
Route::resource('user_attendances/{user_attendance}/user_attendance_breaks', 'UserAttendanceBreaksController');
Route::resource('user_applications', 'UserApplicationsController');
Route::resource('user_applications/{user_application}/application_approvals', 'ApplicationApprovalsController');
Route::resource('supervisor_user', 'SupervisorUsersController');

Route::resource('user_sales', 'UserSalesController');

Route::resource('voucher_types', 'VoucherTypesController');
Route::resource('vouchers', 'VouchersController');

Route::resource('user_locations', 'UserLocationsController');
Route::resource('geolocator_user_locations', 'GeolocatorUserLocationsController');

// Uploads
Route::post('upload_profile_image', 'UploadController@uploadProfileImage');
Route::post('upload_profile', 'UploadController@uploadProfile');
Route::post('upload_signature', 'UploadController@uploadSignature');
Route::post('upload_bill/{id}', 'UploadController@uploadBill');
Route::post('upload_retailer/{id}', 'UploadController@uploadRetailer');

Route::resource('products', 'ProductsController');
Route::resource('products/{product}/skus', 'SkusController');
Route::resource('sku_types', 'SkuTypesController');
Route::resource('offer_types', 'OfferTypesController');
Route::resource('offers', 'OffersController');
Route::resource('skus/{skus}/stocks', 'StocksController');
Route::resource('skus/{sku}/sales', 'SalesController');
Route::get('sales', 'SalesController@all');
// Route::resource('stocks/{stock}/sales', 'SalesController');

Route::resource('reference_plans', 'ReferencePlansController');
Route::resource('reference_plans/{reference_plan}/retailers', 'RetailersController');
Route::get('un_approved_retailers', 'RetailersController@unApprovedRetailers');
Route::get('approve_retailer/{id}', 'RetailersController@singleApproveRetailer');
Route::post('approve_retailer', 'RetailersController@approveRetailer');

Route::resource('units', 'UnitsController');
Route::resource('retailer_categories', 'RetailerCategoriesController');
Route::resource('retailer_classifications', 'RetailerClassificationsController');

Route::resource('marks', 'MarksController');

Route::get('time', 'TimesController@index');
Route::get('geocode', 'GeocodesController@index');
Route::post('sendEmail', 'SendEmailController@index');

Route::resource('users/{user}/user_appointment_letters', 'UserAppointmentLettersController');
Route::resource('users/{user}/user_experience_letters', 'UserExperienceLettersController');
Route::resource('users/{user}/user_warning_letters', 'UserWarningLettersController');
Route::resource('users/{user}/user_promotion_letters', 'UserPromotionLettersController');
Route::resource('users/{user}/user_renewal_letters', 'UserRenewalLettersController');
Route::resource('users/{user}/user_increemental_letters', 'UserIncreementalLettersController');
Route::resource('users/{user}/user_termination_letters', 'UserTerminationLettersController');
Route::resource('users/{user}/user_full_final_letters', 'UserFullFinalLettersController');

Route::resource('users/{user}/user_work_experiences', 'UserWorkExperiencesController');
Route::resource('users/{user}/user_educations', 'UserEducationsController');
Route::resource('users/{user}/user_family_details', 'UserFamilyDetailsController');
Route::resource('users/{user}/user_references', 'UserReferencesController');

Route::resource('users/{user}/notifications', 'NotificationsController');

Route::post('user_profile_image', 'UserUploadsController@profileImage');

Route::post('mobile_photo_image', 'MobileUploadsController@mobilePhotoImage');
Route::post('mobile_residential_proof_image', 'MobileUploadsController@mobileResidentialProofImage');
Route::post('mobile_education_proof_image', 'MobileUploadsController@mobileEducationProofImage');
Route::post('mobile_pan_card_image', 'MobileUploadsController@mobilePanCardImage');
Route::post('mobile_adhaar_card_image', 'MobileUploadsController@mobileAdhaarCardImage');
Route::post('mobile_esi_card_image', 'MobileUploadsController@mobileEsiCardImage');
Route::post('mobile_cancelled_cheque_image', 'MobileUploadsController@mobileCancelledChequeImage');
Route::post('mobile_salary_slip_image', 'MobileUploadsController@mobileSalarySlipImage');
Route::post('mobile_birth_certificate_image', 'MobileUploadsController@mobileBirthCertificateImage');
Route::post('mobile_passport_image', 'MobileUploadsController@mobilePassportImage');
Route::post('mobile_driving_license_image', 'MobileUploadsController@mobileDrivingLicenseImage');
Route::post('mobile_school_leaving_certificate_image', 'MobileUploadsController@mobileSchoolLeavingCertificateImage');
Route::post('mobile_mark_sheet_image', 'MobileUploadsController@mobileMarkSheetImage');
Route::post('mobile_experience_certificate_image', 'MobileUploadsController@mobileExperienceCertificateImage');
Route::post('mobile_prev_emp_app_letter_image', 'MobileUploadsController@mobilePrevEmpAppLetterImage');
Route::post('mobile_form_2_image', 'MobileUploadsController@mobileForm2Image');
Route::post('mobile_form_11_image', 'MobileUploadsController@mobileForm11Image');
Route::post('mobile_graduity_form_image', 'MobileUploadsController@mobileGraduityFormImage');
Route::post('mobile_app_letter_image', 'MobileUploadsController@mobileAppLetterImage');
Route::post('mobile_pds_form_image', 'MobileUploadsController@mobilePdsFormImage');
Route::post('mobile_appointment_letter_sign', 'MobileUploadsController@mobileAppointmentLetterSign');
Route::post('mobile_experience_letter_sign', 'MobileUploadsController@mobileExperienceLetterSign');
Route::post('mobile_renewal_letter_sign', 'MobileUploadsController@mobileRenewalLetterSign');
Route::post('mobile_warning_letter_sign', 'MobileUploadsController@mobileWarningLetterSign');
Route::post('mobile_promotion_letter_sign', 'MobileUploadsController@mobilePromotionLetterSign');
Route::post('mobile_increemental_letter_sign', 'MobileUploadsController@mobileIncreementalLetterSign');
Route::post('mobile_termination_letter_sign', 'MobileUploadsController@mobileTerminationLetterSign');
Route::post('mobile_full_final_letter_sign', 'MobileUploadsController@mobileFullFinalLetterSign');
Route::post('mobile_pds_form_sign', 'MobileUploadsController@mobilePdsFormSign');
Route::post('mobile_form_2_sign', 'MobileUploadsController@mobileForm2Sign');
Route::post('mobile_form_11_sign', 'MobileUploadsController@mobileForm11Sign');
Route::post('mobile_graduity_form_sign', 'MobileUploadsController@mobileGraduityFormSign');


Route::post('company_pds', 'CompanyUploadsController@pds');
Route::post('company_form2', 'CompanyUploadsController@form2');
Route::post('company_form11', 'CompanyUploadsController@form11');
Route::post('company_pf', 'CompanyUploadsController@pf');
Route::post('company_esic', 'CompanyUploadsController@esic');
Route::post('company_esic', 'CompanyUploadsController@esic');
Route::post('company_insurance_claim', 'CompanyUploadsController@insurance_claim');
Route::post('company_insurance_claim', 'CompanyUploadsController@insurance_claim');
Route::post('company_salary_slip', 'CompanyUploadsController@salary_slip');
Route::post('company_pms_policies', 'CompanyUploadsController@pms_policies');
Route::post('company_act_of_misconduct', 'CompanyUploadsController@act_of_misconduct');
Route::post('company_act_of_misconduct', 'CompanyUploadsController@act_of_misconduct');
Route::post('company_uan_activation', 'CompanyUploadsController@uan_activation');
Route::post('company_uan_activation', 'CompanyUploadsController@uan_activation');
Route::post('company_online_claim', 'CompanyUploadsController@online_claim');
Route::post('company_online_claim', 'CompanyUploadsController@online_claim');
Route::post('company_kyc_update', 'CompanyUploadsController@kyc_update');
Route::post('company_kyc_update', 'CompanyUploadsController@kyc_update');
Route::post('company_graduity_form_a', 'CompanyUploadsController@graduity_form_a');

Route::get('pds-forms/{id}', 'FormsController@pdsForm');
Route::get('form-2/{id}', 'FormsController@form2');
Route::get('form-11/{id}', 'FormsController@form11');
Route::get('graduity-forms/{id}', 'FormsController@graduityForm');

Route::get('welcome-email', 'EmailsController@welcomeEmail');
Route::get('appointment-letter-email', 'EmailsController@appointmentLetterEmail');
Route::get('renewal-letter-email', 'EmailsController@renewalLetterEmail');
Route::get('experience-letter-email', 'EmailsController@experienceLetterEmail');
Route::get('warning-letter-email', 'EmailsController@warningLetterEmail');
Route::get('promotion-letter-email', 'EmailsController@promotionLetterEmail');
Route::get('increemental-letter-email', 'EmailsController@increementalLetterEmail');
Route::get('termination-letter-email', 'EmailsController@terminationLetterEmail');
Route::get('full-final-letter-email', 'EmailsController@fullFinalLetterEmail');

Route::get('truncate_users', 'CrudeUsersController@truncate');
Route::get('crude_users', 'CrudeUsersController@index');
Route::post('upload_user', 'CrudeUsersController@uploadUser');
Route::get('process_user', 'CrudeUsersController@processUser');