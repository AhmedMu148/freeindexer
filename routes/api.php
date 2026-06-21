<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BillingController;
use App\Http\Controllers\IndexerController;

Route::post('paypal/ipn', [BillingController::class, 'ipn']);
Route::post('central-payment/webhook', [\App\Http\Controllers\CentralPaymentWebhookController::class, 'handle']);

// CMS API Routes
Route::prefix('v1/cms')
    ->name('api.cms.')
    ->middleware('cms.auth')
    ->group(function () {
        // Pages
        Route::middleware('cms.scope:cms.pages.read')->group(function () {
            Route::get('pages', [\App\Http\Controllers\Api\V1\CmsPageController::class, 'index'])->name('pages.index');
            Route::get('pages/{page}', [\App\Http\Controllers\Api\V1\CmsPageController::class, 'show'])->name('pages.show');
        });

        Route::middleware('cms.scope:cms.pages.write')->group(function () {
            Route::post('pages', [\App\Http\Controllers\Api\V1\CmsPageController::class, 'store'])->name('pages.store');
            Route::match(['put', 'patch'], 'pages/{page}', [\App\Http\Controllers\Api\V1\CmsPageController::class, 'update'])->name('pages.update');
        });

        // Sections
        Route::middleware('cms.scope:cms.sections.read')->group(function () {
            Route::get('sections', [\App\Http\Controllers\Api\V1\CmsSectionController::class, 'index'])->name('sections.index');
            Route::get('sections/{section}', [\App\Http\Controllers\Api\V1\CmsSectionController::class, 'show'])->name('sections.show');
        });

        Route::middleware('cms.scope:cms.sections.write')->group(function () {
            Route::post('sections', [\App\Http\Controllers\Api\V1\CmsSectionController::class, 'store'])->name('sections.store');
            Route::match(['put', 'patch'], 'sections/{section}', [\App\Http\Controllers\Api\V1\CmsSectionController::class, 'update'])->name('sections.update');
        });
    });



