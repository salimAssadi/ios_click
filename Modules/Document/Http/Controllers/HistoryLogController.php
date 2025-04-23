<?php

namespace Modules\Document\Http\Controllers;

use App\Http\Controllers\Controller;
use Modules\Document\Entities\DocumentHistoryLog;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Http\Request;

class HistoryLogController extends Controller
{
    public function index()
    {
        return view('document::history.index');
    }

    public function data()
    {
        $query = DocumentHistoryLog::with(['performer', 'document', 'version']);

        return DataTables::of($query)
            ->addColumn('document_title', function ($log) {
                return '<a href="' . route('tenant.document.show', $log->document_id) . '">' . $log->document->title . '</a>';
            })
            ->addColumn('version_number', function ($log) {
                return $log->version ? 'v' . $log->version->version : 'N/A';
            })
            ->addColumn('performed_by', function ($log) {
                return $log->performer->name;
            })
            ->addColumn('date', function ($log) {
                return $log->created_at->format('Y-m-d H:i');
            })
            ->addColumn('details', function ($log) {
                $details = $log->change_summary;
                if ($log->notes) {
                    $details .= '<br><small class="text-muted">' . $log->notes . '</small>';
                }
                return $details;
            })
            ->rawColumns(['document_title', 'details'])
            ->make(true);
    }
}
