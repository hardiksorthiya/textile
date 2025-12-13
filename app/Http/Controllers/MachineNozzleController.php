<?php

namespace App\Http\Controllers;

use App\Models\MachineNozzle;
use App\Models\MachineCategory;
use Illuminate\Http\Request;

class MachineNozzleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $machineNozzles = MachineNozzle::with('machineCategories')->orderBy('nozzle')->paginate(10);
        $categories = MachineCategory::orderBy('name')->get();
        return view('machine-nozzles.index', compact('machineNozzles', 'categories'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nozzle' => 'required|string|max:255|unique:machine_nozzles,nozzle',
            'categories' => 'required|array',
            'categories.*' => 'exists:machine_categories,id',
        ]);

        $machineNozzle = MachineNozzle::create([
            'nozzle' => $request->nozzle,
        ]);

        // Attach categories
        $machineNozzle->machineCategories()->attach($request->categories);

        return redirect()->route('machine-nozzles.index')
            ->with('success', 'Machine nozzle added successfully.');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, MachineNozzle $machineNozzle)
    {
        $request->validate([
            'nozzle' => 'required|string|max:255|unique:machine_nozzles,nozzle,' . $machineNozzle->id,
            'categories' => 'required|array',
            'categories.*' => 'exists:machine_categories,id',
        ]);

        $machineNozzle->update([
            'nozzle' => $request->nozzle,
        ]);

        // Sync categories
        $machineNozzle->machineCategories()->sync($request->categories);

        return redirect()->route('machine-nozzles.index')
            ->with('success', 'Machine nozzle updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(MachineNozzle $machineNozzle)
    {
        $machineNozzle->delete();

        return redirect()->route('machine-nozzles.index')
            ->with('success', 'Machine nozzle deleted successfully.');
    }
}
