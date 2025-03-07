<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\crm\CrmController;

Route::prefix('crm')->group(function () {
    
    Route::middleware('guest')->group(function () {
        Route::get('register', [RegisteredUserController::class, 'create'])
                    ->name('register');
    
        Route::post('register', [RegisteredUserController::class, 'store']);
    
        Route::get('login', [AuthenticatedSessionController::class, 'create'])
                    ->name('login');
    
        Route::post('login', [AuthenticatedSessionController::class, 'store']);
    
        Route::get('forgot-password', [PasswordResetLinkController::class, 'create'])
                    ->name('password.request');
    
        Route::post('forgot-password', [PasswordResetLinkController::class, 'store'])
                    ->name('password.email');
    
        Route::get('reset-password/{token}', [NewPasswordController::class, 'create'])
                    ->name('password.reset');
    
        Route::post('reset-password', [NewPasswordController::class, 'store'])
                    ->name('password.update');
    });
    
    Route::middleware('auth')->group(function () {
        Route::get('verify-email', [EmailVerificationPromptController::class, '__invoke'])
                    ->name('verification.notice');
    
        Route::get('verify-email/{id}/{hash}', [VerifyEmailController::class, '__invoke'])
                    ->middleware(['signed', 'throttle:6,1'])
                    ->name('verification.verify');
    
        Route::post('email/verification-notification', [EmailVerificationNotificationController::class, 'store'])
                    ->middleware('throttle:6,1')
                    ->name('verification.send');
    
        Route::get('confirm-password', [ConfirmablePasswordController::class, 'show'])
                    ->name('password.confirm');
    
        Route::post('confirm-password', [ConfirmablePasswordController::class, 'store']);
    
        Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])
                    ->name('logout');
    });
    
    Route::get('/', [CrmController::class, 'index'])->name('crm.index');



    //-------------------------------dashboard-------------------------------------------

    Route::get('dashboard', [dashboardController::class, 'index'])->name('dashboard')->middleware(
        [
    
            'XSS',
        ]
    );

    //-------------------------------User-------------------------------------------

    Route::resource('customers', UserController::class)->middleware(
        [
            'auth',
            'XSS',
        ]
    );


    //-------------------------------Subscription-------------------------------------------



    Route::group(
        [
            'middleware' => [
                'auth',
                'XSS',
            ],
        ],
        function () {

            Route::resource('subscriptions', SubscriptionController::class);
            Route::get('coupons/history', [CouponController::class, 'history'])->name('coupons.history');
            Route::delete('coupons/history/{id}/destroy', [CouponController::class, 'historyDestroy'])->name('coupons.history.destroy');
            Route::get('coupons/apply', [CouponController::class, 'apply'])->name('coupons.apply');
            Route::resource('coupons', CouponController::class);
            Route::get('subscription/transaction', [SubscriptionController::class, 'transaction'])->name('subscription.transaction');
            Route::post('subscription/{id}/{user_id}/manual-assign-package', [PaymentController::class, 'subscriptionManualAssignPackage'])->name('subscription.manual_assign_package');
        }
    );

    //-------------------------------Subscription Payment-------------------------------------------

    Route::group(
        [
            'middleware' => [
                'auth',
                'XSS',
            ],
        ],
        function () {

            Route::post('subscription/{id}/stripe/payment', [SubscriptionController::class, 'stripePayment'])->name('subscription.stripe.payment');
        }
    );

    //-------------------------------Settings-------------------------------------------
    Route::group(
        [
            'middleware' => [
                'auth',
                'XSS',
            ],
        ],
        function () {
            Route::get('settings', [SettingController::class, 'index'])->name('setting.index');

            Route::post('settings/account', [SettingController::class, 'accountData'])->name('setting.account');
            Route::delete('settings/account/delete', [SettingController::class, 'accountDelete'])->name('setting.account.delete');
            Route::post('settings/password', [SettingController::class, 'passwordData'])->name('setting.password');
            Route::post('settings/general', [SettingController::class, 'generalData'])->name('setting.general');
            Route::post('settings/smtp', [SettingController::class, 'smtpData'])->name('setting.smtp');
            Route::get('settings/smtp-test', [SettingController::class, 'smtpTest'])->name('setting.smtp.test');
            Route::post('settings/smtp-test', [SettingController::class, 'smtpTestMailSend'])->name('setting.smtp.testing');
            Route::post('settings/payment', [SettingController::class, 'paymentData'])->name('setting.payment');
            Route::post('settings/site-seo', [SettingController::class, 'siteSEOData'])->name('setting.site.seo');
            Route::post('settings/google-recaptcha', [SettingController::class, 'googleRecaptchaData'])->name('setting.google.recaptcha');
            Route::post('settings/company', [SettingController::class, 'companyData'])->name('setting.company');
            Route::post('settings/2fa', [SettingController::class, 'twofaEnable'])->name('setting.twofa.enable');

            Route::get('footer-setting', [SettingController::class, 'footerSetting'])->name('footerSetting');
            Route::post('settings/footer', [SettingController::class, 'footerData'])->name('setting.footer');

            Route::get('language/{lang}', [SettingController::class, 'lanquageChange'])->name('language.change');
            Route::post('theme/settings', [SettingController::class, 'themeSettings'])->name('theme.settings');
        }
    );

    Route::group(
        [
            'middleware' => [
                'auth',
            ],
        ],
        function () {
            Route::post('settings/payment', [SettingController::class, 'paymentData'])->name('setting.payment');
        }
    );


    //-------------------------------Role & Permissions-------------------------------------------
    Route::resource('permission', PermissionController::class)->middleware(
        [
            'auth',
            'XSS',
        ]
    );

    Route::resource('role', RoleController::class)->middleware(
        [
            'auth',
            'XSS',
        ]
    );

    //-------------------------------Contact-------------------------------------------
    Route::resource('contact', ContactController::class)->middleware(
        [
            'auth',
            'XSS',
        ]
    );


    //-------------------------------Plan Payment-------------------------------------------

    Route::group(
        [
            'middleware' => [
                'auth',
                'XSS',
            ],
        ],
        function () {

            Route::post('subscription/{id}/bank-transfer', [PaymentController::class, 'subscriptionBankTransfer'])->name('subscription.bank.transfer');
            Route::get('subscription/{id}/bank-transfer/action/{status}', [PaymentController::class, 'subscriptionBankTransferAction'])->name('subscription.bank.transfer.action');
            Route::post('subscription/{id}/paypal', [PaymentController::class, 'subscriptionPaypal'])->name('subscription.paypal');
            Route::get('subscription/{id}/paypal/{status}', [PaymentController::class, 'subscriptionPaypalStatus'])->name('subscription.paypal.status');
            Route::get('subscription/flutterwave/{sid}/{tx_ref}', [PaymentController::class, 'subscriptionFlutterwave'])->name('subscription.flutterwave');
        }
    );

    //-------------------------------Notification-------------------------------------------
    Route::resource('notification', NotificationController::class)->middleware(
        [
            'auth',
            'XSS',
        ]
    );

    Route::get('email-verification/{token}', [VerifyEmailController::class, 'verifyEmail'])->name('email-verification')->middleware(
        [
            'XSS',
    
        ]
    );
});
