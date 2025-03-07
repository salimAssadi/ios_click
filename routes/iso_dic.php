<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\iso_dic\HomeController;
use App\Http\Controllers\iso_dic\Auth\AuthenticatedIsoDicController;
use App\Http\Controllers\iso_dic\DocumentController;
use App\Http\Controllers\iso_dic\IsoAttachmentController;
use App\Http\Controllers\iso_dic\IsoSpecificationItemController;
use App\Http\Controllers\iso_dic\IsoSystemController;
use App\Http\Controllers\FileManagerController;
use Alexusmai\LaravelFileManager\Controllers\LfmController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\DocController;
use App\Http\Controllers\iso_dic\ProcedureController;
use App\Http\Controllers\iso_dic\SampleController;
use App\Http\Controllers\PdfController;
use App\Http\Controllers\SettingController;

// Group all ISO_DIC routes under a common prefix
Route::prefix('iso_dic')->middleware(['XSS'])->name('iso_dic.')->group(function () {

    // Home and Login Routes
    Route::get('/', [HomeController::class, 'index'])->name('home');
    Route::get('/home', [HomeController::class, 'index'])->name('dashboard');
    Route::get('login', [AuthenticatedIsoDicController::class, 'create'])->name('login');
    Route::post('login', [AuthenticatedIsoDicController::class, 'store']);
    Route::post('logout', [AuthenticatedIsoDicController::class, 'destroy'])->name('logout');

    // Authenticated Routes (Middleware: iso_dic_auth)
    Route::middleware(['iso_dic_auth'])->group(function () {

        // Resourceful Routes
        Route::resources([
            'iso_systems' => IsoSystemController::class,
            'specification_items' => IsoSpecificationItemController::class,
            'attachments' => IsoAttachmentController::class,
            'document' => DocumentController::class,
            'procedures' => ProcedureController::class,
            'samples' => SampleController::class,
            'category' => CategoryController::class,
            'sub-category' => SubCategoryController::class,
            'tag' => TagController::class,
        ]);

        // Document Controller Routes
        Route::controller(DocumentController::class)->prefix('document')->name('document.')->group(function () {
            Route::get('history', 'history')->name('history');
            Route::resource('document', DocumentController::class);
            Route::get('my-document', 'myDocument')->name('my-document');
            Route::get('{id}/comment', 'comment')->name('comment');
            Route::get('{id}/reminder', 'reminder')->name('reminder');
            Route::get('{id}/add-reminder', 'addReminder')->name('add.reminder');
            Route::get('{id}/version-history', 'versionHistory')->name('version.history');
            Route::post('{id}/version-history', 'newVersion')->name('new.version');
            Route::get('{id}/add-share', 'addshareDocumentData')->name('add.share');
            Route::post('{id}/share', 'shareDocumentData')->name('share');
            Route::delete('{id}/share/destroy', 'shareDocumentDelete')->name('share.destroy');
            Route::get('{id}/send-email', 'sendEmail')->name('send.email');
            Route::get('logged/history', 'loggedHistory')->name('logged.history');
            Route::get('logged/{id}/history/show', 'loggedHistoryShow')->name('logged.history.show');
            Route::delete('logged/{id}/history', 'loggedHistoryDestroy')->name('logged.history.destroy');
        });

        // Settings Controller Routes
        Route::controller(SettingController::class)->prefix('settings')->name('setting.')->group(function () {
            Route::get('/', 'index')->name('index');
            Route::post('account', 'accountData')->name('account');
            Route::delete('account/delete', 'accountDelete')->name('account.delete');
            Route::post('password', 'passwordData')->name('password');
            Route::post('general', 'generalData')->name('general');
            Route::post('smtp', 'smtpData')->name('smtp');
            Route::get('smtp-test', 'smtpTest')->name('smtp.test');
            Route::post('smtp-test', 'smtpTestMailSend')->name('smtp.testing');
            Route::post('payment', 'paymentData')->name('payment');
            Route::post('site-seo', 'siteSEOData')->name('site.seo');
            Route::post('google-recaptcha', 'googleRecaptchaData')->name('google.recaptcha');
            Route::post('company', 'companyData')->name('company');
            Route::post('2fa', 'twofaEnable')->name('twofa.enable');
            Route::get('footer-setting', 'footerSetting')->name('footerSetting');
            Route::post('footer', 'footerData')->name('footer');
            Route::get('language/{lang}', 'lanquageChange')->name('language.change');
            Route::post('theme/settings', 'themeSettings')->name('theme.settings');
        });

        // File Manager Route
        Route::get('/file-manager', [FileManagerController::class, 'index'])->name('filemanager');

        // Additional Routes for Procedures
        Route::controller(ProcedureController::class)->prefix('procedures')->name('procedures.')->group(function () {
            Route::get('all', 'all')->name('all');
            Route::post('save/{id?}', 'save')->name('save');
            Route::get('configure/{id}', 'configure')->name('configure');
            Route::post('configure/{id}/save', 'saveConfigure')->name('saveConfigure');;
            Route::post('configure/{id}', 'saveTemplatePath')->name('saveTemplatePath');
            Route::post('status/{id}', 'status')->name('status');
        });

        Route::controller(SampleController::class)->prefix('samples')->name('samples.')->group(function () {
            Route::get('all', 'all')->name('all');
            Route::post('save/{id?}', 'save')->name('save');
            Route::get('configure/{id}', 'configure')->name('configure');
            Route::post('configure/{id}', 'saveConfigure')->name('saveConfigure');;
            Route::post('configure/{id}', 'saveTemplatePath')->name('saveTemplatePath');
            Route::post('status/{id}', 'status')->name('status');
        });

        // ISO Tree Data and Widget Update
        Route::get('/iso-tree-data', [IsoSpecificationItemController::class, 'getTreeData'])->name('tree.data');
        Route::get('/updateWidegit', [IsoSpecificationItemController::class, 'updateWidegit'])->name('specification_items.updateWidegit');

        Route::get('/convert-to-html', [DocController::class, 'convertToHtml'])->name('convert.to.html');

        Route::get('view_sample',function(){
            return view('procedure-template.first-template');
        });
        
        Route::get('/generate-pdf', [PdfController::class, 'generatePdf'])->name('generate.pdf');
    });
});

// Logout Route

// Impersonate Route
Route::impersonate();