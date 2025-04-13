<?php

namespace App\Http\Controllers\iso_dic;

use App\Models\IsoSpecificationItem;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;

class IsoSpecificationItemController extends Controller
{

    // public function index(Request $request){
    //     $isoSystems = \App\Models\IsoSystem::pluck('name_ar', 'id');

    //     $selectedIsoId = $request->input('iso_system_id', $isoSystems->keys()->first());

    //     $topLevelItems = IsoSpecificationItem::whereNull('parent_id')
    //         ->orderBy('item_number')->where('iso_system_id', $selectedIsoId)
    //         ->get();

    //     $specificationItems = $this->flattenHierarchy($topLevelItems);
    //     $treeData = $this->buildTree(IsoSpecificationItem::where('iso_system_id', $selectedIsoId)->get());

    //     // Return view
    //     return view($this->iso_dic_path . '.specification_items.index', compact('isoSystems', 'selectedIsoId', 'specificationItems', 'treeData'));
    // }

   public function index(Request $request)
    {
        $isoSystems = \App\Models\IsoSystem::pluck('name_ar', 'id');
        $selectedIsoId = $request->input('iso_system_id', $isoSystems->keys()->first());
        $filter = $request->query('filter', 'all');
        $query = IsoSpecificationItem::whereNull('parent_id')
            ->with([
                'children' => function ($childQuery){
                    $childQuery->orderBy('item_number', 'asc');
                }
            ])
            ->where('iso_system_id', $selectedIsoId)->orderBy('item_number', 'asc');
            $specificationItems = $query->paginate();

        // if ($filter !== 'all') {
        //     $query->where(function ($q) use ($filter) {
        //         $q->where('completion_status', $filter) // Parent matches the filter
        //             ->orWhereHas('children', function ($childQuery) use ($filter) {
        //                 $childQuery->where('completion_status', $filter); // Child matches the filter
        //             });
        //     });
        // }

        return view($this->iso_dic_path . '.specification_items.index', compact('isoSystems', 'selectedIsoId', 'specificationItems', 'filter'));

    }


    public function show($id){
        $id = decrypt($id);
        $item = IsoSpecificationItem::find($id);
    }



    public function create(){
        $isoSystems = \App\Models\IsoSystem::all();
        $parentItems = \App\Models\IsoSpecificationItem::all()->mapWithKeys(function ($item) {
            $inspectionPreview = mb_substr($item->inspection_question_ar, 0, 30); // Get first 10 characters
            return [$item->id => $item->item_number . ' - ' . $inspectionPreview];
        });
        return view($this->iso_dic_path . '.specification_items.create', compact('isoSystems', 'parentItems'));
    }


    public function store(Request $request){
        // Validate the request
        $request->validate([
            'iso_system_id' => 'required|exists:iso_systems,id',
            'parent_id' => 'nullable|exists:iso_specification_items,id',
            'inspection_question_ar' => 'required|string|max:255',
            'inspection_question_en' => 'required|string|max:255',
            'sub_inspection_question' => 'nullable|string',
            'additional_text_ar' => 'nullable|string',
            'additional_text_en' => 'nullable|string',
            'attachment' => 'nullable|file|mimes:jpg,jpeg,png,pdf,doc,docx|max:2048',
        ]);

        // Generate item number based on hierarchy
        $itemNumber = $this->generateItemNumber($request->iso_system_id, $request->parent_id);

        // Handle file upload (if any)
        $attachmentPath = null;
        if ($request->hasFile('attachment')) {
            $attachmentPath = $request->file('attachment')->store('attachments', 'public');
        }
        // Create new ISO Specification Item
        $item = IsoSpecificationItem::create([
            'iso_system_id' => $request->iso_system_id,
            'parent_id' => $request->parent_id,
            'item_number' => $itemNumber,
            'inspection_question_ar' => $request->inspection_question_ar,
            'inspection_question_en' => $request->inspection_question_en,
            'sub_inspection_question' => $request->sub_inspection_question,
            'additional_text_ar' => $request->additional_text_ar,
            'additional_text_en' => $request->additional_text_en,
            'attachment' => $attachmentPath,
            'status' => true,
        ]);

        return redirect()->back()->with('success', __('item successfully created!'));
    }


    public function edit(IsoSpecificationItem $specification_item) {
        try {
            $IsoSpecificationItem = IsoSpecificationItem::findOrFail($specification_item->id);
            $parentItems = IsoSpecificationItem::all()->mapWithKeys(function ($item) {
                $inspectionPreview = mb_substr($item->inspection_question_ar, 0, 30);
                return [$item->id => $item->item_number . ' - ' . $inspectionPreview];
            });
            $isoSystems = \App\Models\IsoSystem::all();
            if (!$IsoSpecificationItem) {
                return redirect()->back()->with('error', __('Not found...'));
            }

            return view($this->iso_dic_path . '.specification_items.edit', compact('isoSystems', 'parentItems', 'IsoSpecificationItem'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', __('Incorrect Code. Please try again...'));
        }
    }

    public function update(Request $request, $id) {

        $validator = \Validator::make(
            $request->all(),
            [
            'iso_system_id' => 'required|exists:iso_systems,id',
            'parent_id' => 'nullable|exists:iso_specification_items,id',
            'inspection_question_ar' => 'required|string|max:255',
            'inspection_question_en' => 'required|string|max:255',
            'sub_inspection_question' => 'nullable|string',
            'additional_text_ar' => 'nullable|string',
            'additional_text_en' => 'nullable|string',
            'attachment' => 'nullable|file|mimes:jpg,jpeg,png,pdf,doc,docx|max:2048',
        ]);
        if ($validator->fails()) {
            // Collect all error messages into a single string
            $errorMessages = $validator->errors()->all();
            $formattedErrors = implode('<br>', $errorMessages); // Join errors with line breaks
        
            // Redirect back with all errors as a single notification
            return redirect()->back()
                ->with('error', $formattedErrors) // Flash all errors to the session
                ->withInput() // Repopulate the form inputs
                ->withErrors($validator); // Pass detailed errors to the $errors variable
        }
        $item = IsoSpecificationItem::findOrFail($id);
    
        
        $item->update([
            'iso_system_id' => $request->iso_system_id,
            'parent_id' => $request->parent_id,
            'inspection_question_ar' => $request->inspection_question_ar,
            'inspection_question_en' => $request->inspection_question_en,
            'additional_text_ar' => $request->additional_text_ar,
            'additional_text_en' => $request->additional_text_en,
            'attachment' => '',
            'status' => $request->has('status') ? true : false, // Toggle status based on checkbox
        ]);
    
        if ($item->wasChanged('parent_id') || $item->wasChanged('iso_system_id')) {
            $item->item_number = $this->generateItemNumber($item->iso_system_id, $item->parent_id);
            $item->save();
        }
        
        return redirect()->back()->with('success', __('Item successfully updated!'));
    }

    public function destroy($id){
        if (\Auth::check()) {
            $isoSpecificationItem = IsoSpecificationItem::find($id);
            $isoSpecificationItem->delete();
            return redirect()->route('specification_items.index')->with('success', __('Item successfully deleted.'));
        }
    }


    private function generateItemNumber($isoSystemId, $parentId = null){
        if ($parentId) {
            $lastChild = IsoSpecificationItem::where('parent_id', $parentId)
                ->orderBy('item_number', 'desc')
                ->first();
            // Get parent's item number
            $parentItem = IsoSpecificationItem::find($parentId);
            $parentNumber = $parentItem->item_number;

            if ($lastChild) {
                $lastNumber = (int) substr(strrchr($lastChild->item_number, '.'), 1);
                return $parentNumber . '.' . ($lastNumber + 1);
            } else {
                return $parentNumber . '.1';
            }
        } else {
            $lastRootItem = IsoSpecificationItem::whereNull('parent_id')
                ->where('iso_system_id', $isoSystemId)
                ->orderBy('item_number', 'desc')
                ->first();

            if ($lastRootItem) {
                return (int) $lastRootItem->item_number + 1;
            } else {
                return '1';
            }
        }
    }


    public function getTreeData(){
        $items = IsoSpecificationItem::all();
        $tree = $this->buildTree($items);
        return response()->json($tree);
    }

    private function flattenHierarchy($items) {
        $result = [];

        foreach ($items as $item) {
            // Add the current item to the result
            $result[] = $item;

            // Fetch and sort the children of the current item
            $children = $item->children()->orderBy('item_number')->get();

            // Recursively add children to the result
            if ($children->isNotEmpty()) {
                $result = array_merge($result, $this->flattenHierarchy($children));
            }
        }

        return $result;
    }



    private function buildTree($items, $parentId = null){
        $branch = [];
        foreach ($items as $item) {
            if ($item->parent_id == $parentId) {
                $children = $this->buildTree($items, $item->id);

                // Check if the node is a top-level parent (parent_id is null)
                if ($parentId === null) {
                    $text = "<span style='color: red; font-weight: bold;'>{$item->item_number} - " .
                        ($item->inspection_question_ar ?: $item->inspection_question_ar) .
                        "</span>";
                } elseif ($children) {
                    // If the node has children but is not a top-level parent
                    $text = "<span style='color: blue; font-weight: bold;'>{$item->item_number} - " .
                        ($item->inspection_question_ar ?: $item->inspection_question_ar) .
                        "</span>";
                } else {
                    // Leaf nodes (last level without children)
                    $text = "<span style='color: black;'>{$item->item_number} - {$item->inspection_question_ar}</span>";
                }

                $node = [
                    'id' => $item->id,
                    'text' => $text,
                    'icon' => 'ti ti-folder',
                ];

                if ($children) {
                    $node['children'] = $children;
                }

                $branch[] = $node;
            }
        }
        return $branch;
    }
}
