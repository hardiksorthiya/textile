<?php

namespace App\Http\Controllers;

use App\Models\PurchaseOrder;
use App\Models\ProformaInvoice;
use App\Models\PortOfDestination;
use App\Models\PurchaseOrderAttachment;
use App\Models\Payment;
use App\Models\Country;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class PurchaseOrderController extends Controller
{
    /**
     * Display a listing of purchase orders
     */
    public function index()
    {
        $purchaseOrders = PurchaseOrder::with([
            'proformaInvoice.contract',
            'proformaInvoice.seller',
            'portOfDestination',
            'creator',
            'attachments'
        ])
        ->orderBy('created_at', 'desc')
        ->paginate(15);

        return view('purchase-orders.index', compact('purchaseOrders'));
    }

    /**
     * Show the form for creating a new purchase order
     */
    public function create(Request $request)
    {
        // Get proforma invoices (similar to PI create - search functionality)
        $query = ProformaInvoice::with(['contract.creator', 'seller', 'contract.contractMachines.machineCategory']);

        // Search by sales manager
        if ($request->filled('sales_manager_id')) {
            $query->whereHas('contract', function($q) use ($request) {
                $q->where('created_by', $request->sales_manager_id);
            });
        }

        // Search by PI number
        if ($request->filled('pi_number')) {
            $query->where('proforma_invoice_number', 'like', '%' . $request->pi_number . '%');
        }

        // Search by customer name
        if ($request->filled('customer_name')) {
            $query->where('buyer_company_name', 'like', '%' . $request->customer_name . '%');
        }

        // If proforma_invoice_id is provided, select it
        $selectedProformaInvoiceId = $request->filled('proforma_invoice_id') ? $request->proforma_invoice_id : null;
        if ($selectedProformaInvoiceId) {
            $query->where('id', $selectedProformaInvoiceId);
        }

        $proformaInvoices = $query->orderBy('created_at', 'desc')->paginate(10)->withQueryString();

        // Get all users who can be sales managers
        $salesManagers = User::whereHas('createdContracts')->orderBy('name')->get();

        // Get port of destinations
        $portOfDestinations = PortOfDestination::orderBy('name')->get();

        return view('purchase-orders.create', compact(
            'proformaInvoices',
            'salesManagers',
            'selectedProformaInvoiceId',
            'portOfDestinations'
        ));
    }

    /**
     * Get first payment transaction with $ currency for a PI
     */
    public function getFirstPaymentTransaction($proformaInvoiceId)
    {
        $payment = Payment::with(['payeeCountry', 'paymentToSeller', 'bankDetail'])
            ->where('proforma_invoice_id', $proformaInvoiceId)
            ->whereHas('payeeCountry', function($q) {
                $q->where('currency', '$');
            })
            ->orderBy('payment_date', 'asc')
            ->orderBy('created_at', 'asc')
            ->first();

        if ($payment) {
            return response()->json([
                'success' => true,
                'payment' => [
                    'id' => $payment->id,
                    'amount' => (string) $payment->amount,
                    'payment_date' => $payment->payment_date->format('Y-m-d'),
                    'payment_method' => $payment->payment_method,
                    'payment_by' => $payment->payment_by,
                    'transaction_id' => $payment->transaction_id,
                    'payee_country' => $payment->payeeCountry ? $payment->payeeCountry->name : null,
                    'payment_to_seller' => $payment->paymentToSeller ? $payment->paymentToSeller->seller_name : null,
                    'bank_name' => $payment->bankDetail ? $payment->bankDetail->bank_name : null,
                ]
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'No payment transaction found with $ currency for this PI'
        ]);
    }

    /**
     * Store a newly created purchase order
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'proforma_invoice_id' => 'required|exists:proforma_invoices,id',
            'purchase_order_number' => 'required|string|max:255|unique:purchase_orders,purchase_order_number',
            'buyer_name' => 'required|string|max:255',
            'no_of_bill' => 'nullable|integer|min:0',
            'no_of_container' => 'nullable|integer|min:0',
            'size_of_container' => 'nullable|string|max:255',
            'port_of_destination_id' => 'nullable|exists:port_of_destinations,id',
            'notes' => 'nullable|string',
            'attachments' => 'nullable|array',
            'attachments.*' => 'file|mimes:pdf,doc,docx,jpg,jpeg,png,gif,zip,rar|max:10240',
        ]);

        // Generate PO number if not provided or if it already exists
        if (empty($validated['purchase_order_number']) || PurchaseOrder::where('purchase_order_number', $validated['purchase_order_number'])->exists()) {
            $pi = ProformaInvoice::find($validated['proforma_invoice_id']);
            $counter = PurchaseOrder::whereDate('created_at', today())->count() + 1;
            $validated['purchase_order_number'] = 'PO-' . date('Ymd') . '-' . str_pad($counter, 4, '0', STR_PAD_LEFT);
        }

        $validated['created_by'] = Auth::id();

        DB::beginTransaction();
        try {
            $purchaseOrder = PurchaseOrder::create($validated);

            // Handle file attachments
            if ($request->hasFile('attachments')) {
                foreach ($request->file('attachments') as $file) {
                    $filePath = $file->store('purchase-order-attachments', 'public');
                    PurchaseOrderAttachment::create([
                        'purchase_order_id' => $purchaseOrder->id,
                        'file_path' => $filePath,
                        'file_name' => $file->getClientOriginalName(),
                        'file_type' => $file->getMimeType(),
                        'file_size' => $file->getSize(),
                    ]);
                }
            }

            DB::commit();

            return redirect()->route('purchase-orders.index')
                ->with('success', 'Purchase Order created successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to create purchase order: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified purchase order
     */
    public function show(PurchaseOrder $purchaseOrder)
    {
        $purchaseOrder->load([
            'proformaInvoice.contract.creator',
            'proformaInvoice.seller',
            'proformaInvoice.proformaInvoiceMachines.machineCategory',
            'portOfDestination',
            'creator',
            'attachments'
        ]);

        return view('purchase-orders.show', compact('purchaseOrder'));
    }

    /**
     * Show the form for editing the specified purchase order
     */
    public function edit(PurchaseOrder $purchaseOrder)
    {
        $purchaseOrder->load([
            'proformaInvoice.contract.creator',
            'proformaInvoice.seller',
            'proformaInvoice.proformaInvoiceMachines.machineCategory',
            'portOfDestination',
            'attachments'
        ]);

        $portOfDestinations = PortOfDestination::orderBy('name')->get();

        return view('purchase-orders.edit', compact('purchaseOrder', 'portOfDestinations'));
    }

    /**
     * Update the specified purchase order
     */
    public function update(Request $request, PurchaseOrder $purchaseOrder)
    {
        $validated = $request->validate([
            'buyer_name' => 'required|string|max:255',
            'no_of_bill' => 'nullable|integer|min:0',
            'no_of_container' => 'nullable|integer|min:0',
            'size_of_container' => 'nullable|string|max:255',
            'port_of_destination_id' => 'nullable|exists:port_of_destinations,id',
            'notes' => 'nullable|string',
            'attachments' => 'nullable|array',
            'attachments.*' => 'file|mimes:pdf,doc,docx,jpg,jpeg,png,gif,zip,rar|max:10240',
        ]);

        DB::beginTransaction();
        try {
            $purchaseOrder->update($validated);

            // Handle new file attachments
            if ($request->hasFile('attachments')) {
                foreach ($request->file('attachments') as $file) {
                    $filePath = $file->store('purchase-order-attachments', 'public');
                    PurchaseOrderAttachment::create([
                        'purchase_order_id' => $purchaseOrder->id,
                        'file_path' => $filePath,
                        'file_name' => $file->getClientOriginalName(),
                        'file_type' => $file->getMimeType(),
                        'file_size' => $file->getSize(),
                    ]);
                }
            }

            DB::commit();

            return redirect()->route('purchase-orders.index')
                ->with('success', 'Purchase Order updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to update purchase order: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified purchase order
     */
    public function destroy(PurchaseOrder $purchaseOrder)
    {
        // Delete attachments
        foreach ($purchaseOrder->attachments as $attachment) {
            if (Storage::disk('public')->exists($attachment->file_path)) {
                Storage::disk('public')->delete($attachment->file_path);
            }
        }

        $purchaseOrder->delete();

        return redirect()->route('purchase-orders.index')
            ->with('success', 'Purchase Order deleted successfully.');
    }

    /**
     * Delete a specific attachment
     */
    public function deleteAttachment(PurchaseOrderAttachment $attachment)
    {
        if (Storage::disk('public')->exists($attachment->file_path)) {
            Storage::disk('public')->delete($attachment->file_path);
        }

        $attachment->delete();

        return redirect()->back()
            ->with('success', 'Attachment deleted successfully.');
    }
}
