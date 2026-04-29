<?php


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

Route::group(['middleware' => ['get.menu']], function () {
    
    Route::get('/', function () {           //return view('dashboard.homepage'); 
        if(Auth::check()){
            //echo Auth::user();die();
            if(Auth::user()->menuroles == 'seller') {
                return Redirect::to('seller');
            }
            if(Auth::user()->menuroles == 'employee') {
                // Get the employee's store
                $employee = \App\Models\StoreEmployee::where('user_id', Auth::user()->id)
                    ->where('is_active', true)
                    ->first();
                
                if ($employee && $employee->store) {
                    // Redirect to the store's products page or dashboard
                    return redirect()->route('seller.stores.products', $employee->store_id);
                }
                
                // If no store found, redirect to login with error
                Auth::logout();
                return redirect('/login')->with('error', 'No active store found for your account.');
            }
            return Redirect::to('listing');
        }
        return view('auth.login');
    });

    Route::group(['middleware' => ['role:user']], function () {
        Route::get('/colors', function () {     return view('dashboard.colors'); });
        Route::get('/typography', function () { return view('dashboard.typography'); });
        Route::get('/charts', function () {     return view('dashboard.charts'); });
        Route::get('/widgets', function () {    return view('dashboard.widgets'); });
        Route::get('/404', function () {        return view('dashboard.404'); });
        Route::get('/500', function () {        return view('dashboard.500'); });
        Route::prefix('base')->group(function () {  
            Route::get('/breadcrumb', function(){   return view('dashboard.base.breadcrumb'); });
            Route::get('/cards', function(){        return view('dashboard.base.cards'); });
            Route::get('/carousel', function(){     return view('dashboard.base.carousel'); });
            Route::get('/collapse', function(){     return view('dashboard.base.collapse'); });

            Route::get('/forms', function(){        return view('dashboard.base.forms'); });
            Route::get('/jumbotron', function(){    return view('dashboard.base.jumbotron'); });
            Route::get('/list-group', function(){   return view('dashboard.base.list-group'); });
            Route::get('/navs', function(){         return view('dashboard.base.navs'); });

            Route::get('/pagination', function(){   return view('dashboard.base.pagination'); });
            Route::get('/popovers', function(){     return view('dashboard.base.popovers'); });
            Route::get('/progress', function(){     return view('dashboard.base.progress'); });
            Route::get('/scrollspy', function(){    return view('dashboard.base.scrollspy'); });

            Route::get('/switches', function(){     return view('dashboard.base.switches'); });
            Route::get('/tables', function () {     return view('dashboard.base.tables'); });
            Route::get('/tabs', function () {       return view('dashboard.base.tabs'); });
            Route::get('/tooltips', function () {   return view('dashboard.base.tooltips'); });
        });
        Route::prefix('buttons')->group(function () {  
            Route::get('/buttons', function(){          return view('dashboard.buttons.buttons'); });
            Route::get('/button-group', function(){     return view('dashboard.buttons.button-group'); });
            Route::get('/dropdowns', function(){        return view('dashboard.buttons.dropdowns'); });
            Route::get('/brand-buttons', function(){    return view('dashboard.buttons.brand-buttons'); });
        });
        Route::prefix('icon')->group(function () {  // word: "icons" - not working as part of adress
            Route::get('/coreui-icons', function(){         return view('dashboard.icons.coreui-icons'); });
            Route::get('/flags', function(){                return view('dashboard.icons.flags'); });
            Route::get('/brands', function(){               return view('dashboard.icons.brands'); });
        });
        Route::prefix('notifications')->group(function () {  
            Route::get('/alerts', function(){   return view('dashboard.notifications.alerts'); });
            Route::get('/badge', function(){    return view('dashboard.notifications.badge'); });
            Route::get('/modals', function(){   return view('dashboard.notifications.modals'); });
        });
        Route::resource('notes', 'NotesController');

        //Currently using
        Route::resource('category', 'CategoryController');
        Route::resource('question', 'QuestionController');

        Route::get('category/questionList/{id}',        'QuestionController@categoryQuestionList')->name('categoryQuestionList');
        Route::post('question/sort',        'QuestionController@questionsort')->name('questionSort');
        Route::post('/question/option/delete/{id}',       'QuestionController@optionDelete')->name('optiondelete');
        Route::get('category/attributes/{id}',        'CategoryController@categoryAttributes')->name('categoryAttibutes');
        Route::post('category/attributes/store',        'CategoryController@categoryAttributesStore')->name('categoryAttibutesStore');
    });
    Auth::routes();

    /*Route::group(['middleware' => ['role:seller']], function () {
        Route::resource('notes', 'NotesController');
    });*/
    
    Route::get('category/subList/{id}',         'CategoryController@getSubCategoryListFromParent')->name('subCategoryList');
    Route::get('addListings/single',            'SellerController@listingBrandSelection')->name('listingBrandSelect');

    Route::post('brand/check/{id}', 'BrandController@check')->name('brandCheck');
    Route::post('brand/create', 'BrandController@store')->name('brandStore');
    Route::get('brand/approval', 'BrandController@brandApproval')->name('brandApproval');
    Route::post('brand/approval/store', 'BrandController@brandApprovalStore')->name('brandApprovalStore');
    Route::get('my/listing', 'SellerController@myListing')->name('myListing');
    Route::get('add/listing', 'SellerController@addListing')->name('addListing');
    Route::post('my/listing/photos/store', 'SellerController@myListingPhotoStore')->name('myListingPhotoStore');
    Route::get('my/listing/create', 'SellerController@createListing')->name('createListing');
    Route::post('my/listing/storeListingAddition', 'SellerController@storeListingAddition')->name('storeListingAddition');
    Route::put('my/listing/updateListingAddition', 'SellerController@storeListingAddition')->name('updateListingAddition');
    Route::post('listing/status/change/{id}',       'SellerController@listingStatusChange')->name('listingStatusChange');
    
    Route::post('my/listing/storeListingData', 'SellerController@storeListingData')->name('storeListingData');
    Route::put('my/listing/updateListingData', 'SellerController@storeListingData')->name('updateListingData');
    Route::post('my/listing/storeListingSizeChartData', 'SellerController@storeListingSizeChartData')->name('storeListingSizeChartData');

    /**** Seller Dashboard ***/
    Route::get('seller', 'SellerController@index')->name('sellerDashboard');
    Route::get('seller/account', 'SellerRegistrationController@index')->name('sellerRegistration');
    Route::post('seller/create', 'SellerRegistrationController@store')->name('sellerCreate');
    Route::get('store/create', 'SellerController@sellerStoreCreate')->name('sellerStoreCreate');
    /**** End Seller Dashboard ***/

    Route::get('brands', 'BrandController@brandList')->name('brandList');
    Route::post('brands/status/change/{id}', 'BrandController@brandStatusChange')->name('brandStatusChange');
    
    Route::get('order_list', 'OrderController@index')->name('orderList');
    Route::get('order/{id}', 'OrderController@show')->name('orderDetail');
    Route::put('order/detail/status', 'OrderController@orderStatusUpdate')->name('orderStatusUpdate');


    Route::resource('resource/{table}/resource', 'ResourceController')->names([
        'index'     => 'resource.index',
        'create'    => 'resource.create',
        'store'     => 'resource.store',
        'show'      => 'resource.show',
        'edit'      => 'resource.edit',
        'update'    => 'resource.update',
        'destroy'   => 'resource.destroy'
    ]);

    Route::group(['middleware' => ['role:admin']], function () {
        Route::resource('bread',  'BreadController');   //create BREAD (resource)
        Route::resource('users',        'UsersController')->except( ['create', 'store'] );
        Route::resource('roles',        'RolesController');
        Route::resource('mail',        'MailController');
        Route::get('prepareSend/{id}',        'MailController@prepareSend')->name('prepareSend');
        Route::post('mailSend/{id}',        'MailController@send')->name('mailSend');
        Route::get('/roles/move/move-up',      'RolesController@moveUp')->name('roles.up');
        Route::get('/roles/move/move-down',    'RolesController@moveDown')->name('roles.down');
        Route::prefix('menu/element')->group(function () { 
            Route::get('/',             'MenuElementController@index')->name('menu.index');
            Route::get('/move-up',      'MenuElementController@moveUp')->name('menu.up');
            Route::get('/move-down',    'MenuElementController@moveDown')->name('menu.down');
            Route::get('/create',       'MenuElementController@create')->name('menu.create');
            Route::post('/store',       'MenuElementController@store')->name('menu.store');
            Route::get('/get-parents',  'MenuElementController@getParents');
            Route::get('/edit',         'MenuElementController@edit')->name('menu.edit');
            Route::post('/update',      'MenuElementController@update')->name('menu.update');
            Route::get('/show',         'MenuElementController@show')->name('menu.show');
            Route::get('/delete',       'MenuElementController@delete')->name('menu.delete');
        });
        Route::prefix('menu/menu')->group(function () { 
            Route::get('/',         'MenuController@index')->name('menu.menu.index');
            Route::get('/create',   'MenuController@create')->name('menu.menu.create');
            Route::post('/store',   'MenuController@store')->name('menu.menu.store');
            Route::get('/edit',     'MenuController@edit')->name('menu.menu.edit');
            Route::post('/update',  'MenuController@update')->name('menu.menu.update');
            Route::get('/delete',   'MenuController@delete')->name('menu.menu.delete');
        });
        Route::prefix('media')->group(function () {
            Route::get('/',                 'MediaController@index')->name('media.folder.index');
            Route::get('/folder/store',     'MediaController@folderAdd')->name('media.folder.add');
            Route::post('/folder/update',   'MediaController@folderUpdate')->name('media.folder.update');
            Route::get('/folder',           'MediaController@folder')->name('media.folder');
            Route::post('/folder/move',     'MediaController@folderMove')->name('media.folder.move');
            Route::post('/folder/delete',   'MediaController@folderDelete')->name('media.folder.delete');;

            Route::post('/file/store',      'MediaController@fileAdd')->name('media.file.add');
            Route::get('/file',             'MediaController@file');
            Route::post('/file/delete',     'MediaController@fileDelete')->name('media.file.delete');
            Route::post('/file/update',     'MediaController@fileUpdate')->name('media.file.update');
            Route::post('/file/move',       'MediaController@fileMove')->name('media.file.move');
            Route::post('/file/cropp',      'MediaController@cropp');
            Route::get('/file/copy',        'MediaController@fileCopy')->name('media.file.copy');
        });

        Route::get('listing',               'SellerController@adminListingApproval')->name('listing');
        Route::post('listing/map/menu',     'SellerController@listingMenuMapping')->name('listingMenuMapping');
        Route::post('listing/map/menu/store',     'SellerController@listingMenuMappingStore')->name('listingMenuMappingStore');  
        
    });

    // 
    /*Route::group(['middleware' => ['role:seller']], function () {
        // Store Management Routes
        Route::prefix('seller')->group(function () {
            Route::resource('stores', 'StoreController')->names([
                'index' => 'seller.stores.index',
                'create' => 'seller.stores.create',
                'store' => 'seller.stores.store',
                'show' => 'seller.stores.show',
                'edit' => 'seller.stores.edit',
                'update' => 'seller.stores.update',
                'destroy' => 'seller.stores.destroy',
            ]);
            
            // Store Products Management
            Route::get('stores/{store}/products', 'StoreController@manageProducts')->name('stores.products');
            Route::post('stores/{store}/add-product', 'StoreController@addProductToStore')->name('stores.add-product');
            
            // Individual update routes
            Route::put('stores/{store}/update-quantity/{inventory}', 'StoreController@updateProductQuantity')->name('stores.update-quantity');
            Route::put('stores/{store}/update-price/{inventory}', 'StoreController@updateProductPrice')->name('stores.update-price');
            Route::put('stores/{store}/update-mrp/{inventory}', 'StoreController@updateProductMRP')->name('stores.update-mrp');
            Route::delete('stores/{store}/remove-product/{inventory}', 'StoreController@removeProductFromStore')->name('stores.remove-product');
            
            // Store Employees Management
            Route::prefix('stores/{storeId}')->group(function () {
                Route::get('employees', 'StoreEmployeeController@index')->name('seller.stores.employees');
                Route::get('employees/create', 'StoreEmployeeController@create')->name('seller.stores.employees.create');
                Route::post('employees', 'StoreEmployeeController@store')->name('seller.stores.employees.store');
                Route::get('employees/{employeeId}/edit', 'StoreEmployeeController@edit')->name('seller.stores.employees.edit');
                Route::put('employees/{employeeId}', 'StoreEmployeeController@update')->name('seller.stores.employees.update');
                Route::delete('employees/{employeeId}', 'StoreEmployeeController@destroy')->name('seller.stores.employees.destroy');
            });
        });
        
        // Update listing routes to support store mapping
        Route::get('listing/select-store', 'ListingController@selectStore')->name('listing.selectStore');
        Route::get('listing/create-with-store', 'ListingController@createWithStore')->name('listing.createWithStore');
        Route::post('listing/store-with-store', 'ListingController@storeWithStore')->name('listing.storeWithStore');
    });*/

    Route::group([], function () {
        
        Route::prefix('seller')->name('seller.')->group(function () {
            
            // Store CRUD
            Route::resource('stores', 'StoreController')->except(['destroy']);
            Route::delete('stores/{store}', 'StoreController@destroy')->name('stores.destroy');
            
            // Store Products Management
            Route::get('stores/{store}/products', 'StoreController@manageProducts')->name('stores.products');
            Route::post('stores/{store}/add-product', 'StoreController@addProductToStore')->name('stores.add-product');
            Route::put('stores/{store}/update-quantity/{inventory}', 'StoreController@updateProductQuantity')->name('stores.update-quantity');
            Route::put('stores/{store}/update-price/{inventory}', 'StoreController@updateProductPrice')->name('stores.update-price');
            Route::put('stores/{store}/update-mrp/{inventory}', 'StoreController@updateProductMRP')->name('stores.update-mrp');
            Route::delete('stores/{store}/remove-product/{inventory}', 'StoreController@removeProductFromStore')->name('stores.remove-product');
            
            // Store Employees Management
            Route::get('stores/{store}/employees', 'StoreEmployeeController@index')->name('stores.employees');
            Route::get('stores/{store}/employees/create', 'StoreEmployeeController@create')->name('stores.employees.create');
            Route::post('stores/{store}/employees', 'StoreEmployeeController@store')->name('stores.employees.store');
            Route::get('stores/{store}/employees/{employee}/edit', 'StoreEmployeeController@edit')->name('stores.employees.edit');
            Route::put('stores/{store}/employees/{employee}', 'StoreEmployeeController@update')->name('stores.employees.update');
            Route::delete('stores/{store}/employees/{employee}', 'StoreEmployeeController@destroy')->name('stores.employees.destroy');
        });
    });
});