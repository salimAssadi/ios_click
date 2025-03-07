<?php

namespace App\Http\Controllers;

use App\Models\Document;
use App\Models\DocumentHistory;
use App\Models\Notification;
use App\Models\User;
use App\Models\Request as RequestModel;
use Illuminate\Http\Request;

class RequestController extends Controller
{
   
    public function index()
    {
        if (\Auth::user()->can('manage request')) {
            $requests = RequestModel::where('parent_id', '=', parentId())->get();
            return view('request.index', compact('requests'));
        } else {
            return redirect()->back()->with('error', __('Permission Denied!'));
        }
    }


    public function create()
    {
        $documents = Document::where('parent_id', parentId())->get()->pluck('name', 'id');
        $documents->prepend(__('Select Document'), '');
        $users = User::where('parent_id', parentId())->get()->pluck('name', 'id');
        return view('request.create', compact('users', 'documents'));
    }


    public function store(Request $request)
    {
        if (\Auth::user()->can('create request')) {
            // Validate the request
            $validator = \Validator::make(
                $request->all(),
                [
                    'date' => 'required',
                    'time' => 'required',
                    'subject' => 'required',
                    'message' => 'required',
                ]
            );

            if ($validator->fails()) {
                $messages = $validator->getMessageBag();
                return redirect()->back()->with('error', $messages->first());
            }

            // Create Request
            $requestObj = new RequestModel();
            $requestObj->document_id = $requestObj->document_id ?? 0;
            $requestObj->date = $requestObj->date;
            $requestObj->time = $requestObj->time;
            $requestObj->subject = $requestObj->subject;
            $requestObj->message = $requestObj->message;
            $requestObj->assign_user = !empty($requestObj->assign_user) ? implode(',', $requestObj->assign_user) : '';
            $requestObj->created_by = \Auth::user()->id;
            $requestObj->parent_id = parentId();
            $requestObj->save();

            // // Log document history
            // $document = Document::find($request->document_id ?? 0);
            // $data['document_id'] = $document->id ?? 0;
            // $data['action'] = __('Create request');
            // $data['description'] = __('Create request for') . ' ' . ($document->name ?? '') . ' ' . __('created by') . ' ' . \Auth::user()->name;
            // DocumentHistory::history($data);

            // Handle Notifications and Emails
            $module = 'request_create';
            $notification = Notification::where('parent_id', parentId())->where('module', $module)->first();

            $errorMessage = '';
            if (!empty($notification) && $notification->enabled_email == 1) {
                $notification_responce = MessageReplace($notification, $requestObj->id);
                $settings = settings();
                $userIds = explode(',', $requestObj->assign_user);
                $to = User::whereIn('id', $userIds)->pluck('email')->toArray();
                $users = User::whereIn('id', $userIds)->get();


                if (!empty($to)) {
                    $datas = [
                        'user'    => $users,
                        'subject' => $notification_responce['subject'],
                        'message' => $notification_responce['message'],
                        'module'  => $module,
                        'logo'    => $settings['company_logo'],
                    ];

                    $response = commonEmailSend($to, $datas);
                    if ($response['status'] == 'error') {
                        $errorMessage=$response['message'];
                    }
                }
            }



            return redirect()->back()->with('success', __('Request successfully created!') . '</br>' . $errorMessage);
        } else {
            return redirect()->back()->with('error', __('Permission Denied!'));
        }
    }


    public function show(RequestModel $request)
    {
        if (\Auth::user()->can('show request')) {
            return view('request.show', compact('request'));
        } else {
            return redirect()->back()->with('error', __('Permission Denied!'));
        }
    }


    public function edit(RequestModel $request)
    {
        $documents = Document::where('parent_id', parentId())->get()->pluck('name', 'id');
        $documents->prepend(__('Select Document'), '');
        $users = User::where('parent_id', parentId())->get()->pluck('name', 'id');
        return view('request.edit', compact('users', 'documents', 'request'));
    }


    public function update(Request $request, RequestModel $requestObj)
    {
        if (\Auth::user()->can('edit request')) {
            $validator = \Validator::make(
                $request->all(),
                [
                    'date' => 'required',
                    'time' => 'required',
                    'subject' => 'required',
                    'message' => 'required',
                ]
            );
            if ($validator->fails()) {
                $messages = $validator->getMessageBag();

                return redirect()->back()->with('error', $messages->first());
            }

            $requestObj->document_id = $requestObj->document_id;
            $requestObj->date = $requestObj->date;
            $requestObj->time = $requestObj->time;
            $requestObj->subject = $requestObj->subject;
            $requestObj->message = $requestObj->message;
            $requestObj->save();

            // $document = Document::find($requestObj->document_id);
            // $data['document_id'] = !empty($document) ? $document->id : 0;
            // $data['action'] = __('Create request');
            // $data['description'] = __('Update request for') . ' ' . !empty($document) ? $document->name : '' . ' ' . __('updated by') . ' ' . \Auth::user()->name;
            // $data['document_id'] = $document->id;
            // DocumentHistory::history($data);

            return redirect()->back()->with('success', __('Request successfully updated!'));
        } else {
            return redirect()->back()->with('error', __('Permission Denied!'));
        }
    }


    public function destroy(RequestModel $request)
    {
        if (\Auth::user()->can('delete request')) {
            $document = Document::find($request->document_id);

            $request->delete();

            // $data['document_id'] = !empty($document) ? $document->id : 0;
            // $data['action'] = __('Delete request');
            // $data['description'] = __('Delete request for') . ' ' . !empty($document) ? $document->name : '' . ' ' . __('deleted by') . ' ' . \Auth::user()->name;
            // $data['document_id'] = $document->id;
            // DocumentHistory::history($data);
            return redirect()->back()->with('success', 'Request successfully deleted!');
        } else {
            return redirect()->back()->with('error', __('Permission Denied!'));
        }
    }

    public function myRequest()
    {
        if (\Auth::user()->can('manage my request')) {
            $requests = RequestModel::where('parent_id', '=', \Auth::user()->id)
                ->WhereRaw('find_in_set(?, created_by)', [\Auth::user()->id])
                ->get();
            return view('request.own', compact('requests'));
        } else {
            return redirect()->back()->with('error', __('Permission Denied!'));
        }
    }

}
