<?php

namespace App\Http\Controllers;

use App\Models\ProformaInvoice;
use App\Models\SerialNumber;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SerialNumberController extends Controller
{
    /**
     * Display serial numbers index page (list all PIs)
     */
    public function index(Request $request)
    {
        $query = ProformaInvoice::with(['serialNumbers.machineCategory', 'contract.creator', 'creator', 'seller'])
            ->orderBy('created_at', 'desc');

        // Filter by Sales Manager (contract creator or PI creator)
        if ($request->filled('sales_manager_id')) {
            $query->where(function($q) use ($request) {
                $q->whereHas('contract', function($subQ) use ($request) {
                    $subQ->where('created_by', $request->sales_manager_id);
                })
                ->orWhere('created_by', $request->sales_manager_id);
            });
        }

        // Filter by PI Number (exact match when from dropdown)
        if ($request->filled('pi_number')) {
            $query->where('proforma_invoice_number', $request->pi_number);
        }

        // Filter by Customer Name (Buyer) - exact match when from dropdown
        if ($request->filled('customer_name')) {
            $query->where('buyer_company_name', $request->customer_name);
        }

        $proformaInvoices = $query->paginate(15)->withQueryString();
        
        // Get all users who can be sales managers (users who created contracts or PIs)
        $salesManagers = User::where(function($q) {
            $q->whereHas('createdContracts')
              ->orWhereHas('createdProformaInvoices');
        })->orderBy('name')->get();

        return view('serial-numbers.index', compact('proformaInvoices', 'salesManagers'));
    }

    /**
     * Show the form for adding serial numbers for a proforma invoice
     */
    public function show(ProformaInvoice $proformaInvoice)
    {
        $proformaInvoice->load([
            'proformaInvoiceMachines.machineCategory',
            'proformaInvoiceMachines.brand',
            'proformaInvoiceMachines.machineModel',
            'proformaInvoiceMachines.serialNumbers',
            'serialNumbers.machineCategory'
        ]);
        
        // Group machines by category with their quantities
        $machinesByCategory = $proformaInvoice->proformaInvoiceMachines
            ->groupBy('machine_category_id')
            ->map(function($machines) {
                $category = $machines->first()->machineCategory;
                $totalQuantity = $machines->sum('quantity');
                
                // Get existing serial numbers for these machines
                $machineIds = $machines->pluck('id');
                $existingSerialNumbers = SerialNumber::whereIn('proforma_invoice_machine_id', $machineIds)
                    ->get()
                    ->groupBy('proforma_invoice_machine_id');
                
                return [
                    'category' => $category,
                    'machines' => $machines->map(function($machine) use ($existingSerialNumbers) {
                        $serialNumbers = $existingSerialNumbers->get($machine->id, collect())->values();
                        // Create array indexed by position for easy access in view
                        $serialNumbersArray = [];
                        foreach ($serialNumbers as $index => $serial) {
                            $serialNumbersArray[$index] = $serial;
                        }
                        return [
                            'machine' => $machine,
                            'serial_numbers' => $serialNumbersArray,
                        ];
                    }),
                    'total_quantity' => $totalQuantity,
                ];
            });

        return view('serial-numbers.show', compact('proformaInvoice', 'machinesByCategory'));
    }

    /**
     * Store serial numbers for a proforma invoice
     */
    public function store(Request $request, ProformaInvoice $proformaInvoice)
    {
        $request->validate([
            'serial_numbers' => 'required|array',
            'serial_numbers.*.*.machine_id' => 'required|exists:proforma_invoice_machines,id',
            'serial_numbers.*.*.serial_number' => 'nullable|string|max:255',
            'serial_numbers.*.*.khata_number' => 'nullable|string|max:255',
        ]);

        try {
            DB::beginTransaction();

            // Delete existing serial numbers for this PI
            SerialNumber::where('proforma_invoice_id', $proformaInvoice->id)->delete();

            foreach ($request->serial_numbers as $machineId => $instances) {
                if (empty($machineId)) {
                    continue;
                }

                $machine = $proformaInvoice->proformaInvoiceMachines()
                    ->where('id', $machineId)
                    ->first();

                if (!$machine) {
                    continue;
                }

                // Create serial number entry for each instance
                foreach ($instances as $instanceData) {
                    if (!empty($instanceData['serial_number']) || !empty($instanceData['khata_number'])) {
                        SerialNumber::create([
                            'proforma_invoice_id' => $proformaInvoice->id,
                            'proforma_invoice_machine_id' => $machineId,
                            'machine_category_id' => $machine->machine_category_id,
                            'serial_number' => $instanceData['serial_number'] ?? null,
                            'khata_number' => $instanceData['khata_number'] ?? null,
                        ]);
                    }
                }
            }

            DB::commit();

            return redirect()->route('serial-numbers.show', $proformaInvoice)
                ->with('success', 'Serial numbers saved successfully.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            return back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error saving serial numbers: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            return back()->withErrors(['error' => 'Failed to save serial numbers: ' . $e->getMessage()])
                ->withInput();
        }
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
        ->get(['id', 'proforma_invoice_number', 'buyer_company_name']);

        return response()->json($pis);
    }

    /**
     * Get Customers by Sales Manager (AJAX)
     */
    public function getCustomersBySalesManager(Request $request)
    {
        $salesManagerId = $request->get('sales_manager_id');
        
        $customers = ProformaInvoice::where(function($q) use ($salesManagerId) {
            $q->where('created_by', $salesManagerId)
              ->orWhereHas('contract', function($subQ) use ($salesManagerId) {
                  $subQ->where('created_by', $salesManagerId);
              });
        })
        ->select('buyer_company_name')
        ->distinct()
        ->whereNotNull('buyer_company_name')
        ->orderBy('buyer_company_name')
        ->get()
        ->pluck('buyer_company_name')
        ->unique()
        ->values();

        return response()->json($customers);
    }
}
