<?php

namespace App\Http\Controllers;

use App\Models\PILayout;
use Illuminate\Http\Request;

class PILayoutController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $layouts = PILayout::orderBy('is_default', 'desc')->orderBy('name')->get();
        return view('pi-layouts.index', compact('layouts'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $defaultTemplate = $this->getDefaultTemplate();
        $sellers = \App\Models\Seller::orderBy('seller_name')->get();
        return view('pi-layouts.create', compact('defaultTemplate', 'sellers'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'template_html' => 'required|string',
            'is_active' => 'boolean',
            'is_default' => 'boolean',
            'sellers' => 'nullable|array',
            'sellers.*' => 'exists:sellers,id',
        ]);

        // If this is set as default, unset other defaults
        if ($request->is_default) {
            PILayout::where('is_default', true)->update(['is_default' => false]);
        }

        $layout = PILayout::create([
            'name' => $request->name,
            'description' => $request->description,
            'template_html' => $request->template_html,
            'is_active' => $request->has('is_active'),
            'is_default' => $request->has('is_default'),
        ]);

        // Assign layout to selected sellers
        if ($request->has('sellers') && is_array($request->sellers)) {
            \App\Models\Seller::whereIn('id', $request->sellers)->update(['pi_layout_id' => $layout->id]);
        }

        return redirect()->route('pi-layouts.index')
            ->with('success', 'PI Layout created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(PILayout $piLayout)
    {
        return view('pi-layouts.show', compact('piLayout'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(PILayout $piLayout)
    {
        $sellers = \App\Models\Seller::orderBy('seller_name')->get();
        $selectedSellerIds = $piLayout->sellers()->pluck('sellers.id')->toArray();
        return view('pi-layouts.edit', compact('piLayout', 'sellers', 'selectedSellerIds'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, PILayout $piLayout)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'template_html' => 'required|string',
            'is_active' => 'boolean',
            'is_default' => 'boolean',
            'sellers' => 'nullable|array',
            'sellers.*' => 'exists:sellers,id',
        ]);

        // If this is set as default, unset other defaults
        if ($request->is_default && !$piLayout->is_default) {
            PILayout::where('is_default', true)->where('id', '!=', $piLayout->id)->update(['is_default' => false]);
        }

        $piLayout->update([
            'name' => $request->name,
            'description' => $request->description,
            'template_html' => $request->template_html,
            'is_active' => $request->has('is_active'),
            'is_default' => $request->has('is_default'),
        ]);

        // Remove layout from all sellers first
        \App\Models\Seller::where('pi_layout_id', $piLayout->id)->update(['pi_layout_id' => null]);

        // Assign layout to selected sellers
        if ($request->has('sellers') && is_array($request->sellers)) {
            \App\Models\Seller::whereIn('id', $request->sellers)->update(['pi_layout_id' => $piLayout->id]);
        }

        return redirect()->route('pi-layouts.index')
            ->with('success', 'PI Layout updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(PILayout $piLayout)
    {
        // Check if any sellers are using this layout
        if ($piLayout->sellers()->count() > 0) {
            return back()->withErrors(['error' => 'Cannot delete layout. It is being used by ' . $piLayout->sellers()->count() . ' seller(s).']);
        }

        $piLayout->delete();

        return redirect()->route('pi-layouts.index')
            ->with('success', 'PI Layout deleted successfully.');
    }

    /**
     * Get default template HTML
     */
    private function getDefaultTemplate()
    {
        // Return a basic default template
        return '<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Proforma Invoice</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .header { text-align: center; margin-bottom: 30px; }
        .invoice-info { margin-bottom: 20px; }
        table { width: 100%; border-collapse: collapse; margin: 20px 0; }
        table th, table td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        table th { background-color: #f2f2f2; }
        .total { font-weight: bold; font-size: 1.2em; }
    </style>
</head>
<body>
    <div class="header">
        <h1>PROFORMA INVOICE</h1>
        <p>PI Number: {{ $proformaInvoice->proforma_invoice_number }}</p>
    </div>
    
    <div class="invoice-info">
        <p><strong>Contract:</strong> {{ $proformaInvoice->contract->contract_number }}</p>
        <p><strong>Customer:</strong> {{ $proformaInvoice->buyer_company_name }}</p>
        <p><strong>Date:</strong> {{ $proformaInvoice->created_at->format("d M Y") }}</p>
    </div>
    
    <table>
        <thead>
            <tr>
                <th>Description</th>
                <th>Quantity</th>
                <th>Unit Price</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($proformaInvoice->proformaInvoiceMachines as $machine)
            <tr>
                <td>{{ $machine->contractMachine->machineCategory->name ?? "Machine" }}</td>
                <td>{{ $machine->quantity }}</td>
                <td>{{ $proformaInvoice->currency }} {{ number_format($machine->amount, 2) }}</td>
                <td>{{ $proformaInvoice->currency }} {{ number_format($machine->amount * $machine->quantity, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr class="total">
                <td colspan="3" style="text-align: right;">Total:</td>
                <td>{{ $proformaInvoice->currency }} {{ number_format($proformaInvoice->total_amount, 2) }}</td>
            </tr>
        </tfoot>
    </table>
</body>
</html>';
    }
}
