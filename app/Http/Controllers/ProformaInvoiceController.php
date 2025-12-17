<?php

namespace App\Http\Controllers;

use App\Models\ProformaInvoice;
use App\Models\ProformaInvoiceMachine;
use App\Models\Contract;
use App\Models\ContractMachine;
use App\Models\User;
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
            'deliveryTerms'
        ));
    }
    
    /**
     * Store a newly created proforma invoice
     */
    public function store(Request $request)
    {
        $request->validate([
            'contract_id' => 'required|exists:contracts,id',
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
            // Generate proforma invoice number
            $proformaInvoiceNumber = $this->generateProformaInvoiceNumber();
            
            // Calculate total amount and validate quantities
            $totalAmount = 0;
            $contractAmountsInUSD = [];
            
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
                $piPricePlusAmc = $unitAmount + $amcPrice;
                
                // Contract amounts are stored in USD, so we use them directly
                // Total = (PI Price + AMC Price) × Quantity
                $machineTotalUSD = $machineData['quantity'] * $piPricePlusAmc;
                $contractAmountsInUSD[] = $machineTotalUSD;
            }
            
            // Calculate total in base currency (USD from contract)
            $totalAmountUSD = array_sum($contractAmountsInUSD);
            
            // Add overseas freight and port expenses
            $overseasFreight = $request->overseas_freight ?? 0;
            $portExpensesClearing = $request->port_expenses_clearing ?? 0;
            $subtotal = $totalAmountUSD + $overseasFreight + $portExpensesClearing;
            
            // Calculate GST
            $gstPercentage = $request->gst_percentage ?? 18;
            $gstAmount = ($subtotal * $gstPercentage) / 100;
            
            // Final amount with GST
            $finalAmountWithGST = $subtotal + $gstAmount;
            
            // Convert to display currency if needed
            $displayAmount = $finalAmountWithGST;
            if ($request->type_of_sale === 'local' && $request->usd_rate) {
                // Convert USD to INR for local sales
                $displayAmount = $finalAmountWithGST * $request->usd_rate;
            }
            
            // Add commission for high seas
            if ($request->type_of_sale === 'high_seas' && $request->commission) {
                $commissionAmount = ($displayAmount * $request->commission) / 100;
                $displayAmount = $displayAmount + $commissionAmount;
            }
            
            // Create proforma invoice
            $proformaInvoice = ProformaInvoice::create([
                'contract_id' => $contract->id,
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
                'gst_percentage' => $request->gst_percentage ?? 18,
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
            
            return redirect()->route('proforma-invoices.show', $proformaInvoice)
                ->with('success', 'Proforma invoice created successfully.');
                
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Failed to create proforma invoice: ' . $e->getMessage()])->withInput();
        }
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
            'proformaInvoiceMachines.contractMachine.machineCategory',
            'proformaInvoiceMachines.contractMachine.brand',
            'proformaInvoiceMachines.contractMachine.machineModel',
            'creator'
        ]);
        
        return view('proforma-invoices.show', compact('proformaInvoice'));
    }
    
    /**
     * Get contract details for proforma invoice form
     */
    public function getContractDetails(Contract $contract)
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
            'machinesByCategory' => $machinesByCategory->map(function ($machines, $categoryId) {
                $firstMachine = $machines->first();
                return [
                    'category_id' => (string)$categoryId, // Ensure string type for consistent comparison
                    'category_name' => $firstMachine->machineCategory->name ?? 'N/A',
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
     * Generate unique proforma invoice number
     */
    private function generateProformaInvoiceNumber()
    {
        $prefix = 'PI-' . date('Ymd');
        $lastInvoice = ProformaInvoice::where('proforma_invoice_number', 'like', $prefix . '%')
            ->orderBy('proforma_invoice_number', 'desc')
            ->first();
        
        if ($lastInvoice) {
            $lastNumber = (int) substr($lastInvoice->proforma_invoice_number, -3);
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }
        
        return $prefix . '-' . str_pad($newNumber, 3, '0', STR_PAD_LEFT);
    }
}