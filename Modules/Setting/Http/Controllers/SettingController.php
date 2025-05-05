<?php

namespace Modules\Setting\Http\Controllers;

use App\Http\Controllers\BaseModuleController;
use Modules\Setting\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\get;
use Illuminate\Support\Facades\File;

class SettingController extends BaseModuleController
{
    public function __construct()
    {
        parent::__construct();
        $this->viewPath = 'setting::settings';
        $this->routePrefix = 'settings';
        $this->moduleName = 'Setting';
    }
    
    //    ---------------------- Account --------------------------------------------------------
    public function index()
    {
        $loginUser = \Auth::user();
        $settings = settings();
        $timezones = config('timezones');
        $activeTab ='general_settings';
        return $this->view('index', compact('loginUser', 'settings', 'timezones', 'activeTab'));
    }

    public function accountData(Request $request)
    {
        $loginUser = \Auth::user();
        $user = User::find($loginUser->id);
        $validator = \Validator::make(
            $request->all(),
            [
                'name' => 'required',
                'email' => 'required|email|unique:users,email,' . $user->id,
            ]
        );
        if ($validator->fails()) {
            $messages = $validator->getMessageBag();
            return redirect()->back()->with('error', $messages->first());
        }


        if ($request->hasFile('profile')) {
            $filenameWithExt = $request->file('profile')->getClientOriginalName();
            $filename = pathinfo($filenameWithExt, PATHINFO_FILENAME);
            $extension = $request->file('profile')->getClientOriginalExtension();
            $fileNameToStore = $filename . '_' . time() . '.' . $extension;

            $dir = storage_path('uploads/profile/');
            $image_path = $dir . $loginUser->avatar;

            if (\File::exists($image_path)) {
                \File::delete($image_path);
            }

            if (!file_exists($dir)) {
                mkdir($dir, 0777, true);
            }

            $request->file('profile')->storeAs('upload/profile/', $fileNameToStore);
        }

        if (!empty($request->profile)) {
            $user->profile = $fileNameToStore;
        }
        $user->name = $request->name;
        $user->email = $request->email;
        $user->phone_number = $request->phone_number;
        $user->save();


        return redirect()->back()->with('success', 'User profile settings successfully updated.')->with('tab', 'user_profile_settings');
    }

    public function accountDelete(Request $request)
    {
        $loginUser = \Auth::user();
        // $loginUser->delete();

        return redirect()->back()->with('success', 'Your account successfully deleted.');
    }

    //    ---------------------- Password --------------------------------------------------------

    public function passwordData(Request $request)
    {
        if (\Auth::Check()) {
            $validator = \Validator::make(
                $request->all(),
                [
                    'current_password' => 'required',
                    'new_password' => 'required|min:6',
                    'confirm_password' => 'required|same:new_password',
                ]
            );
            if ($validator->fails()) {
                $messages = $validator->getMessageBag();

                return redirect()->back()->with('error', $messages->first());
            }
            $loginUser = \Auth::user();
            $data = $request->All();

            $current_password = $loginUser->password;
            if (Hash::check($data['current_password'], $current_password)) {
                $user_id = $loginUser->id;
                $user = User::find($user_id);
                $user->password = Hash::make($data['new_password']);;
                $user->save();

                return redirect()->back()->with('success', __('Password successfully updated.'))->with('tab', 'password_settings');
            } else {
                return redirect()->back()->with('error', __('Please enter valid current password.'))->with('tab', 'password_settings');
            }
        } else {
            return redirect()->back()->with('error', __('Invalid user.'))->with('tab', 'password_settings');
        }
    }

    //    ---------------------- General --------------------------------------------------------


    public function generalData(Request $request)
    {   
        $tab = 'general_settings';

        $validator = \Validator::make($request->all(), [
            'application_name' => 'required',
            'logo'            => 'nullable|mimes:png',
            'landing_logo'    => 'nullable|mimes:png',
            'favicon'         => 'nullable|mimes:png',
            'light_logo'      => 'nullable|mimes:png',
        ]);
    
        if ($validator->fails()) {
            return redirect()->back()->with('error', $validator->errors()->first());
        }
        
        $tenant = 'assdaf';
        // Store uploaded files
        if ($request->hasFile('company_logo')) {
            $request->file('company_logo')->storeAs($tenant.'/logo', 'logo.png' ,'tenants');
            $company_logo = $tenant.'/logo/logo.png';
            $settings['company_logo'] = $company_logo;

        }
    
       
    
        if ($request->hasFile('company_favicon')) {
            $request->file('company_favicon')->storeAs($tenant.'/logo', 'favicon.png' ,'tenants');
            $company_favicon = $tenant.'/logo/favicon.png';
            $settings['company_favicon'] = $company_favicon;

        }
    
        if ($request->hasFile('light_logo')) {
            $request->file('light_logo')->storeAs($tenant.'/logo', 'light_logo.png' ,'tenants');
            $light_logo = $tenant.'/logo/light_logo.png';
            $settings['light_logo'] = $light_logo;

        }
    
        // Save all request data into settings
        $excludedKeys = ['_token', 'tenant'];
        $settings = $request->except($excludedKeys);
    
        foreach ($settings as $key => $value) {
            if (!empty($value)) {
                if (is_array($value)) {
                    $value = json_encode($value); 
                }
                \DB::insert(
                    'INSERT INTO settings (`value`, `name`, `type`) VALUES (?, ?, ?)
                     ON DUPLICATE KEY UPDATE `value` = VALUES(`value`)',
                    [$value, $key, 'common']
                );
            }
        }
      
       
        return redirect()->back()
            ->with('success', __('General setting successfully saved.'))
            ->with('tab', $tab);
    }
    

    //    ---------------------- SMTP --------------------------------------------------------



    public function smtpData(Request $request)
    {
        if (\Auth::Check()) {
            $validator = \Validator::make(
                $request->all(),
                [
                    'sender_name' => 'required',
                    'sender_email' => 'required',
                    'server_driver' => 'required',
                    'server_host' => 'required',
                    'server_port' => 'required',
                    'server_username' => 'required',
                    'server_password' => 'required',
                    'server_encryption' => 'required',
                ]
            );
            if ($validator->fails()) {
                $messages = $validator->getMessageBag();

                return redirect()->back()->with('error', $messages->first());
            }

            $smtpArray = [
                'FROM_NAME' => $request->sender_name,
                'FROM_EMAIL' => $request->sender_email,
                'SERVER_DRIVER' => $request->server_driver,
                'SERVER_HOST' => $request->server_host,
                'SERVER_PORT' => $request->server_port,
                'SERVER_USERNAME' => $request->server_username,
                'SERVER_PASSWORD' => $request->server_password,
                'SERVER_ENCRYPTION' => $request->server_encryption,
            ];
            foreach ($smtpArray as $key => $val) {
                \DB::insert(
                    'insert into settings (`value`, `name`, `type`,`parent_id`) values (?, ?, ?,?) ON DUPLICATE KEY UPDATE `value` = VALUES(`value`) ',
                    [
                        $val,
                        $key,
                        'smtp',
                        parentId(),
                    ]
                );
            }

            return redirect()->back()->with('success', __('SMTP settings successfully saved.'))->with('tab', 'email_SMTP_settings');
        } else {
            return redirect()->back()->with('error', __('Invalid user.'))->with('tab', 'email_SMTP_settings');
        }
    }

    public function smtpTest(Request $request)
    {
        return view('settings.testmail');
    }

    public function smtpTestMailSend(Request $request)
    {
        if (\Auth::check()) {
            $to = $request->email;
            $errorMessage = '';
            // Data for email
            $data = [
                'module' => 'test_mail',
                'subject' => 'Test Mail',
                'message' => __('This is a test mail.'),
            ];

            // Send email
            $response = sendEmail($to, $data);
            if ($response['status'] == 'error') {
                $errorMessage = $response['message'];
                return redirect()->back()->with('error', $errorMessage)->with('tab', 'email_SMTP_settings');;
            } else {
                $errorMessage = $response['message'];
                return redirect()->back()->with('success', $errorMessage)->with('tab', 'email_SMTP_settings');;
            }
        }
    }

    //    ---------------------- Payment --------------------------------------------------------



  
    public function companyData(Request $request)
    {
        $settings = $request->all();
        unset($settings['_token']);
        unset($settings['_tab']);
        unset($settings['tenant']);
        $tab = 'company_settings';
        
    
        // Save other settings
        foreach ($settings as $key => $value) {
                \DB::insert(
                    'insert into settings (`value`, `name`, `type`, `parent_id`) values (?, ?, ?, ?) ON DUPLICATE KEY UPDATE `value` = VALUES(`value`) ',
                    [
                        $value,
                        $key,
                        'company',
                        parentId()
                    ]
                );
        }
        
        return redirect()->back()->with('success', __('Company settings successfully updated.'))->with('tab', 'company_settings');
    }

    //    ---------------------- Language --------------------------------------------------------

    public function lanquageChange($lang)
    {
        $user = \Auth::user();
        $user->lang = $lang;
        $user->save();

        return redirect()->back()->with('success', __('Language successfully changed.'));
    }

    public function themeSettings(Request $request)
    {
        // dd($request);
        $themeSettings = $request->all();
        unset($themeSettings['_token']);

        foreach ($themeSettings as $key => $val) {
            \DB::insert(
                'insert into settings (`value`, `name`,`type`,`parent_id`) values (?, ?, ?,?) ON DUPLICATE KEY UPDATE `value` = VALUES(`value`) ',
                [
                    $val,
                    $key,
                    'common',
                    parentId(),
                ]
            );
        }

        return redirect()->back()->with('success', __('Theme settings save successfully.'));
    }

    //    ---------------------- SEO Settings --------------------------------------------------------



    

  

    // ---------------------- Footer Setting ---------------------------------------------
    public function footerSetting(Request $request)
    {
        if (!Auth::user()->can('manage footer')) {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }
        $loginUser = Auth::user();
        $pages = Page::where('enabled', 1)->pluck('title', 'id');
        return view('home_pages.footerSetting', compact('loginUser', 'pages'));
    }

    public function footerData(Request $request)
    {
        $settings = $request->all();
        unset($settings['_token']);
        unset($settings['tab']);
        unset($settings['tenant']);

        
        foreach ($settings as $s_key => $s_value) {
            if (!empty($s_value)) {
                \DB::insert(
                    'insert into settings (`value`, `name`, `type`) values (?, ?, ?) ON DUPLICATE KEY UPDATE `value` = VALUES(`value`) ',
                    [
                        $s_value,
                        $s_key,
                        'footer',
                    ]
                );
            }
        }

        return redirect()->back()->with('success', __('Footer settings save successfully.'))->with('tab', $request->tab);
    }


    // ---------------------- 2FA Setting --------------------------------
    public function twofaEnable(Request $request)
    {
        $google2fa = new Google2FA();

        // retrieve secret from the session
        $secret = session("2fa_secret");
        $user = Auth::user();
        if ($google2fa->verify($request->input('otp'), $secret)) {
            // store the secret in the user profile
            // this will enable 2FA for this user
            $user->twofa_secret = $secret;
            $user->save();

            // avoid double OTP check
            session(["2fa_checked" => true]);

            return redirect()->back()->with('success', __('2 FA successfully enabled.'));
        }

        throw ValidationException::withMessages(['otp' => 'Incorrect value. Please try again...']);
    }

    // ---------------------- Tenant Files ---------------------------------------------
    public function getTenantFile($path)
    {
        try {
            if (strpos($path, '..') !== false) {
                abort(404);
            }
            
            if (!Storage::disk('tenants')->exists($path)) {
                abort(404);
            }
            
            $file = Storage::disk('tenants')->path($path);
            
            $type = File::mimeType($file);
            
            $response = response(file_get_contents($file), 200)->header("Content-Type", $type);
            
            return $response;
        } catch (\Exception $e) {
            return abort(404);
        }
    }

    /**
     * Save signature data to the database.
     */
    public function signatureData(Request $request)
    {

        $settings = $request->all();
        unset($settings['_token']);
        unset($settings['_tab']);
        unset($settings['tenant']);
        $tab = 'signature_settings';

        $tenant='assdaf';
        // Handle company signature upload
        if ($request->hasFile('company_signature')) {
            $filenameWithExt = $request->file('company_signature')->getClientOriginalName();
            $filename = pathinfo($filenameWithExt, PATHINFO_FILENAME);
            $extension = $request->file('company_signature')->getClientOriginalExtension();
            $path=$tenant.'/'.'signature/';
            $fileNameToStore = 'company_signature_' . time() . '.' . $extension;

            $request->file('company_signature')->storeAs($path, $fileNameToStore ,'tenants');
            $settings['company_signature'] = $path.$fileNameToStore;
        }else{
            unset($settings['company_signature']);
        }
        
        // Handle company stamp upload
        if ($request->hasFile('company_stamp')) {
            $filenameWithExt = $request->file('company_stamp')->getClientOriginalName();
            $filename = pathinfo($filenameWithExt, PATHINFO_FILENAME);
            $extension = $request->file('company_stamp')->getClientOriginalExtension();
            $path= $tenant.'/'.'stamp/';
            $fileNameToStore = 'company_stamp_' . time() . '.' . $extension;
            
            $request->file('company_stamp')->storeAs($path, $fileNameToStore ,'tenants');
            $settings['company_stamp'] = $path.$fileNameToStore;
        }else{
            unset($settings['company_stamp']);
        }
        
        // Handle signature pad data
        if ($request->has('signature_pad_data') && !empty($request->signature_pad_data)) {
            $settings['signature_pad_data'] = $request->signature_pad_data;
        }else{
            unset($settings['signature_pad_data']);
        }
        
        // Save settings
        foreach ($settings as $key => $value) {
                \DB::insert(
                    'insert into settings (`value`, `name`, `type`) values (?, ?, ?) ON DUPLICATE KEY UPDATE `value` = VALUES(`value`) ',
                    [
                        $value,
                        $key,
                        'signature',
                    ]
                );
        }
        
        return redirect()->back()->with('success', __('Signature settings successfully updated.'))->with('tab', $tab );
    }
}
