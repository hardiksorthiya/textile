<?php

namespace App\Http\Controllers;

use App\Models\DeliveryTerm;
use Illuminate\Http\Request;

class DeliveryTermController extends Controller
{
    public function index()
    {
        $deliveryTerms = DeliveryTerm::orderBy('name')->paginate(10);
        return view('delivery-terms.index', compact('deliveryTerms'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:delivery_terms,name',
        ]);

        DeliveryTerm::create([
            'name' => $request->name,
        ]);

        return redirect()->route('delivery-terms.index')
            ->with('success', 'Delivery term added successfully.');
    }

    public function update(Request $request, DeliveryTerm $deliveryTerm)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:delivery_terms,name,' . $deliveryTerm->id,
        ]);

        $deliveryTerm->update([
            'name' => $request->name,
        ]);

        return redirect()->route('delivery-terms.index')
            ->with('success', 'Delivery term updated successfully.');
    }

    public function destroy(DeliveryTerm $deliveryTerm)
    {
        $deliveryTerm->delete();

        return redirect()->route('delivery-terms.index')
            ->with('success', 'Delivery term deleted successfully.');
    }
}
