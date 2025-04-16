<?php

namespace Modules\Tenant\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;

class MeetingController extends Controller
{
    public function index()
    {
        $meetings = Auth::user()->meetings()->paginate(10);
        return view('tenant::meetings.index', compact('meetings'));
    }

    public function create()
    {
        return view('tenant::meetings.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'agenda' => 'required|string',
            'date' => 'required|date',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'location' => 'required|string|max:255',
            'type' => 'required|string|in:management_review,quality_meeting,audit_meeting,other',
            'status' => 'required|string|in:scheduled,in_progress,completed,cancelled',
            'department_id' => 'required|exists:departments,id',
            'participants' => 'required|array',
            'participants.*' => 'exists:users,id',
            'attachments' => 'nullable|array',
            'attachments.*' => 'file|max:10240', // 10MB max file size
        ]);

        $meeting = Auth::user()->meetings()->create($validated);

        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $attachment) {
                $path = $attachment->store('meeting_attachments');
                $meeting->attachments()->create(['path' => $path]);
            }
        }

        // Send notifications to participants
        foreach ($validated['participants'] as $participantId) {
            // Implement notification logic here
        }

        return redirect()->route('tenant.meetings.show', $meeting)
            ->with('success', __('Meeting created successfully'));
    }

    public function show($id)
    {
        $meeting = Auth::user()->meetings()->with(['participants', 'attachments'])->findOrFail($id);
        return view('tenant::meetings.show', compact('meeting'));
    }

    public function edit($id)
    {
        $meeting = Auth::user()->meetings()->with(['participants', 'attachments'])->findOrFail($id);
        return view('tenant::meetings.edit', compact('meeting'));
    }

    public function update(Request $request, $id)
    {
        $meeting = Auth::user()->meetings()->findOrFail($id);
        
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'agenda' => 'required|string',
            'date' => 'required|date',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'location' => 'required|string|max:255',
            'type' => 'required|string|in:management_review,quality_meeting,audit_meeting,other',
            'status' => 'required|string|in:scheduled,in_progress,completed,cancelled',
            'department_id' => 'required|exists:departments,id',
            'participants' => 'required|array',
            'participants.*' => 'exists:users,id',
            'attachments' => 'nullable|array',
            'attachments.*' => 'file|max:10240', // 10MB max file size
            'minutes' => 'nullable|string',
            'action_items' => 'nullable|array',
            'action_items.*.description' => 'required|string',
            'action_items.*.assigned_to' => 'required|exists:users,id',
            'action_items.*.due_date' => 'required|date',
        ]);

        $meeting->update($validated);

        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $attachment) {
                $path = $attachment->store('meeting_attachments');
                $meeting->attachments()->create(['path' => $path]);
            }
        }

        // Update action items
        if (isset($validated['action_items'])) {
            $meeting->actionItems()->delete(); // Remove old action items
            foreach ($validated['action_items'] as $item) {
                $meeting->actionItems()->create($item);
            }
        }

        return redirect()->route('tenant.meetings.show', $meeting)
            ->with('success', __('Meeting updated successfully'));
    }

    public function destroy($id)
    {
        $meeting = Auth::user()->meetings()->findOrFail($id);
        
        // Delete associated attachments
        foreach ($meeting->attachments as $attachment) {
            Storage::delete($attachment->path);
            $attachment->delete();
        }
        
        // Delete action items
        $meeting->actionItems()->delete();
        
        $meeting->delete();

        return redirect()->route('tenant.meetings.index')
            ->with('success', __('Meeting deleted successfully'));
    }
}
