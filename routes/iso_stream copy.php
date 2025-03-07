<!-- <?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\IsoStreamController;

Route::prefix('iso_stream')->group(function () {

    //-------------------------------User-------------------------------------------

    Route::resource('users', UserController::class)->middleware(
        [
            'auth',
            'XSS',
        ]
    );


    // //-------------------------------Settings-------------------------------------------
    // Route::group(
    //     [
    //         'middleware' => [
    //             'auth',
    //             'XSS',
    //         ],
    //     ],
    //     function () {
    //         Route::get('settings', [SettingController::class, 'index'])->name('setting.index');

    //         Route::post('settings/account', [SettingController::class, 'accountData'])->name('setting.account');
    //         Route::delete('settings/account/delete', [SettingController::class, 'accountDelete'])->name('setting.account.delete');
    //         Route::post('settings/password', [SettingController::class, 'passwordData'])->name('setting.password');
    //         Route::post('settings/general', [SettingController::class, 'generalData'])->name('setting.general');
    //         Route::post('settings/smtp', [SettingController::class, 'smtpData'])->name('setting.smtp');
    //         Route::get('settings/smtp-test', [SettingController::class, 'smtpTest'])->name('setting.smtp.test');
    //         Route::post('settings/smtp-test', [SettingController::class, 'smtpTestMailSend'])->name('setting.smtp.testing');
    //         Route::post('settings/payment', [SettingController::class, 'paymentData'])->name('setting.payment');
    //         Route::post('settings/site-seo', [SettingController::class, 'siteSEOData'])->name('setting.site.seo');
    //         Route::post('settings/google-recaptcha', [SettingController::class, 'googleRecaptchaData'])->name('setting.google.recaptcha');
    //         Route::post('settings/company', [SettingController::class, 'companyData'])->name('setting.company');
    //         Route::post('settings/2fa', [SettingController::class, 'twofaEnable'])->name('setting.twofa.enable');

    //         Route::get('footer-setting', [SettingController::class, 'footerSetting'])->name('footerSetting');
    //         Route::post('settings/footer', [SettingController::class, 'footerData'])->name('setting.footer');

    //         Route::get('language/{lang}', [SettingController::class, 'lanquageChange'])->name('language.change');
    //         Route::post('theme/settings', [SettingController::class, 'themeSettings'])->name('theme.settings');
    //     }
    // );


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

    //-------------------------------Note-------------------------------------------
    Route::resource('note', NoticeBoardController::class)->middleware(
        [
            'auth',
            'XSS',
        ]
    );

    //-------------------------------logged History-------------------------------------------

    Route::group(
        [
            'middleware' => [
                'auth',
                'XSS',
            ],
        ],
        function () {

            Route::get('logged/history', [UserController::class, 'loggedHistory'])->name('logged.history');
            Route::get('logged/{id}/history/show', [UserController::class, 'loggedHistoryShow'])->name('logged.history.show');
            Route::delete('logged/{id}/history', [UserController::class, 'loggedHistoryDestroy'])->name('logged.history.destroy');
        }
    );


    //-------------------------------Document-------------------------------------------

    Route::group(
        [
            'middleware' => [
                'auth',
                'XSS',
            ],
        ],
        function () {
            Route::get('document/history', [DocumentController::class, 'history'])->name('document.history');
            Route::resource('document', DocumentController::class);
            Route::get('my-document', [DocumentController::class, 'myDocument'])->name('document.my-document');
            Route::get('document/{id}/comment', [DocumentController::class, 'comment'])->name('document.comment');
            Route::post('document/{id}/comment', [DocumentController::class, 'commentData'])->name('document.comment');
            Route::get('document/{id}/reminder', [DocumentController::class, 'reminder'])->name('document.reminder');
            Route::get('document/{id}/add-reminder', [DocumentController::class, 'addReminder'])->name('document.add.reminder');
            Route::get('document/{id}/version-history', [DocumentController::class, 'versionHistory'])->name('document.version.history');
            Route::post('document/{id}/version-history', [DocumentController::class, 'newVersion'])->name('document.new.version');
            Route::get('document/{id}/share', [DocumentController::class, 'shareDocument'])->name('document.share');
            Route::post('document/{id}/share', [DocumentController::class, 'shareDocumentData'])->name('document.share');
            Route::get('document/{id}/add-share', [DocumentController::class, 'addshareDocumentData'])->name('document.add.share');
            Route::delete('document/{id}/share/destroy', [DocumentController::class, 'shareDocumentDelete'])->name('document.share.destroy');
            Route::get('document/{id}/send-email', [DocumentController::class, 'sendEmail'])->name('document.send.email');
            Route::post('document/{id}/send-email', [DocumentController::class, 'sendEmailData'])->name('document.send.email');
            Route::get('logged/history', [DocumentController::class, 'loggedHistory'])->name('logged.history');
            Route::get('logged/{id}/history/show', [DocumentController::class, 'loggedHistoryShow'])->name('logged.history.show');
            Route::delete('logged/{id}/history', [DocumentController::class, 'loggedHistoryDestroy'])->name('logged.history.destroy');
        }
    );



    //-------------------------------Reminder-------------------------------------------

    Route::group(
        [
            'middleware' => [
                'auth',
                'XSS',
            ],
        ],
        function () {
            Route::resource('reminder', ReminderController::class);
            Route::get('my-reminder', [ReminderController::class, 'myReminder'])->name('my-reminder');
        }
    );
    //-------------------------------Requests-------------------------------------------

    Route::group(
        [
            'middleware' => [
                'auth',
                'XSS',
            ],
        ],
        function () {
            Route::resource('request', RequestController::class);
            Route::get('my-request', [RequestController::class, 'myRequest'])->name('my-request');
        }
    );

    //-------------------------------Category, Sub Category & Tag-------------------------------------------

    Route::get('category/{id}/sub-category', [CategoryController::class, 'getSubcategory'])->name('category.sub-category');
    Route::resource('category', CategoryController::class)->middleware(
        [
            'auth',
            'XSS',
        ]
    );
    Route::resource('sub-category', SubCategoryController::class)->middleware(
        [
            'auth',
            'XSS',
        ]
    );
    Route::resource('tag', TagController::class)->middleware(
        [
            'auth',
            'XSS',
        ]
    );

    //-------------------------------Notification-------------------------------------------
    Route::resource('notification', NotificationController::class)->middleware(
        [
            'auth',
            'XSS',
        ]
    );
}); -->
