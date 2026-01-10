<?php

namespace App\Http\Controllers;

use App\Models\ProformaInvoice;
use App\Models\ProformaInvoiceMachine;
use App\Models\PIDeliveryDetail;
use App\Models\PIDocument;
use App\Models\Contract;
use App\Models\ContractMachine;
use App\Models\User;
use App\Models\Seller;
use App\Models\PILayout;
use App\Models\Brand;
use App\Models\MachineModel;
use App\Models\Feeder;
use App\Models\MachineHook;
use App\Models\MachineERead;
use App\Models\Color;
use App\Models\MachineNozzle;
use App\Models\MachineDropin;
use App\Models\MachineBeam;
use App\Models\MachineClothRoller;
use App\Models\MachineSoftware;
use App\Models\HsnCode;
use App\Models\Wir;
use App\Models\MachineShaft;
use App\Models\MachineLever;
use App\Models\MachineChain;
use App\Models\MachineHealdWire;
use App\Models\DeliveryTerm;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf as DomPDF;

class ProformaInvoiceController extends Controller
{
    /**
     * Show the form for creating a new proforma invoice with search functionality
     */
    public function create(Request $request)
    {
        $query = Contract::with(['creator', 'contractMachines.machineCategory'])
            ->whereNotNull('approval_status')
            ->where('approval_status', 'approved'); // Only approved contracts
        
        // Search by sales manager (created_by)
        if ($request->filled('sales_manager_id')) {
            $query->where('created_by', $request->sales_manager_id);
        }
        
        // Search by contract number
        if ($request->filled('contract_number')) {
            $query->where('contract_number', 'like', '%' . $request->contract_number . '%');
        }
        
        // Search by customer name (buyer_name)
        if ($request->filled('customer_name')) {
            $query->where('buyer_name', 'like', '%' . $request->customer_name . '%');
        }
        
        // If contract_id is provided, select it
        $selectedContractId = $request->filled('contract_id') ? $request->contract_id : null;
        if ($selectedContractId) {
            $query->where('id', $selectedContractId);
        }
        
        $contracts = $query->orderBy('created_at', 'desc')->paginate(10)->withQueryString();
        
        // Get all users who can be sales managers (users who created contracts)
        $salesManagers = User::whereHas('createdContracts')->orderBy('name')->get();
        
        // Get all options for machine specifications
        $brands = Brand::orderBy('name')->get();
        $machineModels = MachineModel::with('brand')->orderBy('model_no')->get();
        $feeders = Feeder::with('feederBrand')->orderBy('feeder')->get();
        $machineHooks = MachineHook::orderBy('hook')->get();
        $machineEReads = MachineERead::orderBy('name')->get();
        $colors = Color::orderBy('name')->get();
        $machineNozzles = MachineNozzle::orderBy('nozzle')->get();
        $machineDropins = MachineDropin::orderBy('name')->get();
        $machineBeams = MachineBeam::orderBy('name')->get();
        $machineClothRollers = MachineClothRoller::orderBy('name')->get();
        $machineSoftwares = MachineSoftware::orderBy('name')->get();
        $hsnCodes = HsnCode::orderBy('name')->get();
        $wirs = Wir::orderBy('name')->get();
        $machineShafts = MachineShaft::orderBy('name')->get();
        $machineLevers = MachineLever::orderBy('name')->get();
        $machineChains = MachineChain::orderBy('name')->get();
        $machineHealdWires = MachineHealdWire::orderBy('name')->get();
        $deliveryTerms = DeliveryTerm::orderBy('name')->get();
        $sellers = Seller::orderBy('seller_name')->get();
        
        return view('proforma-invoices.create', compact(
            'contracts', 
            'salesManagers', 
            'selectedContractId',
            'brands',
            'machineModels',
            'feeders',
            'machineHooks',
            'machineEReads',
            'colors',
            'machineNozzles',
            'machineDropins',
            'machineBeams',
            'machineClothRollers',
            'machineSoftwares',
            'hsnCodes',
            'wirs',
            'machineShafts',
            'machineLevers',
            'machineChains',
            'machineHealdWires',
            'deliveryTerms',
            'sellers'
        ));
    }
    
    /**
     * Store a newly created proforma invoice
     */
    public function store(Request $request)
    {
        $request->validate([
            'contract_id' => 'required|exists:contracts,id',
            'seller_id' => 'required|exists:sellers,id',
            'type_of_sale' => 'required|in:import,local,high_seas',
            'currency' => 'required|string|max:10',
            'usd_rate' => 'nullable|numeric|min:0',
            'commission' => 'nullable|numeric|min:0|max:100',
            'buyer_company_name' => 'required|string|max:255',
            'pan' => 'nullable|string|max:255',
            'gst' => 'nullable|string|max:255',
            'phone_number' => 'required|string|max:255',
            'phone_number_2' => 'nullable|string|max:255',
            'ifc_certificate_number' => 'nullable|string|max:255',
            'billing_address' => 'nullable|string',
            'shipping_address' => 'nullable|string',
            'machines' => 'required|array|min:1',
            'machines.*.contract_machine_id' => 'required|exists:contract_machines,id',
            'machines.*.machine_category_id' => 'required|exists:machine_categories,id',
            'machines.*.quantity' => 'required|integer|min:1',
            'machines.*.amount' => 'nullable|numeric|min:0',
            'machines.*.amc_price' => 'nullable|numeric|min:0',
            'machines.*.pi_price_plus_amc' => 'nullable|numeric|min:0',
            'machines.*.total_pi_price' => 'nullable|numeric|min:0',
            'machines.*.brand_id' => 'nullable|exists:brands,id',
            'machines.*.machine_model_id' => 'nullable|exists:machine_models,id',
            'machines.*.feeder_id' => 'nullable|exists:feeders,id',
            'machines.*.machine_hook_id' => 'nullable|exists:machine_hooks,id',
            'machines.*.machine_e_read_id' => 'nullable|exists:machine_e_reads,id',
            'machines.*.color_id' => 'nullable|exists:colors,id',
            'machines.*.machine_nozzle_id' => 'nullable|exists:machine_nozzles,id',
            'machines.*.machine_dropin_id' => 'nullable|exists:machine_dropins,id',
            'machines.*.machine_beam_id' => 'nullable|exists:machine_beams,id',
            'machines.*.machine_cloth_roller_id' => 'nullable|exists:machine_cloth_rollers,id',
            'machines.*.machine_software_id' => 'nullable|exists:machine_softwares,id',
            'machines.*.hsn_code_id' => 'nullable|exists:hsn_codes,id',
            'machines.*.wir_id' => 'nullable|exists:wirs,id',
            'machines.*.machine_shaft_id' => 'nullable|exists:machine_shafts,id',
            'machines.*.machine_lever_id' => 'nullable|exists:machine_levers,id',
            'machines.*.machine_chain_id' => 'nullable|exists:machine_chains,id',
            'machines.*.machine_heald_wire_id' => 'nullable|exists:machine_heald_wires,id',
            'machines.*.delivery_term_id' => 'nullable|exists:delivery_terms,id',
            'machines.*.description' => 'nullable|string',
            'overseas_freight' => 'nullable|numeric|min:0',
            'port_expenses_clearing' => 'nullable|numeric|min:0',
            'gst_percentage' => 'nullable|numeric|min:0|max:100',
            'gst_amount' => 'nullable|numeric|min:0',
            'final_amount_with_gst' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
        ]);
        
        $contract = Contract::with('contractMachines')->findOrFail($request->contract_id);
        
        DB::beginTransaction();
        try {
            // Get seller for PI number generation
            $seller = Seller::findOrFail($request->seller_id);
            
            // Generate proforma invoice number based on seller
            $proformaInvoiceNumber = $this->generateProformaInvoiceNumber($seller);
            
            // Calculate total amount and validate quantities
            // Match frontend calculation: per-machine expenses, commission, and GST
            $totalFinalAmountUSD = 0;
            $totalOverseasFreight = 0;
            $totalPortExpenses = 0;
            $totalGSTAmount = 0;
            
            foreach ($request->machines as $machineData) {
                $contractMachine = ContractMachine::with('machineCategory')->findOrFail($machineData['contract_machine_id']);
                
                // Validate quantity doesn't exceed contract quantity
                if ($machineData['quantity'] > $contractMachine->quantity) {
                    $categoryName = $contractMachine->machineCategory->name ?? 'N/A';
                    return back()->withErrors([
                        'machines' => "Quantity for machine category '{$categoryName}' cannot exceed contract quantity of {$contractMachine->quantity}."
                    ])->withInput();
                }
                
                // Use the amount from request if provided, otherwise use contract machine amount
                $unitAmount = isset($machineData['amount']) && $machineData['amount'] > 0 
                    ? $machineData['amount'] 
                    : $contractMachine->amount;
                
                // Add AMC price to the calculation
                $amcPrice = isset($machineData['amc_price']) ? floatval($machineData['amc_price']) : 0;
                $piMachineAmount = $unitAmount * $machineData['quantity'];
                $piTotalAmount = $piMachineAmount + $amcPrice;
                
                // Commission Amount (for High Seas only, per machine)
                $commissionAmount = 0;
                if ($request->type_of_sale === 'high_seas' && $request->commission) {
                    $commissionAmount = ($piTotalAmount * $request->commission) / 100;
                }
                
                // Per-machine expenses
                $overseasFreight = isset($machineData['overseas_freight']) ? floatval($machineData['overseas_freight']) : 0;
                $portExpensesClearing = isset($machineData['port_expenses_clearing']) ? floatval($machineData['port_expenses_clearing']) : 0;
                
                // GST per machine (on PI + AMC amount)
                $gstPercentage = isset($machineData['gst_percentage']) ? floatval($machineData['gst_percentage']) : 0;
                $gstAmount = ($piTotalAmount * $gstPercentage) / 100;
                
                // Machine final amount = PI + AMC + Commission + Freight + Port + GST
                $machineFinalAmount = $piTotalAmount + $commissionAmount + $overseasFreight + $portExpensesClearing + $gstAmount;
                $totalFinalAmountUSD += $machineFinalAmount;
                
                // Track totals for storage
                $totalOverseasFreight += $overseasFreight;
                $totalPortExpenses += $portExpensesClearing;
                $totalGSTAmount += $gstAmount;
            }
            
            // Final amount with all per-machine calculations (in USD)
            $finalAmountWithGST = $totalFinalAmountUSD;
            
            // Store USD amount for all types (frontend handles display conversion)
            // For local sales, amounts are calculated in USD and displayed with ₹ symbol
            // The USD equivalent shown in parentheses is calculated as: displayAmount / usd_rate
            // So we store the USD amount (finalAmountWithGST) without conversion
            $displayAmount = $finalAmountWithGST;
            
            // Note: Commission is already included per-machine for high seas, so no additional commission needed here
            
            // Create proforma invoice
            $proformaInvoice = ProformaInvoice::create([
                'contract_id' => $contract->id,
                'seller_id' => $request->seller_id,
                'proforma_invoice_number' => $proformaInvoiceNumber,
                'created_by' => Auth::id(),
                'total_amount' => $displayAmount,
                'type_of_sale' => $request->type_of_sale,
                'currency' => $request->currency,
                'usd_rate' => $request->usd_rate,
                'commission' => $request->commission,
                'buyer_company_name' => $request->buyer_company_name,
                'pan' => $request->pan,
                'gst' => $request->gst,
                'phone_number' => $request->phone_number,
                'phone_number_2' => $request->phone_number_2,
                'ifc_certificate_number' => $request->ifc_certificate_number,
                'billing_address' => $request->billing_address,
                'shipping_address' => $request->shipping_address,
                'overseas_freight' => $request->overseas_freight,
                'port_expenses_clearing' => $request->port_expenses_clearing,
                // Get GST percentage from PI-level or first machine that has GST > 0
                'gst_percentage' => $this->getGSTPercentageFromRequest($request),
                'gst_amount' => $gstAmount,
                'final_amount_with_gst' => $finalAmountWithGST,
                'notes' => $request->notes,
            ]);
            
            // Create proforma invoice machines
            foreach ($request->machines as $machineData) {
                $contractMachine = ContractMachine::findOrFail($machineData['contract_machine_id']);
                
                $unitAmount = $machineData['amount'] ?? $contractMachine->amount;
                $amcPrice = $machineData['amc_price'] ?? 0;
                $piPricePlusAmc = $unitAmount + $amcPrice;
                $totalPiPrice = $piPricePlusAmc * $machineData['quantity'];
                
                ProformaInvoiceMachine::create([
                    'proforma_invoice_id' => $proformaInvoice->id,
                    'contract_machine_id' => $machineData['contract_machine_id'],
                    'machine_category_id' => $machineData['machine_category_id'] ?? null,
                    'brand_id' => $machineData['brand_id'] ?? null,
                    'machine_model_id' => $machineData['machine_model_id'] ?? null,
                    'quantity' => $machineData['quantity'],
                    'amount' => $unitAmount,
                    'amc_price' => $amcPrice,
                    'pi_price_plus_amc' => $piPricePlusAmc,
                    'total_pi_price' => $totalPiPrice,
                    'description' => $machineData['description'] ?? null,
                    'feeder_id' => $machineData['feeder_id'] ?? null,
                    'machine_hook_id' => $machineData['machine_hook_id'] ?? null,
                    'machine_e_read_id' => $machineData['machine_e_read_id'] ?? null,
                    'color_id' => $machineData['color_id'] ?? null,
                    'machine_nozzle_id' => $machineData['machine_nozzle_id'] ?? null,
                    'machine_dropin_id' => $machineData['machine_dropin_id'] ?? null,
                    'machine_beam_id' => $machineData['machine_beam_id'] ?? null,
                    'machine_cloth_roller_id' => $machineData['machine_cloth_roller_id'] ?? null,
                    'machine_software_id' => $machineData['machine_software_id'] ?? null,
                    'hsn_code_id' => $machineData['hsn_code_id'] ?? null,
                    'wir_id' => $machineData['wir_id'] ?? null,
                    'machine_shaft_id' => $machineData['machine_shaft_id'] ?? null,
                    'machine_lever_id' => $machineData['machine_lever_id'] ?? null,
                    'machine_chain_id' => $machineData['machine_chain_id'] ?? null,
                    'machine_heald_wire_id' => $machineData['machine_heald_wire_id'] ?? null,
                    'delivery_term_id' => $machineData['delivery_term_id'] ?? null,
                ]);
            }
            
            DB::commit();
            
            return redirect()->route('proforma-invoices.index')
                ->with('success', 'Proforma invoice created successfully.');
                
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Failed to create proforma invoice: ' . $e->getMessage()])->withInput();
        }
    }
    
    /**
     * Display a listing of proforma invoices
     */
    public function index(Request $request)
    {
        $query = ProformaInvoice::with(['contract', 'seller', 'creator', 'proformaInvoiceMachines'])
            ->orderBy('created_at', 'desc');

        // Filter by Sales Manager (contract creator)
        if ($request->filled('sales_manager')) {
            $query->whereHas('contract', function($q) use ($request) {
                $q->where('created_by', $request->sales_manager);
            });
        }

        // Filter by Contract Number
        if ($request->filled('contract_number')) {
            $query->whereHas('contract', function($q) use ($request) {
                $q->where('contract_number', 'like', '%' . $request->contract_number . '%');
            });
        }

        // Filter by Customer Name (buyer_name or company_name from contract, or buyer_company_name from PI)
        if ($request->filled('customer_name')) {
            $query->where(function($q) use ($request) {
                $q->where('buyer_company_name', 'like', '%' . $request->customer_name . '%')
                  ->orWhereHas('contract', function($contractQuery) use ($request) {
                      $contractQuery->where('buyer_name', 'like', '%' . $request->customer_name . '%')
                                    ->orWhere('company_name', 'like', '%' . $request->customer_name . '%');
                  });
            });
        }

        // Legacy search support (for backward compatibility)
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('proforma_invoice_number', 'like', '%' . $search . '%')
                  ->orWhere('buyer_company_name', 'like', '%' . $search . '%')
                  ->orWhereHas('contract', function($contractQuery) use ($search) {
                      $contractQuery->where('contract_number', 'like', '%' . $search . '%')
                                    ->orWhere('buyer_name', 'like', '%' . $search . '%')
                                    ->orWhere('company_name', 'like', '%' . $search . '%');
                  })
                  ->orWhereHas('seller', function($sellerQuery) use ($search) {
                      $sellerQuery->where('seller_name', 'like', '%' . $search . '%');
                  });
            });
        }

        // Legacy filter support (for backward compatibility)
        if ($request->filled('pi_number')) {
            $query->where('proforma_invoice_number', 'like', '%' . $request->pi_number . '%');
        }

        if ($request->filled('seller_id')) {
            $query->where('seller_id', $request->seller_id);
        }

        // Get sales managers (users who created contracts)
        $salesManagers = User::whereIn('id', function($query) {
            $query->select('created_by')
                ->from('contracts')
                ->distinct();
        })->select('id', 'name')->orderBy('name')->get();

        $proformaInvoices = $query->orderBy('created_at', 'desc')->paginate(15)->withQueryString();
        $sellers = \App\Models\Seller::orderBy('seller_name')->get();

        return view('proforma-invoices.index', compact('proformaInvoices', 'sellers', 'salesManagers'));
    }
    
    /**
     * Display the specified proforma invoice
     */
    public function show(ProformaInvoice $proformaInvoice)
    {
        $proformaInvoice->load([
            'contract.creator',
            'contract.businessFirm',
            'contract.state',
            'contract.city',
            'contract.area',
            'seller.piLayout',
            'proformaInvoiceMachines.contractMachine.machineCategory',
            'proformaInvoiceMachines.contractMachine.brand',
            'proformaInvoiceMachines.contractMachine.machineModel',
            'deliveryDetails',
            'creator'
        ]);
        
        // Get the layout to use (seller's layout or default)
        // First check if seller exists and has a layout assigned
        $layout = null;
        if ($proformaInvoice->seller && $proformaInvoice->seller->piLayout) {
            $layout = $proformaInvoice->seller->piLayout;
        } else {
            // Fall back to default active layout
            $layout = PILayout::where('is_default', true)->where('is_active', true)->first();
        }
        
        return view('proforma-invoices.show', compact('proformaInvoice', 'layout'));
    }
    
    /**
     * Show the form for editing the specified proforma invoice
     */
    public function edit(ProformaInvoice $proformaInvoice)
    {
        // Load the same data as create method
        $contract = $proformaInvoice->contract;
        $selectedContractId = $contract->id;
        
        $contracts = Contract::with(['creator', 'contractMachines.machineCategory'])
            ->where('id', $contract->id)
            ->paginate(10);
        
        $salesManagers = User::whereHas('createdContracts')->orderBy('name')->get();
        
        // Get all options for machine specifications
        $brands = Brand::orderBy('name')->get();
        $machineModels = MachineModel::with('brand')->orderBy('model_no')->get();
        $feeders = Feeder::with('feederBrand')->orderBy('feeder')->get();
        $machineHooks = MachineHook::orderBy('hook')->get();
        $machineEReads = MachineERead::orderBy('name')->get();
        $colors = Color::orderBy('name')->get();
        $machineNozzles = MachineNozzle::orderBy('nozzle')->get();
        $machineDropins = MachineDropin::orderBy('name')->get();
        $machineBeams = MachineBeam::orderBy('name')->get();
        $machineClothRollers = MachineClothRoller::orderBy('name')->get();
        $machineSoftwares = MachineSoftware::orderBy('name')->get();
        $hsnCodes = HsnCode::orderBy('name')->get();
        $wirs = Wir::orderBy('name')->get();
        $machineShafts = MachineShaft::orderBy('name')->get();
        $machineLevers = MachineLever::orderBy('name')->get();
        $machineChains = MachineChain::orderBy('name')->get();
        $machineHealdWires = MachineHealdWire::orderBy('name')->get();
        $deliveryTerms = DeliveryTerm::orderBy('name')->get();
        $sellers = Seller::orderBy('seller_name')->get();
        
        // Load proforma invoice with all relationships
        $proformaInvoice->load([
            'proformaInvoiceMachines.contractMachine.machineCategory',
            'seller'
        ]);
        
        return view('proforma-invoices.edit', compact(
            'proformaInvoice',
            'contracts',
            'salesManagers',
            'selectedContractId',
            'brands',
            'machineModels',
            'feeders',
            'machineHooks',
            'machineEReads',
            'colors',
            'machineNozzles',
            'machineDropins',
            'machineBeams',
            'machineClothRollers',
            'machineSoftwares',
            'hsnCodes',
            'wirs',
            'machineShafts',
            'machineLevers',
            'machineChains',
            'machineHealdWires',
            'deliveryTerms',
            'sellers'
        ));
    }
    
    /**
     * Update the specified proforma invoice
     */
    public function update(Request $request, ProformaInvoice $proformaInvoice)
    {
        // Similar validation as store method
        $request->validate([
            'seller_id' => 'required|exists:sellers,id',
            'type_of_sale' => 'required|in:import,local,high_seas',
            'currency' => 'required|string|max:10',
            'usd_rate' => 'nullable|numeric|min:0',
            'commission' => 'nullable|numeric|min:0|max:100',
            'buyer_company_name' => 'required|string|max:255',
            'pan' => 'nullable|string|max:255',
            'gst' => 'nullable|string|max:255',
            'phone_number' => 'required|string|max:255',
            'phone_number_2' => 'nullable|string|max:255',
            'ifc_certificate_number' => 'nullable|string|max:255',
            'billing_address' => 'nullable|string',
            'shipping_address' => 'nullable|string',
            'machines' => 'required|array|min:1',
            'machines.*.contract_machine_id' => 'required|exists:contract_machines,id',
            'machines.*.machine_category_id' => 'required|exists:machine_categories,id',
            'machines.*.quantity' => 'required|integer|min:1',
            'machines.*.amount' => 'nullable|numeric|min:0',
            'machines.*.amc_price' => 'nullable|numeric|min:0',
            'overseas_freight' => 'nullable|numeric|min:0',
            'port_expenses_clearing' => 'nullable|numeric|min:0',
            'gst_percentage' => 'nullable|numeric|min:0|max:100',
            'notes' => 'nullable|string',
        ]);
        
        $contract = Contract::with('contractMachines')->findOrFail($proformaInvoice->contract_id);
        
        DB::beginTransaction();
        try {
            // Calculate totals (matching frontend: per-machine calculation)
            $totalFinalAmountUSD = 0;
            $totalOverseasFreight = 0;
            $totalPortExpenses = 0;
            $totalGSTAmount = 0;
            
            foreach ($request->machines as $machineData) {
                $contractMachine = ContractMachine::findOrFail($machineData['contract_machine_id']);
                
                if ($machineData['quantity'] > $contractMachine->quantity) {
                    $categoryName = $contractMachine->machineCategory->name ?? 'N/A';
                    return back()->withErrors([
                        'machines' => "Quantity for machine category '{$categoryName}' cannot exceed contract quantity of {$contractMachine->quantity}."
                    ])->withInput();
                }
                
                $unitAmount = isset($machineData['amount']) && $machineData['amount'] > 0 
                    ? $machineData['amount'] 
                    : $contractMachine->amount;
                
                $amcPrice = isset($machineData['amc_price']) ? floatval($machineData['amc_price']) : 0;
                $piMachineAmount = $unitAmount * $machineData['quantity'];
                $piTotalAmount = $piMachineAmount + $amcPrice;
                
                // Commission Amount (for High Seas only, per machine)
                $commissionAmount = 0;
                if ($request->type_of_sale === 'high_seas' && $request->commission) {
                    $commissionAmount = ($piTotalAmount * $request->commission) / 100;
                }
                
                // Per-machine expenses
                $overseasFreight = isset($machineData['overseas_freight']) ? floatval($machineData['overseas_freight']) : 0;
                $portExpensesClearing = isset($machineData['port_expenses_clearing']) ? floatval($machineData['port_expenses_clearing']) : 0;
                
                // GST per machine (on PI + AMC amount)
                $gstPercentage = isset($machineData['gst_percentage']) ? floatval($machineData['gst_percentage']) : 0;
                $gstAmount = ($piTotalAmount * $gstPercentage) / 100;
                
                // Machine final amount = PI + AMC + Commission + Freight + Port + GST
                $machineFinalAmount = $piTotalAmount + $commissionAmount + $overseasFreight + $portExpensesClearing + $gstAmount;
                $totalFinalAmountUSD += $machineFinalAmount;
                
                // Track totals for storage
                $totalOverseasFreight += $overseasFreight;
                $totalPortExpenses += $portExpensesClearing;
                $totalGSTAmount += $gstAmount;
            }
            
            // Final amount with all per-machine calculations (in USD)
            $finalAmountWithGST = $totalFinalAmountUSD;
            
            // Store USD amount for all types (frontend handles display conversion)
            // For local sales, amounts are calculated in USD and displayed with ₹ symbol
            // The USD equivalent shown in parentheses is calculated as: displayAmount / usd_rate
            // So we store the USD amount (finalAmountWithGST) without conversion
            $displayAmount = $finalAmountWithGST;
            
            // Note: Commission is already included per-machine for high seas, so no additional commission needed here
            
            // Update proforma invoice
            $proformaInvoice->update([
                'seller_id' => $request->seller_id,
                'total_amount' => $displayAmount,
                'type_of_sale' => $request->type_of_sale,
                'currency' => $request->currency,
                'usd_rate' => $request->usd_rate,
                'commission' => $request->commission,
                'buyer_company_name' => $request->buyer_company_name,
                'pan' => $request->pan,
                'gst' => $request->gst,
                'phone_number' => $request->phone_number,
                'phone_number_2' => $request->phone_number_2,
                'ifc_certificate_number' => $request->ifc_certificate_number,
                'billing_address' => $request->billing_address,
                'shipping_address' => $request->shipping_address,
                'overseas_freight' => $totalOverseasFreight,
                'port_expenses_clearing' => $totalPortExpenses,
                // Get GST percentage from PI-level or first machine that has GST > 0
                'gst_percentage' => $this->getGSTPercentageFromRequest($request),
                'gst_amount' => $totalGSTAmount,
                'final_amount_with_gst' => $finalAmountWithGST,
                'notes' => $request->notes,
            ]);
            
            // Delete existing machines and create new ones
            $proformaInvoice->proformaInvoiceMachines()->delete();
            
            foreach ($request->machines as $machineData) {
                $contractMachine = ContractMachine::findOrFail($machineData['contract_machine_id']);
                $unitAmount = $machineData['amount'] ?? $contractMachine->amount;
                $amcPrice = $machineData['amc_price'] ?? 0;
                $piPricePlusAmc = $unitAmount + $amcPrice;
                $totalPiPrice = $piPricePlusAmc * $machineData['quantity'];
                
                ProformaInvoiceMachine::create([
                    'proforma_invoice_id' => $proformaInvoice->id,
                    'contract_machine_id' => $machineData['contract_machine_id'],
                    'machine_category_id' => $machineData['machine_category_id'] ?? null,
                    'brand_id' => $machineData['brand_id'] ?? null,
                    'machine_model_id' => $machineData['machine_model_id'] ?? null,
                    'quantity' => $machineData['quantity'],
                    'amount' => $unitAmount,
                    'amc_price' => $amcPrice,
                    'pi_price_plus_amc' => $piPricePlusAmc,
                    'total_pi_price' => $totalPiPrice,
                    'description' => $machineData['description'] ?? null,
                    'feeder_id' => $machineData['feeder_id'] ?? null,
                    'machine_hook_id' => $machineData['machine_hook_id'] ?? null,
                    'machine_e_read_id' => $machineData['machine_e_read_id'] ?? null,
                    'color_id' => $machineData['color_id'] ?? null,
                    'machine_nozzle_id' => $machineData['machine_nozzle_id'] ?? null,
                    'machine_dropin_id' => $machineData['machine_dropin_id'] ?? null,
                    'machine_beam_id' => $machineData['machine_beam_id'] ?? null,
                    'machine_cloth_roller_id' => $machineData['machine_cloth_roller_id'] ?? null,
                    'machine_software_id' => $machineData['machine_software_id'] ?? null,
                    'hsn_code_id' => $machineData['hsn_code_id'] ?? null,
                    'wir_id' => $machineData['wir_id'] ?? null,
                    'machine_shaft_id' => $machineData['machine_shaft_id'] ?? null,
                    'machine_lever_id' => $machineData['machine_lever_id'] ?? null,
                    'machine_chain_id' => $machineData['machine_chain_id'] ?? null,
                    'machine_heald_wire_id' => $machineData['machine_heald_wire_id'] ?? null,
                    'delivery_term_id' => $machineData['delivery_term_id'] ?? null,
                ]);
            }
            
            DB::commit();
            
            return redirect()->route('proforma-invoices.index')
                ->with('success', 'Proforma invoice updated successfully.');
                
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Failed to update proforma invoice: ' . $e->getMessage()])->withInput();
        }
    }
    
    /**
     * Remove the specified proforma invoice
     */
    public function destroy(ProformaInvoice $proformaInvoice)
    {
        try {
            $proformaInvoice->proformaInvoiceMachines()->delete();
            $proformaInvoice->delete();
            
            return redirect()->route('proforma-invoices.index')
                ->with('success', 'Proforma invoice deleted successfully.');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Failed to delete proforma invoice: ' . $e->getMessage()]);
        }
    }
    
    /**
     * Download proforma invoice as PDF.
     */
    public function downloadPdf(ProformaInvoice $proformaInvoice)
    {
        try {
            $proformaInvoice->load([
                'contract.creator',
                'contract.businessFirm',
                'contract.state',
                'contract.city',
                'contract.area',
                'seller.piLayout',
                'seller.country',
                'seller.bankDetails',
                'proformaInvoiceMachines.contractMachine.machineCategory',
                'proformaInvoiceMachines.contractMachine.brand',
                'proformaInvoiceMachines.contractMachine.machineModel',
                'proformaInvoiceMachines.contractMachine.feeder.feederBrand',
                'proformaInvoiceMachines.contractMachine.machineHook',
                'proformaInvoiceMachines.contractMachine.machineERead',
                'proformaInvoiceMachines.contractMachine.color',
                'proformaInvoiceMachines.contractMachine.machineNozzle',
                'proformaInvoiceMachines.contractMachine.machineDropin',
                'proformaInvoiceMachines.contractMachine.machineBeam',
                'proformaInvoiceMachines.contractMachine.machineClothRoller',
                'proformaInvoiceMachines.contractMachine.machineSoftware',
                'proformaInvoiceMachines.contractMachine.hsnCode',
                'proformaInvoiceMachines.contractMachine.wir',
                'proformaInvoiceMachines.contractMachine.machineShaft',
                'proformaInvoiceMachines.contractMachine.machineLever',
                'proformaInvoiceMachines.contractMachine.machineChain',
                'proformaInvoiceMachines.contractMachine.machineHealdWire',
                'proformaInvoiceMachines.contractMachine.deliveryTerm',
                'creator'
            ]);
            
            // Get the layout to use (seller's layout or default)
            // First check if seller exists and has a layout assigned
            $layout = null;
            if ($proformaInvoice->seller) {
                // Ensure piLayout relationship is loaded
                if (!$proformaInvoice->seller->relationLoaded('piLayout')) {
                    $proformaInvoice->seller->load('piLayout');
                }
                
                // Check if seller has a layout assigned
                if ($proformaInvoice->seller->piLayout) {
                    $layout = $proformaInvoice->seller->piLayout;
                }
            }
            
            // If no seller layout, fall back to default active layout
            if (!$layout) {
                $layout = PILayout::where('is_default', true)->where('is_active', true)->first();
            }
            
            // Log which layout is being used for debugging
            \Log::info('PDF Layout Selection', [
                'proforma_invoice_id' => $proformaInvoice->id,
                'seller_id' => $proformaInvoice->seller->id ?? null,
                'seller_name' => $proformaInvoice->seller->seller_name ?? null,
                'layout_id' => $layout->id ?? null,
                'layout_name' => $layout->name ?? null,
                'has_template' => !empty($layout->template_html ?? null),
                'is_active' => $layout->is_active ?? false,
            ]);
            
            // If layout exists and has template, use it; otherwise use default PDF view
            if ($layout && !empty($layout->template_html) && $layout->is_active) {
                try {
                    // Set execution time limit for template rendering
                    set_time_limit(30); // 30 seconds max
                    
                    // Render the layout template
                    $html = $this->renderLayoutTemplate($layout->template_html, $proformaInvoice);
                    if (!$html || empty(trim($html))) {
                        throw new \Exception('Layout template rendered empty HTML');
                    }
                    
                    // Load HTML into DomPDF
                    $pdf = DomPDF::loadHTML($html);
                } catch (\Exception $e) {
                    // Log the error but fall back to default PDF view
                    \Log::warning('Failed to render custom layout, falling back to default PDF: ' . $e->getMessage(), [
                        'layout_id' => $layout->id,
                        'layout_name' => $layout->name,
                        'proforma_invoice_id' => $proformaInvoice->id,
                        'error_trace' => $e->getTraceAsString()
                    ]);
                    // Fall back to default PDF view
                    $pdf = DomPDF::loadView('proforma-invoices.pdf', compact('proformaInvoice', 'layout'));
                }
            } else {
                // Use default PDF view
                $pdf = DomPDF::loadView('proforma-invoices.pdf', compact('proformaInvoice', 'layout'));
            }
            
            // Set options to enable font subsetting and Unicode support
            $pdf->setOption('enable-font-subsetting', true);
            $pdf->setOption('isHtml5ParserEnabled', true);
            // Disable remote resource fetching to prevent hangs - we use base64 for images
            $pdf->setOption('isRemoteEnabled', false);
            $pdf->setOption('chroot', public_path());
            // Enable font caching for better performance
            $pdf->setOption('fontHeightRatio', 1.1);
            // Set default font to Times New Roman if available
            $pdf->setOption('defaultFont', 'Times-Roman');
            
            return $pdf->download('proforma-invoice-' . $proformaInvoice->proforma_invoice_number . '.pdf');
        } catch (\Exception $e) {
            \Log::error('PDF Generation Error: ' . $e->getMessage(), [
                'proforma_invoice_id' => $proformaInvoice->id,
                'trace' => $e->getTraceAsString()
            ]);
            
            return back()->withErrors(['error' => 'Failed to generate PDF: ' . $e->getMessage()]);
        }
    }
    
    /**
     * Render layout template HTML with proforma invoice data
     */
    private function renderLayoutTemplate($templateHtml, $proformaInvoice)
    {
        // Ensure seller is loaded with all relationships
        if ($proformaInvoice->seller) {
            if (!$proformaInvoice->seller->relationLoaded('country')) {
                $proformaInvoice->seller->load('country');
            }
            if (!$proformaInvoice->seller->relationLoaded('bankDetails')) {
                $proformaInvoice->seller->load('bankDetails');
            }
        }
        
        // Ensure all required relationships are loaded
        if ($proformaInvoice->contract) {
            if (!$proformaInvoice->contract->relationLoaded('state')) {
                $proformaInvoice->contract->load('state');
            }
            if (!$proformaInvoice->contract->relationLoaded('city')) {
                $proformaInvoice->contract->load('city');
            }
            if (!$proformaInvoice->contract->relationLoaded('area')) {
                $proformaInvoice->contract->load('area');
            }
            // Contract model doesn't have deliveryTerm relationship
            // Create delivery_term object from loading_terms field for template compatibility
            if (!isset($proformaInvoice->contract->delivery_term)) {
                if (isset($proformaInvoice->contract->loading_terms) && $proformaInvoice->contract->loading_terms) {
                    $proformaInvoice->contract->delivery_term = (object)['name' => $proformaInvoice->contract->loading_terms];
                } else {
                    $proformaInvoice->contract->delivery_term = (object)['name' => 'WITHIN 60 DAYS AFTER RECEIVING PAYMENT'];
                }
            }
        }
        
        // Remove external font links (Google Fonts CDN) as they don't work in PDF
        // Replace with system fonts or @font-face declarations
        $templateHtml = preg_replace(
            '/<link[^>]*href\s*=\s*["\'][^"\']*fonts\.(cdnfonts|googleapis|bunny)[^"\']*["\'][^>]*>/i',
            '',
            $templateHtml
        );
        
        // Replace "Times New Roman" with "Times-Roman" for DomPDF compatibility
        // DomPDF uses "Times-Roman" as the built-in font name, but also supports "Times New Roman"
        // We'll add both to ensure compatibility
        $templateHtml = str_replace("'Times New Roman'", "'Times-Roman', 'Times New Roman'", $templateHtml);
        $templateHtml = str_replace('"Times New Roman"', '"Times-Roman", "Times New Roman"', $templateHtml);
        // Also handle cases without quotes in font-family declarations
        $templateHtml = preg_replace('/font-family:\s*([^;]*?)Times New Roman([^;]*?);/i', 'font-family: $1Times-Roman, Times New Roman$2;', $templateHtml);
        
        // Create a temporary view file
        $tempPath = storage_path('app/temp_template_' . uniqid() . '.blade.php');
        
        try {
            // Write template to file
            if (file_put_contents($tempPath, $templateHtml) === false) {
                throw new \Exception('Failed to write template file to: ' . $tempPath);
            }

            // Compile the view with data (this processes Blade syntax including asset())
            $rendered = view()->file($tempPath, [
                'proformaInvoice' => $proformaInvoice,
                'seller' => $proformaInvoice->seller ?? null,
                'machines' => $proformaInvoice->proformaInvoiceMachines ?? collect(),
                'contract' => $proformaInvoice->contract ?? null,
            ])->render();

            // Clean up temporary file
            @unlink($tempPath);

            if (empty(trim($rendered))) {
                throw new \Exception('Template rendered empty content');
            }
            
            // Now convert image URLs to base64 data URIs AFTER rendering
            // This prevents DomPDF from trying to fetch remote resources
            $appUrl = rtrim(config('app.url', 'http://localhost'), '/');
            
            // Convert img src URLs to base64 data URIs
            $rendered = preg_replace_callback(
                '/<img([^>]*)\s+src\s*=\s*["\']([^"\']+)["\']([^>]*)>/i',
                function ($matches) use ($appUrl) {
                    $beforeAttrs = $matches[1];
                    $srcUrl = $matches[2];
                    $afterAttrs = $matches[3];
                    
                    // Skip if already base64 encoded
                    if (strpos($srcUrl, 'data:image') === 0) {
                        return $matches[0];
                    }
                    
                    $filePath = null;
                    
                    // Check if it's a local asset URL (various formats)
                    if (strpos($srcUrl, $appUrl . '/storage/') === 0) {
                        // Full URL: http://localhost/storage/...
                        $path = str_replace($appUrl, '', $srcUrl);
                        $path = ltrim($path, '/');
                        $filePath = public_path($path);
                    } elseif (strpos($srcUrl, '/storage/') === 0) {
                        // Relative URL: /storage/...
                        $path = ltrim($srcUrl, '/');
                        $filePath = public_path($path);
                    } elseif (strpos($srcUrl, 'storage/') === 0) {
                        // Relative URL without leading slash: storage/...
                        $filePath = public_path($srcUrl);
                    }
                    
                    // Also check storage_path('app/public') directly
                    if (!$filePath || !file_exists($filePath)) {
                        // Try extracting path from URL
                        if (preg_match('/storage\/(.+)$/i', $srcUrl, $pathMatches)) {
                            $storagePath = storage_path('app/public/' . $pathMatches[1]);
                            if (file_exists($storagePath)) {
                                $filePath = $storagePath;
                            }
                        }
                    }
                    
                    if ($filePath && file_exists($filePath) && is_file($filePath)) {
                        $imageInfo = @getimagesize($filePath);
                        if ($imageInfo !== false) {
                            $imageData = @file_get_contents($filePath);
                            if ($imageData !== false) {
                                $mimeType = $imageInfo['mime'] ?? 'image/png';
                                $base64 = base64_encode($imageData);
                                return '<img' . $beforeAttrs . ' src="data:' . $mimeType . ';base64,' . $base64 . '"' . $afterAttrs . '>';
                            }
                        }
                    }
                    
                    // Return original if conversion failed
                    return $matches[0];
                },
                $rendered
            );

            return $rendered;
        } catch (\Exception $e) {
            // Clean up temporary file on error
            @unlink($tempPath);
            
            // Log the error with more details
            \Log::error('Error rendering PI layout template: ' . $e->getMessage(), [
                'template_path' => $tempPath,
                'template_length' => strlen($templateHtml),
                'proforma_invoice_id' => $proformaInvoice->id ?? null,
                'seller_id' => $proformaInvoice->seller->id ?? null,
                'trace' => $e->getTraceAsString()
            ]);
            
            throw $e;
        }
    }
    
    /**
     * Get contract details for proforma invoice form
     */
    public function getContractDetails(Contract $contract, Request $request)
    {
        $contract->load([
            'contractMachines.machineCategory',
            'contractMachines.brand',
            'contractMachines.machineModel',
            'contractMachines.feeder.feederBrand',
            'contractMachines.machineHook',
            'contractMachines.machineERead',
            'contractMachines.color',
            'contractMachines.machineNozzle',
            'contractMachines.machineDropin',
            'contractMachines.machineBeam',
            'contractMachines.machineClothRoller',
            'contractMachines.machineSoftware',
            'contractMachines.hsnCode',
            'contractMachines.wir',
            'contractMachines.machineShaft',
            'contractMachines.machineLever',
            'contractMachines.machineChain',
            'contractMachines.machineHealdWire',
            'contractMachines.deliveryTerm',
            'creator'
        ]);
        
        // Group machines by category
        $machinesByCategory = $contract->contractMachines->groupBy('machine_category_id');
        
        // Calculate used quantities per category from existing PIs for this contract
        // Exclude current PI if editing (passed as exclude_pi_id parameter)
        $excludePIId = $request->get('exclude_pi_id');
        $usedQuantitiesByCategory = [];
        $existingPIsQuery = ProformaInvoice::where('contract_id', $contract->id);
        
        if ($excludePIId) {
            $existingPIsQuery->where('id', '!=', $excludePIId);
        }
        
        $existingPIs = $existingPIsQuery->with('proformaInvoiceMachines.contractMachine')->get();
        
        foreach ($existingPIs as $pi) {
            foreach ($pi->proformaInvoiceMachines as $piMachine) {
                $categoryId = $piMachine->contractMachine->machine_category_id;
                if (!isset($usedQuantitiesByCategory[$categoryId])) {
                    $usedQuantitiesByCategory[$categoryId] = 0;
                }
                $usedQuantitiesByCategory[$categoryId] += $piMachine->quantity;
            }
        }
        
        return response()->json([
            'contract' => [
                'id' => $contract->id,
                'contract_number' => $contract->contract_number,
                'buyer_name' => $contract->buyer_name,
                'company_name' => $contract->company_name,
                'pan' => $contract->pan,
                'gst' => $contract->gst,
                'phone_number' => $contract->phone_number,
                'phone_number_2' => $contract->phone_number_2,
                'contact_address' => $contract->contact_address,
                'total_amount' => $contract->total_amount,
                'creator' => $contract->creator ? ['name' => $contract->creator->name] : null,
            ],
            'usedQuantitiesByCategory' => $usedQuantitiesByCategory,
            'machinesByCategory' => $machinesByCategory->map(function ($machines, $categoryId) use ($usedQuantitiesByCategory) {
                $firstMachine = $machines->first();
                $categoryIdStr = (string)$categoryId;
                $usedQty = $usedQuantitiesByCategory[$categoryIdStr] ?? 0;
                $contractQty = $firstMachine->quantity ?? 0;
                $availableQty = max(0, $contractQty - $usedQty);
                
                return [
                    'category_id' => $categoryIdStr, // Ensure string type for consistent comparison
                    'category_name' => $firstMachine->machineCategory->name ?? 'N/A',
                    'contract_quantity' => $contractQty,
                    'used_quantity' => $usedQty,
                    'available_quantity' => $availableQty,
                    'machines' => $machines->map(function ($machine) {
                        return [
                            'id' => $machine->id,
                            'category_id' => $machine->machine_category_id,
                            'brand_id' => $machine->brand_id,
                            'brand' => $machine->brand->name ?? 'N/A',
                            'machine_model_id' => $machine->machine_model_id,
                            'model' => $machine->machineModel->model_no ?? 'N/A',
                            'quantity' => $machine->quantity,
                            'amount' => $machine->amount,
                            'total' => $machine->quantity * $machine->amount,
                            'description' => $machine->description,
                            'feeder_id' => $machine->feeder_id,
                            'feeder' => $machine->feeder ? ($machine->feeder->feederBrand->name ?? '') . ' - ' . $machine->feeder->feeder : null,
                            'machine_hook_id' => $machine->machine_hook_id,
                            'machine_hook' => $machine->machineHook ? $machine->machineHook->hook ?? 'N/A' : null,
                            'machine_e_read_id' => $machine->machine_e_read_id,
                            'machine_e_read' => $machine->machineERead ? $machine->machineERead->name ?? 'N/A' : null,
                            'color_id' => $machine->color_id,
                            'color' => $machine->color ? $machine->color->name ?? 'N/A' : null,
                            'machine_nozzle_id' => $machine->machine_nozzle_id,
                            'machine_nozzle' => $machine->machineNozzle ? $machine->machineNozzle->nozzle ?? 'N/A' : null,
                            'machine_dropin_id' => $machine->machine_dropin_id,
                            'machine_dropin' => $machine->machineDropin ? $machine->machineDropin->name ?? 'N/A' : null,
                            'machine_beam_id' => $machine->machine_beam_id,
                            'machine_beam' => $machine->machineBeam ? $machine->machineBeam->name ?? 'N/A' : null,
                            'machine_cloth_roller_id' => $machine->machine_cloth_roller_id,
                            'machine_cloth_roller' => $machine->machineClothRoller ? $machine->machineClothRoller->name ?? 'N/A' : null,
                            'machine_software_id' => $machine->machine_software_id,
                            'machine_software' => $machine->machineSoftware ? $machine->machineSoftware->name ?? 'N/A' : null,
                            'hsn_code_id' => $machine->hsn_code_id,
                            'hsn_code' => $machine->hsnCode ? $machine->hsnCode->name ?? 'N/A' : null,
                            'wir_id' => $machine->wir_id,
                            'wir' => $machine->wir ? $machine->wir->name ?? 'N/A' : null,
                            'machine_shaft_id' => $machine->machine_shaft_id,
                            'machine_shaft' => $machine->machineShaft ? $machine->machineShaft->name ?? 'N/A' : null,
                            'machine_lever_id' => $machine->machine_lever_id,
                            'machine_lever' => $machine->machineLever ? $machine->machineLever->name ?? 'N/A' : null,
                            'machine_chain_id' => $machine->machine_chain_id,
                            'machine_chain' => $machine->machineChain ? $machine->machineChain->name ?? 'N/A' : null,
                            'machine_heald_wire_id' => $machine->machine_heald_wire_id,
                            'machine_heald_wire' => $machine->machineHealdWire ? $machine->machineHealdWire->name ?? 'N/A' : null,
                            'delivery_term_id' => $machine->delivery_term_id,
                            'delivery_term' => $machine->deliveryTerm ? $machine->deliveryTerm->name ?? 'N/A' : null,
                        ];
                    })
                ];
            })->values()
        ]);
    }
    
    /**
     * Generate unique proforma invoice number based on seller
     * Format: PI_SHORT_NAME + ddmmyyyy or PI_SHORT_NAME + ddmmyyyy_A, _B, etc.
     */
    private function generateProformaInvoiceNumber(Seller $seller)
    {
        $piShortName = $seller->pi_short_name;
        $todayDate = date('dmY'); // ddmmyyyy format
        $baseNumber = $piShortName . $todayDate;
        
        // Check for all existing invoices with this base number (with or without suffix) created today
        $existingInvoices = ProformaInvoice::where(function($query) use ($baseNumber) {
                $query->where('proforma_invoice_number', $baseNumber)
                      ->orWhere('proforma_invoice_number', 'like', $baseNumber . '_%');
            })
            ->whereDate('created_at', today())
            ->orderBy('proforma_invoice_number', 'desc')
            ->get();
        
        if ($existingInvoices->isEmpty()) {
            // No duplicate, return base number
            return $baseNumber;
        }
        
        // Check if base number exists (without suffix)
        $baseExists = $existingInvoices->contains(function($invoice) use ($baseNumber) {
            return $invoice->proforma_invoice_number === $baseNumber;
        });
        
        // Get invoices with suffix only
        $suffixedInvoices = $existingInvoices->filter(function($invoice) use ($baseNumber) {
            return strpos($invoice->proforma_invoice_number, $baseNumber . '_') === 0;
        });
        
        if ($suffixedInvoices->isEmpty()) {
            // Base number exists but no suffixes yet, use _A
            return $baseNumber . '_A';
        }
        
        // Get the last suffix
        $lastInvoice = $suffixedInvoices->first();
        $lastSuffix = substr($lastInvoice->proforma_invoice_number, strlen($baseNumber) + 1);
        
        // Increment suffix (A -> B, B -> C, etc.)
        if (strlen($lastSuffix) === 1 && ctype_alpha($lastSuffix)) {
            $nextSuffix = chr(ord($lastSuffix) + 1);
            return $baseNumber . '_' . $nextSuffix;
        }
        
        // Fallback: if suffix is not a single letter, start with _A
        return $baseNumber . '_A';
    }

    /**
     * Show delivery details form for a proforma invoice
     */
    public function deliveryDetails(ProformaInvoice $proformaInvoice)
    {
        $proformaInvoice->load('deliveryDetails');
        
        // Define all delivery document types
        $deliveryDocuments = [
            'Commercial Invoice',
            'Packing List',
            'Country of Origin Certificate',
            'Bill of Landing',
            'Container Number',
            'Port of Arrival',
            'Port of Discharge',
            'Expected Date of Arrival',
            'Marine Insurance',
            'Name of Vessel',
            'Name of CHA (Clearing Agent)',
            'Date of Dispatch',
            'Actual Date of Arrival',
            'Land Insurance',
            'Date of Loading from Port',
            'Delivery at Buyer\'s Factory',
        ];

        // Load delivery details and documents
        $proformaInvoice->load(['deliveryDetails', 'documents']);
        
        // Get existing delivery details indexed by document name
        $existingDetails = $proformaInvoice->deliveryDetails->keyBy('document_name');
        
        // Get existing uploaded images
        $existingImages = $proformaInvoice->documents;

        return view('proforma-invoices.delivery-details', compact('proformaInvoice', 'deliveryDocuments', 'existingDetails', 'existingImages'));
    }

    /**
     * Store or update delivery details for a proforma invoice
     */
    public function storeDeliveryDetails(Request $request, ProformaInvoice $proformaInvoice)
    {
        $request->validate([
            'delivery_details' => 'required|array',
            'delivery_details.*.document_name' => 'required|string|max:255',
            'delivery_details.*.date' => 'nullable|date',
            'delivery_details.*.document_number' => 'nullable|string|max:255',
            'delivery_details.*.no_of_copies' => 'nullable|integer|min:0',
            'delivery_details.*.is_received' => 'nullable',
            'images' => 'nullable|array',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif|max:10240', // Max 10MB per image
        ]);

        try {
            DB::beginTransaction();

            // Get existing delivery details indexed by document name
            $existingDetails = $proformaInvoice->deliveryDetails->keyBy('document_name');

            // Update or create delivery details only for rows with data
            $sortOrder = 0;
            foreach ($request->delivery_details as $index => $detail) {
                if (!empty($detail['document_name'])) {
                    $documentName = $detail['document_name'];
                    
                    // Check if this row has any data to save (date, number, copies, or checkbox)
                    $hasDate = !empty($detail['date']);
                    $hasNumber = !empty($detail['document_number']) && trim($detail['document_number']) !== '';
                    $hasCopies = isset($detail['no_of_copies']) && $detail['no_of_copies'] !== '' && $detail['no_of_copies'] !== null;
                    $hasCheckbox = isset($detail['is_received']);
                    
                    // Only save if there's at least one field filled
                    if ($hasDate || $hasNumber || $hasCopies || $hasCheckbox) {
                        // Checkbox: if not set, it means unchecked (false)
                        $isReceived = isset($detail['is_received']) && ($detail['is_received'] == '1' || $detail['is_received'] === true || $detail['is_received'] === 'on');
                        
                        // Check if this document detail already exists
                        if ($existingDetails->has($documentName)) {
                            // Update existing detail
                            $existingDetail = $existingDetails->get($documentName);
                            $existingDetail->update([
                                'date' => $hasDate ? $detail['date'] : $existingDetail->date,
                                'number' => $hasNumber ? trim($detail['document_number']) : $existingDetail->number,
                                'no_of_copies' => $hasCopies ? (int)$detail['no_of_copies'] : $existingDetail->no_of_copies,
                                'is_received' => $hasCheckbox ? $isReceived : $existingDetail->is_received,
                            ]);
                        } else {
                            // Create new detail
                            PIDeliveryDetail::create([
                                'proforma_invoice_id' => $proformaInvoice->id,
                                'document_name' => $documentName,
                                'date' => $hasDate ? $detail['date'] : null,
                                'number' => $hasNumber ? trim($detail['document_number']) : null,
                                'no_of_copies' => $hasCopies ? (int)$detail['no_of_copies'] : null,
                                'is_received' => $hasCheckbox ? $isReceived : false,
                                'sort_order' => $sortOrder++,
                            ]);
                        }
                    }
                }
            }

            // Handle image uploads
            if ($request->hasFile('images')) {
                $rowNumber = 1;
                foreach ($request->file('images') as $image) {
                    if ($image->isValid()) {
                        $fileName = time() . '_' . uniqid() . '_' . $image->getClientOriginalName();
                        $filePath = $image->storeAs('pi-delivery-images/' . $proformaInvoice->id, $fileName, 'public');
                        
                        PIDocument::create([
                            'proforma_invoice_id' => $proformaInvoice->id,
                            'file_name' => $image->getClientOriginalName(),
                            'file_path' => $filePath,
                            'file_type' => $image->getMimeType(),
                            'file_size' => $image->getSize(),
                            'row_number' => $rowNumber++,
                        ]);
                    }
                }
            }

            DB::commit();

            return redirect()->route('proforma-invoices.show', $proformaInvoice)
                ->with('success', 'Delivery details saved successfully.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            return back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error saving delivery details: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());
            return back()->withErrors(['error' => 'Failed to save delivery details: ' . $e->getMessage()])
                ->withInput();
        }
    }

    /**
     * Display delivery details index page (list all PIs - with or without delivery details)
     */
    public function deliveryDetailsIndex(Request $request)
    {
        $query = ProformaInvoice::with(['deliveryDetails', 'contract.creator', 'creator', 'seller'])
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

        // Filter by PI Number
        if ($request->filled('pi_number')) {
            $query->where('proforma_invoice_number', 'like', '%' . $request->pi_number . '%');
        }

        // Filter by Customer Name
        if ($request->filled('customer_name')) {
            $query->where('buyer_company_name', 'like', '%' . $request->customer_name . '%');
        }

        $proformaInvoices = $query->paginate(15)->withQueryString();
        
        // Get all users who can be sales managers (users who created contracts or PIs)
        $salesManagers = User::where(function($q) {
            $q->whereHas('createdContracts')
              ->orWhereHas('createdProformaInvoices');
        })->orderBy('name')->get();

        return view('proforma-invoices.delivery-details-index', compact('proformaInvoices', 'salesManagers'));
    }

    /**
     * Get GST percentage from request - check PI-level first, then machine-level
     */
    private function getGSTPercentageFromRequest($request)
    {
        // First, check if PI-level GST is provided and > 0
        if ($request->filled('gst_percentage')) {
            $piGst = floatval($request->gst_percentage);
            if ($piGst > 0) {
                return $piGst;
            }
        }
        
        // Otherwise, get from first machine that has GST > 0
        if ($request->has('machines') && is_array($request->machines) && count($request->machines) > 0) {
            foreach ($request->machines as $machineData) {
                if (isset($machineData['gst_percentage']) && 
                    $machineData['gst_percentage'] !== null && 
                    $machineData['gst_percentage'] !== '' && 
                    $machineData['gst_percentage'] !== '0' &&
                    floatval($machineData['gst_percentage']) > 0) {
                    return floatval($machineData['gst_percentage']);
                }
            }
        }
        
        return 0;
    }
}