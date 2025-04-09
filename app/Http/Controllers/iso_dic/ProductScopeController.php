<?php


namespace App\Http\Controllers\iso_dic;
use App\Http\Controllers\Controller;
use App\Models\ProductScope;
use Illuminate\Http\Request;

class ProductScopeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
         $topLevelItems = ProductScope::whereNull('parent_id')->with('children.children')->get();

         $hierarchy = $this->buildHierarchy($topLevelItems);
 
         return response()->json($hierarchy, 200, [], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
 
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    

    public function store(Request $request)
    {
        $validated = $request->validate([
            'products' => 'required|array',
            'products.*.id' => 'nullable|exists:product_scope,id',
            'products.*.name' => 'required|string|max:255',
            'products.*.parent_id' => 'nullable|exists:product_scope,id',
        ]);

        foreach ($validated['products'] as $productData) {
            if (isset($productData['id'])) {
                $product = ProductScope::findOrFail($productData['id']);
                $product->update([
                    'name' => $productData['name'],
                    'parent_id' => $productData['parent_id'] ?? null,
                ]);
            } else {
                ProductScope::create([
                    'name' => $productData['name'],
                    'parent_id' => $productData['parent_id'] ?? null,
                ]);
            }
        }

        return redirect()->back()->with('success', __('Products updated successfully!'));
    }


    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $product = ProductScope::findOrFail($id);
        $product->delete();
        return response()->json(['message' => __('Product deleted successfully')], 200);
    }
}
