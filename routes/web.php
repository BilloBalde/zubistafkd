<?php

use App\Http\Controllers\Authentification;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CurrencySettingController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\EmailController;
use App\Http\Controllers\ExpenseCategoryController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\PaymentSettingController;
use App\Http\Controllers\PlaceController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\StoreController;
use App\Http\Controllers\StoreProductController;
use App\Http\Controllers\BaseDonneeController;
use App\Http\Controllers\IndexController;
use App\Models\Store;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Artisan;
use App\Http\Controllers\VisitController;
// --- E-COMMERCE ROUTES ---
use App\Http\Controllers\Ecommerce\AuthOtpController;
use App\Http\Controllers\Ecommerce\AddressController;
use App\Http\Controllers\Ecommerce\OrderController;
use App\Http\Controllers\Ecommerce\CartController;

use App\Http\Controllers\FactureController;
use App\Http\Controllers\LogisticController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\SaleController;

use App\Exports\ProductsExport;
use App\Exports\PurchasesExport;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Http\Controllers\PhoneAuthController;
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
Route::post('password/email', [ForgotPasswordController::class,'sendResetLinkEmail'])->name('password.email');
Route::post('password/reset', [ForgotPasswordController::class,'showLinkRequestForm']);
Route::get('password/reset/{token}/{email}', [ForgotPasswordController::class,'showResetForm'])->name('password.reset');
Route::post('password/misajour', [ForgotPasswordController::class, 'reset'])->name('password.update');

Route::get('/home', [IndexController::class, 'index'])->middleware('auth.check')->name('home');


Route::get('/beauty', [VisitController::class, 'index']);

Route::post('/send-verification-code', [PhoneAuthController::class, 'sendCode'])->name('send.code');
Route::post('/verify-and-login', [PhoneAuthController::class, 'verifyAndLogin'])->name('verify.login');
Route::post('/logout', [PhoneAuthController::class, 'logout'])->name('logout');
Route::get('/about', function () {
    return view('visitor.about', ['pageName' => 'about-page']);
})->name('about');

Route::get('/product', function () {
    return view('visitor.productDetail', ['pageName' => 'project-details-page']);
})->name('productDetail');

Route::get('/contact', function () {
    return view('visitor.contact', ['pageName' => 'contact-page']);
})->name('contact');

Route::get('/logout', function () {
    Auth::forgetUser();
    Auth::logoutCurrentDevice();
    return redirect()->route('accueil')->with('success', 'User logged out');
})->name('logout');

Route::get('/login', [Authentification::class, 'login'])->name('login');
Route::get('/register', [Authentification::class, 'register'])->name('addUser');
Route::put('/updateUser/{id}', [Authentification::class, 'update'])->name('updateUser');
Route::get('/edit/{user}', [Authentification::class, 'edit'])->name('editUser');
Route::delete('/deleteUser/{id}', [Authentification::class, 'destroy'])->name('deleteUser');
Route::get('/forgotPass', [Authentification::class, 'forgotPass'])->name('forgotPass');
Route::get('/registration/verification/{token}/{email}', [Authentification::class, 'registration_verify'])->name('verification');
Route::post('/register', [Authentification::class, 'create'])->name('enregistrer');
Route::post('/login_submit', [Authentification::class, 'login_submit'])->name('login_submit');
Route::post('/passwordRecovery', [Authentification::class, 'passwordRecovery'])->name('passwordRecovery');
Route::post('/sendEmail', [Authentification::class, 'sendEmail'])->name('sendEmail');
Route::get('/profile', [ProfileController::class, 'index'])->name('profile');
Route::get('/emailSetting', [EmailController::class, 'index'])->name('emailSetting');


Route::get('/panier', [CartController::class, 'show'])->name('panier');
Route::post('/cart/add/{id}', [CartController::class, 'add'])->name('cart.add');
Route::get('/cart/remove/{id}', [CartController::class, 'remove'])->name('cart.remove');

Route::prefix('shop')->group(function () {
    Route::get('/register', [AuthOtpController::class, 'showRegister'])->name('otp.register');
    Route::post('/register', [AuthOtpController::class, 'register'])->name('otp.register_submit');
    Route::get('/login', [AuthOtpController::class, 'showLogin'])->name('otp.login');
    Route::post('/login', [AuthOtpController::class, 'login'])->name('otp.login_submit');
    Route::get('/verify', [AuthOtpController::class, 'showVerify'])->name('otp.verify');
    Route::post('/verify-otp', [AuthOtpController::class, 'verify'])->name('otp.verify_submit');
    Route::post('/logout', [AuthOtpController::class, 'logout'])->name('shop.logout');

    Route::middleware(['auth', 'customer'])->group(function () {
        Route::get('/', [VisitController::class, 'shop'])->name('shop.home');
        Route::get('/addresses', [AddressController::class, 'index'])->name('addresses.index');
        Route::get('/addresses/create', [AddressController::class, 'create'])->name('addresses.create');
        Route::post('/addresses', [AddressController::class, 'store'])->name('addresses.store');
        Route::delete('/addresses/{id}', [AddressController::class, 'destroy'])->name('addresses.destroy');
        Route::get('/checkout', [OrderController::class, 'checkout'])->name('checkout');
        Route::post('/orders', [OrderController::class, 'store'])->name('orders.store');
        Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
        Route::get('/orders/{id}', [OrderController::class, 'show'])->name('orders.show');
    });
});


// routes/web.php
Route::middleware(['auth', 'superuser'])->prefix('admin')->group(function () {
    Route::get('/orders', [App\Http\Controllers\Admin\OrderManagementController::class, 'index'])->name('admin.orders.index');
    Route::get('/orders/confirmed', [App\Http\Controllers\Admin\OrderManagementController::class, 'confirmed'])->name('admin.orders.confirmed');
    Route::post('/orders/{order}/approve', [App\Http\Controllers\Admin\OrderManagementController::class, 'approve'])->name('admin.orders.approve');
    Route::post('/orders/{order}/reject', [App\Http\Controllers\Admin\OrderManagementController::class, 'reject'])->name('admin.orders.reject');
});

Route::post('/profileImage', [ProfileController::class, 'profileImage'])->name('profileImage');
Route::post('/profileInfo', [ProfileController::class, 'profileInfo'])->name('profileInfo');
Route::post('/password', [ProfileController::class, 'passwordupdate'])->name('passwordupdate');
Route::resource('users', UserController::class);
Route::resource('paymentSetting', PaymentSettingController::class);
Route::resource('currencySetting', CurrencySettingController::class);
Route::resource('roles', RoleController::class);
Route::get('/places', [PlaceController::class, 'index'])->name('places.index');
Route::get('/places/{place}', [PlaceController::class, 'edit'])->name('places.edit');
Route::get('/createplace', [PlaceController::class, 'create'])->name('places.create');
Route::post('/createplace', [PlaceController::class, 'store'])->name('places.store');
Route::delete('/places/{id}', [PlaceController::class, 'destroy'])->name('places.destroy');
Route::put('/updateplace/{place}', [PlaceController::class, 'update'])->name('places.update');
Route::get('/customers', [CustomerController::class, 'index'])->name('customers.index');
Route::get('/createcustomer', [CustomerController::class, 'create'])->name('customers.create');
Route::post('/createcustomer', [CustomerController::class, 'store'])->name('customers.store');
Route::get('/customers/{customer}', [CustomerController::class, 'edit'])->name('customers.edit');
Route::put('/customers/{customer}', [CustomerController::class, 'update'])->name('customers.update');
Route::resource('boutiques', StoreController::class);
Route::resource('expensesCategory', ExpenseCategoryController::class);
Route::resource('expenses', ExpenseController::class);

Route::resource('categories', CategoryController::class);
Route::resource('produits', ProductController::class);
Route::resource('logistics', LogisticController::class);
Route::resource('purchases', PurchaseController::class);
Route::get('/purchases/ajout/{numeroPurchase}/{quantity}/{store}', [PurchaseController::class, 'ajout'])->name('purchases.ajout');
Route::resource('factures', FactureController::class);
Route::resource('sales', SaleController::class);
Route::get('/sales/ajout/{numero_facture}/{avance}/{store_id}', [SaleController::class, 'ajout'])->name('sales.ajout');
Route::resource('payments', PaymentController::class);
Route::get('/facture/payment/{id}', [PaymentController::class, 'creation'])->name('payments.creation');
Route::get('/facture/voirPayment/{id}', [PaymentController::class, 'voir'])->name('payments.voir');




Route::get('/products/export-excel', function () {
    return Excel::download(new ProductsExport, 'products.xlsx');
})->name('products.export-excel');

Route::get('/products/export-pdf', function () {
    $dataTable = App\Models\Product::with('categories', 'stores')->get();
    $userStoreId = auth()->user()->role_id == 3
            ? Store::where('user_id', auth()->user()->id)->value('id')
            : null;
    $pdf = Pdf::loadView('products.export', compact('dataTable', 'userStoreId'));
    return $pdf->download('products.pdf');
})->name('products.export-pdf');

Route::get('/purchases/export-excel', function () {
    return Excel::download(new PurchasesExport, 'purchases.xlsx');
})->name('purchases.export-excel');

Route::get('/purchases/export-pdf', function () {
    $dataTable = App\Models\Purchase::all();
    $pdf = Pdf::loadView('purchases.export', compact('dataTable'));
    return $pdf->download('purchases.pdf');
})->name('purchases.export-pdf');


Route::get('/logistics/export-excel', [LogisticController::class, 'exportExcel'])->name('logistics.export-excel');
Route::get('/logistics/export-pdf', [LogisticController::class, 'exportPDF'])->name('logistics.export-pdf');

Route::post('/customers/search', [CustomerController::class, 'search'])->name('customers.search');

Route::post('/purchases/exitAchat/{numeroPurchase}', [PurchaseController::class, 'exitAchat'])->name('exitPurchase');
Route::post('/sales/exitSale/{numero_facture}', [SaleController::class, 'exitSale'])->name('exitSale');

Route::resource('transfers', StoreProductController::class);
Route::get('/baseDonnee', [BaseDonneeController::class, 'index'])->name('baseDonnee.index');
Route::post('/baseDonnee/{id}/delete', [BaseDonneeController::class, 'delete'])->name('deleteLines');

Route::get('/clear-cache', function() {
    Artisan::call('cache:clear');
    Artisan::call('config:clear');
    Artisan::call('route:clear');
    Artisan::call('view:clear');
    return view('refreshed');
})->name('clear-cache');

Route::get('/receipts/transfers/{transfer}', [StoreProductController::class, 'transfer'])->name('receipts.transfers.show');
Route::post('/compagnie', [ProfileController::class, 'companyCreate'])->name('companyCreate');
Route::get('pos', [SaleController::class, 'pos'])->name('pos');
Route::post('pos/storeCustomer', [SaleController::class,'storeCustomer'])->name('pos.storeCustomer');
Route::get('/sales/voir/{numero_facture}', [SaleController::class, 'voirSales'])->name('voirSales');
Route::get('/', [VisitController::class, 'index'])->name('accueil');
Route::get('/products', [VisitController::class, 'indexProducts'])->name('products.index');
Route::post('/produits/load-more', [VisitController::class, 'loadMoreProducts'])->name('products.loadMore');





