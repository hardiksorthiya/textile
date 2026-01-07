<?php

namespace App\Http\Controllers;

use App\Models\ProformaInvoice;
use App\Models\PreErectionDetail;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PreErectionController extends Controller
{
    /**
     * Display pre-erection index page (list all PIs - with or without pre-erection details)
     */
    public function index(Request $request)
    {
        $query = ProformaInvoice::with(['preErectionDetails', 'contract.creator', 'creator', 'seller'])
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

        return view('pre-erection.index', compact('proformaInvoices', 'salesManagers'));
    }

    /**
     * Show the form for creating/editing pre-erection details
     */
    public function show(ProformaInvoice $proformaInvoice)
    {
        $proformaInvoice->load('preErectionDetails');
        
        // Define all technical specifications
        $technicalSpecifications = [
            'Space for No. of Machines',
            'Factory Construction Ready',
            'Foundation Ready',
            'Gantry Ready',
            'Floor Cutting Ready',
            'Customer and Signature Whatsapp Group Ready',
            'Availability of Route to Factory Entry',
            'Grease Pump and Air Pressure Blower Ready',
            'Marking for Machines Done',
        ];

        // Get existing pre-erection details indexed by technical specification
        $existingDetails = $proformaInvoice->preErectionDetails->keyBy('technical_specification');

        return view('pre-erection.show', compact('proformaInvoice', 'technicalSpecifications', 'existingDetails'));
    }

    /**
     * Store or update pre-erection details for a proforma invoice
     */
    public function store(Request $request, ProformaInvoice $proformaInvoice)
    {
        $request->validate([
            'pre_erection_details' => 'required|array',
            'pre_erection_details.*.technical_specification' => 'required|string|max:255',
            'pre_erection_details.*.details' => 'nullable|string',
            'pre_erection_details.*.is_completed' => 'nullable',
        ]);

        try {
            DB::beginTransaction();

            // Get existing pre-erection details indexed by technical specification
            $existingDetails = $proformaInvoice->preErectionDetails->keyBy('technical_specification');

            // Update or create pre-erection details
            $sortOrder = 0;
            foreach ($request->pre_erection_details as $index => $detail) {
                if (!empty($detail['technical_specification'])) {
                    $technicalSpecification = $detail['technical_specification'];
                    
                    // Check if this row has any data to save (details or checkbox)
                    $hasDetails = !empty($detail['details']) && trim($detail['details']) !== '';
                    $hasCheckbox = isset($detail['is_completed']);
                    
                    // Only save if there's at least one field filled
                    if ($hasDetails || $hasCheckbox) {
                        // Checkbox: if not set, it means unchecked (false)
                        $isCompleted = isset($detail['is_completed']) && ($detail['is_completed'] == '1' || $detail['is_completed'] === true || $detail['is_completed'] === 'on');
                        
                        // Check if this detail already exists
                        if ($existingDetails->has($technicalSpecification)) {
                            // Update existing detail
                            $existingDetail = $existingDetails->get($technicalSpecification);
                            $existingDetail->update([
                                'details' => $hasDetails ? trim($detail['details']) : $existingDetail->details,
                                'is_completed' => $hasCheckbox ? $isCompleted : $existingDetail->is_completed,
                            ]);
                        } else {
                            // Create new detail
                            PreErectionDetail::create([
                                'proforma_invoice_id' => $proformaInvoice->id,
                                'technical_specification' => $technicalSpecification,
                                'details' => $hasDetails ? trim($detail['details']) : null,
                                'is_completed' => $hasCheckbox ? $isCompleted : false,
                                'sort_order' => $sortOrder++,
                            ]);
                        }
                    }
                }
            }

            DB::commit();

            return redirect()->route('pre-erection.index')
                ->with('success', 'Pre-erection details saved successfully.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            return back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error saving pre-erection details: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            return back()->withErrors(['error' => 'Failed to save pre-erection details: ' . $e->getMessage()])
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
