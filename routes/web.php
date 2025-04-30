<?php

// use App\Http\Controllers\Auth\VerifyEmailController;

// use App\Http\Controllers\Auth\AuthenticatedSessionController;
// use App\Http\Controllers\Auth\VerifyEmailController;
// use App\Http\Controllers\AuthPageController;
// use Illuminate\Support\Facades\Route;
// use App\Http\Controllers\HomeController;
// use App\Http\Controllers\UserController;
// use App\Http\Controllers\SubscriptionController;
// use App\Http\Controllers\SettingController;
// use App\Http\Controllers\PermissionController;
// use App\Http\Controllers\RoleController;
// use App\Http\Controllers\NoticeBoardController;
// use App\Http\Controllers\ContactController;
// use App\Http\Controllers\CouponController;
// use App\Http\Controllers\DocumentController;
// use App\Http\Controllers\CategoryController;
// use App\Http\Controllers\FAQController;
// use App\Http\Controllers\HomePageController;
// use App\Http\Controllers\IsoSpecificationItemController;
// use App\Http\Controllers\IsoSystemController;
// use App\Http\Controllers\NotificationController;
// use App\Http\Controllers\PageController;
// use App\Http\Controllers\SubCategoryController;
// use App\Http\Controllers\TagController;
// use App\Http\Controllers\ReminderController;
// use App\Http\Controllers\PaymentController;
// use App\Http\Controllers\RequestController;
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

// require __DIR__ . '/auth.php';

Route::get('/',function(){
    return redirect('/tenant/login');
});

// Route::get('/', [HomeController::class, 'index'])->middleware(
//     [

//         'XSS',
//     ]
// );
// Route::get('home', [HomeController::class, 'index'])->name('home')->middleware(
//     [

//         'XSS',
//     ]
// );


// //-------------------------------FAQ-------------------------------------------
// Route::resource('FAQ', FAQController::class)->middleware(
//     [
//         'auth',
//         'XSS',
//     ]
// );

// //-------------------------------Home Page-------------------------------------------
// Route::resource('homepage', HomePageController::class)->middleware(
//     [
//         'auth',
//         'XSS',
//     ]
// );
// //-------------------------------FAQ-------------------------------------------
// Route::resource('pages', PageController::class)->middleware(
//     [
//         'auth',
//         'XSS',
//     ]
// );

// //-------------------------------FAQ-------------------------------------------
// Route::resource('authPage', AuthPageController::class)->middleware(
//     [
//         'auth',
//         'XSS',
//     ]
// );


// Route::get('page/{slug}', [PageController::class, 'page'])->name('page');
//-------------------------------FAQ-------------------------------------------
Route::impersonate();

// Add explicit broadcasting auth route
// Broadcast::routes(['middleware' => ['web']]);

Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])
->name('logout');