<?php

namespace App\Http\Controllers;

use App\Models\Feeder;
use App\Models\FeederBrand;
use App\Models\MachineCategory;
use Illuminate\Http\Request;

class FeederController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $feeders = Feeder::with(['feederBrand', 'machineCategories'])->orderBy('feeder')->paginate(10);
        $feederBrands = FeederBrand::orderBy('name')->get();
        $categories = MachineCategory::orderBy('name')->get();
        return view('feeders.index', compact('feeders', 'feederBrands', 'categories'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'feeder' => 'required|string|max:255|unique:feeders,feeder',
            'feeder_brand_id' => 'required|exists:feeder_brands,id',
            'categories' => 'required|array',
            'categories.*' => 'exists:machine_categories,id',
        ]);

        $feeder = Feeder::create([
            'feeder' => $request->feeder,
            'feeder_brand_id' => $request->feeder_brand_id,
        ]);

        // Attach categories
        $feeder->machineCategories()->attach($request->categories);

        return redirect()->route('feeders.index')
            ->with('success', 'Feeder added successfully.');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Feeder $feeder)
    {
        $request->validate([
            'feeder' => 'required|string|max:255|unique:feeders,feeder,' . $feeder->id,
            'feeder_brand_id' => 'required|exists:feeder_brands,id',
            'categories' => 'required|array',
            'categories.*' => 'exists:machine_categories,id',
        ]);

        $feeder->update([
            'feeder' => $request->feeder,
            'feeder_brand_id' => $request->feeder_brand_id,
        ]);

        // Sync categories
        $feeder->machineCategories()->sync($request->categories);

        return redirect()->route('feeders.index')
            ->with('success', 'Feeder updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Feeder $feeder)
    {
        $feeder->delete();

        return redirect()->route('feeders.index')
            ->with('success', 'Feeder deleted successfully.');
    }
}
