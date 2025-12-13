<?php

namespace App\Http\Controllers;

use App\Models\MachineChain;
use App\Models\MachineCategory;
use Illuminate\Http\Request;

class MachineChainController extends Controller
{
    public function index()
    {
        $machineChains = MachineChain::with('machineCategories')->orderBy('name')->paginate(10);
        $categories = MachineCategory::orderBy('name')->get();
        return view('machine-chains.index', compact('machineChains', 'categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:machine_chains,name',
            'categories' => 'required|array',
            'categories.*' => 'exists:machine_categories,id',
        ]);

        $machineChain = MachineChain::create([
            'name' => $request->name,
        ]);

        $machineChain->machineCategories()->attach($request->categories);

        return redirect()->route('machine-chains.index')
            ->with('success', 'Machine chain added successfully.');
    }

    public function update(Request $request, MachineChain $machineChain)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:machine_chains,name,' . $machineChain->id,
            'categories' => 'required|array',
            'categories.*' => 'exists:machine_categories,id',
        ]);

        $machineChain->update([
            'name' => $request->name,
        ]);

        $machineChain->machineCategories()->sync($request->categories);

        return redirect()->route('machine-chains.index')
            ->with('success', 'Machine chain updated successfully.');
    }

    public function destroy(MachineChain $machineChain)
    {
        $machineChain->delete();

        return redirect()->route('machine-chains.index')
            ->with('success', 'Machine chain deleted successfully.');
    }
}
