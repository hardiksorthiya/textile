<?php

namespace App\Http\Controllers;

use App\Models\MachineShaft;
use App\Models\MachineCategory;
use Illuminate\Http\Request;

class MachineShaftController extends Controller
{
    public function index()
    {
        $machineShafts = MachineShaft::with('machineCategories')->orderBy('name')->paginate(10);
        $categories = MachineCategory::orderBy('name')->get();
        return view('machine-shafts.index', compact('machineShafts', 'categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:machine_shafts,name',
            'categories' => 'required|array',
            'categories.*' => 'exists:machine_categories,id',
        ]);

        $machineShaft = MachineShaft::create([
            'name' => $request->name,
        ]);

        $machineShaft->machineCategories()->attach($request->categories);

        return redirect()->route('machine-shafts.index')
            ->with('success', 'Machine shaft added successfully.');
    }

    public function update(Request $request, MachineShaft $machineShaft)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:machine_shafts,name,' . $machineShaft->id,
            'categories' => 'required|array',
            'categories.*' => 'exists:machine_categories,id',
        ]);

        $machineShaft->update([
            'name' => $request->name,
        ]);

        $machineShaft->machineCategories()->sync($request->categories);

        return redirect()->route('machine-shafts.index')
            ->with('success', 'Machine shaft updated successfully.');
    }

    public function destroy(MachineShaft $machineShaft)
    {
        $machineShaft->delete();

        return redirect()->route('machine-shafts.index')
            ->with('success', 'Machine shaft deleted successfully.');
    }
}
