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

Route::get('/', 'HomePageController@homePage');
Route::get('/ho-tro/', 'HomePageController@homePage');

Auth::routes();

Route::get('/home', 'HomeController@index');
Route::get('/addon/template1', 'Customer\AddonController@get_template1');
Route::post('/cart/action', 'Customer\AddonController@executeAction');
Route::get('/san-pham-da-luu', 'Customer\ProductFavoriteController@indexs');

#region quan ly gio hang
//Route::get('/cart/add', 'Customer\AddonController@addCart');
Route::post('/cart/add', 'Customer\AddonController@addCart');

Route::get('/gio-hang', 'Customer\CartController@showCart');
Route::get('/dat-coc', 'Customer\CartController@showDeposit');
Route::get('/dat-coc-thanh-cong', 'Customer\CartController@depositSuccess');
Route::post('/cart/quantity', 'Customer\CartController@updateQuantity');
Route::post('/cart/shop/service', 'Customer\CartController@updateService');
Route::post('/cart/item/comment', 'Customer\CartController@actionUpdate');
Route::post('/gio-hang/hanh-dong', 'Customer\CartController@action');
Route::delete('/cart/item', 'Customer\CartController@deleteItem');
Route::delete('/cart/shop', 'Customer\CartController@deleteShop');
Route::post('/dat-coc', 'Customer\CartController@depositOrder');
#endregion

#region quan ly nhan vien
Route::get('/user', 'UserController@getUsers');
Route::get('/user/detail/{id}', 'UserController@detailUser');

Route::get('/nhan-vien/{id}', 'Customer\UserController@detail');
Route::post('/nhan-vien/dien-thoai', 'Customer\UserController@add_user_phone');
Route::put('/nhan-vien/dien-thoai', 'Customer\UserController@delete_user_phone');
Route::get('/nhan-vien/sua/{id}', 'Customer\UserController@get_user');
Route::post('/nhan-vien/sua/{id}', 'Customer\UserController@update_user');

Route::get('/user/edit/{id}', 'UserController@getUser');
Route::post('/user/edit/{id}', 'UserController@updateUser');
Route::post('/user/phone', 'UserController@addUserPhone');
Route::put('/user/phone', 'UserController@deleteUserPhone');
Route::get('/user/original_site', 'UserController@listUserOriginalSite');
Route::post('/user/original_site', 'UserController@addUserOriginalSite');
Route::put('/user/original_site/delete', 'UserController@removeUserOriginalSite');
#endregion

#region quan ly dia chi nhan hang
Route::post('/user/address', 'Customer\UserAddressController@addNewUserAddress');
Route::put('/user/address/delete', 'Customer\UserAddressController@deleteUserAddress');
Route::put('/user/address/default', 'Customer\UserAddressController@setDefaultUserAddress');
#endregion

#region -- quet ma vach --
Route::get('/scan', 'ScanController@indexs');
Route::post('/scan/action', 'ScanController@action');
#endregion

#region -- kien hang --
Route::get('/packages', 'PackageController@indexs');
Route::get('/package', 'PackageController@index');
Route::post('/package/action', 'PackageController@action');
Route::get('/package/{code}', 'PackageController@detail');
#endregion

#region quan ly don hang
Route::get('/order', 'OrderController@orders');
Route::get('/order/{id}', 'OrderController@order');
Route::get('/order/detail/{id}', 'OrderController@order');
Route::post('/order/{id}/freight_bill', 'OrderController@insertFreightBill');
Route::put('/order/{id}/freight_bill', 'OrderController@removeFreightBill');
Route::post('/order/{id}/original_bill', 'OrderController@insertOriginalBill');
Route::put('/order/{id}/original_bill', 'OrderController@removeOriginalBill');
Route::post('/order/{id}/action', 'OrderController@action');

#endregion

#region comment
Route::post('/comment', 'CommentController@action');
#endregion

#region he thong
Route::get('/setting', 'SystemConfigController@getList');
Route::post('/setting', 'SystemConfigController@update');

Route::get('/setting/roles', 'SystemConfigController@roles');
Route::get('/setting/role/{id}', 'SystemConfigController@roleDetail');
Route::post('/setting/role/update/{id}', 'SystemConfigController@updateRole');
Route::post('/setting/role', 'SystemConfigController@addRole');
Route::post('/setting/role/permission', 'SystemConfigController@savePermission');
Route::post('/setting/role/user', 'SystemConfigController@updateUserRole');
Route::put('/setting/role/delete', 'SystemConfigController@deleteRole');

//======== Warehouse ==========
Route::get('/warehouses', 'WarehouseController@render');
Route::post('/warehouse', 'WarehouseController@insert');
Route::put('/warehouse/delete', 'WarehouseController@delete');

Route::get('/warehouses_manually', 'WarehouseController@render_manually');
Route::post('/warehouses_manually', 'WarehouseController@insert_manually');
Route::put('/warehouses_manually/delete', 'WarehouseController@delete_manually');

#endregion

#region -- giao dich --
Route::get('transaction/statistic', 'UserTransactionController@statisticTransaction');
Route::get('transactions', 'UserTransactionController@getTransactions');
Route::get('transaction/adjustment', 'UserTransactionController@renderTransactionAdjustment');
Route::post('transaction/adjustment', 'UserTransactionController@createTransactionAdjustment');
#endregion

#region chuc nang nhap/xuat kho cua giang
Route::get('warehouse','ExportWarehouseController@index');
Route::post('actionWarehouse', 'ExportWarehouseController@actionWarehouse');
#endregion

Route::get('/404', 'OtherController@renderPageNotFound');
Route::get('/403', 'OtherController@renderPageNotPermission');
Route::get('/vue', 'OtherController@renderExampleVue');

//================ CUSTOMER ==============
#region -- giao dich --
Route::get('giao-dich', 'Customer\UserTransactionController@getTransactions');
#endregion

#region -- don hang --
Route::get('/don-hang', 'Customer\OrderController@orders');
Route::get('/don-hang/{id}', 'Customer\OrderController@order');
Route::post('/don-hang/{id}/hanh-dong', 'Customer\OrderController@action');
#endregion

#region -- thong bao --
Route::get('/thong-bao', 'Customer\NotificationController@indexs');
#endregion

#region -- bai viet --
Route::get('/taxonomies', 'TaxonomyController@indexs');
Route::get('/taxonomy', 'TaxonomyController@createTaxonomy');

Route::get('/posts', 'PostController@indexs');
Route::get('/post', 'PostController@createPost');
Route::get('/post/{id}', 'PostController@createPost');
Route::get('/post/preview/{id}', 'PostController@previewPost');
Route::post('/post/action', 'PostController@action');

#endregion

#region -- ho tro --
Route::get('/ho-tro/danh-muc/{id}', 'Support\TaxonomyController@indexs');
Route::get('/ho-tro/{id}', 'Support\PostController@index');
#endregion

