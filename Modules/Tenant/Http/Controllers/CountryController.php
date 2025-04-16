<?php

namespace Modules\Tenant\Http\Controllers;

use Illuminate\Routing\Controller;
use Modules\Tenant\Models\Country;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class CountryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $countries = Country::all();
        return view('iso_dic.countries.index', compact('countries'));
    }

    public function getStates(Request $request)
    {
        $countryId = $request->input('country_id');
        $states = Country::find($countryId)->states;
        return view('iso_dic.countries.states', compact('states'));
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
        $validator = Validator::make($request->all(), [
            'name_ar' => 'required|string|max:255',
            'name_en' => 'required|string|max:255',
            'code' => [
                'required',
                'string',
                'max:2',
                'unique:countries',
            ],
        ]);
    
        if ($validator->fails()) {
            $errorMessages = $validator->errors()->all();
            $formattedErrors = implode('<br>', $errorMessages);
            return redirect()->back()
                ->with('error', $formattedErrors)
                ->withInput()
                ->withErrors($validator);
        }
    
        Country::create([
            'name_ar' => $request->name_ar,
            'name_en' => $request->name_en,
            'code' => strtoupper($request->code),
        ]);
    
        return redirect()->route('iso_dic.countries.index')
            ->with('success', 'تم إنشاء الدولة بنجاح!');
    }

    /**
     * Display the specified resource.
     */
    public function edit(Country $country)
    {
        return view('iso_dic.countries.edit', compact('country'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Country $country)
    {
        $validator = Validator::make($request->all(), [
            'name_ar' => 'required|string|max:255',
            'name_en' => 'required|string|max:255',
            'code' => [
                'required',
                'string',
                'max:2',
                Rule::unique('countries')->ignore($country->id),
            ],
        ]);
    
        if ($validator->fails()) {
            $errorMessages = $validator->errors()->all();
            $formattedErrors = implode('<br>', $errorMessages);
            return redirect()->back()
                ->with('error', $formattedErrors)
                ->withInput()
                ->withErrors($validator);
        }
    
        $country->update([
            'name_ar' => $request->name_ar,
            'name_en' => $request->name_en,
            'code' => strtoupper($request->code),
        ]);
    
        return redirect()->route('iso_dic.countries.index')
            ->with('success', 'تم تحديث الدولة بنجاح!');
    }
    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
