<?php

use Illuminate\Support\Facades\Route;

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

Route::get('/', function () {
    return view('welcome');
});



Auth::routes();



Route::get('/privacy-policy', [App\Http\Controllers\CMSPageController::class, 'getPrivacyPolicy']);
Route::get('/terms-condition', [App\Http\Controllers\CMSPageController::class, 'getTermsCondition']);
Route::get('/faq', [App\Http\Controllers\CMSPageController::class, 'getFAQ']);
Route::get('/about-us', [App\Http\Controllers\CMSPageController::class, 'getAboutUs']);

//Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Route::group(['middleware' => 'auth'], function(){
Route::get('/dashboard', function () { 
    return view('dashboard'); 
})->name('dashboard');
Route::get('logout', '\App\Http\Controllers\Auth\LoginController@logout');

//Route::group(['middleware' => 'can:manage_role'], function(){   
    Route::get('/roles', [App\Http\Controllers\RolesController::class,'index']);
    Route::get('/role/get-list', [App\Http\Controllers\RolesController::class,'getRoleList']);
    Route::post('/role/create', [App\Http\Controllers\RolesController::class,'create']);
    Route::get('/role/edit/{id}', [App\Http\Controllers\RolesController::class,'edit']);
    Route::post('/role/update', [App\Http\Controllers\RolesController::class,'update']);
    Route::get('/role/delete/{id}', [App\Http\Controllers\RolesController::class,'delete']);
//});


Route::group(['middleware' => 'can:manage_permission'], function(){
    Route::get('/permission', [App\Http\Controllers\PermissionController::class,'index']);
    Route::get('/permission/get-list', [App\Http\Controllers\PermissionController::class,'getPermissionList']);
    Route::post('/permission/create', [App\Http\Controllers\PermissionController::class,'create']);
    Route::get('/permission/update', [App\Http\Controllers\PermissionController::class,'update']);
    Route::get('/permission/delete/{id}', [App\Http\Controllers\PermissionController::class,'delete']);
});

        Route::get('/categories', [App\Http\Controllers\CategoryController::class, 'index'])->name('categories');
        Route::get('/categories/get-list', [App\Http\Controllers\CategoryController::class, 'getList'])->name('category-list-ajax');;
        Route::get('/category/create', [App\Http\Controllers\CategoryController::class, 'create'])->name('category.create');
        Route::post('/category/create', [App\Http\Controllers\CategoryController::class, 'store'])->name('category.store');
        Route::get('/category/edit/{id}', [App\Http\Controllers\CategoryController::class, 'edit'])->name('category.edit');
        Route::post('/category/edit/{id}', [App\Http\Controllers\CategoryController::class, 'update'])->name('category.update');
        Route::get('/category/view/{id}', [App\Http\Controllers\CategoryController::class, 'view'])->name('category.view');
        Route::get('/category/delete/{id}', [App\Http\Controllers\CategoryController::class, 'delete'])->name('category.delete');

        Route::get('/shopkeepers', [App\Http\Controllers\ShopkeeperController::class, 'index'])->name('shopkeepers');
        Route::get('/shopkeepers/get-list', [App\Http\Controllers\ShopkeeperController::class, 'getList'])->name('shopkeeper-list-ajax');;
        Route::get('/shopkeeper/create', [App\Http\Controllers\ShopkeeperController::class, 'create'])->name('shopkeeper.create');
        Route::post('/shopkeeper/create', [App\Http\Controllers\ShopkeeperController::class, 'store'])->name('shopkeeper.store');
        Route::get('/shopkeeper/edit/{id}', [App\Http\Controllers\ShopkeeperController::class, 'edit'])->name('shopkeeper.edit');
        Route::post('/shopkeeper/edit/{id}', [App\Http\Controllers\ShopkeeperController::class, 'update'])->name('shopkeeper.update');
        Route::get('/shopkeeper/view/{id}', [App\Http\Controllers\ShopkeeperController::class, 'view'])->name('shopkeeper.view');
        Route::get('/shopkeeper/delete/{id}', [App\Http\Controllers\ShopkeeperController::class, 'delete'])->name('shopkeeper.delete');

        Route::get('/shops', [App\Http\Controllers\ShopController::class, 'index'])->name('shops');
        Route::get('/shops/get-list', [App\Http\Controllers\ShopController::class, 'getList'])->name('shops-list-ajax');
        Route::get('/shop/create', [App\Http\Controllers\ShopController::class, 'create'])->name('shop.create');
        Route::post('/shop/create', [App\Http\Controllers\ShopController::class, 'store'])->name('shop.store');
        Route::get('/shop/edit/{id}', [App\Http\Controllers\ShopController::class, 'edit'])->name('shop.edit');
        Route::get('/shop/view/{id}', [App\Http\Controllers\ShopController::class, 'view'])->name('shop.view');
        Route::post('/shop/edit/{id}', [App\Http\Controllers\ShopController::class, 'update'])->name('shop.update');
        Route::get('/shop/delete/{id}', [App\Http\Controllers\ShopController::class, 'delete'])->name('shop.delete');

        Route::get('/sub-categories', [App\Http\Controllers\SubcategoryController::class, 'index'])->name('sub-categories');
        Route::get('/sub-categories/get-list', [App\Http\Controllers\SubcategoryController::class, 'getList'])->name('sub-categories-list-ajax');
        Route::get('/sub-category/create', [App\Http\Controllers\SubcategoryController::class, 'create'])->name('sub-category.create');
        Route::post('/sub-category/create', [App\Http\Controllers\SubcategoryController::class, 'store'])->name('sub-category.store');
        Route::get('/sub-category/edit/{id}', [App\Http\Controllers\SubcategoryController::class, 'edit'])->name('sub-category.edit');
        Route::get('/sub-category/view/{id}', [App\Http\Controllers\SubcategoryController::class, 'view'])->name('sub-category.view');
        Route::post('/sub-category/edit/{id}', [App\Http\Controllers\SubcategoryController::class, 'update'])->name('sub-category.update');
        Route::get('/sub-category/delete/{id}', [App\Http\Controllers\SubcategoryController::class, 'delete'])->name('sub-category.delete');

        Route::get('/products', [App\Http\Controllers\ProductController::class, 'index'])->name('products');
        Route::get('/products/get-list', [App\Http\Controllers\ProductController::class, 'getList'])->name('products-list-ajax');
        Route::get('/product/create', [App\Http\Controllers\ProductController::class, 'create'])->name('product.create');
        Route::post('/product/create', [App\Http\Controllers\ProductController::class, 'store'])->name('product.store');
        Route::get('/product/edit/{id}', [App\Http\Controllers\ProductController::class, 'edit'])->name('product.edit');
        Route::post('/product/edit/{id}', [App\Http\Controllers\ProductController::class, 'update'])->name('product.update');
        Route::delete('/product/delete/{id}', [App\Http\Controllers\ProductController::class, 'delete'])->name('product.delete');
        Route::post('/get-shop-by-shopkeeper', [App\Http\Controllers\ProductController::class, 'getShopByShopkeeper'])->name('get-shop-by-shopkeeper');
        Route::post('/get-subcat-by-category', [App\Http\Controllers\ProductController::class, 'getSubcatByCategory'])->name('get-subcat-by-category');
        Route::get('/product/view/{id}', [App\Http\Controllers\ProductController::class, 'view'])->name('product.view');


        Route::get('/discounts', [App\Http\Controllers\DiscountController::class, 'index'])->name('discounts');
        Route::get('/discounts/get-list', [App\Http\Controllers\DiscountController::class, 'getList'])->name('discounts-list-ajax');
        Route::get('/discount/create', [App\Http\Controllers\DiscountController::class, 'create'])->name('discount.create');
        Route::post('/discount/create', [App\Http\Controllers\DiscountController::class, 'store'])->name('discount.store');
        Route::get('/discount/edit/{id}', [App\Http\Controllers\DiscountController::class, 'edit'])->name('discount.edit');
        Route::post('/discount/edit/{id}', [App\Http\Controllers\DiscountController::class, 'update'])->name('discount.update');
        Route::get('/discount/delete/{id}', [App\Http\Controllers\DiscountController::class, 'delete'])->name('discount.delete');
        Route::get('/discount/view/{id}', [App\Http\Controllers\DiscountController::class, 'view'])->name('discount.view');

        Route::group(['prefix' => 'CMS-Pages'], function () {
            Route::get('/', [App\Http\Controllers\CMSPageController::class, 'index'])->name('cmsPages.index');
            Route::get('/create', [App\Http\Controllers\CMSPageController::class, 'create'])->name('cmsPages.create');
            Route::post('/store', [App\Http\Controllers\CMSPageController::class, 'store'])->name('cmsPages.store');
            Route::get('/get-list', [App\Http\Controllers\CMSPageController::class, 'getCMSPageList']);
            Route::get('/edit/{id}', [App\Http\Controllers\CMSPageController::class, 'edit'])->name('cmsPages.edit');
            Route::post('/update/{id}', [App\Http\Controllers\CMSPageController::class, 'update'])->name('cmsPages.update');
        });
    
    });
        

