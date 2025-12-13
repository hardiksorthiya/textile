<?php

namespace App\Http\Controllers;

use App\Models\Area;
use App\Models\City;
use Illuminate\Http\Request;

class AreaController extends Controller
{
    public function index()
    {
        $areas = Area::with('city.state')->orderBy('name')->paginate(10);
        $cities = City::with('state')->orderBy('name')->get();
        return view('areas.index', compact('areas', 'cities'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'city_id' => 'required|exists:cities,id',
        ]);

        Area::create([
            'name' => $request->name,
            'city_id' => $request->city_id,
        ]);

        return redirect()->route('areas.index')
            ->with('success', 'Area added successfully.');
    }

    public function update(Request $request, Area $area)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'city_id' => 'required|exists:cities,id',
        ]);

        $area->update([
            'name' => $request->name,
            'city_id' => $request->city_id,
        ]);

        return redirect()->route('areas.index')
            ->with('success', 'Area updated successfully.');
    }

    public function destroy(Area $area)
    {
        $area->delete();

        return redirect()->route('areas.index')
            ->with('success', 'Area deleted successfully.');
    }
}
