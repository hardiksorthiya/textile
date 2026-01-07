<?php

namespace App\Http\Controllers;

use App\Models\MachineStatus;
use App\Models\Contract;
use App\Models\ProformaInvoice;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MachineStatusController extends Controller
{
    /**
     * Display a listing of machine statuses - Show all Contracts and PIs (with or without status)
     */
    public function index(Request $request)
    {
        // Start with Contracts - show all contracts (with or without machine status)
        $contractQuery = Contract::with(['machineStatus', 'creator', 'proformaInvoices.machineStatus'])
            ->orderBy('created_at', 'desc');

        // Start with PIs - show all PIs (with or without machine status)
        $piQuery = ProformaInvoice::with(['machineStatus', 'contract.machineStatus', 'contract.creator', 'creator', 'seller'])
            ->orderBy('created_at', 'desc');

        // Filter by Sales Manager
        if ($request->filled('sales_manager_id')) {
            $salesManagerId = $request->sales_manager_id;
            
            // Filter contracts by creator
            $contractQuery->where('created_by', $salesManagerId);
            
            // Filter PIs by creator or contract creator
            $piQuery->where(function($q) use ($salesManagerId) {
                $q->whereHas('contract', function($subQ) use ($salesManagerId) {
                    $subQ->where('created_by', $salesManagerId);
                })
                ->orWhere('created_by', $salesManagerId);
            });
        }

        // Filter by Contract Number
        if ($request->filled('contract_number')) {
            $contractNumber = trim($request->contract_number);
            $contractQuery->where('contract_number', 'like', '%' . $contractNumber . '%');
            $piQuery->whereHas('contract', function($subQ) use ($contractNumber) {
                $subQ->where('contract_number', 'like', '%' . $contractNumber . '%');
            });
        }

        // Filter by PI Number
        if ($request->filled('pi_number')) {
            $piNumber = trim($request->pi_number);
            $piQuery->where('proforma_invoice_number', 'like', '%' . $piNumber . '%');
        }

        // Get results
        $contracts = $contractQuery->get();
        $proformaInvoices = $piQuery->get();

        // Combine and paginate manually (or show all)
        $allItems = collect();
        
        // Add contracts
        foreach ($contracts as $contract) {
            $allItems->push([
                'type' => 'contract',
                'contract' => $contract,
                'proforma_invoice' => null,
                'machine_status' => $contract->machineStatus,
            ]);
        }
        
        // Add PIs (only if they don't have a contract already listed, or if PI doesn't have contract)
        foreach ($proformaInvoices as $pi) {
            // Only add if PI doesn't have a contract, or if contract is not already in the list
            if (!$pi->contract_id || !$contracts->contains('id', $pi->contract_id)) {
                $allItems->push([
                    'type' => 'pi',
                    'contract' => $pi->contract,
                    'proforma_invoice' => $pi,
                    'machine_status' => $pi->machineStatus,
                ]);
            }
        }

        // Sort by created_at desc
        $allItems = $allItems->sortByDesc(function($item) {
            if ($item['type'] === 'contract') {
                return $item['contract']->created_at;
            } else {
                return $item['proforma_invoice']->created_at;
            }
        });

        // Paginate manually
        $currentPage = $request->get('page', 1);
        $perPage = 15;
        $items = $allItems->slice(($currentPage - 1) * $perPage, $perPage)->values();
        $paginator = new \Illuminate\Pagination\LengthAwarePaginator(
            $items,
            $allItems->count(),
            $perPage,
            $currentPage,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        // Get all sales managers (users who created contracts or PIs)
        $salesManagers = \App\Models\User::where(function($q) {
            $q->whereHas('createdContracts')
              ->orWhereHas('createdProformaInvoices');
        })->orderBy('name')->get();

        // Get PIs and Contracts for dropdowns (filtered by sales manager if selected)
        $pis = collect();
        $contractsForDropdown = collect();
        
        if ($request->filled('sales_manager_id')) {
            $salesManagerId = $request->sales_manager_id;
            // Get PIs created by this manager or linked to contracts created by this manager
            $pis = ProformaInvoice::where(function($q) use ($salesManagerId) {
                $q->where('created_by', $salesManagerId)
                  ->orWhereHas('contract', function($subQ) use ($salesManagerId) {
                      $subQ->where('created_by', $salesManagerId);
                  });
            })
            ->orderBy('proforma_invoice_number')
            ->get(['id', 'proforma_invoice_number']);
            
            // Get Contracts created by this manager
            $contractsForDropdown = Contract::where('created_by', $salesManagerId)
                ->orderBy('contract_number')
                ->get(['id', 'contract_number']);
        } else {
            // Get all PIs and Contracts
            $pis = ProformaInvoice::orderBy('proforma_invoice_number')
                ->get(['id', 'proforma_invoice_number']);
            $contractsForDropdown = Contract::orderBy('contract_number')
                ->get(['id', 'contract_number']);
        }

        return view('machine-statuses.index', compact('paginator', 'salesManagers', 'pis', 'contractsForDropdown'));
    }

    /**
     * Get PIs by Sales Manager (AJAX)
     */
    public function getPINumbersBySalesManager(Request $request)
    {
        $salesManagerId = $request->get('sales_manager_id');
        
        $pis = ProformaInvoice::where(function($q) use ($salesManagerId) {
            $q->where('created_by', $salesManagerId)
              ->orWhereHas('contract', function($subQ) use ($salesManagerId) {
                  $subQ->where('created_by', $salesManagerId);
              });
        })
        ->orderBy('proforma_invoice_number')
        ->get(['id', 'proforma_invoice_number']);

        return response()->json($pis);
    }

    /**
     * Get Contracts by Sales Manager (AJAX)
     */
    public function getContractsBySalesManager(Request $request)
    {
        $salesManagerId = $request->get('sales_manager_id');
        
        $contracts = Contract::where('created_by', $salesManagerId)
            ->orderBy('contract_number')
            ->get(['id', 'contract_number']);

        return response()->json($contracts);
    }

    /**
     * Show the form for creating/editing machine status
     */
    public function create(Request $request)
    {
        $contractId = $request->get('contract_id');
        $piId = $request->get('proforma_invoice_id');

        $contract = null;
        $proformaInvoice = null;
        $machineStatus = null;

        if ($contractId) {
            $contract = Contract::with('proformaInvoices')->findOrFail($contractId);
            $machineStatus = MachineStatus::where('contract_id', $contractId)->first();
        } elseif ($piId) {
            $proformaInvoice = ProformaInvoice::with('contract')->findOrFail($piId);
            $machineStatus = MachineStatus::where('proforma_invoice_id', $piId)->first();
            if ($proformaInvoice->contract) {
                $contract = $proformaInvoice->contract;
            }
        }

        // Auto-populate dates from related records if status doesn't exist
        if (!$machineStatus) {
            $machineStatus = new MachineStatus();
            
            // Get contract date
            if ($contract) {
                $machineStatus->contract_date = $contract->created_at ? $contract->created_at->format('Y-m-d') : null;
                $machineStatus->contract_date_completed = $contract->created_at ? true : false;
            }

            // Get PI date - from proforma invoice if available, or from contract's PIs
            if ($proformaInvoice) {
                // Use the specific PI's created_at date
                $machineStatus->proforma_invoice_date = $proformaInvoice->created_at ? $proformaInvoice->created_at->format('Y-m-d') : null;
                $machineStatus->proforma_invoice_completed = $proformaInvoice->created_at ? true : false;
            } elseif ($contract && $contract->proformaInvoices && $contract->proformaInvoices->count() > 0) {
                // If creating from contract, get the first/latest PI date
                $latestPI = $contract->proformaInvoices->sortByDesc('created_at')->first();
                if ($latestPI && $latestPI->created_at) {
                    $machineStatus->proforma_invoice_date = $latestPI->created_at->format('Y-m-d');
                    $machineStatus->proforma_invoice_completed = true;
                }
            }

            // Get China Payment date - check both PI and Contract payments
            $chinaCountry = \App\Models\Country::where('name', 'like', '%China%')->first();
            if ($chinaCountry) {
                $chinaPayment = null;
                
                // First check payments linked to PI
                if ($proformaInvoice) {
                    $chinaPayment = Payment::where('proforma_invoice_id', $proformaInvoice->id)
                        ->where('payee_country_id', $chinaCountry->id)
                        ->orderBy('payment_date', 'desc')
                        ->first();
                }
                
                // If no PI payment found, check contract payments
                if (!$chinaPayment && $contract) {
                    $chinaPayment = Payment::where('contract_id', $contract->id)
                        ->where('payee_country_id', $chinaCountry->id)
                        ->orderBy('payment_date', 'desc')
                        ->first();
                }
                
                // Also check payments linked to contract's PIs
                if (!$chinaPayment && $contract && $contract->proformaInvoices) {
                    $piIds = $contract->proformaInvoices->pluck('id');
                    if ($piIds->count() > 0) {
                        $chinaPayment = Payment::whereIn('proforma_invoice_id', $piIds)
                            ->where('payee_country_id', $chinaCountry->id)
                            ->orderBy('payment_date', 'desc')
                            ->first();
                    }
                }
                
                if ($chinaPayment && $chinaPayment->payment_date) {
                    $machineStatus->china_payment_date = $chinaPayment->payment_date->format('Y-m-d');
                    $machineStatus->china_payment_completed = true;
                }
            }
        }

        return view('machine-statuses.create', compact('contract', 'proformaInvoice', 'machineStatus'));
    }

    /**
     * Store or update machine status
     */
    public function store(Request $request)
    {
        $request->validate([
            'contract_id' => 'nullable|exists:contracts,id',
            'proforma_invoice_id' => 'nullable|exists:proforma_invoices,id',
            'contract_date' => 'nullable|date',
            'contract_date_completed' => 'nullable|boolean',
            'proforma_invoice_date' => 'nullable|date',
            'proforma_invoice_completed' => 'nullable|boolean',
            'china_payment_date' => 'nullable|date',
            'china_payment_completed' => 'nullable|boolean',
            'actual_dispatch_date' => 'nullable|date',
            'actual_dispatch_completed' => 'nullable|boolean',
            'expected_arrival_date' => 'nullable|date',
            'expected_arrival_completed' => 'nullable|boolean',
            'actual_arrival_date' => 'nullable|date',
            'actual_arrival_completed' => 'nullable|boolean',
        ]);

        try {
            DB::beginTransaction();

            $machineStatus = MachineStatus::updateOrCreate(
                [
                    'contract_id' => $request->contract_id,
                    'proforma_invoice_id' => $request->proforma_invoice_id,
                ],
                [
                    'contract_date' => $request->contract_date ?? null,
                    'contract_date_completed' => $request->has('contract_date_completed') ? true : false,
                    'proforma_invoice_date' => $request->proforma_invoice_date ?? null,
                    'proforma_invoice_completed' => $request->has('proforma_invoice_completed') ? true : false,
                    'china_payment_date' => $request->china_payment_date ?? null,
                    'china_payment_completed' => $request->has('china_payment_completed') ? true : false,
                    'actual_dispatch_date' => $request->actual_dispatch_date ?? null,
                    'actual_dispatch_completed' => $request->has('actual_dispatch_completed') ? true : false,
                    'expected_arrival_date' => $request->expected_arrival_date ?? null,
                    'expected_arrival_completed' => $request->has('expected_arrival_completed') ? true : false,
                    'actual_arrival_date' => $request->actual_arrival_date ?? null,
                    'actual_arrival_completed' => $request->has('actual_arrival_completed') ? true : false,
                ]
            );

            DB::commit();

            $redirectRoute = $request->contract_id 
                ? route('contracts.show', $request->contract_id)
                : route('proforma-invoices.show', $request->proforma_invoice_id);

            return redirect($redirectRoute)
                ->with('success', 'Machine status updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Failed to save machine status: ' . $e->getMessage()])
                ->withInput();
        }
    }
}
