<?php

namespace Modules\Tenant\Http\Controllers;

use Illuminate\Routing\Controller;
use Modules\Document\Entities\DocumentRequest;
use Modules\Document\Entities\Status;
//use Modules\Tenant\Models\Category;
use Modules\Tenant\Models\Contact;
use Modules\Tenant\Models\Custom;
use Modules\Tenant\Models\Document;
use Modules\Tenant\Models\FAQ;
use Modules\Tenant\Models\HomePage;
use Modules\Tenant\Models\NoticeBoard;
use Modules\Tenant\Models\PackageTransaction;
use Modules\Tenant\Models\Page;
use Modules\Tenant\Models\Reminder;
use Modules\Tenant\Models\SubCategory;
use Modules\Tenant\Models\Subscription;
use Modules\Tenant\Models\Support;
use Modules\Tenant\Models\Sample;
use Modules\Tenant\Models\IsoSpecificationItem;
use Modules\Tenant\Models\Procedure;
use Modules\Tenant\Models\IsoReference;
use Modules\Tenant\Models\IsoInstruction;
use Modules\Tenant\Models\IsoPolicy;
use Modules\Tenant\Models\User;
use Modules\Tenant\Models\IsoSystem;
use Carbon\Carbon;


class HomeController extends Controller
{

    public function index()
    {    
        // dd(auth('tenant')->user());
        
        if (auth('tenant')->check()) {
            if (auth('tenant')->user()->type == 'super admin') {
              
                $result['totalUser'] = User::where('parent_id', parentId())->count();
                $result['totalISOSystem'] = IsoSystem::where('status', '1')->count();
                $result['totalSpecificationItem'] = IsoSpecificationItem::where('iso_system_id', currentISOSystem())->count();
                $result['totalProcedures'] = Procedure::count();
                $result['totalSamples'] = Sample::count();
                $result['totalReferences'] = IsoReference::count();
                $result['totalInstructions'] = IsoInstruction::count();
                $result['totalPolicies'] = IsoPolicy::count();

                // $result['totalContact'] = Contact::where('parent_id', \Auth::user()->id)->count();

                // Document Statistics
                $result['underReviewDocs'] = DocumentRequest::whereHas('approvalStatus', function($q) {
                    $q->where('code', 'under_review');
                })->count();

                $result['pendingApprovalDocs'] = DocumentRequest::whereHas('approvalStatus', function($q) {
                    $q->where('code', 'pending_approval');
                })->count();

                $result['approvedDocs'] = DocumentRequest::whereHas('approvalStatus', function($q) {
                    $q->where('code', 'approved');
                })->count();

                $result['archivedDocs'] = DocumentRequest::whereHas('approvalStatus', function($q) {
                    $q->where('code', 'archived');
                })->count();
                $result['draftDocs'] = Document::where('status_id', 11)->count();

                $result['documentByCategory'] = $this->documentByCategory();
                $result['documentBySubCategory'] = $this->documentBySubCategory();
                $result['settings'] = settings();


                return view('tenant::dashboard.index', compact('result'));
            }else{
                // Placeholder data for customer dashboard
                $result = [
                    'recentDocuments' => [
                        ['name' => 'ISO 9001 Documentation', 'status' => 'Under Review', 'date' => '2025-04-20'],
                        ['name' => 'Quality Manual', 'status' => 'Approved', 'date' => '2025-04-15'],
                        ['name' => 'Process Procedures', 'status' => 'Pending', 'date' => '2025-04-25'],
                    ],
                    'documentStats' => [
                        'total' => 15,
                        'pending' => 5,
                        'approved' => 8,
                        'rejected' => 2
                    ],
                    'quickActions' => [
                        ['name' => 'New Document', 'icon' => 'fas fa-file-alt', 'route' => '#'],
                        ['name' => 'View Documents', 'icon' => 'fas fa-folder-open', 'route' => '#'],
                        ['name' => 'My Profile', 'icon' => 'fas fa-user', 'route' => '#'],
                    ]
                ];

                return view('tenant::dashboard.customer', compact('result'));
            }
        } else {
            if (!file_exists(setup())) {
                header('location:install');
                die;
            } else {
                return redirect()->route('tenant.login');
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
        // $categories = Category::where('parent_id', parentId())->get();
        // $documents = [];
        // $cat = [];
        // foreach ($categories as $category) {
        //     $documents[] = Document::where('parent_id', parentId())->where('category_id', $category->id)->count();
        //     $cat[] = $category->title;
        // }
        $result['data'] = [];
        $result['category'] = [];
        return $result;
    }
    public function documentBySubCategory()
    {
        // $categories = SubCategory::where('parent_id', parentId())->get();
        // $documents = [];
        // $cat = [];
        // foreach ($categories as $category) {
        //     $documents[] = Document::where('parent_id', parentId())->where('category_id', $category->id)->count();
        //     $cat[] = $category->title;
        // }
        $result['data'] = [];
        $result['category'] = [];
        return $result;
    }
}
