<?php

namespace App\Constants;

class FileInfo
{

    /*
    |--------------------------------------------------------------------------
    | File Information
    |--------------------------------------------------------------------------
    |
    | This class basically contain the path of files and size of images.
    | All information are stored as an array. Developer will be able to access
    | this info as method and property using FileManager class.
    |
    */

    public function fileInfo(){
        $data['withdrawVerify'] = [
            'path'=>'assets/images/verify/withdraw'
        ];
        $data['depositVerify'] = [
            'path'      =>'assets/images/verify/deposit'
        ];
        $data['verify'] = [
            'path'      =>'assets/verify'
        ];
        $data['default'] = [
            'path'      => 'assets/images/default.png',
        ];
        $data['withdrawMethod'] = [
            'path'      => 'assets/images/withdraw/method',
            'size'      => '800x800',
        ];
        $data['ticket'] = [
            'path'      => 'assets/support',
        ];
        $data['logoIcon'] = [
            'path'      => 'assets/images/logoIcon',
        ];
        $data['favicon'] = [
            'size'      => '128x128',
        ];
        $data['extensions'] = [
            'path'      => 'assets/images/extensions',
            'size'      => '36x36',
        ];
        $data['seo'] = [
            'path'      => 'assets/images/seo',
            'size'      => '1180x600',
        ];
        $data['userProfile'] = [
            'path'      =>'assets/images/user/profile',
            'size'      =>'140x140',
        ];
        $data['adminProfile'] = [
            'path'      =>'assets/admin/images/profile',
            'size'      =>'400x400',
        ];
        $data['agentProfile'] = [
            'path'      =>'assets/images/agent/profile',
            'size'      =>'140x140',
        ];
        $data['merchantProfile'] = [
            'path'      =>'assets/images/merchant/profile',
            'size'      =>'300x250',
        ];
        $data['qr_code_template'] = [
            'path'=>'assets/images/qr_code_template',
            'size'=> '2480x3508',
        ];
        $data['temporary'] = [
            'path'      =>'assets/images/temporary'
        ];
        $data['mobile_operator'] = [
            'path'=>'assets/images/mobile_operator',
            'size'=> '160x90',
        ];
        $data['setup_donation'] = [
            'path'=>'assets/images/setup_donation',
            'size'=> '150x150',
        ];
        $data['setup_utility'] = [
            'path'=>'assets/images/setup_utility',
            'size'=> '100x100',
        ];
        $data['setup_bank'] = [
            'path'=>'assets/images/setup_bank',
            'size'=> '150x120',
        ];
        $data['promotional_notify'] = [
            'path'=>'assets/images/promotional_notify',
            'size'=> '400x225',
        ];

        $data['pdf'] = [
            'path'=>'assets/images/pdf',
        ];
        return $data;
	}

}
