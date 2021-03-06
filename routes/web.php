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

$proxy_url    = getenv('PROXY_URL');
$proxy_schema = getenv('PROXY_SCHEMA');

if (!empty($proxy_url)) {
   URL::forceRootUrl($proxy_url);
}

if (!empty($proxy_schema)) {
   URL::forceScheme($proxy_schema);
}

Route::get('/', function () {
    return view('welcome');
});

//Auth::routes();

// Authentication Routes...
Route::get('login', 'Auth\LoginController@showLoginForm')->name('login');
Route::post('login', 'Auth\LoginController@login');
Route::post('logout', 'Auth\LoginController@logout')->name('logout');
// Registration Routes...
//Route::get('register', 'Auth\RegisterController@showRegistrationForm')->name('register');
//Route::post('register', 'Auth\RegisterController@register');
// Password Reset Routes...
Route::get('password/reset', 'Auth\ForgotPasswordController@showLinkRequestForm')->name('password.request');
Route::post('password/email', 'Auth\ForgotPasswordController@sendResetLinkEmail')->name('password.email');
Route::get('password/reset/{token}', 'Auth\ResetPasswordController@showResetForm')->name('password.reset');
Route::post('password/reset', 'Auth\ResetPasswordController@reset');

Route::get('/home', 'HomeController@index')->name('home');
Route::get('/profile', 'UserController@profile')->name('profile');

Route::group(['prefix' => 'resource'], function () {
    Route::post('/company/branches', 'CompanyController@branches')->name('companies.branches');
    Route::post('/company/customers', 'CompanyController@customers')->name('companies.customers');
    Route::post('/branch/emission_points', 'BranchController@emissionPoints')->name('branches.emissionPoints');
    Route::post('/branch/products', 'BranchController@products')->name('branches.products');
    Route::post('/retention_taxes/taxes', 'RetentionTaxController@retentionTaxes')->name('retentionTaxes.taxes');
    Route::post('/retention_taxes/tax_descriptions', 'RetentionTaxController@retentionTaxDescriptions')->name('retentionTaxes.taxDescriptions');
    Route::post('/retention_tax_descriptions/tax_description', 'RetentionTaxDescriptionController@taxDescription')->name('retentionTaxDescriptions.taxDescription');
    Route::post('/company/quotas', 'CompanyController@quotas')->name('companies.quotas');
    Route::group(['middleware' => ['permission:read_products']], function () {
        Route::post('/product/taxes', 'ProductController@taxes')->name('products.taxes');
    });
    Route::group(['middleware' => ['permission:read_customers']], function () {
        Route::post('/customers/customer', 'CustomerController@customers')->name('customers.customer');
    });
    Route::get('/paymentmethods', 'PaymentMethodController@paymentMethods')->name('paymentmethods');
    Route::get('/timeunits', 'TimeUnitController@timeUnits')->name('timeunits');
    Route::post('/ivataxes', 'IvaTaxController@tax')->name('ivataxes');
});

Route::group(['prefix' => 'manage'], function () {
    /**
     * Routes for companies
     */
    Route::post('/companies/store', 'CompanyController@validateRequest')->name('companies.store');
    Route::put('/companies/update/{company}', 'CompanyController@validateRequest')->name('companies.update');
    Route::group(['middleware' => ['permission:read_companies']], function () {
        Route::get('/companies', 'CompanyController@index')->name('companies.index');
    });
    Route::group(['middleware' => ['permission:create_companies']], function () {
        Route::get('/companies/create', 'CompanyController@create')->name('companies.create');
    });
    Route::group(['middleware' => ['permission:read_companies']], function () {
        Route::get('/companies/{company}', 'CompanyController@show')->name('companies.show');
    });
    Route::group(['middleware' => ['permission:update_companies']], function () {
        Route::get('/companies/{company}/edit', 'CompanyController@edit')->name('companies.edit');
    });
    Route::group(['middleware' => ['permission:delete_soft_companies']], function () {
        Route::delete('/companies/{company}/delete', 'CompanyController@delete')->name('companies.delete');
    });
    Route::group(['middleware' => ['permission:delete_hard_companies']], function () {
        Route::delete('/companies/{company}/destroy', 'CompanyController@destroy')->name('companies.destroy');
    });
    Route::group(['middleware' => ['permission:delete_soft_companies']], function () {
        Route::post('/companies/{company}/restore', 'CompanyController@restore')->name('companies.restore');
    });

    /**
      * Routes for branches
      */
    Route::post('/branches/store', 'BranchController@validateRequest')->name('branches.store');
    Route::put('/branches/update/{branch}', 'BranchController@validateRequest')->name('branches.update');
    Route::group(['middleware' => ['permission:read_branches']], function () {
        Route::get('/branches', 'BranchController@index')->name('branches.index');
    });
    Route::group(['middleware' => ['permission:create_branches']], function () {
        Route::get('/branches/create', 'BranchController@create')->name('branches.create');
    });
    Route::group(['middleware' => ['permission:read_branches']], function () {
        Route::get('/branches/{branch}', 'BranchController@show')->name('branches.show');
    });
    Route::group(['middleware' => ['permission:update_branches']], function () {
        Route::get('/branches/{branch}/edit', 'BranchController@edit')->name('branches.edit');
    });
    Route::group(['middleware' => ['permission:delete_soft_branches']], function () {
        Route::delete('/branches/{branch}/delete', 'BranchController@delete')->name('branches.delete');
    });
    Route::group(['middleware' => ['permission:delete_hard_branches']], function () {
        Route::delete('/branches/{branch}/destroy', 'BranchController@destroy')->name('branches.destroy');
    });
    Route::group(['middleware' => ['permission:delete_soft_branches']], function () {
        Route::post('/branches/{branch}/restore', 'BranchController@restore')->name('branches.restore');
    });

    /**
     * Routes for emission points
     */
    Route::post('/emission_points/store', 'EmissionPointController@validateRequest')->name('emission_points.store');
    //Route::put('/emission_points/update/{emission_point}', 'EmissionPointController@validateRequest')->name('emission_points.update');
    Route::group(['middleware' => ['permission:read_emission_points']], function () {
        Route::get('/emission_points', 'EmissionPointController@index')->name('emission_points.index');
    });
    Route::group(['middleware' => ['permission:create_emission_points']], function () {
        Route::get('/emission_points/create', 'EmissionPointController@create')->name('emission_points.create');
    });
    Route::group(['middleware' => ['permission:read_emission_points']], function () {
        Route::get('/emission_points/{emission_point}', 'EmissionPointController@show')->name('emission_points.show');
    });
    Route::group(['middleware' => ['permission:update_emission_points']], function () {
        Route::get('/emission_points/{emission_point}/edit', 'EmissionPointController@edit')->name('emission_points.edit');
    });
    Route::group(['middleware' => ['permission:delete_soft_emission_points']], function () {
        Route::delete('/emission_points/{emission_point}/delete', 'EmissionPointController@delete')->name('emission_points.delete');
    });
    Route::group(['middleware' => ['permission:delete_hard_emission_points']], function () {
        Route::delete('/emission_points/{emission_point}/destroy', 'EmissionPointController@destroy')->name('emission_points.destroy');
    });
    Route::group(['middleware' => ['permission:delete_soft_emission_points']], function () {
        Route::post('/emission_points/{emission_point}/restore', 'EmissionPointController@restore')->name('emission_points.restore');
    });

    /**
     * Routes for customers
     */
    Route::post('/customers/store', 'CustomerController@validateRequest')->name('customers.store');
    Route::put('/customers/update/{customer}', 'CustomerController@validateRequest')->name('customers.update');
    Route::group(['middleware' => ['permission:read_customers']], function () {
        Route::get('/customers', 'CustomerController@index')->name('customers.index');
    });
    Route::group(['middleware' => ['permission:create_customers']], function () {
        Route::get('/customers/create', 'CustomerController@create')->name('customers.create');
    });
    Route::group(['middleware' => ['permission:read_customers']], function () {
        Route::get('/customers/{customer}', 'CustomerController@show')->name('customers.show');
    });
    Route::group(['middleware' => ['permission:update_customers']], function () {
        Route::get('/customers/{customer}/edit', 'CustomerController@edit')->name('customers.edit');
    });
    Route::group(['middleware' => ['permission:delete_soft_customers']], function () {
        Route::delete('/customers/{customer}/delete', 'CustomerController@delete')->name('customers.delete');
    });
    Route::group(['middleware' => ['permission:delete_hard_customers']], function () {
        Route::delete('/customers/{customer}/destroy', 'CustomerController@destroy')->name('customers.destroy');
    });
    Route::group(['middleware' => ['permission:delete_soft_customers']], function () {
        Route::post('/customers/{customer}/restore', 'CustomerController@restore')->name('customers.restore');
    });

    /**
     * Routes for users
     */
    Route::post('/users/store', 'UserController@validateRequest')->name('users.store');
    Route::put('/users/update/{user}', 'UserController@validateRequest')->name('users.update');
    Route::group(['middleware' => ['permission:read_users']], function () {
        Route::get('/users', 'UserController@index')->name('users.index');
    });
    Route::group(['middleware' => ['permission:create_users']], function () {
        Route::get('/users/create', 'UserController@create')->name('users.create');
    });
    Route::group(['middleware' => ['permission:read_users']], function () {
        Route::get('/users/{user}', 'UserController@show')->name('users.show');
    });
    Route::group(['middleware' => ['permission:update_users']], function () {
        Route::get('/users/{user}/edit', 'UserController@edit')->name('users.edit');
    });
    Route::group(['middleware' => ['permission:delete_soft_users']], function () {
        Route::delete('/users/{user}/delete', 'UserController@delete')->name('users.delete');
    });
    Route::group(['middleware' => ['permission:delete_hard_users']], function () {
        Route::delete('/users/{user}/destroy', 'UserController@destroy')->name('users.destroy');
    });
    Route::group(['middleware' => ['permission:delete_soft_users']], function () {
        Route::post('/users/{user}/restore', 'UserController@restore')->name('users.restore');
    });

    /**
     * Routes for products
     */
    Route::post('/products/store', 'ProductController@validateRequest')->name('products.store');
    Route::put('/products/update/{product}', 'ProductController@validateRequest')->name('products.update');
    Route::group(['middleware' => ['permission:read_products']], function () {
        Route::get('/products', 'ProductController@index')->name('products.index');
    });
    Route::group(['middleware' => ['permission:create_products']], function () {
        Route::get('/products/create', 'ProductController@create')->name('products.create');
    });
    Route::group(['middleware' => ['permission:read_products']], function () {
        Route::get('/products/{product}', 'ProductController@show')->name('products.show');
    });
    Route::group(['middleware' => ['permission:update_products']], function () {
        Route::get('/products/{product}/edit', 'ProductController@edit')->name('products.edit');
    });
    Route::group(['middleware' => ['permission:delete_soft_products']], function () {
        Route::delete('/products/{product}/delete', 'ProductController@delete')->name('products.delete');
    });
    Route::group(['middleware' => ['permission:delete_hard_products']], function () {
        Route::delete('/products/{product}/destroy', 'ProductController@destroy')->name('products.destroy');
    });
    Route::group(['middleware' => ['permission:delete_soft_products']], function () {
        Route::post('/products/{product}/restore', 'ProductController@restore')->name('products.restore');
    });

    /**
     * Routes for vouchers
     */
    Route::group(['middleware' => ['permission:create_vouchers']], function () {
        Route::get('/vouchers/{id}', 'VoucherController@getVoucherView')->where('id', '[1-5]{1}');
    });
    Route::group(['middleware' => ['permission:create_vouchers']], function () {
        Route::get('/vouchers/{id}/draft/{voucherId}', 'VoucherController@getDraftVoucherView')->where('id', '[1-5]{1}');
    });
    Route::group(['middleware' => ['permission:create_vouchers']], function () {
        Route::delete('/vouchers/{id}/destroy_draft', 'VoucherController@destroyDraft')->name('vouchers.destroy_draft');
    });
    Route::group(['middleware' => ['permission:create_vouchers']], function () {
        Route::get('/vouchers/{id}/edit_draft', 'VoucherController@editDraft')->name('vouchers.edit_draft');
    });
    Route::group(['middleware' => ['permission:create_vouchers']], function () {
        Route::post('/vouchers/store_draft', 'VoucherController@storeDraft')->name('vouchers.store_draft');
    });
    Route::group(['middleware' => ['permission:create_vouchers']], function () {
        Route::put('/vouchers/{state}/update_draft/{voucherId}', 'VoucherController@updateDraft')->name('vouchers.update_draft');
    });
    Route::group(['middleware' => ['permission:create_vouchers']], function () {
        Route::get('/vouchers/draft', 'VoucherController@indexDraft')->name('vouchers.index_draft');
    });

    //Routes for quotas
    Route::post('/quotas/store', 'QuotasController@validateRequest')->name('quotas.store');
    Route::put('/quotas/update/{quotas}', 'QuotasController@validateRequest')->name('quotas.update');

    Route::group(['middleware' => ['role:admin']], function () {
        Route::get('/quotas', 'QuotasController@index')->name('quotas.index');
    });
    Route::group(['middleware' => ['role:admin']], function () {
        Route::get('/quotas/create', 'QuotasController@create')->name('quotas.create');
    });
    Route::group(['middleware' => ['role:admin']], function () {
        Route::get('/quotas/{quota}', 'QuotasController@show')->name('quotas.show');
    });
    Route::group(['middleware' => ['role:admin']], function () {
        Route::get('/quotas/{quota}/edit', 'QuotasController@edit')->name('quotas.edit');
    });
    Route::group(['middleware' => ['role:admin']], function () {
        Route::delete('/quotas/{quota}/delete', 'QuotasController@delete')->name('quotas.delete');
    });

});

Route::group(['prefix' => 'voucher'], function () {
    Route::post('/send_mail', 'MailController@sendMailVoucher')->name('vouchers.sendmail');
    Route::group(['middleware' => ['permission:report_vouchers']], function () {
        Route::get('/', 'VoucherController@index')->name('vouchers.index');
    });
    Route::group(['middleware' => ['permission:report_vouchers']], function () {
        Route::post('/', 'VoucherController@filter')->name('vouchers.filter');
    });
    Route::group(['middleware' => ['permission:report_vouchers']], function () {
        Route::post('/download', 'VoucherController@download')->name('vouchers.download');
    });
    Route::group(['middleware' => ['permission:create_vouchers']], function () {
        Route::get('/create', 'VoucherController@create')->name('vouchers.create');
    });
    //Route::post('/create/{state}', 'VoucherController@validateRequest')->where('state', '^(?:[1-9]|10)$')->name('vouchers.store');
    Route::group(['middleware' => ['permission:create_vouchers']], function () {
        Route::post('/create/{state}', 'VoucherController@validateRequest')->where('state', '^(?:[1-9]|10)$')->name('vouchers.store');
    });
    Route::group(['middleware' => ['permission:create_vouchers']], function () {
        Route::put('/update/{state}/{id}', 'VoucherController@validateRequest')->where('state', '^(?:[1-9]|10)$')->name('vouchers.update');
    });
    Route::group(['middleware' => ['permission:send_vouchers']], function () {
        Route::post('/send/{voucher}', 'VoucherController@send')->name('vouchers.send');
    });
    Route::group(['middleware' => ['permission:report_vouchers']], function () {
        Route::get('/{voucher}/edit', 'VoucherController@edit')->name('vouchers.edit');
    });
    Route::group(['middleware' => ['permission:report_vouchers']], function () {
        Route::get('/{id}/edit/{voucherId}', 'VoucherController@getVoucherView')->where('id', '[1-5]{1}');
    });
    Route::group(['middleware' => ['permission:report_vouchers']], function () {
        Route::get('/{voucher}/view', 'VoucherController@index')->name('vouchers.view');
    });
    Route::group(['middleware' => ['permission:report_vouchers']], function () {
        Route::get('/{voucher}/html', 'VoucherController@html')->name('vouchers.html');
    });
    Route::group(['middleware' => ['permission:report_vouchers']], function () {
        Route::get('/{voucher}/xml', 'VoucherController@xml')->name('vouchers.xml');
    });
    Route::group(['middleware' => ['permission:report_vouchers']], function () {
        Route::get('/{voucher}/pdf', 'VoucherController@pdf')->name('vouchers.pdf');
    });

});
