<?php

namespace App\Http\Controllers\iso_dic;

use App\Http\Controllers\Controller;

use App\Models\Category;
use App\Models\Contact;
use App\Models\Custom;
use App\Models\Document;
use App\Models\FAQ;
use App\Models\HomePage;
use App\Models\NoticeBoard;
use App\Models\PackageTransaction;
use App\Models\Page;
use App\Models\Reminder;
use App\Models\SubCategory;
use App\Models\Subscription;
use App\Models\Support;
use App\Models\User;
use Carbon\Carbon;

class HomeController extends Controller
{

    public function index()
    {   
        
        if (\Auth::check()) {
            if (\Auth::user()->type == 'super admin') {
              
                $result['totalUser'] = User::where('parent_id', parentId())->count();
                $result['totalDocument'] = Document::where('parent_id', parentId())->count();
                $result['todayDocument'] = Document::whereDate('created_at', Carbon::today())->where('parent_id', parentId())->count();
                $result['totalCategory'] = Category::where('parent_id', parentId())->count();
                $result['totalReminder'] = Reminder::where('parent_id', parentId())->count();
                $result['todayReminder'] = Reminder::whereDate('date', Carbon::today())->where('parent_id', parentId())->count();

                // $result['totalContact'] = Contact::where('parent_id', \Auth::user()->id)->count();

                $result['documentByCategory'] = $this->documentByCategory();
                $result['documentBySubCategory'] = $this->documentBySubCategory();
                $result['settings'] = settings();


                return view($this->iso_dic_path.'.dashboard.index', compact('result'));
            }
        } else {
            if (!file_exists(setup())) {
                header('location:install');
                die;
            } else {
                return redirect()->route('iso_dic.login');
            }
        }
    }

    public function organizationByMonth()
    {
        $start = strtotime(date('Y-01'));
        $end = strtotime(date('Y-12'));

        $currentdate = $start;

        $organization = [];
        while ($currentdate <= $end) {
            $organization['label'][] = date('M-Y', $currentdate);

            $month = date('m', $currentdate);
            $year = date('Y', $currentdate);
            $organization['data'][] = User::where('type', 'owner')->whereMonth('created_at', $month)->whereYear('created_at', $year)->count();
            $currentdate = strtotime('+1 month', $currentdate);
        }


        return $organization;
    }

    public function paymentByMonth()
    {
        $start = strtotime(date('Y-01'));
        $end = strtotime(date('Y-12'));

        $currentdate = $start;

        $payment = [];
        while ($currentdate <= $end) {
            $payment['label'][] = date('M-Y', $currentdate);

            $month = date('m', $currentdate);
            $year = date('Y', $currentdate);
            $payment['data'][] = PackageTransaction::whereMonth('created_at', $month)->whereYear('created_at', $year)->sum('amount');
            $currentdate = strtotime('+1 month', $currentdate);
        }

        return $payment;
    }

    public function documentByCategory()
    {
        $categories = Category::where('parent_id', parentId())->get();
        $documents = [];
        $cat = [];
        foreach ($categories as $category) {
            $documents[] = Document::where('parent_id', parentId())->where('category_id', $category->id)->count();
            $cat[] = $category->title;
        }
        $result['data'] = $documents;
        $result['category'] = $cat;
        return $result;
    }
    public function documentBySubCategory()
    {
        $categories = SubCategory::where('parent_id', parentId())->get();
        $documents = [];
        $cat = [];
        foreach ($categories as $category) {
            $documents[] = Document::where('parent_id', parentId())->where('category_id', $category->id)->count();
            $cat[] = $category->title;
        }
        $result['data'] = $documents;
        $result['category'] = $cat;
        return $result;
    }
}
