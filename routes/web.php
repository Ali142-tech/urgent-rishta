<?php
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;
use app\MasterData;  // Import the Package model
use Illuminate\Support\Facades\Artisan;

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
Auth::routes(['verify' => true]);

Route::get('/info', function () {
    phpinfo();
});

// index/home routes
Route::get('/', [App\Http\Controllers\HomeController::class, 'index'])->name('index');
Route::get('home', [App\Http\Controllers\HomeController::class, 'home'])->name('home');
Route::get('states/{id}', [App\Http\Controllers\HomeController::class, 'states']);
Route::get('cities/{id}/{iscountry}', [App\Http\Controllers\HomeController::class, 'cities']);
Route::post('member/searchresults/{refresh?}', [App\Http\Controllers\HomeController::class, 'search'])->name('searchresults');

// Admin page routes
Route::get('admin/profiles', [App\Http\Controllers\AdminController::class, 'profiles']); // route to index which will list profiles
Route::post('admin/profiles/refresh', [App\Http\Controllers\AdminController::class, 'refreshProfiles']); // list all profiles in admin dashboard
Route::get('admin/interests', [App\Http\Controllers\AdminController::class, 'interests']);
Route::post('admin/interests/refresh', [App\Http\Controllers\AdminController::class, 'refreshInterests']); // list all interests in admin dashboard
Route::get('admin/packages', [App\Http\Controllers\AdminController::class, 'packages']);
Route::get('admin/packages/modal/{id?}', [App\Http\Controllers\AdminController::class, 'renderPackagesModal']);
Route::get('admin/package-subscribers', [App\Http\Controllers\AdminController::class, 'packageSubscribers']);
Route::post('admin/package-subscribers/refresh', [App\Http\Controllers\AdminController::class, 'refreshPackageSubscribers']);
Route::get('admin/appointments', [App\Http\Controllers\AdminController::class, 'appointments']);
Route::post('admin/appointments/refresh', [App\Http\Controllers\AdminController::class, 'refreshAppointments']);
// admin profile routes
Route::delete('admin/profile/{id}',[App\Http\Controllers\AdminController::class, 'deleteProfile']); // delete profile in admin dashboard
Route::get('admin/profile/toggle/{user}', [App\Http\Controllers\AdminController::class, 'toggleActive']); // toggle status of profile in admin dashboard
Route::get('admin/profile/resendemail/{id}',[App\Http\Controllers\AdminController::class, 'resendVerificationEmail']); // send email verification email to profile in admin dashboard
Route::get('admin/profile/requestreset/{id}',[App\Http\Controllers\AdminController::class, 'requestPasswordReset']); // send password reset email to profile in admin dashboard
Route::post('admin/profile/updatepackage/{id}',[App\Http\Controllers\AdminController::class, 'updateProfilePackage']); // update package for profile in admin dashboard
Route::get('admin/profile/listing/{type}/{id}', [App\Http\Controllers\AdminController::class, 'showListingModal']);
Route::get('admin/profile/package/modal/{id}', [App\Http\Controllers\AdminController::class, 'renderUpdatePackageModal']);
// admin package routes
Route::post('admin/packages/',[App\Http\Controllers\AdminController::class, 'addPackage']); // add a new package in admin dashboard
Route::post('admin/packages/{id}',[App\Http\Controllers\AdminController::class, 'updatePackage']); // update package details in admin dashboard
Route::delete('admin/packages/{id}',[App\Http\Controllers\AdminController::class, 'deletePackage']); // delete package in admin dashboard
//fix dataid route
Route::get('admin/fix-dataid/{table}', [App\Http\Controllers\AdminController::class, 'fixDataId']);
//generate thumnails route
Route::get('admin/generate-thumbnails', [App\Http\Controllers\AdminController::class, 'generateThumbnails']);
//generate blurs route
Route::get('admin/generate-blurs', [App\Http\Controllers\AdminController::class, 'generateBlurs']);

Route::get('admin/phpinfo', [App\Http\Controllers\AdminController::class, 'phpInfo']);
Route::get('admin/art-optimize', [App\Http\Controllers\AdminController::class, 'artisanOptimize']);
Route::get('admin/art-all-clear', [App\Http\Controllers\AdminController::class, 'artisanAllClear']);
Route::get('admin/publish-image', [App\Http\Controllers\AdminController::class, 'registerPublishImageVendor']);

// check email in use route
Route::post('eiu', [App\Http\Controllers\Auth\RegisterController::class, 'emailInUse']);

// profile routes
Route::get('member/profile',[App\Http\Controllers\ProfileController::class, 'profile']);
Route::get('member/profile/{id?}',[App\Http\Controllers\ProfileController::class, 'profile']);
Route::get('member/profile/notifications/refresh', [App\Http\Controllers\ProfileController::class, 'notifications']);
Route::post('member/profile/account/terminate', [App\Http\Controllers\ProfileController::class, 'accountTerminate']);
Route::get('member/profile/password/update', [App\Http\Controllers\ProfileController::class, 'passwordUpdate']);
Route::post('member/profile/password/update', [App\Http\Controllers\ProfileController::class, 'updatePassword'])->name('change.password');
// profile images
Route::get('member/profile/images/modal',[App\Http\Controllers\ProfileController::class, 'renderImagesModal']);
Route::post('member/profile/images/update/{action}/{id}',[App\Http\Controllers\ProfileController::class, 'updateImage']);
// profile update
Route::post('member/profile/update/{section}',[App\Http\Controllers\ProfileController::class, 'updateProfile']);
Route::post('member/profile/update/images/upload',[App\Http\Controllers\ProfileController::class, 'uploadImages']);
// filtered processing route
Route::post('member/profile/filtered/{action}/{type}/{id}', [App\Http\Controllers\ProfileController::class, 'updateFiltered']);
// interest route
Route::post('member/profile/interest/{action}/{id}', [App\Http\Controllers\ProfileController::class, 'updateInterest']);
Route::post('member/profile/interest/{action}/{id}/{who}', [App\Http\Controllers\ProfileController::class, 'updateInterest']);
// profile listing view
Route::get('member/profile/listing/{type}', [App\Http\Controllers\ProfileController::class, 'showListing']);
// profile links
// Route::get('shortlist', [App\Http\Controllers\ProfileController::class, 'shortlistView']);
// Route::get('followed', [App\Http\Controllers\ProfileController::class, 'followedView']);
// Route::get('ignored', [App\Http\Controllers\ProfileController::class, 'ignoredView']);
// Route::get('privacy', [App\Http\Controllers\ProfileController::class, 'privacyView']);
// Route::get('messaging', [App\Http\Controllers\ProfileController::class, 'messagingView']);
// Route::get('member', [App\Http\Controllers\ProfileController::class, 'memberView']);
// non dynamic routes
Route::get('packages', [App\Http\Controllers\HomeController::class, 'packagesView']);
Route::get('packages/checkout/{id}', [App\Http\Controllers\PaymentController::class, 'startOnlinePackageCheckout'])
    ->middleware(['auth', 'verified'])
    ->name('packages.checkout');

Route::get('payment/stripe/success/{reference}', [App\Http\Controllers\PaymentController::class, 'stripeSuccess'])
    ->name('stripe.success');
Route::get('payment/stripe/cancel/{reference}', [App\Http\Controllers\PaymentController::class, 'stripeCancel'])
    ->name('stripe.cancel');
Route::get('stories', [App\Http\Controllers\HomeController::class, 'storiesView']);
Route::get('faqs', [App\Http\Controllers\HomeController::class, 'faqsView']);
Route::get('tandc', [App\Http\Controllers\HomeController::class, 'termsAndConditionsView']);
Route::get('privacy', [App\Http\Controllers\HomeController::class, 'privacyPolicyView']);
Route::get('contact-us', [App\Http\Controllers\HomeController::class, 'contactUsView']);
Route::post('contact-us',[App\Http\Controllers\HomeController::class, 'contactUsEmail']);

// Appointments (auth required)
Route::get('appointments', [App\Http\Controllers\AppointmentController::class, 'index'])->name('appointments.index');
Route::post('appointments', [App\Http\Controllers\AppointmentController::class, 'store'])->name('appointments.store');
Route::post('appointments/{id}/cancel', [App\Http\Controllers\AppointmentController::class, 'cancel'])->name('appointments.cancel');

// New route for package details

// Route::get('packages/{id}', function ($id) {
//     // Fetch package by id
//     $package = App\MasterData::findOrFail($id);
//     return view('package.show', compact('package'));
// })->name('packages.show');


// Route::get('package_detail/{id}', function ($id) {
//     $package = Package::findOrFail($id);  // Fetch the package by ID
//     return view('package_detail', compact('package'));  // Pass the package to the view
// });
// Add this route to your web.php

Route::get('package-details/{id}', [App\Http\Controllers\PackageController::class, 'showPackageDetails']);




Route::get('optimize-all', function () {
    try {
        Artisan::call('config:cache');
        Artisan::call('route:cache');
        Artisan::call('view:cache');
        Artisan::call('optimize');

        return response()->json([
            'status' => 'success',
            'message' => 'Application optimized successfully!'
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'status' => 'error',
            'message' => $e->getMessage()
        ]);
    }
});

Route::get('clear-all', function () {
    try {
        Artisan::call('cache:clear');
        Artisan::call('config:clear');
        Artisan::call('route:clear');
        Artisan::call('view:clear');
        Artisan::call('optimize:clear');

        return response()->json([
            'status' => 'success',
            'message' => 'All caches cleared successfully!'
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'status' => 'error',
            'message' => $e->getMessage()
        ]);
    }
});

