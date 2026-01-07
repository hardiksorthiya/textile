<?php

namespace App\Http\Controllers;

use App\Models\ProformaInvoice;
use App\Models\MachineErectionDetail;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class MachineErectionController extends Controller
{
    /**
     * Display machine erection index page (list all PIs - with or without machine erection details)
     */
    public function index(Request $request)
    {
        $query = ProformaInvoice::with(['machineErectionDetails', 'contract.creator', 'creator', 'seller', 'proformaInvoiceMachines.machineCategory'])
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

        return view('machine-erection.index', compact('proformaInvoices', 'salesManagers'));
    }

    /**
     * Show the form for creating/editing machine erection details
     */
    public function show(ProformaInvoice $proformaInvoice)
    {
        $proformaInvoice->load(['machineErectionDetails.machineCategory', 'proformaInvoiceMachines.machineCategory']);
        
        // Get unique machine categories from PI machines with quantities
        $machineCategoriesWithQuantity = $proformaInvoice->proformaInvoiceMachines()
            ->with('machineCategory')
            ->get()
            ->groupBy('machine_category_id')
            ->map(function ($machines) {
                $category = $machines->first()->machineCategory;
                $totalQuantity = $machines->sum('quantity');
                return [
                    'category' => $category,
                    'quantity' => $totalQuantity
                ];
            })
            ->filter();
        
        // Separate categories and quantities for easier access in view
        $machineCategories = $machineCategoriesWithQuantity->map(function($item) {
            return $item['category'];
        });

        // Define default points to follow (can be customized)
        $defaultPointsToFollow = [
            // First set of points
            'Loom Placed at Foundation',
            'Delivery List & Damage Check',
            'Liner Level & Packing',
            'U Clamp Fitting',
            'Air Valve-Caps Remove by User Level Person',
            'Salo Bolt Measurement Given',
            'Pullborn',
            'Beam Pipe Fitting',
            'Powder Stand Fitting',
            'Gripper & Tape Remover',
            'Back Rest Bolt Setting',
            'X-Axis Alignment Checking by Gauge',
            'Liner Level of Loom & JC Check',
            'Harness Hanging From JC',
            // Second set of points
            'Harness Hanging Frame JD',
            'Harness Filling From Carderboard',
            'Harness Under Motion Spring Filling',
            'Sate Shaft Filling & Harness Level Zero Check',
            'JD all Long all Boxer Height',
            'Electric Connections From Main Line',
            'Electric Connections Between JD & Loom',
            'Oil Pressure Check by Elec. Engineer',
            'Harness Fixing after Zero Level Check',
            'Empty Running & Harness Final Level Check',
            'Beam Drawing',
            'Reed Drawing',
            'Beam Grilling',
            'Warp Filling & Piecing',
            // Third set of points (some may overlap)
            'Beam Setting',
            'Warp Filling & Pinning',
            'Plain/Cloth Treding & Harness Mistake Check',
            'Final Cloth with Design',
            'Machine in Production @ 370 RPM',
        ];

        // Get existing machine erection details grouped by category and point
        $existingDetails = $proformaInvoice->machineErectionDetails->groupBy(function ($detail) {
            return $detail->machine_category_id . '_' . $detail->point_to_follow;
        });

        return view('machine-erection.show', compact('proformaInvoice', 'machineCategories', 'machineCategoriesWithQuantity', 'defaultPointsToFollow', 'existingDetails'));
    }

    /**
     * Store or update machine erection details for a proforma invoice
     */
    public function store(Request $request, ProformaInvoice $proformaInvoice)
    {
        $request->validate([
            'machine_erection_details' => 'required|array',
            'machine_erection_details.*' => 'required|array',
            'machine_erection_details.*.*.machine_category_id' => 'required|exists:machine_categories,id',
            'machine_erection_details.*.*.point_to_follow' => 'required|string|max:255',
            'machine_erection_details.*.*.machine_dates' => 'required|array',
            'machine_erection_details.*.*.machine_dates.*' => 'nullable|date',
        ]);

        try {
            DB::beginTransaction();

            // Delete existing details for this PI
            MachineErectionDetail::where('proforma_invoice_id', $proformaInvoice->id)->delete();

            // Save new details
            $sortOrder = 0;
            foreach ($request->machine_erection_details as $categoryId => $categoryPoints) {
                if (is_array($categoryPoints)) {
                    foreach ($categoryPoints as $pointIndex => $pointData) {
                        if (!empty($pointData['point_to_follow']) && !empty($pointData['machine_category_id'])) {
                            $pointToFollow = $pointData['point_to_follow'];
                            $machineCategoryId = $pointData['machine_category_id'];
                            
                            // Save dates for each machine (1-10)
                            if (isset($pointData['machine_dates']) && is_array($pointData['machine_dates'])) {
                                foreach ($pointData['machine_dates'] as $machineNumber => $date) {
                                    if (!empty($date)) {
                                        // Parse date format dd-mm to yyyy-mm-dd (assume current year if year not provided)
                                        $parsedDate = null;
                                        if ($date) {
                                            $dateParts = explode('-', $date);
                                            if (count($dateParts) === 2) {
                                                // Only day and month provided, use current year
                                                $parsedDate = date('Y') . '-' . $dateParts[1] . '-' . $dateParts[0];
                                            } elseif (count($dateParts) === 3) {
                                                // Full date provided
                                                $parsedDate = $dateParts[2] . '-' . $dateParts[1] . '-' . $dateParts[0];
                                            } else {
                                                $parsedDate = $date;
                                            }
                                        }
                                        
                                        MachineErectionDetail::create([
                                            'proforma_invoice_id' => $proformaInvoice->id,
                                            'machine_category_id' => $machineCategoryId,
                                            'point_to_follow' => $pointToFollow,
                                            'machine_number' => (int)$machineNumber,
                                            'date' => $parsedDate,
                                            'sort_order' => $sortOrder,
                                        ]);
                                    }
                                }
                            }
                            $sortOrder++;
                        }
                    }
                }
            }

            DB::commit();

            return redirect()->route('machine-erection.index')
                ->with('success', 'Machine erection details saved successfully.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            return back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error saving machine erection details: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            return back()->withErrors(['error' => 'Failed to save machine erection details: ' . $e->getMessage()])
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
