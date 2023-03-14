<?php

use App\Http\Controllers\AttributeController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\PlanController;
use App\Http\Controllers\FileController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\LogoutController;
use App\Http\Controllers\RefreshTokenController;
use App\Http\Controllers\ForgetPasswordController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\VoucherController;
use App\Http\Controllers\TagController;
use App\Http\Controllers\TransactionController;

// auth route
Route::prefix('auth')
    ->as('auth')
    ->middleware([
        'guest',
        'throttle:20',
    ])->group(function () {
        Route::post('login', [LoginController::class, 'handle'])->name('.user.login.handle');
        Route::post('resend', [RegisterController::class, 'resend'])->name('.user.resend');
        Route::post('verify', [RegisterController::class, 'verify'])->name('.user.verify');
        Route::post('register', [RegisterController::class, 'handle'])->name('.user.register');
        Route::post('forget-password', [ForgetPasswordController::class, 'forgetPassword'])->name('.user.forget-password');
        Route::post('change-password', [ForgetPasswordController::class, 'changePassword'])->name('.user.change-password');
    });
Route::prefix('auth')
    ->as('auth')
    ->middleware(['auth:sanctum'])
    ->group(function () {
        Route::post('logout', [LogoutController::class, 'logout'])->name('.user.logout');
        Route::get('user', [LoginController::class, 'current_user'])->name('.user.current_user');
        Route::post('refresh', [RefreshTokenController::class, 'refreshToken'])->name('.user.token.refresh');
    });
// end auth route
//panel route
Route::prefix('panel')
    ->as('panel.')
    ->middleware(['auth:sanctum'])
    ->group(function () {
        Route::apiResource('categories', CategoryController::class);
        Route::apiResource('users', UserController::class);
        Route::apiResource('plans', PlanController::class);
        Route::apiResource('files', FileController::class);
        Route::apiResource('comments', CommentController::class)->only(['index', 'show', 'destroy']);
        Route::apiResource('orders', OrderController::class)->except(['update']);
        Route::apiResource('vouchers', VoucherController::class);
        Route::apiResource('tags', TagController::class);
        Route::apiResource('attributes', AttributeController::class);
        Route::apiResource('transactions', TransactionController::class)->only(['inedx', 'show']);
        Route::post('users/{user}/change-avatar', [UserController::class, 'changeAvatar'])->whereNumber('id')->name('.users.avatar');
        Route::patch('users/{user}/change-password', [UserController::class, 'changePassword'])->whereNumber('id')->name('.users.password');
        Route::post('files/{file}/upload-media', [FileController::class, 'uploadFileMedia'])->whereNumber('id')->name('.files.media');

        //file comments
        Route::post('files/{file}/comments', [FileController::class, 'assignComment'])->name('file.comment');
        Route::put('files/{file}/comments/{comment}', [FileController::class, 'updateComment'])->name('file.update.comment');
        Route::patch('files/{file}/generate-download-link', [FileController::class, 'generateS3TemporaryUrl'])->name('file.generate.download.link');
        Route::post('files/{file}/attributes', [FileController::class, 'assignAttributes'])->name('file.attributes');

        //user plan
        Route::post('users/{user}/plans', [UserController::class, 'assignPlan'])->name('user.assign.plan');
        Route::put('users/{user}/plans/{planId}/de-activate', [UserController::class, 'deActivatePlan'])->name('user.inActive.plan');
        Route::get('users/{user}/active-plan', [UserController::class, 'activePlan'])->name('user.activePlan');
        // assign vouchers to user
        Route::post('users/{user}/assign-vouchers', [UserController::class, 'assignVouchers'])->name('user.assign-vouchers');
        //apply voucher
        Route::post('vouchers/apply-voucher', [VoucherController::class, 'apply'])->name('voucher.apply');
        //dashboard details 
        Route::get('dashboard/details', [DashboardController::class, 'details'])->name('dashboard.details');
        //dashboard latest orders 
        Route::get('dashboard/latest-orders', [DashboardController::class, 'latestOrders'])->name('dashboard.latest.orders');
        //dashboard latest comments 
        Route::get('dashboard/latest-comments', [DashboardController::class, 'latestComments'])->name('dashboard.latest.comments');
    });
// end panel route
// profile route
Route::prefix('user')
    ->as('profile')
    ->controller(ProfileController::class)
    ->middleware('auth:sanctum')
    ->group(function () {
        Route::get('profile', 'show')->name('.show');
        Route::put('profile', 'update')->name('.update');
        Route::post('profile/change-avatar', 'changeAvatar')->name('.avatar');
        Route::post('profile/change-password', 'changePassword')->name('.password');
        Route::get('profile/plans', 'plans')->name('.plans');
        Route::get('profile/files', 'files')->name('.files');
        Route::get('profile/orders', 'orders')->name('.orders');
    });
//end profile route

// front route
Route::prefix('frontend')
    ->as('frontend.')
    ->group(function () {
        Route::get('users', [UserController::class, 'index'])->name('users');
        Route::get('files', [FileController::class, 'index'])->name('files');
        Route::get('tags', [TagController::class, 'index'])->name('tags');
        Route::get('files/most-visited', [FileController::class, 'mostVisited'])->name('files.most_visited');
        Route::get('files/{file}', [FileController::class, 'show'])->middleware('viewerCounter')->name('show.files');
        Route::get('plans', [PlanController::class, 'index'])->name('plans');
        Route::get('plans/{plan}', [PlanController::class, 'show'])->name('show.plans');
        Route::get('categories', [CategoryController::class, 'index'])->name('categories');
        Route::get('categories/menubar', [CategoryController::class, 'menubar'])->name('categories.menubar');
        Route::get('apply-voucher-code', [VoucherController::class], 'apply')->name('apply.voucher.code');
        Route::get('plans/{plan}/comments', [PlanController::class, 'CommentsOfPlan'])->name('plan.comments');
        Route::get('files/{file}/comments', [FileController::class, 'CommentsOfFile'])->name('file.comments');
        Route::get('transaction/plan/verify', [PlanController::class, 'verifyTransaction'])->name('plan.verify.transaction');
        Route::get('transaction/order/verify', [OrderController::class, 'verifyTransaction'])->name('file.verify.transaction');

        Route::middleware('auth:sanctum')->group(function () {
            // tracking transaction with uuid
            Route::get('transaction/tracking/{uuid}', [TransactionController::class, 'trackingByUuid'])->name('tracking.transaction');
            Route::post('plans/{plan}/purchase', [PlanController::class, 'buySubscription'])->name('plan.purchase');
            Route::post('files/{file}/reactions', [FileController::class, 'toggleReaction'])->name('file.reaction');
            Route::post('files/{file}/download', [FileController::class, 'download'])->middleware('downloadFile')->name('file.download');
            Route::get('users/{user}/active-plan', [UserController::class, 'activePlan'])->name('user.activePlan');
            Route::get('users/{userId}/files/{fileId}', [UserController::class, 'userHasFile'])->name('user.has.file');
        });
    });
