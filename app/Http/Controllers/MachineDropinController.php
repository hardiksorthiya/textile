<?php

namespace App\Http\Controllers;

use App\Models\MachineDropin;
use App\Models\MachineCategory;
use Illuminate\Http\Request;

class MachineDropinController extends Controller
{
    public function index()
    {
        $machineDropins = MachineDropin::with('machineCategories')->orderBy('name')->paginate(10);
        $categories = MachineCategory::orderBy('name')->get();
        return view('machine-dropins.index', compact('machineDropins', 'categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:machine_dropins,name',
            'categories' => 'required|array',
            'categories.*' => 'exists:machine_categories,id',
        ]);

        $machineDropin = MachineDropin::create([
            'name' => $request->name,
        ]);

        $machineDropin->machineCategories()->attach($request->categories);

        return redirect()->route('machine-dropins.index')
            ->with('success', 'Machine dropin added successfully.');
    }

    public function update(Request $request, MachineDropin $machineDropin)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:machine_dropins,name,' . $machineDropin->id,
            'categories' => 'required|array',
            'categories.*' => 'exists:machine_categories,id',
        ]);

        $machineDropin->update([
            'name' => $request->name,
        ]);

        $machineDropin->machineCategories()->sync($request->categories);

        return redirect()->route('machine-dropins.index')
            ->with('success', 'Machine dropin updated successfully.');
    }

    public function destroy(MachineDropin $machineDropin)
    {
        $machineDropin->delete();

        return redirect()->route('machine-dropins.index')
            ->with('success', 'Machine dropin deleted successfully.');
    }
}
