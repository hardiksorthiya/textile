<?php

namespace App\Http\Controllers;

use App\Models\FlangeSize;
use App\Models\MachineCategory;
use Illuminate\Http\Request;

class FlangeSizeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $flangeSizes = FlangeSize::with('machineCategories')->orderBy('name')->paginate(10);
        $categories = MachineCategory::orderBy('name')->get();
        return view('flange-sizes.index', compact('flangeSizes', 'categories'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:flange_sizes,name',
            'categories' => 'required|array',
            'categories.*' => 'exists:machine_categories,id',
        ]);

        $flangeSize = FlangeSize::create([
            'name' => $request->name,
        ]);

        // Attach categories
        $flangeSize->machineCategories()->attach($request->categories);

        return redirect()->route('flange-sizes.index')
            ->with('success', 'Flange size added successfully.');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, FlangeSize $flangeSize)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:flange_sizes,name,' . $flangeSize->id,
            'categories' => 'required|array',
            'categories.*' => 'exists:machine_categories,id',
        ]);

        $flangeSize->update([
            'name' => $request->name,
        ]);

        // Sync categories
        $flangeSize->machineCategories()->sync($request->categories);

        return redirect()->route('flange-sizes.index')
            ->with('success', 'Flange size updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(FlangeSize $flangeSize)
    {
        $flangeSize->delete();

        return redirect()->route('flange-sizes.index')
            ->with('success', 'Flange size deleted successfully.');
    }
}
