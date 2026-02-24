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

//Route::get('/admin',[LoginController::class,'showAdminLoginForm'])->name('admin.login-view');
//Route::post('/admin',[LoginController::class,'adminLogin'])->name('admin.login');
//

//
Route::get('/admin/register',[RegisterController::class,'showAdminRegisterForm'])->name('admin.register-view');
Route::post('/admin/register',[RegisterController::class,'createAdmin'])->name('admin.register');

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');



// user and role Management
Route::group(['as' => 'admin.', 'prefix' => 'admin/'], function () {

    Route::resource('roles', RoleController::class);
    Route::resource('users', UserController::class);
	
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

	
	
});

