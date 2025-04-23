<?php

namespace Modules\Document\Http\Controllers;

use Modules\Document\Entities\Category;
// use Modules\Document\Models\SubCategory;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;




class CategoryController extends Controller
{

    public function index()
    {
        if ( !Auth::user()->can('manage category')) {
            $categories = Category::where('parent_id', '=', \Auth::user()->id)->get();
            return view('document::category.index', compact('categories'));
        } else {
            return redirect()->back()->with('error', __('Permission Denied!'));
        }
    }


    public function create()
    {
        return view('document::category.create');
    }


    public function store(Request $request)
    {
        if (!Auth::user()->can('create category')) {
            $validator = \Validator::make(
                $request->all(), [
                'title' => 'required',
            ]
            );
            if ($validator->fails()) {
                $messages = $validator->getMessageBag();

                return redirect()->back()->with('error', $messages->first());
            }

            $category = new Category();
            $category->title = $request->title;
            $category->parent_id = \Auth::user()->id;
            $category->save();

            return redirect()->back()->with('success', __('Category successfully created!'));
        } else {
            return redirect()->back()->with('error', __('Permission Denied!'));
        }
    }


    public function show(category $category)
    {
        //
    }


    public function edit(category $category)
    {
        if (!Auth::user()->can('edit category')) {
            return view('document::category.edit', compact('category'));
        } else {
            return redirect()->back()->with('error', __('Permission Denied!'));
        }
    }


    public function update(Request $request, category $category)
    {
        if (!Auth::user()->can('edit category')) {
            $validator = \Validator::make(
                $request->all(), [
                'title' => 'required',
            ]
            );
            if ($validator->fails()) {
                $messages = $validator->getMessageBag();

                return redirect()->back()->with('error', $messages->first());
            }

            $category->title = $request->title;
            $category->save();

            return redirect()->back()->with('success', __('Category successfully updated!'));
        } else {
            return redirect()->back()->with('error', __('Permission Denied!'));
        }
    }


    public function destroy(category $category)
    {
        if (!Auth::user()->can('delete category')) {
            $category->delete();
            return redirect()->back()->with('success', 'Category successfully deleted!');
        } else {
            return redirect()->back()->with('error', __('Permission Denied!'));
        }
    }


    public function getSubcategory($category_id)
    {
        $subCategory = SubCategory::where('category_id', $category_id)->get()->pluck('title', 'id');
        return response()->json($subCategory);
    }
}
