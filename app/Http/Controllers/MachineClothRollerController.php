<?php

namespace App\Http\Controllers;

use App\Models\MachineClothRoller;
use App\Models\MachineCategory;
use Illuminate\Http\Request;

class MachineClothRollerController extends Controller
{
    public function index()
    {
        $machineClothRollers = MachineClothRoller::with('machineCategories')->orderBy('name')->paginate(10);
        $categories = MachineCategory::orderBy('name')->get();
        return view('machine-cloth-rollers.index', compact('machineClothRollers', 'categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:machine_cloth_rollers,name',
            'categories' => 'required|array',
            'categories.*' => 'exists:machine_categories,id',
        ]);

        $machineClothRoller = MachineClothRoller::create([
            'name' => $request->name,
        ]);

        $machineClothRoller->machineCategories()->attach($request->categories);

        return redirect()->route('machine-cloth-rollers.index')
            ->with('success', 'Machine cloth roller added successfully.');
    }

    public function update(Request $request, MachineClothRoller $machineClothRoller)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:machine_cloth_rollers,name,' . $machineClothRoller->id,
            'categories' => 'required|array',
            'categories.*' => 'exists:machine_categories,id',
        ]);

        $machineClothRoller->update([
            'name' => $request->name,
        ]);

        $machineClothRoller->machineCategories()->sync($request->categories);

        return redirect()->route('machine-cloth-rollers.index')
            ->with('success', 'Machine cloth roller updated successfully.');
    }

    public function destroy(MachineClothRoller $machineClothRoller)
    {
        $machineClothRoller->delete();

        return redirect()->route('machine-cloth-rollers.index')
            ->with('success', 'Machine cloth roller deleted successfully.');
    }
}
