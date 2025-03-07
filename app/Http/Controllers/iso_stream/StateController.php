<?php
namespace App\Http\Controllers;

use App\Models\Country;
use App\Models\State;
use Illuminate\Http\Request;

class StateController extends Controller
{
    public function index($countryId)
    {
        $country = Country::findOrFail($countryId);
        $states = $country->states;

        return response()->json([
            'data' => $states
        ]);
    }
}
