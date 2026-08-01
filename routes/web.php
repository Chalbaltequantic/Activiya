<?php
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\VerificationController;
use App\Http\Controllers\Auth\RegisterController;

use App\Http\Controllers\Admin\BilldataController;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;


use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\AdminUserController as UserController;
use App\Http\Controllers\Admin\TruckMasterController;
use App\Http\Controllers\Admin\SpotbyController;
use App\Http\Controllers\Admin\ConsigneeReturnDurationController;


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

/*Route::get('/', function () {
    return view('welcome');
});
*/




Auth::routes();


Route::get('/', [LoginController::class, 'showAdminLoginForm'])->name('admin.login-view');
Route::post('/', [LoginController::class, 'adminLogin'])->name('admin.login');

Route::get('/admin',[LoginController::class,'showAdminLoginForm'])->name('admin.adminlogin-view');
Route::post('/admin',[LoginController::class,'adminLogin'])->name('admin.adminlogin');
//

//
Route::get('/admin/register',[RegisterController::class,'showAdminRegisterForm'])->name('admin.register-view');
Route::post('/admin/register',[RegisterController::class,'createAdmin'])->name('admin.register');

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');



// user and role Management
Route::group(['as' => 'admin.', 'prefix' => 'admin/'], function () {

    Route::resource('roles', RoleController::class);
    Route::resource('users', UserController::class);
	
	Route::get('change-password', [App\Http\Controllers\Admin\PasswordController::class, 'index'])->name('change.password');
    Route::post('change-password', [App\Http\Controllers\Admin\PasswordController::class, 'update'])->name('change.password.update');
	
	
	
	Route::get('dashboard', [App\Http\Controllers\Admin\DashboardController::class, 'index'])->name('dashboard');

	
	////BILL data upload Route
	Route::post('/import', [App\Http\Controllers\Admin\BilldataController::class, 'import'])->name('excel.import');
	
	//Bill Data Upload Route
	Route::get('/billdata', [App\Http\Controllers\Admin\BilldataController::class, 'index'])->name('billdata');
	
	Route::get('/billdata/freight-shipment-history', [App\Http\Controllers\Admin\BilldataController::class, 'billdatalist'])->name('billdatalist');
	
	//manual upload data
	Route::get('/billdata/manual-upload', [App\Http\Controllers\Admin\BilldataController::class, 'manualupload'])->name('manualupload');
	
	Route::post('billdata/save_manual_upload', [App\Http\Controllers\Admin\BilldataController::class, 'save_manual_billdata']);
	//////////
	Route::get('billdata/editbilldata/{id}', [App\Http\Controllers\Admin\BilldataController::class,'getBilldataDetails'])->name('getBilldataDetails');
	
	Route::post('billdata/updatebilldata', [App\Http\Controllers\Admin\BilldataController::class, 'save_billdata']);
	
	Route::get('deletebilldata/{id}', 'App\Http\Controllers\Admin\BilldataController@DeleteBillData')->name('DeleteBillData');
	
	Route::post('billdata/bulk-delete', [App\Http\Controllers\Admin\BilldataController::class, 'bulkDelete'])
    ->name('billdata.bulkDelete');
	
	///Bil data freight detail update by Account1 
	
	Route::get('freightdata/update-freight', [App\Http\Controllers\Admin\BilldataController::class, 'bill_data_freight_index'])->name('freightdata');
	
	Route::post('freightdata/update', [App\Http\Controllers\Admin\BilldataController::class, 'updateMultiple'])->name('freightdata.updateMultiple');
	
	//file upload & delete for freight invoice, pod and approval after entering freight detail
	Route::post('freight/upload-file', [App\Http\Controllers\Admin\BilldataController::class, 'upload'])->name('file.upload');
	Route::post('freight/delete-file', [App\Http\Controllers\Admin\BilldataController::class, 'delete'])->name('file.delete');
		
	//Validate Freight Info Data
	Route::get('freightinfo/validate-freight-info', [App\Http\Controllers\Admin\BilldataController::class, 'freight_info_validate_index'])->name('validatefreightdata');
	
	
	// Validate selected rows via AJAX
		Route::post('freight/validate', [App\Http\Controllers\Admin\BilldataController::class, 'validateAjax'])->name('freight.validate');
		Route::post('freight/store', [App\Http\Controllers\Admin\BilldataController::class, 'storeValidatedData'])->name('freight.store');

	//////Edit Returned freight
	Route::post('freight-returned/update', [App\Http\Controllers\Admin\BilldataController::class, 'updateReturnedFreightAjax'])
    ->name('freight.returned.ajax.update');
	
	///////SIte plant Data Route
	
	Route::post('siteplantimport', [App\Http\Controllers\Admin\SiteplantController::class, 'import'])->name('siteplantexcel.import');
	
	Route::get('/siteplant', [App\Http\Controllers\Admin\SiteplantController::class, 'index'])->name('siteplantdata');
	//manual upload data
	Route::get('/siteplant/manual-upload', [App\Http\Controllers\Admin\SiteplantController::class, 'manualupload'])->name('siteplantmanualupload');
	
	Route::post('siteplant/save_manual_upload', [App\Http\Controllers\Admin\SiteplantController::class, 'save_manual_siteplantdata']);
	//////////
	Route::get('siteplant/editsiteplantdata/{id}', [App\Http\Controllers\Admin\SiteplantController::class,'getSiteplantdataDetails'])->name('getSiteplantdataDetails');
	
	Route::post('siteplant/updatesiteplantdata', [App\Http\Controllers\Admin\SiteplantController::class, 'save_siteplantdata']);
	
	Route::get('deletesiteplantdata/{id}', 'App\Http\Controllers\Admin\SiteplantController@DeleteSiteplantData')->name('DeleteSiteplantData');
	
	
	///////Rate Master Data Route
	
	Route::post('rateimport', [App\Http\Controllers\Admin\RatedataController::class, 'import'])->name('ratemasterexcel.import');
	
	//Bill Data Upload Route
	Route::get('/ratedata', [App\Http\Controllers\Admin\RatedataController::class, 'index'])->name('ratedata');
	//manual upload data
	Route::get('/ratedata/manual-upload', [App\Http\Controllers\Admin\RatedataController::class, 'manualupload'])->name('ratedatamanualupload');
	
	Route::post('ratedata/save_manual_upload', [App\Http\Controllers\Admin\RatedataController::class, 'save_manual_ratedata']);
	//////////
	Route::get('ratedata/editratedata/{id}', [App\Http\Controllers\Admin\RatedataController::class,'getRatedataDetails'])->name('getRatedataDetails');
	
	Route::post('ratedata/updateratedata', [App\Http\Controllers\Admin\RatedataController::class, 'save_ratedata']);
	
	Route::get('deleteratedata/{id}', 'App\Http\Controllers\Admin\RatedataController@DeleteRateData')->name('DeleteRateData');
	
	
	//Manage Vendor data
	Route::get('/vendor', [App\Http\Controllers\Admin\VendorController::class, 'index'])->name('vendor');	
	
	Route::get('vendor/addvendor', [App\Http\Controllers\Admin\VendorController::class, 'AddVendor'])->name('vendor.add');
	Route::post('vendor/insertvendor', [App\Http\Controllers\Admin\VendorController::class, 'save_vendordata'])->name('store.vendor');
	
	
	Route::get('vendor/editvendor/{id}', [App\Http\Controllers\Admin\VendorController::class,'getVendorDetails'])->name('getVendorDetails');
	Route::post('vendor/updatevendor', [App\Http\Controllers\Admin\VendorController::class, 'save_vendordata']);	
	
	
	Route::get('deletevendor/{id}', 'App\Http\Controllers\Admin\VendorController@DeleteVendorData')->name('DeleteVendorData');
	
	
	// Vendor Address routes
	Route::get('vendor/{vendor}/addresses', [App\Http\Controllers\Admin\VendorAddressController::class, 'index'])->name('vendor-addresses.index');
	Route::get('vendors/{vendor}/addresses/create', [App\Http\Controllers\Admin\VendorAddressController::class, 'create'])->name('vendor-addresses.create');
	Route::post('vendors/{vendor}/addresses', [App\Http\Controllers\Admin\VendorAddressController::class, 'store'])->name('vendor-addresses.store');
	Route::get('vendors/{vendor}/addresses/{id}/edit', [App\Http\Controllers\Admin\VendorAddressController::class, 'edit'])->name('vendor-addresses.edit');
	Route::put('vendors/{vendor}/addresses/{id}', [App\Http\Controllers\Admin\VendorAddressController::class, 'update'])->name('vendor-addresses.update');
	Route::delete('vendors/{vendor}/addresses/{id}', [App\Http\Controllers\Admin\VendorAddressController::class, 'destroy'])->name('vendor-addresses.destroy');

// Vendor Bank Account routes
	Route::get('vendor/{vendor}/bank-accounts', [App\Http\Controllers\Admin\VendorBankAccountController::class, 'index'])->name('vendor-bank-accounts.index');
	Route::get('vendors/{vendor}/bank-accounts/create', [App\Http\Controllers\Admin\VendorBankAccountController::class, 'create'])->name('vendor-bank-accounts.create');
	Route::post('vendors/{vendor}/bank-accounts', [App\Http\Controllers\Admin\VendorBankAccountController::class, 'store'])->name('vendor-bank-accounts.store');
	Route::get('vendors/{vendor}/bank-accounts/{id}/edit', [App\Http\Controllers\Admin\VendorBankAccountController::class, 'edit'])->name('vendor-bank-accounts.edit');
	Route::put('vendors/{vendor}/bank-accounts/{id}', [App\Http\Controllers\Admin\VendorBankAccountController::class, 'update'])->name('vendor-bank-accounts.update');
	Route::delete('vendors/{vendor}/bank-accounts/{id}', [App\Http\Controllers\Admin\VendorBankAccountController::class, 'destroy'])->name('vendor-bank-accounts.destroy');
	
	////TruckMasterController
	Route::resource('truck_master', App\Http\Controllers\Admin\TruckMasterController::class);
	
	
	//Appointment Data Upload Route
	
	Route::post('/appointmentimport', [App\Http\Controllers\Admin\AppointmentController::class, 'import'])->name('appointmentexcel.import');
	
	Route::get('/appointment', [App\Http\Controllers\Admin\AppointmentController::class, 'index'])->name('appointment');
	
	Route::get('/appointmentdata/appointment-history', [App\Http\Controllers\Admin\AppointmentController::class, 'appointmentdatalist'])->name('appointmentdatalist');
	
	//manual upload data
	Route::get('/appointment/manual-upload', [App\Http\Controllers\Admin\AppointmentController::class, 'manualupload'])->name('appointmentmanualupload');
	
	Route::post('/appointment/save_appointment_manual_upload', [App\Http\Controllers\Admin\AppointmentController::class, 'save_manual_appointmentdata'])->name('save_manual');
	//////////
	Route::get('appointment/editappointmentdata/{id}', [App\Http\Controllers\Admin\AppointmentController::class,'getAppointmentdataDetails'])->name('getAppointmentdataDetails');
	
	Route::post('appointment/updateappointmentdata', [App\Http\Controllers\Admin\AppointmentController::class, 'save_appointmentdata']);
	
	Route::get('deleteappointmentdata/{id}', 'App\Http\Controllers\Admin\AppointmentController@DeleteAppointmentData')->name('DeleteAppointmentData');
	
	///Step 2 Appointment
	Route::get('appointmentdata/update-appointment-data', [App\Http\Controllers\Admin\AppointmentController::class, 'appointment_update_by_vendor_branch'])->name('appointmentdata');
	
	Route::post('appointmentdata/update', [App\Http\Controllers\Admin\AppointmentController::class, 'updateMultipleAppointment'])->name('appointmentdata.updateMultipleAppointment');
	
	Route::get('appointmentdata/assign-appointment-data', [App\Http\Controllers\Admin\AppointmentController::class, 'appointment_send_ho_consignee'])->name('appointment_send_ho_consignee');
	
	Route::post('/appointments/update-selection', [App\Http\Controllers\Admin\AppointmentController::class, 'updateSelection'])->name('appointments.updateSelection');
	Route::post('/appointments/check-selection', [App\Http\Controllers\Admin\AppointmentController::class, 'checkSelection'])->name('appointments.checkSelection');
	
	//Appointment HO TO CONSIGNEE
	
	Route::get('/appointments/assign', [App\Http\Controllers\Admin\AppointmentController::class, 'HoTOConsignee'])->name('appointments.assign');
	
	Route::post('/appointments/assign', [App\Http\Controllers\Admin\AppointmentController::class, 'assignHoToConsignee'])->name('appointments.assign.submit');
	
	//Consignee accept, reject, reschedule
	
	Route::get('/appointments/accept', [App\Http\Controllers\Admin\AppointmentController::class, 'Appointment_accept_reject_reschedule'])->name('appointments.accept');
	
	Route::post('/appointments/update-status', [App\Http\Controllers\Admin\AppointmentController::class, 'updateStatus'])->name('appointments.updateStatus.acceptreject');
	
	
	///Appointment Site operator / Driver
	Route::get('appointments/delivery-status', [App\Http\Controllers\Admin\AppointmentController::class, 'Appointment_delivery_status'])->name('appointments.deliverystatus');
	
	Route::post('appointments/{id}/update-delivery-status', [App\Http\Controllers\Admin\AppointmentController::class, 'updateDeliveryStatus'])->name('appointments.updateStatus');
	
	// Delivery OTP update by driver
	Route::post('appointments/update-delivery-otp', 
    [App\Http\Controllers\Admin\AppointmentController::class, 'updateDeliveryOtp']
	)->name('update.delivery.otp');
	
	Route::get('appointments/{id}/history-ajax', [App\Http\Controllers\Admin\AppointmentController::class, 'ajaxHistory'])->name('appointments.ajaxHistory');
	
	///Appointment POD file(Front & Back) upload 
	Route::get('appointments/podfiles', [App\Http\Controllers\Admin\AppointmentController::class, 'Appointment_pod_files'])->name('appointments.podfile');
	
	Route::post('appointments/upload-pod', [App\Http\Controllers\Admin\AppointmentController::class, 'uploadPODFile'])->name('pod.upload');


	//Mapping Data Upload Route
	Route::get('/mapping', [App\Http\Controllers\Admin\MappingController::class, 'index'])->name('mapping');
	//manual upload data
	Route::get('/mapping/manual-upload', [App\Http\Controllers\Admin\MappingController::class, 'manualupload'])->name('mappingmanualupload');

	Route::post('mapping/save_manual_upload', [App\Http\Controllers\Admin\MappingController::class, 'save_manual_mappingdata']);
	//////////
	Route::get('mapping/editmappingdata/{id}', [App\Http\Controllers\Admin\MappingController::class,'getMappingdataDetails'])->name('getmappingdataDetails');
	
	Route::post('mapping/updatemappingdata', [App\Http\Controllers\Admin\MappingController::class, 'save_mappingdata']);
	
	Route::get('deletemapping/{id}', [App\Http\Controllers\Admin\MappingController::class, 'DeleteMappingData'])->name('DeleteMappingData');
	Route::get('/mapping/mappind-data-list', [App\Http\Controllers\Admin\MappingController::class, 'mappingdatalist'])->name('mappingdatalist');
	
	//Employee Code mapping to be used for pre Appointment employee_code_mapping
	
	Route::get('employeemapping', [App\Http\Controllers\Admin\EmployeeMappingController::class, 'index'])->name('employeemapping');
	//manual upload data
	Route::get('employeemapping/manual-upload', [App\Http\Controllers\Admin\EmployeeMappingController::class, 'manualupload'])->name('employeemappingmanualupload');

	Route::post('employeemapping/save_manual_upload', [App\Http\Controllers\Admin\EmployeeMappingController::class, 'save_employee_mappingdata']);
	//////////
	Route::get('employeemapping/editployee-mappingdata/{id}', [App\Http\Controllers\Admin\EmployeeMappingController::class,'getEmployeeMappingdataDetails'])->name('getemployeemappingdataDetails');
	
	Route::post('employeemapping/update', [App\Http\Controllers\Admin\EmployeeMappingController::class, 'save_employeemappingdata']);
	
	Route::get('employeemapping/delete/{id}', [App\Http\Controllers\Admin\EmployeeMappingController::class, 'DeleteEmployeeMappingData'])->name('DeleteemployeeMappingData');
	Route::get('employeemapping/list', [App\Http\Controllers\Admin\EmployeeMappingController::class, 'employeemappingdatalist'])->name('employeemappingdatalist');
	
	
	//End of employee code mapping
	
		//Consignee duration setup Data Upload Route
		//App\Http\Controllers\Admin\ConsigneeReturnDurationController
		
	Route::get('consignee-return-duration/data-list', [App\Http\Controllers\Admin\ConsigneeReturnDurationController::class, 'Returndatalist']);
	
	Route::get('/consignee-return-duration', [App\Http\Controllers\Admin\ConsigneeReturnDurationController::class, 'index'])->name('return-duration');
	//manual upload data
	Route::get('/consignee-return-duration/manual-upload', [App\Http\Controllers\Admin\ConsigneeReturnDurationController::class, 'manualupload'])->name('returnmanualupload');

	Route::post('consignee-return-duration/save_manual_upload', [App\Http\Controllers\Admin\ConsigneeReturnDurationController::class, 'save_manual_returndata']);
	//////////
	Route::get('consignee-return-duration/editdata/{id}', [App\Http\Controllers\Admin\ConsigneeReturnDurationController::class,'getreturndataDetails'])->name('getreturndataDetails');
	
	Route::post('consignee-return-duration/updatedata', [App\Http\Controllers\Admin\ConsigneeReturnDurationController::class, 'save_data']);
	
	Route::get('consignee-return-duration/{id}', [App\Http\Controllers\Admin\ConsigneeReturnDurationController::class, 'DeleteReturnData'])->name('DeleteReturnDurationData');
	
	
	//////////////////////////////////SPOT by
	
	////SPOT BY data upload Route
	Route::get('/spotby', [App\Http\Controllers\Admin\SpotbyController::class, 'index'])->name('spotby');
	Route::post('/spotbyimport', [App\Http\Controllers\Admin\SpotbyController::class, 'import'])->name('spotbyimport');
	
	
	Route::get('/spotby/history', [App\Http\Controllers\Admin\SpotbyController::class, 'spotbylist'])->name('spotbylist');
	
	//manual upload data
	Route::get('/spotby/manual-upload', [App\Http\Controllers\Admin\SpotbyController::class, 'manualupload'])->name('spotbymanualupload');
	
	Route::post('spotby/save_manual_upload', [App\Http\Controllers\Admin\SpotbyController::class, 'save_manual_spotby']);
	//////////
	Route::get('spotby/editspotby/{id}', [App\Http\Controllers\Admin\SpotbyController::class,'getspotbyDetails'])->name('getspotbyDetails');
	
	Route::post('spotby/updatespotby', [App\Http\Controllers\Admin\SpotbyController::class, 'save_spotby']);
	
	Route::get('deletespotby/{id}', 'App\Http\Controllers\Admin\SpotbyController@Deletespotby')->name('Deletespotby');
	////////////////////
	//Route::post('/spotby/vendors/save', [App\Http\Controllers\Admin\SpotbyVendorController::class, 'store'])->name('spotby.vendors.store');
	
	///Spot by Associate vendor 
	
	Route::get('spotby/selectvendor', [App\Http\Controllers\Admin\SpotbyController::class, 'spotbyselectvendor'])->name('selectvendor');
	
	Route::post('spotby/vendors/bulk-save', [App\Http\Controllers\Admin\SpotbyVendorController::class, 'bulkStore'])->name('spotby.vendors.bulkStore');

// Vendor side ROUND 1 - USER1 ROUND 1 Seller
    Route::get('spotbuy/vendor/quotes', [App\Http\Controllers\Admin\SpotbyController::class, 'vendorQuote'])->name('vendor.quotes.index');
   
	Route::post('spotbuy/vendor/quotes/save-all', [App\Http\Controllers\Admin\SpotbyController::class, 'storeAll'])->name('vendor.quotes.saveAll');
	
	////ROUND 2 - USER1 ROUND 2 Seller
	
	Route::get('/spotbuy/vendor/quotesround2', [App\Http\Controllers\Admin\SpotbyController::class, 'vendorQuoteRound2'])->name('vendor.quotes.round2');
   
	Route::post('/spotbuy/vendor/quotes/save-all-round2', [App\Http\Controllers\Admin\SpotbyController::class, 'storeAllRound2'])->name('vendor.quotes.saveAllRound2');
	
	
	///////////////B1 R2 seller Rank and Buyer Quote 
	Route::get('spotbuy/client/quotes', [App\Http\Controllers\Admin\SpotbyController::class, 'buyerB1R2Quote'])->name('buyerB1R2Quote');
	
	Route::post('spotbuy/client-offers/store', [App\Http\Controllers\Admin\SpotbyController::class, 'storeClientOffers'])->name('client.offers.store');
	
	///////////////B1 R3 seller Rank and Buyer Quote 
	Route::get('spotbuy/client/quotes-r3', [App\Http\Controllers\Admin\SpotbyController::class, 'buyerRevisedQuoteB1R3'])->name('buyerB1R3Quote');
	
	Route::post('spotbuy/client-offers/store-r3', [App\Http\Controllers\Admin\SpotbyController::class, 'storeClientOffersB1R3'])->name('client.offers.storer3');
	
	//////B3 ROUND 3 approval

	Route::get('spotbuy/client/approval-r3', [App\Http\Controllers\Admin\SpotbyController::class, 'buyerQuoteRound3Approver'])->name('buyerQuoteRound3Approver');
	
	Route::post('/spotby/bulk-approval', [App\Http\Controllers\Admin\SpotbyController::class, 'bulkApproval'])->name('spotby.bulkApproval');
	
	
	////Tracking data upload Route
	Route::post('/trackingimport', [App\Http\Controllers\Admin\TrackingController::class, 'import'])->name('excel.import.tracking');
	
	//Tracking Data Upload Route
	Route::get('/trackingdata', [App\Http\Controllers\Admin\TrackingController::class, 'index'])->name('trackingdata');
	
	Route::get('/trackingdata/tracking-history', [App\Http\Controllers\Admin\TrackingController::class, 'trackingdatalist'])->name('trackingdatalist');
	
	//Tracking manual upload data
	Route::get('/trackingdata/manual-upload', [App\Http\Controllers\Admin\TrackingController::class, 'manualupload'])->name('manualupload.tracking');
	
	Route::post('trackingdata/save_manual_upload', [App\Http\Controllers\Admin\TrackingController::class, 'save_manual_trackingdata']);
	//////////
	Route::get('trackingdata/edittrackingdata/{id}', [App\Http\Controllers\Admin\TrackingController::class,'getTrackingdataDetails'])->name('getTrackingdataDetails');
	
	Route::post('tracking/updatetrackingdata', [App\Http\Controllers\Admin\TrackingController::class, 'save_trackingdata']);
	
	Route::get('deletetrackingdata/{id}', 'App\Http\Controllers\Admin\TrackingController@DeleteTrackingData')->name('DeleteTrackingData');
	
	//// Step 2 Vendor Update Tracking data
	
	Route::get('trackingdata/update-tracking-data', [App\Http\Controllers\Admin\TrackingController::class, 'manualupload_by_vendor'])->name('vendortrackingdataupdate');
	
	Route::post('trackingdata/update', [App\Http\Controllers\Admin\TrackingController::class, 'save_manual_trackingdata_by_vendor'])->name('trackingdata.updateMultipleTracking');
	
	//// Step 3 consignor/consignee/Vendor Update Tracking data
	
	Route::get('trackingdata/update-tracking-data-by-ven-consign', [App\Http\Controllers\Admin\TrackingController::class, 'update_by_vendor_consign'])->name('update_by_vendor_consign');
	
	Route::post('trackingdata/update-tracking', [App\Http\Controllers\Admin\TrackingController::class, 'save_trackingdata_by_vendor_consign'])->name('trackingdata.updateMultipleTrackingByvenconsign');
	
	
	////Material Master Routes
	Route::get('material', [App\Http\Controllers\Admin\MaterialController::class, 'index'])->name('material');
	
	Route::post('materialimport', [App\Http\Controllers\Admin\MaterialController::class, 'import'])->name('materialexcel.import');
	
	Route::get('materialdata/material-data-list', [App\Http\Controllers\Admin\MaterialController::class, 'materialdatalist'])->name('materialdatalist');
	
	//manual upload data
	Route::get('material/manual-upload', [App\Http\Controllers\Admin\MaterialController::class, 'manualupload'])->name('materialmanualupload');
	
	Route::post('material/save_manual_upload', [App\Http\Controllers\Admin\MaterialController::class, 'save_manual_materialdata'])->name('save_manual_materialdata');
	//////////
	Route::get('material/editmaterialdata/{id}', [App\Http\Controllers\Admin\MaterialController::class,'getMaterialdataDetails'])->name('getMaterialdataDetails');
	
	Route::post('material/updatematerialdata', [App\Http\Controllers\Admin\MaterialController::class, 'save_materialdata']);
	
	Route::get('deletematerialdata/{id}', 'App\Http\Controllers\Admin\MaterialController@DeleteMaterialData')->name('DeleteMaterialData');
	
	//Load Optimizer
	Route::get('loadoptimizer', [App\Http\Controllers\Admin\LoadoptimizerController::class, 'index'])->name('lop');
	
	Route::post('loadoptimizer/excelimport', [App\Http\Controllers\Admin\LoadoptimizerController::class, 'import'])->name('lopexcel.import');
	
	Route::get('loadoptimizer/manual-upload', [App\Http\Controllers\Admin\LoadoptimizerController::class, 'manualupload'])->name('lopmanualupload');
	
	Route::post('loadoptimizer/save_manual_upload', [App\Http\Controllers\Admin\LoadoptimizerController::class, 'save_manual_data'])->name('save_lop_manual');
	
	Route::post('load-optimizer/fetch-row', [App\Http\Controllers\Admin\LoadoptimizerController::class, 'fetchRowData'])
    ->name('loadoptimizer.fetchRow');
	
	//load summary
	Route::get('loadoptimizer/load-summary', [App\Http\Controllers\Admin\LoadoptimizerController::class, 'loadSummary'])->name('loadSummary');
	//qualified load summary qualifiedloadsummary
	Route::get('loadoptimizer/qualified-load-summary', [App\Http\Controllers\Admin\LoadoptimizerController::class, 'qualifiedloadsummary'])->name('qualifiedloadsummary');
	///summary items
	Route::get('load-summary/{ref}/items', [App\Http\Controllers\Admin\LoadoptimizerController::class, 'viewLoadedItems'])->name('load.summary.items');
	
	///summary approval
	Route::post('load-summary/send-approval', [App\Http\Controllers\Admin\LoadoptimizerController::class, 'sendApproval'])->name('sendApproval');
	
	//summary qualification Approved / Rejected
	Route::get('loadoptimizer/load-summary/approval', [App\Http\Controllers\Admin\LoadoptimizerController::class, 'loadSummaryApproval'])->name('loadSummaryApproval');
	
	Route::post(
    'load-summary/{id}/update-status',
    [App\Http\Controllers\Admin\LoadoptimizerController::class, 'updateStatus'])->name('load.summary.ApproveReject');
	
	
	Route::post('load-summary/{ref}/edit', [App\Http\Controllers\Admin\LoadoptimizerController::class, 'edit']);
	
	//get sku description by sku
	Route::get('loadoptimizer/sku/{sku}', 
    [App\Http\Controllers\Admin\LoadoptimizerController::class, 'getSku']);
	
	Route::post('load-summary/calc-util', 
    [App\Http\Controllers\Admin\LoadoptimizerController::class, 'calculateUtil']);
	
	Route::post(
    'load-optimizer/{ref}/update-skus',
    [App\Http\Controllers\Admin\LoadoptimizerController::class, 'updateSummaryItems']
		)->name('load.summary.items.update');
		
	// Delete (soft delete) SKU from Load Optimizer
	Route::post(
    'load-optimizer/item/{id}/delete',
    [App\Http\Controllers\Admin\LoadoptimizerController::class, 'deleteItem']
	)->name('load.optimizer.item.delete');	
	
	
	//Route for Pre Appointment
	
	
	Route::post('preappointmentimport', [App\Http\Controllers\Admin\PreAppointmentController::class, 'import'])->name('preappointmentexcel.import');
	
	Route::get('preappointment', [App\Http\Controllers\Admin\PreAppointmentController::class, 'index'])->name('preappointment');
	
	Route::get('preappointmentdata/appointment-history', [App\Http\Controllers\Admin\PreAppointmentController::class, 'appointmentdatalist'])->name('preappointmentdatalist');
	
	//manual upload data
	Route::get('preappointment/manual-upload', [App\Http\Controllers\Admin\PreAppointmentController::class, 'manualupload'])->name('preappointmentmanualupload');
	
	Route::post('preappointment/save_appointment_manual_upload', [App\Http\Controllers\Admin\PreAppointmentController::class, 'save_manual_appointmentdata'])->name('preappointment_save_manual');
	//////////
	Route::get('preappointment/editappointmentdata/{id}', [App\Http\Controllers\Admin\PreAppointmentController::class,'getAppointmentdataDetails'])->name('getPreAppointmentdataDetails');
	
	Route::post('preappointment/updateappointmentdata', [App\Http\Controllers\Admin\PreAppointmentController::class, 'save_appointmentdata']);
	
	Route::get('delete-preappointment-data/{id}', 'App\Http\Controllers\Admin\PreAppointmentController@DeleteAppointmentData')->name('DeletePreAppointmentData');
	
	Route::get('preappointmentdata/assign-appointment-date', [App\Http\Controllers\Admin\PreAppointmentController::class, 'pre_appointment_request_boards'])->name('pre_appointment_request_boards');
	
	Route::post('/preappointment/update-date', [App\Http\Controllers\Admin\PreAppointmentController::class, 'updateDateTime'])
    ->name('preappointment.updateDateTime');
	
	////update lrno, lr date, appintment status(Supply, reschedule, Close)
	Route::get('preappointment/update-appointment-lr-detail', [App\Http\Controllers\Admin\PreAppointmentController::class, 'appointment_lr_detail_update'])->name('appointmentlr.detail.data.update');
	
	Route::post('preappointmentdata/updatelrdetail', [App\Http\Controllers\Admin\PreAppointmentController::class, 'updateAppointment'])->name('preappointment.update.lr');
	/////Update Delivery status
	
	Route::get('preappointments/delivery-status', [App\Http\Controllers\Admin\PreAppointmentController::class, 'Appointment_delivery_status'])->name('preappointments.deliverystatus');

	Route::post('preappointments/{id}/update-delivery-status', [App\Http\Controllers\Admin\PreAppointmentController::class, 'updateDeliveryStatus'])->name('preappointments.updateStatus');
	
	///PreAppointment POD file(Front & Back) upload 
	Route::get('preappointments/podfiles', [App\Http\Controllers\Admin\PreAppointmentController::class, 'Appointment_pod_files'])->name('preappointments.podfile');
	
	Route::post('preappointments/upload-pod', [App\Http\Controllers\Admin\PreAppointmentController::class, 'uploadPODFile'])->name('preappointmentspod.upload');

	//Load summary Vendor auto allocation
	
	Route::get('loadsummary/load-summary-auto-allocation', [App\Http\Controllers\Admin\LoadoptimizerController::class, 'loadsummary_auto_allocation'])->name('loadsummary_auto_allocation');
	
	Route::post('loadsummary/auto-allocation/process',
    [App\Http\Controllers\Admin\LoadoptimizerController::class, 'processAutoAllocation']
	)->name('loadsummary.auto.process');
	
	//Load summary Allocated vendor edit

	/*Route::get(
    'loadsummary/{id}/edit-vendor',
    [App\Http\Controllers\Admin\LoadoptimizerController::class, 'editVendor']
	)->name('loadsummary.edit.vendor');*/
	
	Route::get('loadsummary/edit-vendor', [App\Http\Controllers\Admin\LoadoptimizerController::class, 'editVendor']);


	Route::post(
		'loadsummary/update-vendor',
		[App\Http\Controllers\Admin\LoadoptimizerController::class, 'updateVendor']
	)->name('loadsummary.update.vendor');	
	
	//Send load summary to Allocated vendor

	/*Route::post(
    'loadsummary/send-to-vendor',
    [App\Http\Controllers\Admin\LoadoptimizerController::class, 'sendToVendor']
		)->name('loadsummary.send.vendor');*/

	Route::get(
		'loadsummary/vendor/loads',
		[App\Http\Controllers\Admin\LoadoptimizerController::class, 'vendorLoads']
	)->name('vendor.loads');
	
		Route::post('loadsummary/alocation/send', 
        [App\Http\Controllers\Admin\VendorAllocationController::class, 'send']
    )->name('vendor.send');

	/* 
		Allocate summary Approver 
	*/
	// Approver
    Route::get('loadsummary/approver/loads', [App\Http\Controllers\Admin\ApproverController::class, 'index'])->name('loadsummary.allocation.send');
	
    Route::post('loadsummary/approver/action', [App\Http\Controllers\Admin\ApproverController::class, 'action'])->name('approver.action');


	/*
		Vendor load Acept / Deploy / Deny(reject)
	*/
	
	Route::post('vendor/load/{id}/accept', [App\Http\Controllers\Admin\VendorLoadController::class, 'accept'])->name('vendor.load.accept');
	
	  Route::post('/vendor/load/deploy', 
        [App\Http\Controllers\Admin\VendorLoadController::class, 'deploy']
    )->name('vendor.load.deploy');
	

    Route::post('/vendor/load/reject', 
        [App\Http\Controllers\Admin\VendorLoadController::class, 'reject']
    )->name('vendor.load.reject');
	
	 Route::get('loadsummary/placementstatus', [App\Http\Controllers\Admin\LoadoptimizerController::class, 'placementStatus'])->name('update.placement.status');
	 
	 Route::post('vendor/load/placement-status',
    [App\Http\Controllers\Admin\VendorLoadController::class, 'submitPlacementStatus'])->name('vendor.placement.status');
	
	Route::get('loadsummary/track-placement-status', [App\Http\Controllers\Admin\LoadoptimizerController::class, 'trackplacementStatus'])->name('track.placement.status');

	Route::get(
    'placement/history/{load}',
    [App\Http\Controllers\Admin\LoadoptimizerController::class, 'placementHistory']
)->name('placement.history');


/////Manual load summary
	Route::get('manual-load-summary/manual-upload', [App\Http\Controllers\Admin\ManualLoadSummaryController::class, 'manualupload'])->name('manualloadsummary');

	Route::post('manual-load-summary/save', [App\Http\Controllers\Admin\ManualLoadSummaryController::class, 'save_manual_data'])->name('save_manual_load_summary');

	Route::post('manual-load-summary/fetch-manual-load-summary-row', [App\Http\Controllers\Admin\ManualLoadSummaryController::class, 'fetchRowData'])
	->name('manualloadsummary.fetchRow');
	
	Route::get('manual-load-summary/list', [App\Http\Controllers\Admin\ManualLoadSummaryController::class, 'ManualsummaryList'])->name('manualloadSummarydatalist');
	
	/////allocate vendor 
	
	Route::post('manual-load-summary/vendor-auto-allocation/process',
    [App\Http\Controllers\Admin\ManualLoadSummaryController::class, 'vendorAutoAllocationProcess']
	)->name('manualloadsummary.vendor.allocation.process');
	
	/////////////////InVOICE GENERATE & list

	Route::get('invoice/create',[App\Http\Controllers\Admin\InvoiceController::class,'create'])
		->name('invoice.create');

	Route::post('invoice/store',[App\Http\Controllers\Admin\InvoiceController::class,'store'])
		->name('invoice.store');

	Route::get('invoice/pdf/{id}',[App\Http\Controllers\Admin\InvoiceController::class,'pdf'])
		->name('invoice.pdf');
	
	Route::get('invoice/list', [App\Http\Controllers\Admin\InvoiceController::class, 'index'])
    ->name('invoice.list');	
	
	// Edit invoice (invoice + invoice_items only)
	Route::get('invoice/{id}/edit', [App\Http\Controllers\Admin\InvoiceController::class, 'edit'])->name('invoice.edit');
	Route::post('invoice/{id}/update', [App\Http\Controllers\Admin\InvoiceController::class, 'update'])->name('invoice.update');

	// Upload annexure from XLS/XLSX
	Route::get('invoice/{id}/upload-annexure', [App\Http\Controllers\Admin\InvoiceController::class, 'uploadAnnexureForm'])->name('invoice.upload_annexure_form');
	Route::post('invoice/{id}/upload-annexure', [App\Http\Controllers\Admin\InvoiceController::class, 'uploadAnnexureStore'])->name('invoice.upload_annexure_store');

	
	/////////////////LR GENERATE & list

	Route::get('lr/create',[App\Http\Controllers\Admin\LrController::class,'create'])
		->name('lr.create');

	Route::post('lr/store',[App\Http\Controllers\Admin\LrController::class,'store'])
		->name('lr.store');
		
	Route::get('lr/{id}/edit', [App\Http\Controllers\Admin\LrController::class,'edit'])->name('lr.edit');
	Route::post('lr/{id}/update', [App\Http\Controllers\Admin\LrController::class,'update'])->name('lr.update');	

	Route::get('lr/pdf/{id}',[App\Http\Controllers\Admin\LrController::class,'pdf'])
		->name('lr.pdf');
	
	Route::get('lr/list', [App\Http\Controllers\Admin\LrController::class, 'index'])
    ->name('lr.list');	
	
	
	/*----------------------------------------------------------
	 |						DIGI WIM                           |
	------------------------------------------------------------*/
	Route::get('digiwim', [App\Http\Controllers\Admin\DigiWimController::class, 'index'])->name('digiWim');
	
	Route::post('digiwimimport', [App\Http\Controllers\Admin\DigiWimController::class, 'import'])->name('digiwim.import');
	
	Route::get('digiwim/data-list', [App\Http\Controllers\Admin\DigiWimController::class, 'digiwimdatalist'])->name('digiwimdatalist');
	
	//manual upload data
	Route::get('digiwim/manual-upload', [App\Http\Controllers\Admin\DigiWimController::class, 'manualupload'])->name('digiwimmanualupload');
	
	Route::post('digiwim/fetch-row', [App\Http\Controllers\Admin\DigiWimController::class, 'fetchRowData'])
    ->name('digiwim.fetchRow');
	
	Route::post('digiwim/save_manual_upload', [App\Http\Controllers\Admin\DigiWimController::class, 'save_manual_data'])->name('save_manual_digiwimdata');
	//////////
	Route::get('digiwim/editdigiwimdata/{id}', [App\Http\Controllers\Admin\DigiWimController::class,'getDigiwimldataDetails'])->name('getDigiwimdataDetails');
	
	Route::post('digiwim/updatedigiwimdata', [App\Http\Controllers\Admin\DigiWimController::class, 'save_digiwimdata']);
	
	Route::get('deletematerialdata/{id}', 'App\Http\Controllers\Admin\DigiWimController@DeleteDigiwimData')->name('DeleteDigiwimData');
	
	/////Unloading
	
	Route::get('digiwim/unloading-operation/create', [App\Http\Controllers\Admin\DigiWimController::class, 'createOperation'])
    ->name('digiwim.operation.create');

	Route::post('digiwim/operation/store-header', [App\Http\Controllers\Admin\DigiWimController::class, 'storeOperationHeader'])
		->name('digiwim.operation.storeHeader');

	/*Route::post('digiwim/operation/store-item', [App\Http\Controllers\Admin\DigiWimController::class, 'storeOperationItem'])
		->name('digiwim.operation.storeItem');
	*/
	Route::get('digiwim/operation/list', [App\Http\Controllers\Admin\DigiWimController::class, 'operationList'])
		->name('digiwim.operation.list');

	Route::get('digiwim/operation/pdf/{id}', [App\Http\Controllers\Admin\DigiWimController::class, 'operationPdf'])
		->name('digiwim.operation.pdf');
		
	Route::post('digiwim/operation/store-item', [App\Http\Controllers\Admin\DigiWimController::class, 'storeOperationItem'])
    ->name('digiwim.operation.storeItem');
	
	Route::get(
    'digiwim/operation/materials/{id}',
    [App\Http\Controllers\Admin\DigiWimController::class, 'viewMaterials']
	)->name('digiwim.operation.materials');
	
	
	
	/*----------------------------------------------------------
	 |						DIGI WIM PRELOADING                          |
	------------------------------------------------------------*/
	Route::get('digiwim/preloading', [App\Http\Controllers\Admin\DigiwimPreloadingController::class, 'index'])->name('digiWimPreloading');
	
	Route::post('digiwim/preloading/import', [App\Http\Controllers\Admin\DigiwimPreloadingController::class, 'import'])->name('digiwim_preloading.import');
	
	Route::get('digiwim/preloading/data-list', [App\Http\Controllers\Admin\DigiwimPreloadingController::class, 'digiwimpreloadingdatalist'])->name('digiwimpreloadingdatalist');
	
	//manual upload data
	Route::get('digiwim/preloading/manual-upload', [App\Http\Controllers\Admin\DigiwimPreloadingController::class, 'manualupload'])->name('digiwimpreloadingmanualupload');
	
	Route::post('digiwim/preloading/fetch-row', [App\Http\Controllers\Admin\DigiwimPreloadingController::class, 'fetchRowData'])
    ->name('digiwim.preloading.fetchRow');
	
	Route::post('digiwim/preloading/save_manual_upload', [App\Http\Controllers\Admin\DigiwimPreloadingController::class, 'save_manual_data'])->name('save_manual_digiwimpreloadingdata');
	//////////
	Route::get('digiwim/preloading/editdigiwimdata/{id}', [App\Http\Controllers\Admin\DigiwimPreloadingController::class,'getDigiwimldataDetails'])->name('getDigiwimpreloadingdataDetails');
	
	Route::post('digiwim/preloading/updatedigiwimdata', [App\Http\Controllers\Admin\DigiwimPreloadingController::class, 'save_digiwimdata']);
	
	Route::get('digiwim/preloading/deletematerialdata/{id}', 'App\Http\Controllers\Admin\DigiwimPreloadingController@DeleteDigiwimData')->name('DeleteDigiwimPreloadingData');
	
	/////Unloading
	
	Route::get('digiwim/preloading/unloading-operation/create', [App\Http\Controllers\Admin\DigiwimPreloadingController::class, 'createOperation'])
    ->name('digiwimpreloading.operation.create');

	Route::post('digiwim/preloading/operation/store-header', [App\Http\Controllers\Admin\DigiwimPreloadingController::class, 'storeOperationHeader'])
		->name('digiwimpreloading.operation.storeHeader');

	Route::get('digiwim/preloading/operation/list', [App\Http\Controllers\Admin\DigiwimPreloadingController::class, 'operationList'])
		->name('digiwimpreloading.operation.list');

	Route::get('digiwim/preloading/operation/pdf/{id}', [App\Http\Controllers\Admin\DigiwimPreloadingController::class, 'operationPdf'])
		->name('digiwimpreloading.operation.pdf');
		
	Route::post('digiwim/preloading/operation/store-item', [App\Http\Controllers\Admin\DigiwimPreloadingController::class, 'storeOperationItem'])
    ->name('digiwimpreloading.operation.storeItem');
	
	Route::get(
    'digiwim/preloading/operation/materials/{id}',
    [App\Http\Controllers\Admin\DigiwimPreloadingController::class, 'viewMaterials']
	)->name('digiwimpreloading.operation.materials');
	
	///LEDGER & Inventory
	Route::get('digiwim/ledger', [App\Http\Controllers\Admin\DigiwimLedgerController::class, 'index'])
    ->name('digiwim.ledger');
	
	Route::get('digiwim/inventory',[App\Http\Controllers\Admin\DigiwimInventoryController::class, 'index'])->name('digiwim.inventory');
	
	//EGR
   
	
	Route::resource('digiwim-egr', App\Http\Controllers\Admin\DigiwimEgrController::class);
	Route::resource('digiwim-egp', App\Http\Controllers\Admin\DigiwimEgpController::class);
	
	/* DigiWim Goods PO Upload */
	
		Route::get(
            'digiwim-goods-po/manual-upload',
            [App\Http\Controllers\Admin\DigiwimGoodsPoUploadController::class,'manualUpload']
        )->name('digiwim-goods-po.manual-upload');

        Route::post(
            'digiwim-goods-po/save-manual-upload',
            [App\Http\Controllers\Admin\DigiwimGoodsPoUploadController::class,'saveManualUpload']
        )->name('digiwim-goods-po.save');
		
		/********AJAX Data Fetch **************/
		Route::post(
            'digiwim-goods-po/fetch-row',
            [App\Http\Controllers\Admin\DigiwimGoodsPoUploadController::class,'fetchRow']
        )->name('digiwim-goods-po.fetch-row');
		
		Route::get(
            'digiwim-goods-po',
            [App\Http\Controllers\Admin\DigiwimGoodsPoUploadController::class,'index']
        )->name('digiwim-goods-po.index');
		
		Route::get(
            'digiwim-goods-po/{id}/edit',
            [App\Http\Controllers\Admin\DigiwimGoodsPoUploadController::class,'edit']
        )->name('digiwim-goods-po.edit');
		
		Route::post(
            'digiwim-goods-po/{id}/update',
            [App\Http\Controllers\Admin\DigiwimGoodsPoUploadController::class,'update']
        )->name('digiwim-goods-po.update');
		
		Route::delete(
            'digiwim-goods-po/{id}',
            [App\Http\Controllers\Admin\DigiwimGoodsPoUploadController::class,'destroy']
        )->name('digiwim-goods-po.destroy');
		
		 Route::get(
            'digiwim-goods-po/excel-upload',
            [App\Http\Controllers\Admin\DigiwimGoodsPoUploadController::class,'excelUpload']
        )->name('digiwim-goods-po.excel-upload');

        Route::post(
            'digiwim-goods-po/import-excel',
            [App\Http\Controllers\Admin\DigiwimGoodsPoUploadController::class,'importExcel']
        )->name('digiwim-goods-po.import');
	

	/* DigiWim Inventory IRA */

	/* Pending IRA */

	Route::get(
		'digiwim-inventory-ira',
		[App\Http\Controllers\Admin\DigiwimInventoryIraController::class, 'index']
	)->name('digiwim-inventory-ira.index');


	/* Add IRA Activity (AJAX) */

	Route::post(
		'digiwim-inventory-ira/add-activity',
		[App\Http\Controllers\Admin\DigiwimInventoryIraController::class, 'addActivity']
	)->name('digiwim-inventory-ira.add-activity');


	/* End IRA (AJAX) */

	Route::post(
		'digiwim-inventory-ira/end-activity',
		[App\Http\Controllers\Admin\DigiwimInventoryIraController::class, 'endActivity']
	)->name('digiwim-inventory-ira.end-activity');


	/* IRA History */

	Route::get(
		'digiwim-inventory-ira/history',
		[App\Http\Controllers\Admin\DigiwimInventoryIraController::class, 'history']
	)->name('digiwim-inventory-ira.history');


	/* View IRA Activities (AJAX) */

	Route::get(
		'digiwim-inventory-ira/view-activities/{id}',
		[App\Http\Controllers\Admin\DigiwimInventoryIraController::class, 'viewActivities']
	)
	->whereNumber('id')
	->name('digiwim-inventory-ira.view-activities');


	/* Inventory Book Vs IRA */

	Route::get(
		'digiwim-inventory-ira/inventory-book',
		[App\Http\Controllers\Admin\DigiwimInventoryIraController::class, 'inventoryBook']
	)->name('digiwim-inventory-ira.inventory-book');


	/* Inventory Detail Report */

	Route::get(
		'digiwim-inventory-ira/report/{inventoryKey}',
		[App\Http\Controllers\Admin\DigiwimInventoryIraController::class, 'report']
	)
	->where('inventoryKey', '[A-Fa-f0-9]{64}')
	->name('digiwim-inventory-ira.report');
	
	///Load Box count from V placement screen
	
	Route::post('load-box-count/store', [App\Http\Controllers\Admin\LoadBoxCountController::class, 'store'])
    ->name('load.boxcount.store');

	Route::get('load-box-count/list/{load_id}/{source_type}', [App\Http\Controllers\Admin\LoadBoxCountController::class, 'list'])
		->name('load.boxcount.list');

	Route::delete('load-box-count/delete/{id}', [App\Http\Controllers\Admin\LoadBoxCountController::class, 'destroy'])
		->name('load.boxcount.delete');
	
	Route::post('load-box-count/update-count/{id}', [App\Http\Controllers\Admin\LoadBoxCountController::class, 'updateCount'])
    ->name('load.boxcount.updateCount');

	Route::post('load-box-count/update-remark/{id}', [App\Http\Controllers\Admin\LoadBoxCountController::class, 'updateRemark'])
    ->name('load.boxcount.updateRemark');	
		
		/*Report for Freight Bill Processing Dashboard	*/
	Route::get('freight-bill-processing-dashboard',[App\Http\Controllers\Admin\FreightBillProcessingReportController::class,'index'])
	->name('freight-bill-processing.index');

	Route::get('freight-bill-processing-dashboard/export-xls',[FreightBillProcessingReportController::class,'exportXls'])->name('freight-bill-processing.export-xls');	

});

