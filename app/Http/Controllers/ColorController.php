<?php

namespace App\Http\Controllers;

use App\Models\Color;
use App\Models\MachineCategory;
use Illuminate\Http\Request;

class ColorController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $colors = Color::with('machineCategories')->orderBy('name')->paginate(10);
        $categories = MachineCategory::orderBy('name')->get();
        return view('colors.index', compact('colors', 'categories'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:colors,name',
            'categories' => 'required|array',
            'categories.*' => 'exists:machine_categories,id',
        ]);

        $color = Color::create([
            'name' => $request->name,
        ]);

        // Attach categories
        $color->machineCategories()->attach($request->categories);

        return redirect()->route('colors.index')
            ->with('success', 'Color added successfully.');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Color $color)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:colors,name,' . $color->id,
            'categories' => 'required|array',
            'categories.*' => 'exists:machine_categories,id',
        ]);

        $color->update([
            'name' => $request->name,
        ]);

        // Sync categories
        $color->machineCategories()->sync($request->categories);

        return redirect()->route('colors.index')
            ->with('success', 'Color updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Color $color)
    {
        $color->delete();

        return redirect()->route('colors.index')
            ->with('success', 'Color deleted successfully.');
    }
}
