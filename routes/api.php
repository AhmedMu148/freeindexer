<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BillingController;
use App\Http\Controllers\IndexerController;
use App\Http\Controllers\CentralPaymentWebhookController;
use App\Http\Controllers\Api\V1\CmsPageController;
use App\Http\Controllers\Api\V1\CmsSectionController;


Route::post('paypal/ipn', [BillingController::class, 'ipn']);
Route::post('/webhooks/central-payment', [CentralPaymentWebhookController::class, 'handle'])
    ->name('webhooks.central-payment');
Route::post('/central-payment/webhook', [CentralPaymentWebhookController::class, 'handle'])
    ->name('webhooks.central-payment.old');
// CMS API Routes
Route::prefix('v1/cms')
    ->name('api.cms.')
    ->middleware('cms.auth')
    ->group(function () {
        // Pages
        Route::middleware('cms.scope:cms.pages.read')->group(function () {
            Route::get('pages', [CmsPageController::class, 'index'])->name('pages.index');
            Route::get('pages/{page}', [CmsPageController::class, 'show'])->name('pages.show');
        });

        Route::middleware('cms.scope:cms.pages.write')->group(function () {
            Route::post('pages', [CmsPageController::class, 'store'])->name('pages.store');
            Route::match(['put', 'patch'], 'pages/{page}', [CmsPageController::class, 'update'])->name('pages.update');
        });

        // Sections
        Route::middleware('cms.scope:cms.sections.read')->group(function () {
            Route::get('sections', [CmsSectionController::class, 'index'])->name('sections.index');
            Route::get('sections/{section}', [CmsSectionController::class, 'show'])->name('sections.show');
        });

        Route::middleware('cms.scope:cms.sections.write')->group(function () {
            Route::post('sections', [CmsSectionController::class, 'store'])->name('sections.store');
            Route::match(['put', 'patch'], 'sections/{section}', [CmsSectionController::class, 'update'])->name('sections.update');
        });
    });
