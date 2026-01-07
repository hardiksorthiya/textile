<?php

namespace App\Http\Controllers;

use App\Models\ProformaInvoice;
use App\Models\IAFittingDetail;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class IAFittingController extends Controller
{
    /**
     * Display IA Fitting index page (list all PIs - with or without IA fitting details)
     */
    public function index(Request $request)
    {
        $query = ProformaInvoice::with(['iaFittingDetails', 'contract.creator', 'creator', 'seller', 'proformaInvoiceMachines.machineCategory'])
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

        return view('ia-fitting.index', compact('proformaInvoices', 'salesManagers'));
    }

    /**
     * Show the form for creating/editing IA fitting details
     */
    public function show(ProformaInvoice $proformaInvoice)
    {
        $proformaInvoice->load(['iaFittingDetails.machineCategory', 'serialNumbers']);
        
        // Get all serial numbers that have both serial_number and khata_number
        $allSerialNumbers = $proformaInvoice->serialNumbers()
            ->whereNotNull('serial_number')
            ->whereNotNull('khata_number')
            ->where('serial_number', '!=', '')
            ->where('khata_number', '!=', '')
            ->with('machineCategory')
            ->orderBy('machine_category_id')
            ->orderBy('serial_number')
            ->get();
        
        // Group serial numbers by category for dropdown organization
        $serialNumbersByCategory = $allSerialNumbers->groupBy('machine_category_id');
        
        // Get unique machine categories from serial numbers that have both values
        $machineCategories = $allSerialNumbers->pluck('machineCategory')->unique('id')->filter();

        // Define default details (can be customized)
        $defaultDetails = [
            [
                'name' => 'Running Speed',
                'type' => 'text',
                'sort_order' => 1,
            ],
            [
                'name' => 'Efficiency',
                'type' => 'text',
                'sort_order' => 2,
            ],
            [
                'name' => 'Master',
                'type' => 'radio',
                'sort_order' => 3,
            ],
            [
                'name' => 'Complain',
                'type' => 'textarea',
                'sort_order' => 4,
            ],
        ];

        // Get existing IA fitting details grouped by category, machine number, and detail name
        $existingDetails = $proformaInvoice->iaFittingDetails->groupBy(function ($detail) {
            return $detail->machine_category_id . '_' . $detail->machine_number . '_' . $detail->detail_name;
        });

        return view('ia-fitting.show', compact('proformaInvoice', 'machineCategories', 'defaultDetails', 'existingDetails', 'allSerialNumbers', 'serialNumbersByCategory'));
    }

    /**
     * Store or update IA fitting details for a proforma invoice
     */
    public function store(Request $request, ProformaInvoice $proformaInvoice)
    {
        $request->validate([
            'serial_number_id' => 'required|exists:serial_numbers,id',
            'ia_fitting_details' => 'required|array',
            'ia_fitting_details.*.machine_category_id' => 'required|exists:machine_categories,id',
            'ia_fitting_details.*.detail_name' => 'required|string|max:255',
            'ia_fitting_details.*.value' => 'nullable|string',
            'ia_fitting_details.*.value_type' => 'required|in:text,radio,textarea',
            'ia_fitting_details.*.sort_order' => 'nullable|integer',
        ]);

        try {
            DB::beginTransaction();

            // Get the serial number to find machine category and number
            $serialNumber = \App\Models\SerialNumber::findOrFail($request->serial_number_id);
            
            if ($serialNumber->proforma_invoice_id != $proformaInvoice->id) {
                throw new \Exception('Serial number does not belong to this proforma invoice.');
            }

            // Calculate machine number based on serial number position within same category
            $allSerialNumbers = \App\Models\SerialNumber::where('proforma_invoice_id', $proformaInvoice->id)
                ->where('machine_category_id', $serialNumber->machine_category_id)
                ->whereNotNull('serial_number')
                ->whereNotNull('khata_number')
                ->where('serial_number', '!=', '')
                ->where('khata_number', '!=', '')
                ->orderBy('id')
                ->get();
            
            $machineNumberIndex = $allSerialNumbers->search(function($sn) use ($serialNumber) {
                return $sn->id == $serialNumber->id;
            });
            $machineNumber = $machineNumberIndex !== false ? $machineNumberIndex + 1 : 1;

            // Delete existing details for this specific machine
            IAFittingDetail::where('proforma_invoice_id', $proformaInvoice->id)
                ->where('machine_category_id', $serialNumber->machine_category_id)
                ->where('machine_number', $machineNumber)
                ->delete();

            // Save new details
            foreach ($request->ia_fitting_details as $detailIndex => $detailData) {
                if (!empty($detailData['detail_name']) && !empty($detailData['machine_category_id'])) {
                    // Only save if value is provided (not empty)
                    if (!empty($detailData['value']) && trim($detailData['value']) !== '') {
                        IAFittingDetail::create([
                            'proforma_invoice_id' => $proformaInvoice->id,
                            'machine_category_id' => $serialNumber->machine_category_id,
                            'machine_number' => $machineNumber,
                            'detail_name' => $detailData['detail_name'],
                            'value' => trim($detailData['value']),
                            'value_type' => $detailData['value_type'] ?? 'text',
                            'sort_order' => $detailData['sort_order'] ?? ($detailIndex + 1),
                        ]);
                    }
                }
            }

            DB::commit();

            return redirect()->route('ia-fitting.show', $proformaInvoice)
                ->with('success', 'IA Fitting details saved successfully.')
                ->with('selected_serial_number_id', $request->serial_number_id);
        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            return back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error saving IA fitting details: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            return back()->withErrors(['error' => 'Failed to save IA fitting details: ' . $e->getMessage()])
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
