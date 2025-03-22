<?php

namespace App\Http\Controllers\iso_dic;

use App\Models\IsoSpecificationItem;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;

class IsoSpecificationItemController extends Controller
{


    public function index(Request $request)
    {
        $isoSystems = \App\Models\IsoSystem::pluck('name_ar', 'id');

        $selectedIsoId = $request->input('iso_system_id', $isoSystems->keys()->first());

        $topLevelItems = IsoSpecificationItem::whereNull('parent_id')
            ->orderBy('item_number')->where('iso_system_id', $selectedIsoId)
            ->get();

        $specificationItems = $this->flattenHierarchy($topLevelItems);
        $treeData = $this->buildTree(IsoSpecificationItem::where('iso_system_id', $selectedIsoId)->get());

        // Return view
        return view($this->iso_dic_path . '.specification_items.index', compact('isoSystems', 'selectedIsoId', 'specificationItems', 'treeData'));
    }


    public function show($id)
    {
        $id = decrypt($id);
        $item = IsoSpecificationItem::find($id);
    }
    
   

    public function create()
    {
        $isoSystems = \App\Models\IsoSystem::all();
        $parentItems = \App\Models\IsoSpecificationItem::all()->mapWithKeys(function ($item) {
            $inspectionPreview = mb_substr($item->inspection_question, 0, 30); // Get first 10 characters
            return [$item->id => $item->item_number . ' - ' . $inspectionPreview];
        });
        return view($this->iso_dic_path . '.specification_items.create', compact('isoSystems', 'parentItems'));
    }


    public function store(Request $request)
    {
        // Validate the request
        $request->validate([
            'iso_system_id' => 'required|exists:iso_systems,id',
            'parent_id' => 'nullable|exists:iso_specification_items,id',
            'inspection_question' => 'required|string|max:255',
            'sub_inspection_question' => 'nullable|string',
            'additional_text' => 'nullable|string',
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
            'inspection_question' => $request->inspection_question,
            'sub_inspection_question' => $request->sub_inspection_question,
            'additional_text' => $request->additional_text,
            'attachment' => $attachmentPath,
            'status' => true,
        ]);

        return redirect()->back()->with('success', __('item successfully created!'));
    }


    public function edit(IsoSpecificationItem $specification_item)
    {
        try {
            $IsoSpecificationItem = IsoSpecificationItem::findOrFail($specification_item->id);
            if ($IsoSpecificationItem) {
                return response()->json([
                    "status" => "success",
                    "data" => $IsoSpecificationItem
                ]);
            } else {
                return response()->json([
                    "status" => "error",
                    "message" => "Item not found"
                ]);
            }
        } catch (\Exception $e) {
            return response()->json([
                "status" => "error",
                "message" => $e->getMessage()
            ]);
        }
    }


    public function destroy($id)
    {
        if (\Auth::check()) {
            $isoSpecificationItem = IsoSpecificationItem::find($id);
            $isoSpecificationItem->delete();
            return redirect()->route('specification_items.index')->with('success', __('Item successfully deleted.'));
        }
    }


    private function generateItemNumber($isoSystemId, $parentId = null)
    {
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


    public function getTreeData()
    {
        $items = IsoSpecificationItem::all();
        $tree = $this->buildTree($items);
        return response()->json($tree);
    }

    private function flattenHierarchy($items)
    {
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



    private function buildTree($items, $parentId = null)
    {
        $branch = [];
        foreach ($items as $item) {
            if ($item->parent_id == $parentId) {
                $children = $this->buildTree($items, $item->id);

                // Check if the node is a top-level parent (parent_id is null)
                if ($parentId === null) {
                    $text = "<span style='color: red; font-weight: bold;'>{$item->item_number} - " .
                        ($item->inspection_question ?: $item->inspection_question) .
                        "</span>";
                } elseif ($children) {
                    // If the node has children but is not a top-level parent
                    $text = "<span style='color: blue; font-weight: bold;'>{$item->item_number} - " .
                        ($item->inspection_question ?: $item->inspection_question) .
                        "</span>";
                } else {
                    // Leaf nodes (last level without children)
                    $text = "<span style='color: black;'>{$item->item_number} - {$item->inspection_question}</span>";
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
