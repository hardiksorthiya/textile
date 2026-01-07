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

        return redirect()->route('settings.pi-layouts')
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
     * Preview the layout template
     */
    public function preview(PILayout $piLayout)
    {
        // Get a sample proforma invoice for preview, or create mock data
        $sampleProformaInvoice = \App\Models\ProformaInvoice::with([
            'contract.businessFirm',
            'contract.state',
            'contract.city',
            'contract.area',
            'seller.country',
            'seller.bankDetails',
            'creator',
            'proformaInvoiceMachines.contractMachine.machineCategory',
            'proformaInvoiceMachines.contractMachine.brand',
            'proformaInvoiceMachines.contractMachine.machineModel',
            'proformaInvoiceMachines.contractMachine.color',
            'proformaInvoiceMachines.contractMachine.hsnCode',
        ])->first();

        // If no proforma invoice exists, create mock data
        if (!$sampleProformaInvoice) {
            $sampleProformaInvoice = $this->createMockProformaInvoice();
        }

        // Compile the template HTML with sample data
        $previewHtml = $this->renderTemplate($piLayout->template_html, $sampleProformaInvoice);

        return response($previewHtml)->header('Content-Type', 'text/html');
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

        return redirect()->route('settings.pi-layouts')
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

        return redirect()->route('settings.pi-layouts')
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

    /**
     * Render template HTML with proforma invoice data
     */
    private function renderTemplate($templateHtml, $proformaInvoice)
    {
        try {
            // Create a temporary view file
            $tempPath = storage_path('app/temp_template_' . uniqid() . '.blade.php');
            file_put_contents($tempPath, $templateHtml);

            // Compile the view with data
            $rendered = view()->file($tempPath, [
                'proformaInvoice' => $proformaInvoice,
                'seller' => $proformaInvoice->seller ?? null,
                'machines' => $proformaInvoice->proformaInvoiceMachines ?? collect(),
            ])->render();

            // Clean up temporary file
            @unlink($tempPath);

            return $rendered;
        } catch (\Exception $e) {
            // Return error message if template fails to compile
            return '<div class="alert alert-danger">Error rendering template: ' . htmlspecialchars($e->getMessage()) . '</div>';
        }
    }

    /**
     * Create mock proforma invoice for preview
     */
    private function createMockProformaInvoice()
    {
        $mock = new \stdClass();
        
        // Basic invoice data
        $mock->id = 0;
        $mock->proforma_invoice_number = 'PI-PREVIEW-001';
        $mock->buyer_company_name = 'Sample Company Ltd.';
        $mock->currency = 'USD';
        $mock->total_amount = 50000.00;
        $mock->created_at = now();
        $mock->billing_address = "123 Sample Street\nSample City, Sample State 12345";
        $mock->shipping_address = "123 Sample Street\nSample City, Sample State 12345";
        $mock->notes = 'This is a preview with sample data.';
        
        // Mock contract
        $mock->contract = new \stdClass();
        $mock->contract->contract_number = 'CONTRACT-PREVIEW-001';
        $mock->contract->buyer_name = 'Sample Customer';
        $mock->contract->company_name = 'Sample Company Ltd.';
        $mock->contract->businessFirm = (object)['name' => 'Sample Business Firm'];
        $mock->contract->state = (object)['name' => 'Sample State'];
        $mock->contract->city = (object)['name' => 'Sample City'];
        $mock->contract->area = (object)['name' => 'Sample Area'];
        $mock->contract->loading_terms = 'FOB';
        $mock->contract->payment_terms = '30 days';
        $mock->contract->contact_address = $mock->billing_address;
        
        // Mock seller
        $mock->seller = new \stdClass();
        $mock->seller->seller_name = 'Sample Seller';
        $mock->seller->address = 'Seller Address, Seller City';
        $mock->seller->country = (object)['name' => 'CHINA'];
        $mock->seller->bankDetails = collect([]);
        
        // Mock creator
        $mock->creator = new \stdClass();
        $mock->creator->name = 'System User';
        
        // Mock machines collection
        $machine = new \stdClass();
        $machine->id = 1;
        $machine->quantity = 2;
        $machine->amount = 25000.00;
        
        $machine->contractMachine = new \stdClass();
        $machine->contractMachine->machineCategory = (object)['name' => 'Sample Machine Category'];
        $machine->contractMachine->brand = (object)['name' => 'Sample Brand'];
        $machine->contractMachine->machineModel = (object)['model_no' => 'MODEL-001'];
        $machine->contractMachine->color = (object)['name' => 'Red'];
        $machine->contractMachine->hsnCode = (object)['name' => 'HSN123456'];
        
        $mock->proformaInvoiceMachines = collect([$machine]);
        
        return $mock;
    }
}
