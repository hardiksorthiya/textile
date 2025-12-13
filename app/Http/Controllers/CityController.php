<?php

namespace App\Http\Controllers;

use App\Models\City;
use App\Models\State;
use Illuminate\Http\Request;

class CityController extends Controller
{
    public function index()
    {
        $cities = City::with('state')->orderBy('name')->paginate(10);
        $states = State::orderBy('name')->get();
        return view('cities.index', compact('cities', 'states'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'state_id' => 'required|exists:states,id',
        ]);

        City::create([
            'name' => $request->name,
            'state_id' => $request->state_id,
        ]);

        return redirect()->route('cities.index')
            ->with('success', 'City added successfully.');
    }

    public function update(Request $request, City $city)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'state_id' => 'required|exists:states,id',
        ]);

        $city->update([
            'name' => $request->name,
            'state_id' => $request->state_id,
        ]);

        return redirect()->route('cities.index')
            ->with('success', 'City updated successfully.');
    }

    public function destroy(City $city)
    {
        $city->delete();

        return redirect()->route('cities.index')
            ->with('success', 'City deleted successfully.');
    }
}
