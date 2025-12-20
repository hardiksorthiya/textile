<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\Contract;
use App\Models\ProformaInvoice;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PaymentController extends Controller
{
    /**
     * Display all payments (collect and return) in one table
     */
    public function index(Request $request)
    {
        $query = Payment::with(['contract.creator', 'proformaInvoice.contract.creator', 'proformaInvoice.seller', 'creator'])
            ->orderBy('payment_date', 'desc');

        // Filter by type
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        // Filter by Sales Manager
        if ($request->filled('sales_manager')) {
            $query->where(function($q) use ($request) {
                $q->whereHas('contract', function($contractQuery) use ($request) {
                    $contractQuery->where('created_by', $request->sales_manager);
                })
                ->orWhereHas('proformaInvoice.contract', function($piQuery) use ($request) {
                    $piQuery->where('created_by', $request->sales_manager);
                });
            });
        }

        // Filter by Contract Number
        if ($request->filled('contract_number')) {
            $query->where(function($q) use ($request) {
                $q->whereHas('contract', function($contractQuery) use ($request) {
                    $contractQuery->where('contract_number', 'like', '%' . $request->contract_number . '%');
                })
                ->orWhereHas('proformaInvoice.contract', function($piQuery) use ($request) {
                    $piQuery->where('contract_number', 'like', '%' . $request->contract_number . '%');
                });
            });
        }

        // Filter by Customer Name
        if ($request->filled('customer_name')) {
            $query->where(function($q) use ($request) {
                $q->whereHas('contract', function($contractQuery) use ($request) {
                    $contractQuery->where('buyer_name', 'like', '%' . $request->customer_name . '%')
                                  ->orWhere('company_name', 'like', '%' . $request->customer_name . '%');
                })
                ->orWhereHas('proformaInvoice', function($piQuery) use ($request) {
                    $piQuery->where('buyer_company_name', 'like', '%' . $request->customer_name . '%');
                });
            });
        }

        // Get sales managers
        $salesManagers = User::whereIn('id', function($query) {
            $query->select('created_by')
                ->from('contracts')
                ->distinct();
        })->select('id', 'name')->orderBy('name')->get();

        $payments = $query->paginate(15)->withQueryString();

        return view('payments.index', compact('payments', 'salesManagers'));
    }

    /**
     * Display collect payments list or create form
     */
    public function collectPayment(Request $request)
    {
        // Get sales managers
        $salesManagers = User::whereIn('id', function($query) {
            $query->select('created_by')
                ->from('contracts')
                ->distinct();
        })->select('id', 'name')->orderBy('name')->get();

        // If contract_id or proforma_invoice_id is provided, show payment form
        if ($request->filled('contract_id')) {
            $contract = Contract::with(['creator'])->findOrFail($request->contract_id);
            return view('payments.collect-payment', compact('salesManagers', 'contract'));
        }

        if ($request->filled('proforma_invoice_id')) {
            $proformaInvoice = ProformaInvoice::with(['contract.creator', 'seller'])->findOrFail($request->proforma_invoice_id);
            return view('payments.collect-payment', compact('salesManagers', 'proformaInvoice'));
        }

        // Get contracts filtered by sales manager
        $contracts = collect();
        if ($request->filled('sales_manager')) {
            $contracts = Contract::with(['creator'])
                ->where('created_by', $request->sales_manager)
                ->whereNotNull('approval_status')
                ->where('approval_status', 'approved')
                ->orderBy('contract_number')
                ->get();
        }

        // Get proforma invoices filtered by sales manager
        $proformaInvoices = collect();
        if ($request->filled('sales_manager')) {
            $proformaInvoices = ProformaInvoice::with(['contract.creator', 'seller'])
                ->whereHas('contract', function($q) use ($request) {
                    $q->where('created_by', $request->sales_manager);
                })
                ->orderBy('proforma_invoice_number')
                ->get();
        }

        return view('payments.collect-payment', compact('salesManagers', 'contracts', 'proformaInvoices'));
    }

    /**
     * Get contracts by sales manager (AJAX)
     */
    public function getContractsBySalesManager(Request $request)
    {
        if (!$request->filled('sales_manager_id')) {
            return response()->json([]);
        }

        $contracts = Contract::with(['creator'])
            ->where('created_by', $request->sales_manager_id)
            ->whereNotNull('approval_status')
            ->where('approval_status', 'approved')
            ->orderBy('contract_number')
            ->get()
            ->map(function($contract) {
                return [
                    'id' => $contract->id,
                    'contract_number' => $contract->contract_number,
                    'buyer_name' => $contract->buyer_name,
                    'company_name' => $contract->company_name,
                    'total_amount' => $contract->total_amount,
                    'creator' => $contract->creator ? ['name' => $contract->creator->name] : null,
                ];
            });

        return response()->json($contracts);
    }

    /**
     * Get proforma invoices by sales manager (AJAX)
     */
    public function getProformaInvoicesBySalesManager(Request $request)
    {
        if (!$request->filled('sales_manager_id')) {
            return response()->json([]);
        }

        $proformaInvoices = ProformaInvoice::with(['contract.creator', 'seller'])
            ->whereHas('contract', function($q) use ($request) {
                $q->where('created_by', $request->sales_manager_id);
            })
            ->orderBy('proforma_invoice_number')
            ->get()
            ->map(function($pi) {
                return [
                    'id' => $pi->id,
                    'proforma_invoice_number' => $pi->proforma_invoice_number,
                    'buyer_company_name' => $pi->buyer_company_name,
                    'total_amount' => $pi->total_amount,
                    'contract' => $pi->contract ? [
                        'contract_number' => $pi->contract->contract_number,
                        'creator' => $pi->contract->creator ? ['name' => $pi->contract->creator->name] : null,
                    ] : null,
                    'seller' => $pi->seller ? ['seller_name' => $pi->seller->seller_name] : null,
                ];
            });

        return response()->json($proformaInvoices);
    }

    /**
     * Display return payments list or create form
     */
    public function returnPayment(Request $request)
    {
        // Get sales managers
        $salesManagers = User::whereIn('id', function($query) {
            $query->select('created_by')
                ->from('contracts')
                ->distinct();
        })->select('id', 'name')->orderBy('name')->get();

        // If contract_id or proforma_invoice_id is provided, show payment form
        if ($request->filled('contract_id')) {
            $contract = Contract::with(['creator'])->findOrFail($request->contract_id);
            return view('payments.return-payment', compact('salesManagers', 'contract'));
        }

        if ($request->filled('proforma_invoice_id')) {
            $proformaInvoice = ProformaInvoice::with(['contract.creator', 'seller'])->findOrFail($request->proforma_invoice_id);
            return view('payments.return-payment', compact('salesManagers', 'proformaInvoice'));
        }

        // Get contracts filtered by sales manager
        $contracts = collect();
        if ($request->filled('sales_manager')) {
            $contracts = Contract::with(['creator'])
                ->where('created_by', $request->sales_manager)
                ->whereNotNull('approval_status')
                ->where('approval_status', 'approved')
                ->orderBy('contract_number')
                ->get();
        }

        // Get proforma invoices filtered by sales manager
        $proformaInvoices = collect();
        if ($request->filled('sales_manager')) {
            $proformaInvoices = ProformaInvoice::with(['contract.creator', 'seller'])
                ->whereHas('contract', function($q) use ($request) {
                    $q->where('created_by', $request->sales_manager);
                })
                ->orderBy('proforma_invoice_number')
                ->get();
        }

        return view('payments.return-payment', compact('salesManagers', 'contracts', 'proformaInvoices'));
    }

    /**
     * Store a new payment
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'type' => 'required|in:collect,return',
            'contract_id' => 'nullable|exists:contracts,id',
            'proforma_invoice_id' => 'nullable|exists:proforma_invoices,id',
            'amount' => 'required|numeric|min:0',
            'payment_date' => 'required|date',
            'payment_method' => 'nullable|string|max:255',
            'reference_number' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
        ]);

        $validated['created_by'] = Auth::id();

        Payment::create($validated);

        $redirectRoute = $validated['type'] === 'collect' 
            ? 'payments.collect-payment' 
            : 'payments.return-payment';

        return redirect()->route($redirectRoute)
            ->with('success', ucfirst($validated['type']) . ' payment added successfully.');
    }
}
