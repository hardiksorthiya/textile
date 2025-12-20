<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\Contract;
use App\Models\ProformaInvoice;
use App\Models\User;
use App\Models\Country;
use App\Models\Seller;
use App\Models\SellerBankDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf as DomPDF;

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

        // Get countries for Payee dropdown
        $countries = Country::orderBy('name')->get();

        // If contract_id or proforma_invoice_id is provided, show payment form
        if ($request->filled('contract_id')) {
            $contract = Contract::with(['creator'])->findOrFail($request->contract_id);
            return view('payments.collect-payment', compact('salesManagers', 'contract', 'countries'));
        }

        if ($request->filled('proforma_invoice_id')) {
            $proformaInvoice = ProformaInvoice::with(['contract.creator', 'seller'])->findOrFail($request->proforma_invoice_id);
            return view('payments.collect-payment', compact('salesManagers', 'proformaInvoice', 'countries'));
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

        return view('payments.collect-payment', compact('salesManagers', 'contracts', 'proformaInvoices', 'countries'));
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

        // Get countries for Payee dropdown
        $countries = Country::orderBy('name')->get();

        // If contract_id or proforma_invoice_id is provided, show payment form
        if ($request->filled('contract_id')) {
            $contract = Contract::with(['creator'])->findOrFail($request->contract_id);
            return view('payments.return-payment', compact('salesManagers', 'contract', 'countries'));
        }

        if ($request->filled('proforma_invoice_id')) {
            $proformaInvoice = ProformaInvoice::with(['contract.creator', 'seller'])->findOrFail($request->proforma_invoice_id);
            return view('payments.return-payment', compact('salesManagers', 'proformaInvoice', 'countries'));
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

        // Get countries for Payee dropdown
        $countries = Country::orderBy('name')->get();

        return view('payments.return-payment', compact('salesManagers', 'contracts', 'proformaInvoices', 'countries'));
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
            'payment_by' => 'nullable|string|max:255',
            'payee_country_id' => 'nullable|exists:countries,id',
            'payment_to_seller_id' => 'nullable|exists:sellers,id',
            'bank_detail_id' => 'nullable|exists:seller_bank_details,id',
            'transaction_id' => 'nullable|string|max:255',
            'swift_copy' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
            'notes' => 'nullable|string',
        ]);

        $validated['created_by'] = Auth::id();

        // Handle SWIFT copy image upload
        if ($request->hasFile('swift_copy')) {
            $swiftCopyPath = $request->file('swift_copy')->store('swift-copies', 'public');
            $validated['swift_copy'] = $swiftCopyPath;
        }

        Payment::create($validated);

        $redirectRoute = $validated['type'] === 'collect' 
            ? 'payments.collect-payment' 
            : 'payments.return-payment';

        return redirect()->route($redirectRoute)
            ->with('success', ucfirst($validated['type']) . ' payment added successfully.');
    }

    /**
     * Get sellers by country (AJAX)
     */
    public function getSellersByCountry(Request $request)
    {
        if (!$request->filled('country_id')) {
            return response()->json([]);
        }

        $countryId = (int) $request->country_id;

        $sellers = Seller::with(['country'])
            ->where('country_id', $countryId)
            ->orderBy('seller_name')
            ->get()
            ->map(function($seller) {
                return [
                    'id' => $seller->id,
                    'seller_name' => $seller->seller_name,
                    'pi_short_name' => $seller->pi_short_name ?? '',
                    'country' => $seller->country ? ['name' => $seller->country->name] : null,
                ];
            });

        return response()->json($sellers);
    }

    /**
     * Get bank details by seller (AJAX)
     */
    public function getBankDetailsBySeller(Request $request)
    {
        if (!$request->filled('seller_id')) {
            return response()->json([]);
        }

        $bankDetails = SellerBankDetail::where('seller_id', $request->seller_id)
            ->orderBy('bank_name')
            ->get()
            ->map(function($bank) {
                return [
                    'id' => $bank->id,
                    'bank_name' => $bank->bank_name,
                    'account_number' => $bank->account_number,
                    'ifsc_code' => $bank->ifsc_code,
                    'branch_name' => $bank->branch_name,
                    'account_holder_name' => $bank->account_holder_name,
                ];
            });

        return response()->json($bankDetails);
    }

    /**
     * Display the specified payment
     */
    public function show(Payment $payment)
    {
        $payment->load([
            'contract.creator',
            'proformaInvoice.contract.creator',
            'proformaInvoice.seller',
            'creator',
            'payeeCountry',
            'paymentToSeller',
            'bankDetail'
        ]);

        return view('payments.show', compact('payment'));
    }

    /**
     * Show the form for editing the specified payment
     */
    public function edit(Payment $payment)
    {
        $payment->load([
            'contract.creator',
            'proformaInvoice.contract.creator',
            'proformaInvoice.seller',
            'payeeCountry',
            'paymentToSeller',
            'bankDetail'
        ]);

        // Get sales managers
        $salesManagers = User::whereIn('id', function($query) {
            $query->select('created_by')
                ->from('contracts')
                ->distinct();
        })->select('id', 'name')->orderBy('name')->get();

        // Get countries
        $countries = Country::orderBy('name')->get();

        // Get sellers for the selected country if exists
        $sellers = collect();
        if ($payment->payee_country_id) {
            $sellers = Seller::where('country_id', $payment->payee_country_id)
                ->orderBy('seller_name')
                ->get();
        }

        // Get bank details for the selected seller if exists
        $bankDetails = collect();
        if ($payment->payment_to_seller_id) {
            $bankDetails = SellerBankDetail::where('seller_id', $payment->payment_to_seller_id)
                ->orderBy('bank_name')
                ->get();
        }

        return view('payments.edit', compact('payment', 'salesManagers', 'countries', 'sellers', 'bankDetails'));
    }

    /**
     * Update the specified payment
     */
    public function update(Request $request, Payment $payment)
    {
        $validated = $request->validate([
            'amount' => 'required|numeric|min:0',
            'payment_date' => 'required|date',
            'payment_method' => 'nullable|string|max:255',
            'payment_by' => 'nullable|string|max:255',
            'payee_country_id' => 'nullable|exists:countries,id',
            'payment_to_seller_id' => 'nullable|exists:sellers,id',
            'bank_detail_id' => 'nullable|exists:seller_bank_details,id',
            'transaction_id' => 'nullable|string|max:255',
            'swift_copy' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
            'notes' => 'nullable|string',
        ]);

        // Handle SWIFT copy image upload
        if ($request->hasFile('swift_copy')) {
            // Delete old file if exists
            if ($payment->swift_copy && Storage::disk('public')->exists($payment->swift_copy)) {
                Storage::disk('public')->delete($payment->swift_copy);
            }
            $swiftCopyPath = $request->file('swift_copy')->store('swift-copies', 'public');
            $validated['swift_copy'] = $swiftCopyPath;
        }

        $payment->update($validated);

        return redirect()->route('payments.index')
            ->with('success', 'Payment updated successfully.');
    }

    /**
     * Remove the specified payment
     */
    public function destroy(Payment $payment)
    {
        // Delete SWIFT copy file if exists
        if ($payment->swift_copy && Storage::disk('public')->exists($payment->swift_copy)) {
            Storage::disk('public')->delete($payment->swift_copy);
        }

        $payment->delete();

        return redirect()->route('payments.index')
            ->with('success', 'Payment deleted successfully.');
    }

    /**
     * Download payment as PDF
     */
    public function downloadPdf(Payment $payment)
    {
        $payment->load([
            'contract.creator',
            'proformaInvoice.contract.creator',
            'proformaInvoice.seller',
            'creator',
            'payeeCountry',
            'paymentToSeller',
            'bankDetail'
        ]);

        $pdf = DomPDF::loadView('payments.pdf', compact('payment'));

        $fileName = 'payment-' . ($payment->type === 'collect' ? 'collect' : 'return') . '-' . $payment->id . '-' . $payment->payment_date->format('Y-m-d') . '.pdf';

        return $pdf->download($fileName);
    }
}
