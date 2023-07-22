<?php 
namespace Config;

// Create a new instance of our RouteCollection class.
$routes = Services::routes();

// Load the system's routing file first, so that the app and ENVIRONMENT
// can override as needed.
if (file_exists(SYSTEMPATH . 'Config/Routes.php'))
{
	require SYSTEMPATH . 'Config/Routes.php';
}

/**
 * --------------------------------------------------------------------
 * Router Setup
 * --------------------------------------------------------------------
 */
$routes->setDefaultNamespace('App\Controllers');
$routes->setDefaultController('Home');
$routes->setDefaultMethod('index');
$routes->setTranslateURIDashes(false);
$routes->set404Override();
$routes->setAutoRoute(true);

/**
 * --------------------------------------------------------------------
 * Route Definitions
 * --------------------------------------------------------------------
 */

// We get a performance increase by specifying the default
// route since we don't have to scan directories.
$routes->get('/', 'Home::index');

// ROUTES FOR ALL AUTHINTICATION
$routes->post('api/admin_Login', 'Auth::admin');//done
$routes->post('api/admin_Details', 'Admin::adminDetails');//done
$routes->post('api/Logout', 'Auth::logOut');//done
$routes->post('api/infoUpdate', 'Admin::adminUpdate');//done
$routes->post('api/add_Provider', 'Admin::addProvider');//done
$routes->post('api/update_Probider_By_Admin', 'Admin::updateprobiderbyadmin');//done
$routes->post('api/get_Provider_Detail', 'Admin::getproviderdetail');//done
$routes->post('api/active_inactive_provider', 'Admin::activeinactiveprovider');//done
$routes->post('api/add_ota', 'Admin::addOta');//done
$routes->post('api/update_Ota_By_Admin', 'Admin::updateOtaByAdmin');//done
$routes->post('api/get_Ota_Detail', 'Admin::getOtaDetail');//done
$routes->post('api/active_inactive_ota', 'Admin::activeInactiveOta');//done
$routes->post('api/provider_List', 'ListFilter::providerlist');//done
$routes->post('api/ota_List', 'ListFilter::otalist');//dones
$routes->post('api/user_Registration', 'UserRegistration::userregistration');//done
$routes->post('api/user_List', 'ListFilter::userlist');//done
$routes->post('api/get_user_detail', 'Admin::getUserDetail');//done
$routes->post('api/active_inactive_user', 'Admin::activeInactiveUSer');//done
$routes->post('api/all_login', 'Auth::allLogin');//done
$routes->post('api/all_logout', 'Auth::allLogOut');//done
$routes->post('api/add_package', 'Package::addPackage');//done
$routes->post('api/Package_List', 'ListFilter::packagelist'); // for admin done 
$routes->post('api/active_inactive_package', 'Admin::activeInactivePackage'); // for admin done
$routes->post('api/get_package_detail', 'Admin::getPackageDetail');  // for admin  done
$routes->post('api/package_list_by_provider', 'ListFilter::PackageListByProvider');  // for admin done
$routes->post('api/update_provider_by_provider', 'Provider::updateProviderByProvider'); //
$routes->post('api/update_customer_by_admin', 'Admin::updateCustomerByAdmin');
$routes->post('api/get_package_details_by_admin', 'Admin::getPackageDetailsByAdmin');
$routes->post('api/accept_payment_from_user', 'Admin::acceptPayment');
$routes->post('api/guide_detail', 'Admin::guideDetail');
$routes->post('api/guide_list', 'ListFilter::listOfGuide');
$routes->post('api/active_inactive_guide', 'Admin::activeInactiveGuide');
$routes->post('api/verify_guide', 'ListFilter::verifyGuide');

// common api
$routes->post('api/password_forgot', 'Auth::forgotPassword');//done
$routes->post('api/change_password', 'Auth::passwordChange');//done
$routes->post('api/all_details_by_self', 'Admin::allDetailsBySelf');//done

// for provider
$routes->post('api/package_list_for_provider', 'ListFilter::packageListForProvider');//done
$routes->post('api/package_status_by_provider', 'Provider::activeInactivePackageByProvider');//done
$routes->post('api/list_of_booking_package_by_provider', 'ListFilter::listOfBookingPackageByProvider');//done
$routes->post('api/get_booking_detail_by_provider', 'Provider::getBookingDetailByProvider');//done
$routes->post('api/accept_reject_booking_by_provider', 'Provider::acceptRejectBookingByProvider');//done
$routes->post('api/img', 'Package::storeMultipleFile');
$routes->post('api/get_package_detail_for_provider', 'Provider::getPackageDetailForProvider');//done
$routes->post('api/add_activities', 'Package::addActivities');//done
$routes->post('api/get_activitie_detail_for_provider', 'Provider::getActivitiesDetailForProvider');//done
$routes->post('api/active_inactive_activitie_by_provider', 'Provider::activeInactiveActivitiesByProvider');//done
$routes->post('api/activities_list_for_provider', 'ListFilter::activitiesListForProvider');//done
$routes->post('api/service_provided_by_provider', 'Provider::serviceProvidedByProvider');
$routes->post('api/update_package_by_provider', 'Package::updatePackageByProvider');
$routes->post('api/update_activitie_by_provider', 'Package::updateActivitiesByProvider');



// for user
$routes->post('api/package_list_for_user', 'UserRegistration::packageListForUser');//done
$routes->post('api/get_package_detail_for_user', 'UserRegistration::getPackageDetailForUser');//done
$routes->post('api/package_booking_user', 'Payment::packageBookingUser');//done
$routes->post('api/failed_payment', 'Payment::failedPayment');//done

$routes->post('api/pax_details_for_user', 'UserRegistration::paxDetailsForUser');//kam ki nai h
$routes->post('api/SearchForUser', 'UserRegistration::packageListSearchForUser');//kam ki nai h
$routes->post('api/list_of_booking_history', 'ListFilter::listOfBookingHistory');//done
$routes->post('api/booked_package_detail_by_id', 'User::bookingPackageDetailById');
$routes->post('api/user_regt_login', 'UserRegistration::userRegtLogin');
$routes->post('api/otp_verification', 'UserRegistration::otpVerification');


// master data
$routes->post('api/master_data_for_all', 'Dashboard::masterDataForAll');
$routes->post('api/all_city', 'Dashboard::Allcity');
$routes->post('api/all_state', 'Dashboard::AllState');
$routes->post('api/all_country', 'Dashboard::AllCountry');
$routes->post('api/tryjson', 'Admin::tryjson');
$routes->post('api/included_master', 'Dashboard::includedMaster');
$routes->post('api/not_included_master', 'Dashboard::notIncludedMaster');
$routes->post('api/ideaol_master', 'Dashboard::ideaolMaster');
$routes->post('api/all_city_for_user', 'UserRegistration::AllcityForUser');
$routes->post('api/all_state_for_user', 'UserRegistration::AllStateForUser');
$routes->post('api/all_country_for_user', 'UserRegistration::AllCountryForUser');
$routes->post('api/ideaol_master_for_user', 'UserRegistration::ideaolMasterForUser');
$routes->post('api/arab_city_for_user', 'UserRegistration::arabCityForUser');
$routes->post('api/vehicle_master', 'Dashboard::vehicleMaster');
$routes->post('api/pax_master', 'Dashboard::paxMaster');
$routes->post('api/service_master', 'Dashboard::serviceMaster');
$routes->post('api/get_all_provider', 'Meals::getAllProvider');//done




// guide
$routes->post('api/guide_registration', 'UserRegistration::guideRegistration');





// user Home
$routes->post('api/arabCity', 'Dashboard::arabCity');
$routes->get('getVourchar', 'Vourchar::getVourchar');
$routes->post('api/successPayment', 'Payment::successPayment');
$routes->post('api/payment_stripe_checkout', 'Payment::paymentStripeCheckout');


/**
 * --------------------------------------------------------------------
 * Additional Routing
 * --------------------------------------------------------------------
 *
 * There will often be times that you need additional routing and you
 * need it to be able to override any defaults in this file. Environment
 * based routes is one such time. require() additional route files here
 * to make that happen.
 *
 * You will have access to the $routes object within that file without
 * needing to reload it.
 */
if (file_exists(APPPATH . 'Config/' . ENVIRONMENT . '/Routes.php'))
{
	require APPPATH . 'Config/' . ENVIRONMENT . '/Routes.php';
}

// ADD BY RIZ - 19 JULY 2022
$routes->group('meals', function ($routes) {
	$routes->post('list', 'Meals::allList');
	$routes->post('add', 'Meals::addMeals');
	$routes->post('view', 'Meals::viewMeals');
	$routes->post('update', 'Meals::updateMeals');
	$routes->post('delete', 'Meals::deleteMeals');
	$routes->post('allcuisines', 'Meals::getAllCuisions');

	$routes->post('checkout', 'Meals::mealsCheckOut');
	$routes->post('meals-success', 'Meals::mealsSuccessPayment');

	$routes->post('meals-cod', 'Meals::mealsCodBooking');
});

$routes->group('enquiry', function($routes) {
	$routes->post('list', 'Enquiry::list');
	$routes->post('add', 'Enquiry::addEnquiry');
	$routes->post('view', 'Enquiry::viewEnquiry');
	$routes->post('enquiry-status', 'Enquiry::changeEnquiryStatus');

	$routes->post('change-booking-status', 'Enquiry::changeBookingStatus');
});

$routes->group('guide', function ($route) {	
	$route->post('update-profile', 'Guide::updateProfile');
	$route->post('update-document', 'Guide::updateDocumnet');
	$route->post('update-avatar', 'Guide::updateAvatar');
	$route->post('list', 'Guide::allGuide');
	$route->post('send-enquiry', 'Guide::sendEnquiry');
	$route->post('enquiry-list', 'Guide::EnquiryList');
	$route->post('view-enquiry', 'Guide::EnquiryView');
	$route->post('info', 'Guide::Info');
	$route->post('delete-account', 'Guide::deleteAccount');
});

$routes->group('bookings', function ($route) {
	$route->post('package', 'Bookings::packageBookings');
	$route->post('details', 'Bookings::bookingDetails');
	$route->post('mark-complete', 'Bookings::markCompleted');
});

$routes->group('transactions', function ($route) { 
	$route->post('list', 'Transactions::transactionList');
	$route->post('details', 'Transactions::transactionDetails');
});

$routes->group('masters', function ($route) { 
	$route->post('package-duration', 'Masters::packageDuration');
	$route->post('app-version', 'Masters::UserAppVersion');
	$route->post('check-profile', 'Masters::checkProfileStatus');
	$route->post('language', 'Masters::langauge');
	$route->post('check-mail', 'Masters::checkMail');
});

$routes->post('api/resend-otp', 'UserRegistration::resendOTP');

$routes->group('transport', function ($route) { 
	$route->post('list', 'Transport::list');
	$route->post('add', 'Transport::addEnquiry');
	$route->post('view', 'Transport::viewEnquiry');
});

$routes->post('package-enquiry', 'Package::packageEnquirySend');
$routes->post('package-enquiry-list', 'Package::packageEnquiryList');
$routes->post('package-enquiry-view', 'Package::packageEnquiryView');
$routes->post('package-delete', 'Package::packageDelete');

// ADD BY RIZ - 07 OCT 2022 - SABEEL
$routes->group('sabeel', function ($routes) {
	$routes->post('list', 'Sabeel::allList');
	$routes->post('add', 'Sabeel::addSabeel');
	$routes->post('view', 'Sabeel::viewSabeel');
	$routes->post('update', 'Sabeel::updateSabeel');
	$routes->post('delete', 'Sabeel::deleteSabeel');

	$routes->post('booking-list', 'Sabeel::bookingList');
	$routes->post('booking-view', 'Sabeel::bookingView');

	$routes->post('checkout', 'Sabeel::sabeelCheckOut');
	$routes->post('sabeel-success', 'Sabeel::sabeelSuccessPayment');

	$routes->post('sabeel-cod', 'Sabeel::sabeelCodBooking');
});

// TEST EMAIL TEMPLATE
$routes->get('template', 'UserRegistration::EmailTemplate');
$routes->post('check-fcm', 'Masters::testFCM');

$routes->post('provider-dashboard', 'Dashboard::providerDashboard');
$routes->post('ota-dashboard', 'Dashboard::otaDashboard');
$routes->post('admin-dashboard', 'Dashboard::adminDashboard');

$routes->post('package-cod', 'Payment::packageCodBooking');

// END - RIZ