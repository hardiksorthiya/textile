<?php

namespace App\Http\Controllers;

use App\Models\MachineModel;
use App\Models\Brand;
use Illuminate\Http\Request;

class MachineModelController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $machineModels = MachineModel::with('brands')->orderBy('model_no')->paginate(10);
        $brands = Brand::orderBy('name')->get();
        return view('machine-models.index', compact('machineModels', 'brands'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'model_no' => 'required|string|max:255|unique:machine_models,model_no',
            'brands' => 'required|array|min:1',
            'brands.*' => 'exists:brands,id',
        ]);

        $machineModel = MachineModel::create([
            'model_no' => $request->model_no,
            'brand_id' => null, // Keep for backward compatibility, but not used
        ]);

        // Attach multiple brands
        $machineModel->brands()->attach($request->brands);

        return redirect()->route('machine-models.index')
            ->with('success', 'Machine model added successfully.');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, MachineModel $machineModel)
    {
        $request->validate([
            'model_no' => 'required|string|max:255|unique:machine_models,model_no,' . $machineModel->id,
            'brands' => 'required|array|min:1',
            'brands.*' => 'exists:brands,id',
        ]);

        $machineModel->update([
            'model_no' => $request->model_no,
        ]);

        // Sync multiple brands (replace all existing brands with new ones)
        $machineModel->brands()->sync($request->brands);

        return redirect()->route('machine-models.index')
            ->with('success', 'Machine model updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(MachineModel $machineModel)
    {
        $machineModel->delete();

        return redirect()->route('machine-models.index')
            ->with('success', 'Machine model deleted successfully.');
    }
}
