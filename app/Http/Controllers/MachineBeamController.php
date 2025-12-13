<?php

namespace App\Http\Controllers;

use App\Models\MachineBeam;
use App\Models\MachineCategory;
use Illuminate\Http\Request;

class MachineBeamController extends Controller
{
    public function index()
    {
        $machineBeams = MachineBeam::with('machineCategories')->orderBy('name')->paginate(10);
        $categories = MachineCategory::orderBy('name')->get();
        return view('machine-beams.index', compact('machineBeams', 'categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:machine_beams,name',
            'categories' => 'required|array',
            'categories.*' => 'exists:machine_categories,id',
        ]);

        $machineBeam = MachineBeam::create([
            'name' => $request->name,
        ]);

        $machineBeam->machineCategories()->attach($request->categories);

        return redirect()->route('machine-beams.index')
            ->with('success', 'Machine beam added successfully.');
    }

    public function update(Request $request, MachineBeam $machineBeam)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:machine_beams,name,' . $machineBeam->id,
            'categories' => 'required|array',
            'categories.*' => 'exists:machine_categories,id',
        ]);

        $machineBeam->update([
            'name' => $request->name,
        ]);

        $machineBeam->machineCategories()->sync($request->categories);

        return redirect()->route('machine-beams.index')
            ->with('success', 'Machine beam updated successfully.');
    }

    public function destroy(MachineBeam $machineBeam)
    {
        $machineBeam->delete();

        return redirect()->route('machine-beams.index')
            ->with('success', 'Machine beam deleted successfully.');
    }
}
