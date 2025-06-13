<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\SystemController;
use App\Http\Controllers\SessionsController;
use App\Http\Controllers\MasterController;
use App\Http\Controllers\DokumenController;
use App\Http\Controllers\lcl\RegisterController;
use App\Http\Controllers\lcl\ManifestController;
use App\Http\Controllers\lcl\StrippingController;
use App\Http\Controllers\lcl\GateInController;
use App\Http\Controllers\lcl\DeliveryController;
use App\Http\Controllers\lcl\RackingController;
use App\Http\Controllers\BarcodeAutoGateController;
use App\Http\Controllers\PhotoController;
use App\Http\Controllers\PlacementContainerController;
use App\Http\Controllers\QrController;
use App\Http\Controllers\EasyGoController;
use App\Http\Controllers\ReportController;

use App\Http\Controllers\TestJsonController;

// Android
use App\Http\Controllers\android\AndroidHomeController;
use App\Http\Controllers\android\LclController;
use App\Http\Controllers\android\AndroidGateController;
use App\Http\Controllers\android\FCLAndroidController;

// invoice
use App\Http\Controllers\invoice\DashboardInvoiceController;
use App\Http\Controllers\invoice\MasterInvoiceController;
use App\Http\Controllers\invoice\FormController;
use App\Http\Controllers\invoice\InvoiceController;
use App\Http\Controllers\invoice\ReportInvoiceController;

// Perpanjangan
use App\Http\Controllers\invoice\FormPerpanjanganController;
use App\Http\Controllers\invoice\InvoicePerpanjanganController;

//BeaCukai 
use App\Http\Controllers\beaCukai\BeaCukaiController;
use App\Http\Controllers\beaCukai\BeacukaiP2Controller;

// Pengiriman
use App\Http\Controllers\PengirimanController;
use App\Http\Controllers\pengiriman\CoariCodecoController;


// FCL
 use App\Http\Controllers\FCL\RegisterFCLController;
 use App\Http\Controllers\FCL\Realisasi\GateInFCLCotroller;
 use App\Http\Controllers\FCL\Delivery\DeliveryFCLController;
 use App\Http\Controllers\FCL\ContainerDokController;
 use App\Http\Controllers\FCL\ReportFCLController;
 
 //  Invoice Fcl
 use App\Http\Controllers\invoiceFCL\InvoiceFCLMainController;
 use App\Http\Controllers\invoiceFCL\MasterTarifFCLController;
 use App\Http\Controllers\invoiceFCL\FormFCLController;
 use App\Http\Controllers\invoiceFCL\BackendInvoiceController;
 use App\Http\Controllers\invoiceFCL\Behandle\InvoiceBehandleFCLController;
 

//  CodecoCoari
use App\Http\Controllers\DokumenBc\CoariController;
use App\Http\Controllers\DokumenBc\CodecoController;

// Cfs
use App\Http\Controllers\CFS\CfsDefaultController;
use App\Http\Controllers\CFS\InvoiceCSFController;

// User Profile
use App\Http\Controllers\Auth\ProfileController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::middleware(['auth', 'check.segel'])->group(function () {
    Route::put('/tcontainer_fcl/{tcontainer_fcl}', [TcontainerFclController::class, 'update']);
});




Route::get('/', function () {
    return redirect('/login');
});
// routes/web.php
Route::post('/unset-session/{key}', 'SessionController@unsetSession')->name('unset-session');
Auth::routes();

Route::middleware(['auth', 'RedirectIfAuthenticated'])->get('/', [App\Http\Controllers\HomeController::class, 'index']);
Route::middleware(['auth', 'role:admin|adminLCL|tpsLCL'])->get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
Route::middleware(['auth', 'role:admin|adminFCL|tpsFCL'])->get('/dashboard-fcl', [App\Http\Controllers\HomeController::class, 'indexFCL'])->name('dashboard.fcl');
Route::middleware(['auth', 'role:admin|adminLCL|invoiceLCL'])->get('/dashboard-invoiceLCL', [App\Http\Controllers\HomeController::class, 'indexInvoiceLCL'])->name('dashboard.invoiceLCL');
Route::middleware(['auth', 'role:admin|adminFCL|invoiceFCL'])->get('/dashboard-invoiceFCL', [App\Http\Controllers\HomeController::class, 'indexInvoiceLFCL'])->name('dashboard.invoiceFCL');
Route::get('/testJson', [TestJsonController::class, 'test']);

Route::controller(SystemController::class)->group(function () {
    // user
    Route::get('/user/index-user', 'IndexUser')->name('user.index');
    Route::middleware('permission:editUser')->get('/user/edit-{id?}', 'editUser')->name('user.edit');
    Route::middleware('permission:createUser')->post('/user/create', 'createUser')->name('user.create');
    Route::middleware('permission:editUser')->post('/user/update', 'updateUser')->name('user.update');
    Route::middleware('permission:deleteUser')->delete('/user/delete{id?}', 'deleteUser')->name('user.delete');
    // role
    Route::middleware('permission:role')->group(function(){
        Route::get('/role/index-role', 'IndexRole')->name('role.index');
        Route::get('/role/edit-{id?}', 'editRole')->name('role.edit');
        Route::post('/role/create', 'createRole')->name('role.create');
        Route::post('/role/update', 'updateRole')->name('role.update');
        Route::delete('/role/delete{id?}', 'deleteRole')->name('role.delete');
    });

    // Route::middleware('permission:permission')->group(function(){
        Route::get('/permisson/index', 'indexPermisson');
        Route::post('/permisson/post', 'postPermisson')->name('system.permission.post');
    
        // AssingPermission
        Route::get('/user/permission-index/{id?}', 'assignPermission');
        Route::post('/user/permission-post/{id?}', 'assignPermissionPost');
    // });
    // Permisson
});

Route::middleware('permission:dataMaster')->controller(MasterController::class)->group(function (){
    // customer
    Route::get('/master/customer', 'CustomerIndex')->name('master.customer.index');
    Route::post('/master/customer-excel', 'CustomerExcel')->name('master.customer.excel');
    Route::post('/master/customer-post', 'CustomerPost')->name('master.customer.post');
    Route::get('/master/customer-edit{id?}', 'CustomerEdit')->name('master.customer.edit');
    Route::post('/master/customer-update', 'CustomerUpdate')->name('master.customer.update');
    Route::delete('/master/customer-delete{id?}', 'CustomerDelete')->name('master.customer.delete');

    // Consolidator
    Route::get('/master/consolidator', 'consolidatorIndex')->name('master.consolidator.index');
    Route::post('/master/consolidator-excel', 'consolidatorExcel')->name('master.consolidator.excel');
    Route::post('/master/consolidator-post', 'consolidatorPost')->name('master.consolidator.post');
    Route::get('/master/consolidator-edit{id?}', 'consolidatorEdit')->name('master.consolidator.edit');
    Route::post('/master/consolidator-update', 'consolidatorUpdate')->name('master.consolidator.update');
    Route::delete('/master/consolidator-delete{id?}', 'consolidatorDelete')->name('master.consolidator.delete');

    // Eseal
    Route::get('/master/eseal', 'esealIndex')->name('master.eseal.index');
    Route::post('/master/eseal-excel', 'esealExcel')->name('master.eseal.excel');
    Route::post('/master/eseal-post', 'esealPost')->name('master.eseal.post');
    Route::get('/master/eseal-edit{id?}', 'esealEdit')->name('master.eseal.edit');
    Route::post('/master/eseal-update', 'esealUpdate')->name('master.eseal.update');
    Route::delete('/master/eseal-delete{id?}', 'esealDelete')->name('master.eseal.delete');

    // Negara
    Route::get('/master/negara', 'negaraIndex')->name('master.negara.index');
    Route::post('/master/negara-excel', 'negaraExcel')->name('master.negara.excel');
    Route::post('/master/negara-post', 'negaraPost')->name('master.negara.post');
    Route::get('/master/negara-edit{id?}', 'negaraEdit')->name('master.negara.edit');
    Route::post('/master/negara-update', 'negaraUpdate')->name('master.negara.update');
    Route::delete('/master/negara-delete{id?}', 'negaraDelete')->name('master.negara.delete');

    // Packing
    Route::get('/master/packing', 'packingIndex')->name('master.packing.index');
    Route::post('/master/packing-excel', 'packingExcel')->name('master.packing.excel');
    Route::post('/master/packing-post', 'packingPost')->name('master.packing.post');
    Route::get('/master/packing-edit{id?}', 'packingEdit')->name('master.packing.edit');
    Route::post('/master/packing-update', 'packingUpdate')->name('master.packing.update');
    Route::delete('/master/packing-delete{id?}', 'packingDelete')->name('master.packing.delete');

    // Perusahaan
    Route::get('/master/perusahaan', 'perusahaanIndex')->name('master.perusahaan.index');
    Route::post('/master/perusahaan-excel', 'perusahaanExcel')->name('master.perusahaan.excel');
    Route::post('/master/perusahaan-post', 'perusahaanPost')->name('master.perusahaan.post');
    Route::get('/master/perusahaan-edit{id?}', 'perusahaanEdit')->name('master.perusahaan.edit');
    Route::post('/master/perusahaan-update', 'perusahaanUpdate')->name('master.perusahaan.update');
    Route::delete('/master/perusahaan-delete{id?}', 'perusahaanDelete')->name('master.perusahaan.delete');

    // PPJK
    Route::get('/master/ppjk', 'ppjkIndex')->name('master.ppjk.index');
    Route::post('/master/ppjk-excel', 'ppjkExcel')->name('master.ppjk.excel');
    Route::post('/master/ppjk-post', 'ppjkPost')->name('master.ppjk.post');
    Route::get('/master/ppjk-edit{id?}', 'ppjkEdit')->name('master.ppjk.edit');
    Route::post('/master/ppjk-update', 'ppjkUpdate')->name('master.ppjk.update');
    Route::delete('/master/ppjk-delete{id?}', 'ppjkDelete')->name('master.ppjk.delete');

     // Gudang
     Route::get('/master/gudang', 'gudangIndex')->name('master.gudang.index');
     Route::post('/master/gudang-post', 'gudangPost')->name('master.gudang.post');
     Route::get('/master/gudang-edit{id?}', 'gudangEdit')->name('master.gudang.edit');
     Route::post('/master/gudang-update', 'gudangUpdate')->name('master.gudang.update');
     Route::delete('/master/gudang-delete{id?}', 'gudangDelete')->name('master.gudang.delete');

    //  Depo MTY
     Route::get('/master/depoMT', 'depoMTIndex')->name('master.depoMT.index');
     Route::post('/master/depoMT-post', 'depoMTPost')->name('master.depoMT.post');
     Route::get('/master/depoMT-edit{id?}', 'depoMTEdit')->name('master.depoMT.edit');
     Route::post('/master/depoMT-update', 'depoMTUpdate')->name('master.depoMT.update');
     Route::delete('/master/depoMT-delete{id?}', 'depoMTDelete')->name('master.depoMT.delete');

    //  Pelabuhan
     Route::get('/master/pelabuhan', 'pelabuhanIndex')->name('master.pelabuhan.index');
     Route::post('/master/pelabuhan-post', 'pelabuhanPost')->name('master.pelabuhan.post');
     Route::get('/master/pelabuhan-edit{id?}', 'pelabuhanEdit')->name('master.pelabuhan.edit');
     Route::post('/master/pelabuhan-update', 'pelabuhanUpdate')->name('master.pelabuhan.update');
     Route::delete('/master/pelabuhan-delete{id?}', 'pelabuhanDelete')->name('master.pelabuhan.delete');

    //  Lokasi Sandar
     Route::get('/master/lokasiSandar', 'lokasiSandarIndex')->name('master.lokasiSandar.index');
     Route::post('/master/lokasiSandar-post', 'lokasiSandarPost')->name('master.lokasiSandar.post');
     Route::get('/master/lokasiSandar-edit{id?}', 'lokasiSandarEdit')->name('master.lokasiSandar.edit');
     Route::post('/master/lokasiSandar-update', 'lokasiSandarUpdate')->name('master.lokasiSandar.update');
     Route::delete('/master/lokasiSandar-delete{id?}', 'lokasiSandarDelete')->name('master.lokasiSandar.delete');

    //  Kapal
     Route::get('/master/ves', 'vesIndex')->name('master.ves.index');
     Route::post('/master/ves-post', 'vesPost')->name('master.ves.post');
     Route::get('/master/ves-edit{id?}', 'vesEdit')->name('master.ves.edit');
     Route::post('/master/ves-update', 'vesUpdate')->name('master.ves.update');
     Route::delete('/master/ves-delete{id?}', 'vesDelete')->name('master.ves.delete');

    //  Shipping Line
     Route::get('/master/shippingLines', 'shippingLinesIndex')->name('master.shippingLines.index');
     Route::post('/master/shippingLines-post', 'shippingLinesPost')->name('master.shippingLines.post');
     Route::get('/master/shippingLines-edit{id?}', 'shippingLinesEdit')->name('master.shippingLines.edit');
     Route::post('/master/shippingLines-update', 'shippingLinesUpdate')->name('master.shippingLines.update');
     Route::delete('/master/shippingLines-delete{id?}', 'shippingLinesDelete')->name('master.shippingLines.delete');

    //  rack
     Route::get('/master/rack', 'placementManifestIndex')->name('master.rack.index');
     Route::get('/master/placementManifest/createIndex', 'pmCreateIndex');
     Route::post('/master/placementManifest/updateGrid', 'pmUpdateGrid');
     Route::post('/master/placementManifest/kapasitas', 'kapasitasGudang');
     Route::post('/master/placementManifest/barcodeCreate', 'pmCreateBarcode');
     Route::get('/master/placementManifest/barcodeView', 'pmViewBarcode');
     Route::get('/master/placementManifest/tierView', 'tierView');

    //  Yard
     Route::get('/master/yard', 'yardIndex')->name('master.yard.index');
     Route::get('/master/yard-detail-{id?}', 'yardDetail')->name('master.yard.detail');
     Route::post('/master/yard-update', 'yardUpdate')->name('master.yard.update');
     Route::post('/master/yard-reset', 'yardReset')->name('master.yard.reset');
     Route::get('/master/yard-view{id?}', 'yardView')->name('master.yard.view');
     Route::get('/master/yard-rowTierView', 'rowTierView')->name('master.yard.rowTierView');

    //  Keterangan Photo
    Route::get('/master/photo', 'photoIndex');
    Route::get('/master/photoData{id?}', 'photoData');
    Route::post('/master/photo-post', 'photoPost');
    Route::delete('/master/photo-delete{id?}', 'photoDelete');

    // Driver

    Route::prefix('/master/driver')->group(function(){
        Route::get('/index', 'DriverIndex')->name('master.driver.index');
        Route::get('/data', 'DriverData')->name('master.driver.data');
        Route::get('/getData-{id?}', 'DriverGetData')->name('master.driver.getData');
        Route::post('/post', 'DriverPost')->name('master.driver.post');
        Route::post('/delete', 'DriverDelete')->name('master.driver.delete');
    });
});

Route::middleware('permission:tpsOnline')->controller(DokumenController::class)->group(function (){
   
    // PLP
    Route::get('/dokumen/plp', 'plpIndex')->name('dokumen.plp.index');
    Route::get('/dokumen/plpData', 'plpData')->name('dokumen.plp.data');
    Route::get('/dokumen/plp/detail{id?}', 'plpDetail')->name('dokumen.plp.detail');
    Route::get('/dokumen/plp/cont{id?}', 'plpCont')->name('dokumen.plp.detailCont');
    Route::post('/dokumen/plp/updateDetail', 'plpUpdateDetail')->name('dokumen.plp.update.detail');
    Route::post('/dokumen/plp/updateCont', 'plpUpdateCont')->name('dokumen.plp.update.cont');
    Route::post('/dokumen/plp-getData', 'GetResponPLP_onDemand')->name('dokumen.plp.onDemand');
    Route::post('/dokumen/plp-getData-automatic', 'GetResponPLP_Tujuan')->name('dokumen.plp.tujuan');
    Route::post('/dokumen/plp-cetakJob', 'createJob')->name('dokumen.plp.cetakJob');

    // SPJM
    Route::get('/dokumen/spjm', 'spjmIndex')->name('dokumen.spjm.index');
    Route::get('/dokumen/spjmData', 'spjmData')->name('dokumen.spjm.data');
    Route::get('/dokumen/spjm/detail{id?}', 'spjmDetail')->name('dokumen.spjm.detail');
    Route::get('/dokumen/spjm/cont{id?}', 'spjmCont')->name('dokumen.spjm.detailCont');
    Route::post('/dokumen/spjm/updateDetail', 'spjmUpdateDetail')->name('dokumen.spjm.update.detail');
    Route::post('/dokumen/spjm/updateCont', 'spjmUpdateCont')->name('dokumen.spjm.update.cont');
    Route::post('/dokumen/spjm-getData', 'GetSPJM_onDemand')->name('dokumen.spjm.onDemand');
    Route::post('/dokumen/spjm-getData-automatic', 'GetSPJM')->name('dokumen.spjm.automatic');

     // SPPB BC23
     Route::get('/dokumen/bc23', 'bc23Index')->name('dokumen.bc23.index');
     Route::get('/dokumen/bc23Container/{id?}', 'bc23ContainerList')->name('dokumen.bc23.containerList');
     Route::get('/dokumen/bc23Data', 'bc23Data')->name('dokumen.bc23.data');
     Route::get('/dokumen/bc23/detail{id?}', 'bc23Detail')->name('dokumen.bc23.detail');
     Route::get('/dokumen/bc23/cont{id?}', 'bc23Cont')->name('dokumen.bc23.detailCont');
     Route::post('/dokumen/bc23/updateDetail', 'bc23UpdateDetail')->name('dokumen.bc23.update.detail');
     Route::post('/dokumen/bc23-getData', 'GetImpor_SPPBBC23_OnDemand')->name('dokumen.bc23.onDemand');
     Route::post('/dokumen/bc23-getData-automatic', 'GetBC23Permit')->name('dokumen.bc23.permit');
     
     // SPPB
     Route::get('/dokumen/sppb', 'sppbIndex')->name('dokumen.sppb.index');
     Route::get('/dokumen/sppbData', 'sppbData')->name('dokumen.sppb.data');
     Route::get('/dokumen/sppbContainer/{id?}', 'SPPBContainerList')->name('dokumen.sppb.containerList');
     Route::get('/dokumen/sppb/detail{id?}', 'sppbDetail')->name('dokumen.sppb.detail');
     Route::get('/dokumen/sppb/cont{id?}', 'sppbCont')->name('dokumen.sppb.detailCont');
     Route::post('/dokumen/sppb/updateDetail', 'sppbUpdateDetail')->name('dokumen.sppb.update.detail');
     Route::post('/dokumen/sppb-getData', 'GetImpor_SPPB_OnDemand')->name('dokumen.sppb.onDemand');
     Route::get('/dokumen/sppb-getData-automatic', 'GetImporPermit')->name('dokumen.sppb.import');
     
     //   manual
     Route::get('/dokumen/manual', 'manualIndex')->name('dokumen.manual.index');
     Route::get('/dokumen/manualData', 'manualData')->name('dokumen.manual.data');
     Route::get('/dokumen/manualContainer/{id?}', 'manualContainerList')->name('dokumen.manual.containerList');
     Route::get('/dokumen/manual/detail{id?}', 'manualDetail')->name('dokumen.manual.detail');
     Route::get('/dokumen/manual/cont{id?}', 'manualCont')->name('dokumen.manual.detailCont');
     Route::post('/dokumen/manual/updateDetail', 'manualUpdateDetail')->name('dokumen.manual.update.detail');
     Route::post('/dokumen/manual-getData', 'GetDokumenManual_OnDemand')->name('dokumen.manual.onDemand');
     Route::post('/dokumen/manual-getData-automatic', 'GetDokumenManual')->name('dokumen.manual.auto');
     
     //   Pabean
     Route::get('/dokumen/pabean', 'pabeanIndex')->name('dokumen.pabean.index');
     Route::get('/dokumen/pabeanData', 'pabeanData')->name('dokumen.pabean.data');
     Route::get('/dokumen/pabeanContainer/{id?}', 'pabeanContainerList')->name('dokumen.pabean.containerList');
      Route::get('/dokumen/pabean/detail{id?}', 'pabeanDetail')->name('dokumen.pabean.detail');
      Route::get('/dokumen/pabean/cont{id?}', 'pabeanCont')->name('dokumen.pabean.detailCont');
      Route::post('/dokumen/pabean/updateDetail', 'pabeanUpdateDetail')->name('dokumen.pabean.update.detail');
      Route::post('/dokumen/pabean-getData', 'GetDokumenPabean_OnDemand')->name('dokumen.pabean.onDemand');
      Route::post('/dokumen/pabean-getData-otomatic', 'GetDokumenPabean')->name('dokumen.pabean.otomatis');
});

// LCL
    // Register
    Route::middleware('permission:registerLCL')->controller(RegisterController::class)->group(function(){
        Route::get('/lcl/register', 'index')->name('lcl.register.index');
        Route::get('/lcl/registerData', 'indexData')->name('lcl.register.data');
        Route::post('/lcl/register/post', 'create')->name('lcl.register.create');
        Route::get('/lcl/register/detail-{id?}', 'detail')->name('lcl.register.detail');
        Route::post('/lcl/register/update', 'update')->name('lcl.register.update');
        Route::post('/lcl/register/containerCreate', 'createContainer')->name('lcl.register.container.create');
        Route::delete('/lcl/register/containerDelete{id?}', 'deleteContainer')->name('lcl.register.container.delete');
        Route::get('/lcl/register/containerEdit{id?}', 'editContainer')->name('lcl.register.container.edit');
        Route::post('/lcl/register/containerUpdate', 'updateContainer')->name('lcl.register.container.update');
        Route::post('/lcl/register/dokumenPLP', 'postPLP')->name('lcl.register.dokumen.update');
        Route::post('/lcl/register/barcodeGate', 'createBarcode')->name('lcl.register.barcode.gate');
    });

    Route::middleware('permission:mainfestDetail')->controller(ManifestController::class)->group(function(){
        Route::get('/lcl/manifest', 'index')->name('lcl.manifest.idex');
        Route::get('/lcl/manifest/data', 'indexData')->name('lcl.manifest.data');
        Route::get('/lcl/manifest/detail-{id?}', 'detail')->name('lcl.manifest.detail');
        Route::get('/lcl/manifest/detailManifestData-{id?}', 'detailManifestData');
        Route::post('/lcl/manifest/create', 'create')->name('lcl.manifest.create');
        Route::post('/lcl/manifest/excel', 'excel')->name('lcl.manifest.excel');
        Route::post('/lcl/manifest/excelNew', 'newExcel')->name('lcl.manifest.new');
        Route::get('/lcl/manifest/edit-{id?}', 'edit')->name('lcl.manifest.edit');
        Route::post('/lcl/manifest/delete-{id?}', 'delete')->name('lcl.manifest.delete');
        Route::post('/lcl/manifest/update', 'update')->name('lcl.manifest.update');
        Route::post('/lcl/manifest/approve-{id?}', 'approve')->name('lcl.manifest.approve');
        Route::post('/lcl/manifest/unapprove-{id?}', 'unapprove')->name('lcl.manifest.unapprove');
        Route::post('/lcl/manifest/permohonanStripping', 'permohonanStripping');
        Route::get('/lcl/manifest/suratIzinStripping/{id?}', 'suratIzinStripping');

        // Item
        Route::get('/lcl/manifest/item-{id?}', 'itemIndex')->name('lcl.manifest.item.index');
        Route::post('/lcl/manifest/itemUpdate', 'itemUpdate')->name('lcl.manifest.item.update');
        Route::get('/lcl/manifest/barcode-{id?}', 'barcodeIndex')->name('lcl.manifest.barcode.index');
        Route::get('/lcl/manifest/bonMuat/{id?}', 'bonMuatManifest');
    });

    // Stripping
    Route::middleware('permission:stripping')->controller(StrippingController::class)->group(function(){
        Route::get('/lcl/realisasi/stripping', 'index')->name('lcl.stripping.index');
        Route::get('/lcl/realisasi/stripping/data', 'indexData')->name('lcl.stripping.dataIndex');
        Route::get('/lcl/realisasi/stripping/proses-{id?}', 'proses')->name('lcl.stripping.proses');
        Route::get('/lcl/realisasi/stripping/prosesData-{id?}', 'prosesData');
        Route::post('/lcl/realisasi/stripping/updateCont', 'updateCont')->name('lcl.stripping.cont.update');
        Route::get('/lcl/realisasi/stripping-photoCont{id?}', 'photoCont')->name('lcl.stripping.photoCont');
        Route::post('/lcl/realisasi/stripping/store', 'store')->name('lcl.stripping.store');
        Route::get('/lcl/realisasi/stripping-photoManifest{id?}', 'photoManifest')->name('lcl.stripping.photoManifest');
        Route::post('/lcl/realisasi/stripping/end', 'end')->name('lcl.stripping.end');

    });

    // Delivery
    Route::controller(DeliveryController::class)->group(function(){
        Route::middleware('permission:behandleLCL')->group(function(){
            Route::get('/lcl/delivery/behandle/index', 'indexBehandle')->name('lcl.delivery.behandle');
            Route::get('/lcl/delivery/behandle/behandleData', 'behandleData');
            Route::post('/lcl/delivery/behandle/spjmCheck', 'spjmBehandle')->name('lcl.delivery.spjmCheck');
            Route::post('/lcl/delivery/behandle/update', 'behandle')->name('lcl.delivery.updateBehandle');
            Route::get('/lcl/realisasi/behandle-detail{id?}', 'detailBehandle')->name('lcl.delivery.detailBehandle');
            Route::post('/lcl/delivery/behandle/readyCheck-{id?}', 'readyCheckBehandle')->name('lcl.delivery.readyCheckBehandle');
            Route::post('/lcl/delivery/behandle/finishCheck-{id?}', 'finishBehandle')->name('lcl.delivery.finishBehandle');
        });
        Route::middleware('permission:gateOutLCL')->group(function(){
            Route::get('/lcl/delivery/gateOut', 'indexGateOut')->name('lcl.delivery.gateOut');
            Route::get('/lcl/delivery/dataGateOut', 'dataGateOut');
            Route::post('/lcl/delivery/gateOut/check', 'dokumenGateOut')->name('lcl.delivery.DokumenGateOut');
            Route::post('/lcl/delivery/gateOut/update', 'gateOut')->name('lcl.delivery.updateGateOut');
            Route::get('/lcl/realisasi/GateOut-detail{id?}', 'detailGateOut')->name('lcl.delivery.detailGateOut');
            Route::post('/lcl/delivery/gateOut-barcodeGate', 'createBarcode')->name('lcl.delivery.barcodeGate');
        });

        Route::get('/lcl/delivery/cetakSuratJalan-{id?}', 'cetakSuratJalan');

        Route::get('/barcode/autoGate-indexManifest{id?}', 'manifestBarcode');

    });

    Route::controller(RackingController::class)->group(function(){
        Route::get('/lcl/realisasi/racking', 'index')->name('lcl.racking.index');
        Route::get('/lcl/realisasi/indexData', 'indexTable');
        Route::get('/lcl/realisasi/racking/detail-{id?}', 'detail')->name('lcl.racking.detail');
        Route::get('/lcl/realisasi/racking/itemTableData-{id?}', 'itemTableData');
        Route::post('/lcl/realisasi/racking/update', 'update')->name('lcl.racking.update');
        Route::get('/lcl/realisasi/racking/itemBarcode-{id?}', 'itemBarcode')->name('lcl.racking.itemBarcode');
        Route::post('/lcl/realisasi/racking/unPlace-{id?}', 'unPlace')->name('lcl.racking.unPlace');
        Route::post('/lcl/realisasi/racking/updatePhoto', 'updatePhoto')->name('lcl.racking.updatePhoto');
        Route::get('/lcl/realisasi/racking/photoPlacement{id?}', 'photoPlacement')->name('lcl.racking.photoPlacement');

    });

    // Gate In
    Route::controller(GateInController::class)->group(function(){
        Route::middleware('permission:gateInLCL')->group(function(){
            Route::get('/lcl/realisasi/gateIn', 'index')->name('lcl.gateIn.index');
            Route::get('/lcl/realisasi/gateIn-edt{id?}', 'edit')->name('lcl.gateIn.edit');
            Route::post('/lcl/realisasi/gateIn-update', 'update')->name('lcl.gateIn.update');
            Route::get('/lcl/realisasi/gateIn-detail{id?}', 'detail')->name('lcl.gateIn.detail');
            Route::post('/lcl/realisasi/gateIn-detailDelete', 'detailDelete')->name('lcl.gateIn.delete.detail');
        });
        Route::middleware('permission:sealLCL')->group(function(){
            Route::get('/lcl/realisasi/seal', 'indexSeal')->name('lcl.seal.index');
            Route::get('/lcl/realisasi/dataSeal', 'dataSeal')->name('lcl.seal.data');
            Route::post('/lcl/realisasi/seal-update', 'updateSeal')->name('lcl.seal.update');
            Route::post('/lcl/realisasi/easyGo-send', 'easyGoSend');
            Route::post('/lcl/realisasi/easyGo-closeDO', 'closeDO');
        });
        Route::middleware('permission:buangMTY')->group(function(){
            Route::get('/lcl/realisasi/buangMT', 'indexMt')->name('lcl.mty.index');
            Route::get('/lcl/realisasi/emptyTable', 'emptyTable');
            Route::get('/lcl/realisasi/mty-detail{id?}', 'detailMt')->name('lcl.mty.detail');
            Route::post('/lcl/realisasi/mty-update', 'updateMt')->name('lcl.mty.update');
            Route::post('/lcl/realisasi/mty-barcodeGate', 'createBarcode')->name('lcl.mty.barcode.gate');
            Route::get('/lcl/realisasi/suratJalan-BuangEmpty{id?}', 'suratJalan');
        });
        
    });

// Photo
Route::controller(PhotoController::class)->group(function(){
    Route::get('/photo/lcl/manifest', 'indexLclManifest')->name('photo.lcl.manifest');
    Route::get('/photo/lcl/container', 'indexLclContainer')->name('photo.lcl.container');
    Route::get('/photo/fcl/container', 'indexFclContainer')->name('photo.fcl.container');

    Route::post('/photo/lcl/manifestPost', 'storeManifest')->name('photo.lcl.storeManifest');
    Route::post('/photo/lcl/ContainerPost', 'storeContainer')->name('photo.lcl.storeContainer');

    Route::post('/photo/fcl/ContainerPost', 'storeContainerFcl')->name('photo.fcl.storeContainer');

    Route::get('/getContainerLclKeterangan', 'getKeteranganContainerLcl');
    Route::get('/getManifestLclKeterangan', 'getKeteranganManifestLcl');
});
// Barcode
Route::controller(BarcodeAutoGateController::class)->group(function(){
    Route::middleware('permission:barcode')->group(function(){
        Route::get('/barcode/autoGate-index{id?}', 'index')->name('barcode.autoGate.index');
        Route::get('/barcode/autoGate-bonmuat{id?}', 'bonmuat')->name('barcode.autoGate.bonmuat');
        Route::get('/barcode/autoGate-PrintAll{id?}', 'indexViewAll');
        Route::get('/barcode/autoGate-photoIn{id?}', 'photoIn');
        Route::get('/barcode/autoGate-photoOut{id?}', 'photoOut');
        Route::get('/autoGate-barcode', 'indexAll')->name('barcode.autoGate.indexAll');
        Route::get('/autoGate-barcode/data', 'indexData')->name('barcode.autoGate.indexData');
        Route::get('/fcl/sp2/{id?}', 'cetakSP2FCL');
        Route::post('/fcl/updateSP2', 'updateSP2');
    });
    
    Route::post('/autoGate', 'autoGateNotification')->name('autoGate.autoGateNotification');
});

Route::controller(PlacementContainerController::class)->group(function(){
    Route::get('/lcl/realisasi/placementCont', 'indexLCL')->name('placementCont.lcl.index');
    Route::get('/get/slot', 'getSlot')->name('placementCont.getSlot');
    Route::get('/get/row', 'getRow')->name('placementCont.getRow');
    Route::get('/get/tier', 'getTier')->name('placementCont.getTier');
    
    Route::get('/lcl/realisasi/placementEdit-{id?}', 'edit')->name('placementCont.lcl.edit');
    Route::post('/lcl/realisasi/placementUpdate', 'updateLCL')->name('placementCont.lcl.update');
    Route::get('/lcl/realisasi/placementDetail{id?}', 'detail')->name('placementCont.lcl.detail');

    Route::get('/fcl/realisasi/placementCont', 'indexFCL')->name('placementCont.fcl.index');
    Route::get('/fcl/realisasi/placementEdit-{id?}', 'editFCL')->name('placementCont.fcl.edit');
    Route::post('/fcl/realisasi/placementUpdate', 'updateFCL')->name('placementCont.fcl.update');
    Route::get('/fcl/realisasi/placementDetail{id?}', 'detailFCL')->name('placementCont.fcl.detail');
});

// BcGatter
Route::controller(BeaCukaiController::class)->group(function(){
    Route::get('/bc/dashboard', 'home')->name('bc.dashboard');
    Route::get('/bc/lcl/realisasi/buangMT', 'buangMt')->name('bc.buangMt.index');
    Route::post('/bc/lcl/realisasi/buangMTpost-{id?}', 'buangMtPost')->name('bc.buangMt.post');

    Route::get('/bc/lcl/realisasi/stripping', 'strippingIndex')->name('bc.stripping.index');
    Route::get('/bc/lcl/realisasi/stripping/data', 'strippingIndexData')->name('bc.stripping.indexData');
    Route::post('/bc/lcl/realisasi/stripping/approveCont', 'strippingApproveCont')->name('bc.stripping.aprroveCont');
    Route::post('/bc/lcl/realisasi/stripping/BatalapproveCont', 'strippingBatalApproveCont')->name('bc.stripping.batalApprove');
    
    Route::get('/bc/lcl/realisasi/stripping/detil-{id?}', 'strippingDetail');
    Route::get('/bc/lcl/realisasi/stripping/detilData-{id?}', 'strippingDetailData');
    Route::post('/bc/lcl/realisasi/stripping/manifest/approve', 'approveStrippingManifest');
    Route::post('/bc/lcl/realisasi/stripping/manifest/Batalapprove', 'BatalapproveStrippingManifest');

    Route::post('/bc/lcl/realisasi/stripping-approveAll', 'strippingApproveAll');
    
    Route::get('/bc/lcl/delivery/behandle', 'behandle')->name('bc.behandle.index');
    Route::post('/bc/lcl/delivery/behandleUpdate', 'behandleUpdate')->name('bc.behandle.update');
    Route::post('/bc/lcl/delivery/approve-{id?}', 'approveBehandle')->name('bc.behandle.approve');
    
    Route::get('/bc/lcl/delivery/gateOut', 'gateOut')->name('bc.gateOut.index');
    Route::post('/bc/lcl/delivery/gateOutapprove-{id?}', 'approveGateOut')->name('bc.gateOut.approve');

    // FCL
    Route::get('/bc/fcl/holdContainerIndex', 'HoldContainerIndex');
    Route::get('/bc/fcl/holdContainerDataTable', 'holdContainerDataTable');
    Route::post('/bc/fcl/releaseFCLCont', 'releaseFCLCont');
    
    Route::get('/bc/fcl/releaseContainerIndex', 'ReleaseContainerIndex');
    Route::get('/bc/fcl/releaseContainerDataTable', 'releaseContainerDataTable');
    Route::post('/bc/fcl/holdFCLCont', 'holdFCLCont');
});

// Qr Reader
Route::controller(QrController::class)->group(function(){
    Route::get('/qr-reader/index', 'index')->name('QrReader.index');
    Route::get('/qr-reader/{qr?}', 'detail')->name('QrReader.detil');
});

Route::controller(EasyGoController::class)->group(function(){
    Route::post('/inputdo', 'vts_inputdo')->name('easygo-inputdo');
    Route::post('/inputdo/callback', 'vts_inputdo_callback')->name('easygo-inputdo-callback');
});

Route::controller(ReportController::class)->group(function(){
    // Container
    Route::get('/lcl/report/cont', 'indexCont')->name('report.lcl.cont');
    Route::get('/lcl/report/dataCont', 'dataCont');
    Route::get('/lcl/report/contPhoto{id?}', 'photoCont')->name('report.lcl.photoCont');
    Route::get('/lcl/report/contGenerate', 'generateCont')->name('report.lcl.generateCont');
    Route::get('/lcl/report/contGenerateJICT', 'contGenerateJICT')->name('report.lcl.contGenerateJICT');

    // Manifest
    Route::get('/lcl/report/manifest', 'indexManifest')->name('report.lcl.manifest');
    Route::get('/lcl/report/manifestDataTable', 'manifestDataTable');
    Route::get('/lcl/report/manifestPhoto{id?}', 'photoManifest')->name('report.lcl.photoManifest');
    Route::get('/lcl/report/manifestGenerate', 'generateManifest')->name('report.lcl.generateManifest');
    Route::get('/lcl/report/manifestGenerateBeaCukai', 'generateManifestBeaCukaiNew')->name('report.lcl.generateManifestBeaCukaiNew');

    // Daily
    Route::get('/lcl/report/daily', 'indexDaily')->name('report.lcl.daily');
});

// Android
Route::controller(AndroidHomeController::class)->group(function(){
    Route::get('/android/dashboard', 'indexDashboard')->name('android.dashboard');
}); 

Route::controller(LclController::class)->group(function(){
    Route::get('/android/lcl/stripping/index', 'indexStripping');
    Route::get('/android/lcl/stripping/manifest', 'indexStrippingManifest');
    Route::get('/android/searchCont{id?}', 'searchCont');

    // Placement
    Route::get('/android/lcl/placementCont', 'plcamenContIndex');

    // Racking
    Route::get('/android/lcl/racking', 'rackingIndex');
    Route::get('/android/lcl/rackingDetail-{qr?}', 'rackingDetil');
    Route::post('/android/lcl/rackingAndroid', 'postRacking');
   
    // Behandle
    Route::get('/android/lcl/behandle', 'behandleIndex');
    Route::get('/android/lcl/behandleDetail-{qr?}', 'behandleDetil');

    // Photo Container
    Route::get('/android/photo/photoCont', 'photoCont');

    // Photo Manifest
    Route::get('/android/photo/photoManifest', 'photoManifest');
    Route::get('/android/photo/photoManifest-{qr?}', 'photoManifestDetil');

    // Muat
    Route::prefix('/android/muat')->group(function(){
        Route::get('/index', 'indexMuat');
        Route::get('/detil/{barcode?}', 'detilMuat');
        Route::post('/mulaiMuat', 'mulaiMuat');
        Route::post('/selesaiMuat', 'selesaiMuat');
        Route::post('/muatItem', 'muatItem');
    });
});

    Route::controller(FCLAndroidController::class)->group(function(){
        Route::get('/android/photo/photoContFCL', 'photoCont');
        Route::get('/android/fcl/searchCont{id?}', 'searchCont');
    });
// Android Gate

Route::controller(AndroidGateController::class)->group(function(){
    Route::get('/android/gate/index', 'index');
    Route::get('/android/gate/reciving/{qr?}', 'recivingBarcode');
    Route::get('/android/gate/fcl/{qr?}', 'indexGateFCL');
    Route::get('/android/gate/lcl/{qr?}', 'indexGateLCL');
    Route::get('/android/gate/manifest/{qr?}', 'indexGateManifest');
});

// Invoice
    Route::controller(DashboardInvoiceController::class)->group(function(){
        Route::get('/dashboard-invoice', 'dashboard');
    });
    // Master
    Route::middleware('permission:tarifLCL')->controller(MasterInvoiceController::class)->group(function(){
        Route::get('/invoice/master/tarif', 'tarifIndex');
        Route::post('/invoice/master/tarif-Post', 'tarifPost');
        Route::delete('/invoice/master/tarif-Delete{id?}', 'tarifDelete');
        Route::get('/invoice/master/tarif-Edit{id?}', 'tarifEdit');
        Route::post('/invoice/master/tarif-Update', 'tarifUpdate');
    });

    // Form
    Route::middleware('permission:formInvoiceLCL')->controller(FormController::class)->group(function(){
        Route::get('/invoice/form/index', 'index')->name('form.index');
        Route::post('/invoice/form/create', 'create');
        Route::delete('/invoice/form/delete-{id?}', 'delete');
        Route::get('/get-manifest-data/{id}', 'getManifestData');
        Route::get('/get-customer-data/{id}', 'getCustomerData');
        // Step1
        Route::get('/invoice/form/formStep1/{id?}', 'formIndex')->name('invoice.step1');     
        Route::post('/invoice/form/submitStep1', 'step1Post');     
        // Step2
        Route::get('/invoice/form/formStep2/{id?}', 'step2Index')->name('invoice.step2'); // Corrected the parameter format    
        Route::post('/invoice/form/submitStep2', 'step2Post');     
        // Step3
        Route::get('/invoice/form/formStep3/{id?}', 'preinvoice')->name('invoice.preinvoice'); // Corrected the parameter format    
        Route::post('/invoice/form/submitStep3', 'step3Post');     
    });

    // Index
    Route::middleware('permission:invoiceLCL')->controller(InvoiceController::class)->group(function(){
        Route::get('/invoice/form/unpaid', 'unpaidIndex')->name('invoice.unpaid');
        Route::get('/invoice/form/unpaidData', 'unpaidData');
        Route::get('/invoice/form/paidData', 'paidData')->name('invoice.lcl.paidData');
        Route::get('/invoice/pranota-{id?}', 'pranotaIndex');
        Route::post('/invoice/deleteHeader-{id?}', 'deleteInvoice');
        Route::get('/invoice/actionButton-{id?}', 'invoiceGetData');
        Route::post('/invoice/paid', 'invoicePaid');
        Route::get('/invoice/photoKTP-{id?}', 'photoKTP');
        
        Route::post('invoice/searchForEdit', 'searchForEdit')->name('invoice.lcl.searchForEdit');
        Route::post('invoice/updateTanggal', 'updateTanggal')->name('invoice.lcl.updateTanggal');

        Route::get('/invoice/form/paid', 'paidIndex')->name('invoice.paid');
        Route::get('/invoice/invoicePrint-{id?}', 'invoiceIndex');
        Route::get('/invoice/barcodeBarang-{id?}', 'barcodeIndex');
        Route::get('/invoice/dokButton-{id?}', 'invoiceGetManifestData');
        Route::post('/invoice/updateDokumen', 'invoiceUpdateDokumen');
    });

    // Form Perpanjangan
    Route::middleware('permission:perpajanganLCL')->controller(FormPerpanjanganController::class)->group(function(){
        Route::get('/invoice/form/perpanjangan/index', 'index')->name('form.index');
        Route::post('/invoice/form/perpanjangan/create', 'create');
        Route::delete('/invoice/form/perpanjangan/delete-{id?}', 'delete');
        Route::get('/get-oldInvocie-data/{id}', 'getOldInvoiceData');
        // Step1
        Route::get('/invoice/form/perpanjangan/formStep1/{id?}', 'formIndex')->name('invoice.perpanjangan.step1');     
        Route::post('/invoice/form/perpanjangan/submitStep1', 'step1Post');     
        // Step2
        Route::get('/invoice/form/perpanjangan/formStep2/{id?}', 'step2Index')->name('invoice.perpanjangan.step2'); // Corrected the parameter format    
        Route::post('/invoice/form/perpanjangan/submitStep2', 'step2Post');     
        // Step3
        Route::get('/invoice/form/perpanjangan/formStep3/{id?}', 'preinvoice')->name('invoice.perpanjangan.preinvoice'); // Corrected the parameter format    
        Route::post('/invoice/form/perpanjangan/submitStep3', 'step3Post');
    });

    // Invoice Perpanjangan
    Route::middleware('permission:perpajanganLCL')->controller(InvoicePerpanjanganController::class)->group(function(){
        Route::get('/invoice/form/perpanjangan/unpaid', 'unpaidIndex')->name('invoice.perpanjangan.unpaid');
        Route::get('/invoice/perpanjangan/pranota-{id?}', 'pranotaIndex');
        Route::delete('/invoice/perpanjangan/deleteHeader-{id?}', 'deleteInvoice');
        Route::get('/invoice/perpanjangan/actionButton-{id?}', 'invoiceGetData');
        Route::post('/invoice/perpanjangan/paid', 'invoicePaid');
        Route::get('/invoice/perpanjangan/photoKTP-{id?}', 'photoKTP');
        
        Route::get('/invoice/form/perpanjangan/paid', 'paidIndex')->name('invoice.paid');
        Route::get('/invoice/perpanjangan/invoicePrint-{id?}', 'invoiceIndex');
        Route::get('/invoice/perpanjangan/barcodeBarang-{id?}', 'barcodeIndex');
        Route::get('/invoice/perpanjangan/dokButton-{id?}', 'invoiceGetManifestData');
        Route::post('/invoice/perpanjangan/updateDokumen', 'invoiceUpdateDokumen');
    }); 

    Route::controller(ReportInvoiceController::class)->group(function(){
        Route::get('/invoice/report', 'index');
        Route::get('/invoice/reportGenerateExcel', 'generateExcel');
        Route::get('/invoice/reportGeneratePdf', 'generatePdf');
    });

    // BeaCukai P2
    Route::controller(BeacukaiP2Controller::class)->group(function(){
        Route::get('/bc-p2/dashboard', 'indexDashboard')->name('bcP2.dashboard');
        Route::get('/bc-p2/logData', 'logData');
        Route::get('/bc-p2/logDataFCL', 'logDataFCL');

        Route::get('/bc-p2/lcl/list-manifest', 'listManifestIndex');
        Route::get('/bc-p2/lcl/list-manifest/data', 'listManifestData');
        Route::get('/bc-p2/lcl/list-manifest/lockModal{id?}', 'listManifestModal');
        Route::post('/bc-p2/lcl/list-manifest/lockSubmit', 'lockSubmit');

        Route::get('/bc-p2/list-segelMerah', 'listSegelIndex');
        Route::get('/bc-p2/list-segelMerah/data', 'listSegelData');
        Route::post('/bc-p2/list-segelMerah/unlockSubmit', 'unlockSubmit');

        Route::get('/bc-p2/fcl/list-container', 'indexListContainer');
        Route::get('/bc-p2/fcl/containerData', 'listContainerData');
        Route::get('/bc-p2/fcl/list-container/lockModal{id?}', 'listContainerModal');
        Route::post('/bc-p2/fcl/list-container/lockSubmit', 'lockSubmitFCL');

        Route::get('/bc-p2/fcl/list-segelMerah', 'listSegelIndexFCL');
        Route::get('/bc-p2/fcl/list-segelMerah/dataFCL', 'listSegelDataFCL');
        Route::post('/bc-p2/fcl/list-segelMerah/unlockSubmitFCL', 'unlockSubmitFCL');


        Route::get('/bc-p2/detil-log/{id?}', 'logDetil');
        Route::get('/bc-p2/detil-logFCL/{id?}', 'logDetilFCL');
    });

    Route::prefix('/dokumen/pengiriman')->controller(PengirimanController::class)->group(function(){
        Route::prefix('/container')->group(function(){
            Route::get('/index', 'containerIndex');
            Route::get('/data', 'containerData');
        });
        Route::prefix('/manifest')->group(function(){
            Route::get('/index', 'manifestIndex');
        });
    });

    
    Route::prefix('/pengiriman')->controller(CoariCodecoController::class)->group(function(){
        Route::prefix('/coari')->group(function(){
            Route::post('/cont', 'coariCont');
            Route::get('/data', 'containerData');
        });
        Route::prefix('/manifest')->group(function(){
            Route::get('/index', 'manifestIndex');
        });
    });

    // FCL
    Route::prefix('/fcl')->group(function(){
        Route::middleware('permission:registerFCL')->prefix('/register')->controller(RegisterFCLController::class)->group(function(){
            Route::get('/index', 'index')->name('fcl.register.index');
            Route::get('/data', 'indexData');
            Route::get('/detail-{id}', 'detail')->name('fcl.register.detail');
            Route::get('/detailDataContainer-{id}', 'detilData');
            Route::get('/containerEdit{id}', 'editContainer');
            Route::post('/create', 'create')->name('fcl.register.create');
            Route::post('/update', 'update')->name('fcl.register.update');
            Route::post('/updateContainer', 'updateContainer')->name('fcl.register.updateCont');
            Route::post('/createContainer', 'createContainer')->name('fcl.register.container.create');
            Route::post('/postPLP', 'postPLP')->name('fcl.register.updatePLP');
            Route::delete('/containerDelete{id}', 'deleteContainer');
            Route::post('/barcodeGate', 'createBarcode');
            Route::post('/barcodeGateAll', 'createBarcodeAll');

            Route::get('/generateExcelPLP/{id?}', 'generateExcel');
            Route::get('/cetakPLP/{id?}', 'cetakPLP');
        });

        Route::prefix('/realisasi')->controller(GateInFCLCotroller::class)->group(function(){
            Route::middleware('permission:gateInFCL')->group(function(){
                Route::get('/gateIn', 'index')->name('fcl.gateIn.index');
                Route::get('/gateIn-edt{id?}', 'edit')->name('fcl.gateIn.edit');
                Route::post('/gateIn-update', 'update')->name('fcl.gateIn.update');
                Route::get('/gateIn-detail{id?}', 'detail')->name('fcl.gateIn.detail');
                Route::post('/gateIn-detailDelete', 'detailDelete')->name('fcl.gateIn.delete.detail');
                Route::get('/seal', 'indexSeal');
                Route::post('/seal-update', 'updateSeal')->name('fcl.seal.update');
                Route::post('/easyGo-send', 'easyGoSend');
                Route::post('/easyGo-closeDO', 'closeDO');
            });
        });

        Route::prefix('/delivery')->controller(DeliveryFCLController::class)->group(function(){
            Route::middleware(['permission:behandleFCL', 'permission:gateOutFCL'])->group(function(){
                Route::get('/behandle', 'indexBehandle');
                Route::get('/behandle-data', 'behandleData')->name('fcl.behandle.dataTable');
                Route::get('/dataCont/{id}', 'getDataCont');
                Route::get('/searchSPJM', 'searchSPJM')->name('fcl.behandle.searchSPJM');
    
                Route::post('/behandleReadyCheck{id}', 'readyCheckBehandle');
                Route::post('/prosesCheckBehandle{id}', 'prosesCheckBehandle');
                Route::post('/finishCheckBehandle{id}', 'finishCheckBehandle');
                Route::post('/behandleUpdate', 'updateDataBehandle')->name('fcl.delivery.updateBehandle');
                Route::post('/gatePassBonMuat', 'gatePassBonMuat')->name('fcl.delivery.gatePassBonMuat');
                Route::post('/gateOutFCL', 'gateOutFCL')->name('fcl.delivery.gateOutFCL');
    
                Route::get('/behandleDetil{id}', 'detailBehandle');
    
                Route::get('/gateOut', 'indexGateOut');
                Route::get('/dataGateOutFCL', 'dataGateOutFCL')->name('fcl.delivery.dataGateOutFCL');
                Route::post('/searchDockGate', 'searchingDokumenGate')->name('fcl.delivery.searchDokumenGate');
            });
            
        });

        Route::middleware('permission:containerFCL')->prefix('/containerList')->controller(ContainerDokController::class)->group(function(){
            Route::get('/index', 'index');
            Route::get('/dataTable', 'dataTable');
            Route::post('/dataDok', 'dataDok');
            Route::post('/updateCont', 'updateCont')->name('fcl.containerList.update');
        });

        Route::prefix('/report')->controller(ReportFCLController::class)->group(function(){
            Route::get('/index', 'index');
            Route::get('/dataCont', 'dataCont');
            Route::get('/photoCont{id?}', 'photoCont');
            Route::get('/formatStandar', 'formatStandar');
            Route::get('/formatJict', 'formatJict');
            Route::get('/formatBeacukai', 'formatBeacukai');
            Route::get('/formatBeacukaiNew', 'formatBeacukaiNew');
            Route::get('/formatStandarAkhir', 'formatStandarAkhir');
            Route::get('/formatBeacukaiAkhir', 'formatBeacukaiAkhir');
            
            // Daily Report
            
            Route::get('/indexDaily', 'indexDaily')->name('report.fcl.daily');
            Route::get('/dataContDaily', 'dataContDaily');
        });
    });

    Route::prefix('/invoiceFCL')->group(function(){
        Route::get('/dashboard', [InvoiceFCLMainController::class, 'dashboardInvoiceFCL']);
        Route::middleware('permission:invoiceFCL')->prefix('/masterTarif')->group(function(){
            Route::get('/index', [InvoiceFCLMainController::class, 'indexMasterTarif']);
            Route::controller(MasterTarifFCLController::class)->group(function(){
                // Tarif TPS
                Route::get('/dataTarifTPS', 'dataTableTPS');
                Route::post('/createTarifTPS', 'postTarifTPS')->name('invoice.fcl.createTarifTPS');
                Route::get('/editTarif{id?}', 'editTarifTPS');
                Route::post('/updateTarifTPS', 'updateTarifTPS')->name('invoice.fcl.updateTarifTPS');
                
                // Tarif WMS
                Route::get('/dataTarifWMS', 'dataTableWMS');
                Route::post('/createTarifWMS', 'postTarifWMS')->name('invoice.fcl.createTarifWMS');
                Route::get('/editTarifWMS/{id?}', 'editTarifWMS');
                Route::post('/updateTarifWMS', 'updateTarifWMS')->name('invoice.fcl.updateTarifWMS');
                
            });
        });
        Route::middleware('permission:formInvoiceFCL')->prefix('/form')->group(function(){
            Route::get('/index', [InvoiceFCLMainController::class, 'indexForm']);
            Route::controller(FormFCLController::class)->group(function(){
                Route::get('/dataTable', 'dataTable');
                Route::get('/createIndex/Step1', 'indexStep1');
                Route::get('/createEdit/Step1/{id?}', 'editStep1');
                Route::get('/getBLAWB', 'getBLAWB');
                Route::get('/getBLData', 'getBLData');
                Route::post('/step1Post', 'postStep1');
                Route::post('/step1Update', 'updateStep1');
                // Step 2
                Route::get('/indexStep2/{id?}', 'indexStep2');
                Route::post('/postStep2', 'postStep2')->name('invoice.lcl.postStep2');

                // Cancel Form
                Route::post('/cancelForm/{id?}', 'cancelForm');

                // Perpanjangan
                Route::prefix('/extend')->group(function(){
                    Route::get('/index', 'indexPerpanjangan');
                    Route::get('/dataTable', 'dataTablePerpanjangan');
                    Route::get('/createIndex/Step1', 'indexStep1Perpanjangan');
                    Route::get('/createEdit/Step1/{id?}', 'editStep1Perpanjangan');
                    Route::get('/getBLAWB', 'getBLAWBPerpanjangan');
                    Route::get('/getBLData', 'getBLDataPerpanjangan');
                    Route::post('/step1Post', 'postStep1Perpanjangan');
                    Route::post('/step1Update', 'updateStep1Perpanjangan');
                    // Step 2
                    Route::get('/indexStep2/{id?}', 'indexStep2Perpanjangan');
                    Route::post('/postStep2', 'postStep2Perpanjangan');
                    
                    // Cancel Form
                    Route::post('/cancelForm/{id?}', 'cancelForm');
                });
            });
        });

        Route::prefix('/invoice')->group(function(){
            Route::get('/index', [InvoiceFCLMainController::class, 'invoiceIndex']);
            Route::controller(BackendInvoiceController::class)->group(function(){
                Route::middleware('permission:invoiceFCL')->group(function(){
                    Route::get('/dataTable', 'dataTable');
                    Route::get('/pranota-{id?}', 'pranota');
                    Route::get('/invoice-{id?}', 'Invoice');
                    Route::get('/tranparansi-{id?}', 'Tranparansi');
                    Route::get('/getDataInvoice-{id?}', 'getDataInvoice');
                    Route::post('/paidInvoice', 'paidInvoice');
                    Route::post('/cancelInvoice', 'cancelInvoice');
                    Route::post('/deleteInvoice', 'deleteInvoice');
                    Route::get('/edit/{id?}', 'editInvoice');
                    Route::post('/updateInvoice', 'updateInvoice');
                    
                    Route::post('/deleteKPT/{id?}', 'hapusPhotoKTP');
                    Route::post('/uploadKTP', 'uploadKtp');
                    Route::prefix('/report')->group(function(){
                        Route::get('/index', 'indexReport');
                        Route::get('/excel', 'excelReport');
                        Route::get('/pdf', 'pdfReport');
                    });
                });
                
                Route::middleware('permission:hiddenInvoiceFCL')->post('/hiddenInvoice', 'hiddenInvoice');
            });
        });

        Route::prefix('/behandle')->group(function(){
            Route::controller(InvoiceBehandleFCLController::class)->group(function(){
                Route::middleware(['permission:invoiceFCL', 'permission:formInvoiceFCL'])->group(function(){

                    Route::get('/form-index', 'formIndex')->name('invoiceFCL.behandle.formIndex');
                    Route::get('/form-data', 'formData')->name('invoiceFCL.behandle.formData');
                    Route::post('/form-create', 'formCreate')->name('invoiceFCL.behandle.formCreate');
                        // Step1
                        Route::get('/form-step1/{id?}', 'indexStep1')->name('invoiceFCL.behandle.step1');
                        Route::get('/form-getCont', 'getContainer')->name('invoiceFCL.behandle.getContainer');
                        Route::post('/form-postStep1', 'postStep1')->name('invoiceFCL.behandle.postStep1');
                        Route::post('/form-delete', 'delete')->name('invoiceFCL.behandle.delete');
    
                        // Preinvoice
                        Route::get('/preinvoice/{id?}', 'preinvoice')->name('invoiceFCL.behandel.preinvoice');
                        Route::post('/create-invoice', 'createInvoice')->name('invoiceFCL.behandel.createInvoice');
    
                    // Invoice
                    Route::get('/invoice-index', 'invoiceIndex')->name('invoiceFCL.behandle.invoiceIndex');
                    Route::get('/invoice-data', 'invoiceData')->name('invoiceFCL.behandle.invoiceData');
                    Route::get('/invoice-pranota/{id?}', 'invoicePranota')->name('invoiceFCL.behandle.invoicePranota');
                    Route::post('/invoice-pay', 'invoicePay')->name('invoiceFCL.behandle.invoicePay');
                    Route::get('/invoice-invoice/{id?}', 'invoiceInvoice')->name('invoiceFCL.behandle.invoiceInvoice');
                    Route::post('/invoice-cancel', 'invoiceCancel')->name('invoiceFCL.behandle.invoiceCancel');
                    Route::get('/invoice-edit/{id?}', 'editInvoice')->name('invoiceFCL.behandle.editInvoice');
                    Route::post('/invoice-update', 'updateInvoice')->name('invoiceFCL.behandle.updateInvoice');
                    
                    // Report
                    Route::get('/invoice-report', 'indexReport')->name('invoiceFCL.behandle.indexReport');
                    Route::get('/data-report', 'dataReport')->name('invoiceFCL.behandle.dataReport');
                });
                // Form
                
                Route::middleware('permission:hiddenInvoiceFCL')->post('/invoice-hidden', 'hiddenInvoice')->name('invoiceFCL.behandle.hiddenInvoice');
            });
        });
    });

// Coari Codeco Controller
Route::prefix('/pengiriman')->group(function(){
    // Coari
    Route::prefix('/coari-lcl')->group(function(){
        Route::controller(CoariController::class)->group(function(){
            Route::get('/cont-index', 'indexContLCL')->name('pengiriman.lcl.containerIndex');
            Route::get('/cont-data', 'dataContLCL')->name('pengiriman.lcl.containerData');
            Route::post('/cont-KirimUlang', 'sendContLCL')->name('pengiriman.lcl.sendContLCL');
            
            Route::get('/manifest-index', 'indexManifestLCL')->name('pengiriman.lcl.manifestIndex');
            Route::get('/manifest-data', 'dataManifestLCL')->name('pengiriman.lcl.manifestData');
            Route::post('/manifest-KirimUlang', 'sendManifestLCL')->name('pengiriman.lcl.sendManifestLCL');
            Route::post('/manifest-kirimManual', 'kirimManualManifest')->name('pengiriman.lcl.kirimManualManifest');

            // Route::get('/manifest-')
        });
    });

    // Codeco
    Route::prefix('/codeco-lcl')->group(function(){
        Route::controller(CodecoController::class)->group(function(){
            Route::get('/cont-index', 'indexContLCL');
            Route::get('/cont-data', 'dataContLCL');
            Route::post('/cont-kirimUlang', 'sendContLCL');

            Route::get('/manifest-index', 'indexManifestLCL');
            Route::get('/manifest-data', 'dataManifestLCL');
            Route::post('/manifest-KirimUlang', 'kirimManualManifest');
            Route::post('/manifest-kirimManual', 'kirimManualManifest');
        });
    });

    Route::prefix('/coari-fcl')->group(function(){
        Route::controller(CoariController::class)->group(function(){
            Route::get('/index', 'indexContFCL')->name('pengiriman.fcl.index');
            Route::get('/data', 'dataContFCL')->name('pengiriman.fcl.data');
        });
    });

    Route::prefix('/codeco-fcl')->group(function(){
        Route::controller(CodecoController::class)->group(function(){
            Route::get('/index', 'indexContFCL');
            Route::get('/data', 'dataContFCL');
        });
    });
});

Route::post('/testCoari', [CoariCodecoController::class, 'CoariKms']);
Route::post('/testCodeco', [CoariCodecoController::class, 'CodecoKms']);
Route::post('/testCoariContLCL', [CoariCodecoController::class, 'coariCont']);
Route::post('/testCoariContFCL', [CoariCodecoController::class, 'coariContFCL']);

Route::post('/testCodecoContLCL', [CoariCodecoController::class, 'CodecoCont']);
Route::post('/testCodecoContFCL', [CoariCodecoController::class, 'CodecoContFCL']);

Route::prefix('/lcl/cfs')->group(function(){
    Route::controller(CfsDefaultController::class)->group(function(){
        Route::get('/container', 'indexContainer')->name('cfs.container.index');
        Route::get('/contianer-data', 'dataContainer')->name('cfs.container.data');
        Route::post('/contianer-resend', 'resendContainer')->name('cfs.container.resend');

        Route::get('/manifest', 'indexManifest')->name('cfs.manifest.index');
        Route::get('/manifest-data', 'dataManifest')->name('cfs.manifest.data');
        Route::post('/manifest-resend-coari', 'coariManifest')->name('cfs.manifest.coari');
        Route::post('/manifest-resend-codeco', 'codecoManifest')->name('cfs.manifest.codeco');
        Route::post('/manifest-resend-detil', 'detilManifest')->name('cfs.manifest.detil');
    });    
});

Route::middleware('permission:invoiceCSF')->prefix('/invoice/cfs')->group(function(){
    Route::controller(InvoiceCSFController::class)->group(function(){
        Route::get('/index', 'index')->name('cfs.invoice.index');
        Route::get('/data', 'data')->name('cfs.invoice.data');
        Route::get('/print', 'print')->name('cfs.invoice.print');
    });
});

Route::prefix('/user-profile')->group(function(){
    Route::controller(ProfileController::class)->group(function(){
        Route::get('/index', 'index')->name('user.profile.index');
        Route::post('/update', 'update')->name('user.profile.update');
    });
});