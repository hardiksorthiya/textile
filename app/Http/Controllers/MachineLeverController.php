<?php

namespace App\Http\Controllers;

use App\Models\MachineLever;
use App\Models\MachineCategory;
use Illuminate\Http\Request;

class MachineLeverController extends Controller
{
    public function index()
    {
        $machineLevers = MachineLever::with('machineCategories')->orderBy('name')->paginate(10);
        $categories = MachineCategory::orderBy('name')->get();
        return view('machine-levers.index', compact('machineLevers', 'categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:machine_levers,name',
            'categories' => 'required|array',
            'categories.*' => 'exists:machine_categories,id',
        ]);

        $machineLever = MachineLever::create([
            'name' => $request->name,
        ]);

        $machineLever->machineCategories()->attach($request->categories);

        return redirect()->route('machine-levers.index')
            ->with('success', 'Machine lever added successfully.');
    }

    public function update(Request $request, MachineLever $machineLever)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:machine_levers,name,' . $machineLever->id,
            'categories' => 'required|array',
            'categories.*' => 'exists:machine_categories,id',
        ]);

        $machineLever->update([
            'name' => $request->name,
        ]);

        $machineLever->machineCategories()->sync($request->categories);

        return redirect()->route('machine-levers.index')
            ->with('success', 'Machine lever updated successfully.');
    }

    public function destroy(MachineLever $machineLever)
    {
        $machineLever->delete();

        return redirect()->route('machine-levers.index')
            ->with('success', 'Machine lever deleted successfully.');
    }
}
