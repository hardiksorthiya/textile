<?php

namespace App\Http\Controllers;

use App\Models\Contract;
use App\Models\ContractMachine;
use App\Models\User;
use App\Models\MachineCategory;
use App\Models\Brand;
use App\Models\BusinessFirm;
use App\Models\State;
use App\Models\City;
use App\Models\Area;
use App\Models\DeliveryTerm;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf as DomPDF;

class ContractController extends Controller
{
    /**
     * Display a listing of contracts with other contract details.
     */
    public function index()
    {
        $contracts = Contract::with(['lead', 'businessFirm', 'state', 'city', 'area'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);
        
        return view('contracts.index', compact('contracts'));
    }

    /**
     * Show the form for editing other contract details.
     */
    public function edit(Contract $contract)
    {
        $contract->load(['lead', 'businessFirm', 'state', 'city', 'area', 'contractMachines']);
        $categories = MachineCategory::orderBy('name')->get();
        $brands = Brand::orderBy('name')->get();
        $businessFirms = BusinessFirm::orderBy('name')->get();
        $states = State::orderBy('name')->get();
        $cities = City::where('state_id', $contract->state_id)->orderBy('name')->get();
        $areas = Area::where('city_id', $contract->city_id)->orderBy('name')->get();
        $deliveryTerms = DeliveryTerm::orderBy('name')->get();
        
        // Prepare machine data for JavaScript
        $machinesData = $contract->contractMachines->map(function($machine) {
            return [
                'machine_category_id' => (string)$machine->machine_category_id,
                'brand_id' => $machine->brand_id ? (string)$machine->brand_id : '',
                'machine_model_id' => $machine->machine_model_id ? (string)$machine->machine_model_id : '',
                'quantity' => $machine->quantity,
                'amount' => $machine->amount,
                'description' => $machine->description ?? '',
                'feeder_id' => $machine->feeder_id ? (string)$machine->feeder_id : '',
                'machine_hook_id' => $machine->machine_hook_id ? (string)$machine->machine_hook_id : '',
                'machine_e_read_id' => $machine->machine_e_read_id ? (string)$machine->machine_e_read_id : '',
                'color_id' => $machine->color_id ? (string)$machine->color_id : '',
                'machine_nozzle_id' => $machine->machine_nozzle_id ? (string)$machine->machine_nozzle_id : '',
                'machine_dropin_id' => $machine->machine_dropin_id != null ? (string)$machine->machine_dropin_id : '',
                'machine_beam_id' => $machine->machine_beam_id ? (string)$machine->machine_beam_id : '',
                'machine_cloth_roller_id' => $machine->machine_cloth_roller_id ? (string)$machine->machine_cloth_roller_id : '',
                'machine_software_id' => $machine->machine_software_id ? (string)$machine->machine_software_id : '',
                'hsn_code_id' => $machine->hsn_code_id ? (string)$machine->hsn_code_id : '',
                'wir_id' => $machine->wir_id ? (string)$machine->wir_id : '',
                'machine_shaft_id' => $machine->machine_shaft_id ? (string)$machine->machine_shaft_id : '',
                'machine_lever_id' => $machine->machine_lever_id ? (string)$machine->machine_lever_id : '',
                'machine_chain_id' => $machine->machine_chain_id ? (string)$machine->machine_chain_id : '',
                'machine_heald_wire_id' => $machine->machine_heald_wire_id ? (string)$machine->machine_heald_wire_id : '',
                'delivery_term_id' => $machine->delivery_term_id ? (string)$machine->delivery_term_id : '',
                'categoryItems' => null,
                'machineModels' => []
            ];
        })->toArray();
        
        return view('contracts.edit', compact('contract', 'categories', 'brands', 'machinesData', 'businessFirms', 'states', 'cities', 'areas', 'deliveryTerms'));
    }

    /**
     * Update the other contract details.
     */
    public function update(Request $request, Contract $contract)
    {
        $request->validate([
            // Personal Information
            'business_firm_id' => 'required|exists:business_firms,id',
            'buyer_name' => 'required|string|max:255',
            'company_name' => 'nullable|string|max:255',
            'contact_address' => 'nullable|string',
            'state_id' => 'required|exists:states,id',
            'city_id' => 'required|exists:cities,id',
            'area_id' => 'required|exists:areas,id',
            'email' => 'nullable|email|max:255',
            'phone_number' => 'required|string|max:20',
            'phone_number_2' => 'nullable|string|max:20',
            'gst' => 'nullable|string|max:50',
            'pan' => 'nullable|string|max:50',
            // Machine Details
            'machines' => 'nullable|array|min:1',
            'machines.*.machine_category_id' => 'required_with:machines|exists:machine_categories,id',
            'machines.*.brand_id' => 'nullable|exists:brands,id',
            'machines.*.machine_model_id' => 'nullable|exists:machine_models,id',
            'machines.*.quantity' => 'required_with:machines|integer|min:1',
            'machines.*.amount' => 'required_with:machines|numeric|min:0',
            'machines.*.description' => 'nullable|string',
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
            // Other Buyer Expenses Details
            'overseas_freight' => 'nullable|string|max:255',
            'demurrage_detention_cfs_charges' => 'nullable|string|max:255',
            'air_pipe_connection' => 'nullable|string|max:255',
            'custom_duty' => 'nullable|string|max:255',
            'port_expenses_transport' => 'nullable|string|max:255',
            'crane_foundation' => 'nullable|string|max:255',
            'humidification' => 'nullable|string|max:255',
            'damage' => 'nullable|string|max:255',
            'gst_custom_charges' => 'nullable|string|max:255',
            'compressor' => 'nullable|string|max:255',
            'optional_spares' => 'nullable|string|max:255',
            'other_buyer_expenses_in_print' => 'nullable|boolean',
            // Other Details
            'payment_terms' => 'nullable|string|max:255',
            'quote_validity' => 'nullable|string|max:255',
            'loading_terms' => 'nullable|string|max:255',
            'warranty' => 'nullable|string|max:255',
            'complimentary_spares' => 'nullable|string|max:255',
            'other_details_in_print' => 'nullable|boolean',
            // Difference of Specification
            'cam_jacquard_chain_jacquard' => 'nullable|string|max:255',
            'hooks_5376_to_6144_jacquard' => 'nullable|string|max:255',
            'warp_beam' => 'nullable|string|max:255',
            'reed_space_380_to_420_cm' => 'nullable|string|max:255',
            'color_selector_8_to_12' => 'nullable|string|max:255',
            'hooks_5376_to_2688_jacquard' => 'nullable|string|max:255',
            'extra_feeder' => 'nullable|string|max:255',
            'difference_specification_in_print' => 'nullable|boolean',
        ]);

        // Update machine details if provided
        if ($request->has('machines') && is_array($request->machines)) {
            // Delete existing contract machines
            $contract->contractMachines()->delete();
            
            // Calculate total amount and prepare machine details for storage
            $totalAmount = 0;
            $machineDetails = [];
            
            foreach ($request->machines as $machineData) {
                $quantity = (int)($machineData['quantity'] ?? 1);
                $amount = (float)($machineData['amount'] ?? 0);
                $machineTotal = $quantity * $amount;
                $totalAmount += $machineTotal;
                
                // Prepare machine detail for JSON storage
                $machineDetail = [
                    'machine_category_id' => $machineData['machine_category_id'],
                    'brand_id' => $machineData['brand_id'] ?? null,
                    'machine_model_id' => $machineData['machine_model_id'] ?? null,
                    'quantity' => $quantity,
                    'amount' => $amount,
                    'machine_total' => $machineTotal,
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
                ];
                
                $machineDetails[] = $machineDetail;
                
                // Store in contract_machines table
                ContractMachine::create([
                    'contract_id' => $contract->id,
                    'machine_category_id' => $machineData['machine_category_id'],
                    'brand_id' => $machineData['brand_id'] ?? null,
                    'machine_model_id' => $machineData['machine_model_id'] ?? null,
                    'quantity' => $quantity,
                    'amount' => $amount,
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
            
            // Update contract with total amount and machine details
            $contract->update([
                'total_amount' => $totalAmount,
                'machine_details' => $machineDetails
            ]);
        }

        $contract->update([
            // Personal Information
            'business_firm_id' => $request->business_firm_id,
            'buyer_name' => $request->buyer_name,
            'company_name' => $request->company_name,
            'contact_address' => $request->contact_address,
            'state_id' => $request->state_id,
            'city_id' => $request->city_id,
            'area_id' => $request->area_id,
            'email' => $request->email,
            'phone_number' => $request->phone_number,
            'phone_number_2' => $request->phone_number_2,
            'gst' => $request->gst,
            'pan' => $request->pan,
            // Other Buyer Expenses Details
            'overseas_freight' => $request->overseas_freight,
            'demurrage_detention_cfs_charges' => $request->demurrage_detention_cfs_charges,
            'air_pipe_connection' => $request->air_pipe_connection,
            'custom_duty' => $request->custom_duty,
            'port_expenses_transport' => $request->port_expenses_transport,
            'crane_foundation' => $request->crane_foundation,
            'humidification' => $request->humidification,
            'damage' => $request->damage,
            'gst_custom_charges' => $request->gst_custom_charges,
            'compressor' => $request->compressor,
            'optional_spares' => $request->optional_spares,
            'other_buyer_expenses_in_print' => $request->has('other_buyer_expenses_in_print') ? (bool)$request->other_buyer_expenses_in_print : $contract->other_buyer_expenses_in_print,
            // Other Details
            'payment_terms' => $request->payment_terms,
            'quote_validity' => $request->quote_validity,
            'loading_terms' => $request->loading_terms,
            'warranty' => $request->warranty,
            'complimentary_spares' => $request->complimentary_spares,
            'other_details_in_print' => $request->has('other_details_in_print') ? (bool)$request->other_details_in_print : $contract->other_details_in_print,
            // Difference of Specification
            'cam_jacquard_chain_jacquard' => $request->cam_jacquard_chain_jacquard,
            'hooks_5376_to_6144_jacquard' => $request->hooks_5376_to_6144_jacquard,
            'warp_beam' => $request->warp_beam,
            'reed_space_380_to_420_cm' => $request->reed_space_380_to_420_cm,
            'color_selector_8_to_12' => $request->color_selector_8_to_12,
            'hooks_5376_to_2688_jacquard' => $request->hooks_5376_to_2688_jacquard,
            'extra_feeder' => $request->extra_feeder,
            'difference_specification_in_print' => $request->has('difference_specification_in_print') ? (bool)$request->difference_specification_in_print : $contract->difference_specification_in_print,
            // Reset approval status - contract needs to be re-approved after update
            'approval_status' => 'pending',
            'approved_by' => null,
            'approved_at' => null,
            'approval_notes' => null,
        ]);

        return redirect()->route('contracts.index')
            ->with('success', 'Contract details updated successfully. Contract has been sent for approval again.');
    }

    /**
     * Show the signature page for a contract.
     */
    public function signature(Contract $contract)
    {
        $contract->load(['lead', 'businessFirm', 'state', 'city', 'area']);
        return view('contracts.signature', compact('contract'));
    }

    /**
     * Store the customer signature.
     */
    public function storeSignature(Request $request, Contract $contract)
    {
        $request->validate([
            'signature' => 'required|string',
        ]);

        $contract->update([
            'customer_signature' => $request->signature,
            'approval_status' => 'pending',
        ]);

        return redirect()->route('contracts.index')
            ->with('success', 'Contract signed successfully. Waiting for approval.');
    }

    /**
     * Show contracts pending approval.
     */
    public function pendingApproval()
    {
        $this->authorize('view contract approvals');

        $contracts = Contract::with(['lead', 'businessFirm', 'state', 'city', 'area'])
            ->where('approval_status', 'pending')
            ->whereNotNull('customer_signature')
            ->orderBy('created_at', 'desc')
            ->paginate(20);
        
        return view('contracts.pending-approval', compact('contracts'));
    }

    /**
     * Approve a contract.
     */
    public function approve(Request $request, Contract $contract)
    {
        $this->authorize('approve contracts');

        $request->validate([
            'approval_notes' => 'nullable|string|max:1000',
        ]);

        $contract->update([
            'approval_status' => 'approved',
            'approved_by' => Auth::id(),
            'approved_at' => now(),
            'approval_notes' => $request->approval_notes,
        ]);

        return redirect()->route('contracts.pending-approval')
            ->with('success', 'Contract approved successfully.');
    }

    /**
     * Reject a contract.
     */
    public function reject(Request $request, Contract $contract)
    {
        $this->authorize('reject contracts');

        $request->validate([
            'approval_notes' => 'required|string|max:1000',
        ]);

        $contract->update([
            'approval_status' => 'rejected',
            'approved_by' => Auth::id(),
            'approved_at' => now(),
            'approval_notes' => $request->approval_notes,
        ]);

        return redirect()->route('contracts.pending-approval')
            ->with('success', 'Contract rejected.');
    }

    /**
     * Remove the specified contract from storage.
     */
    public function destroy(Contract $contract)
    {
        $contract->delete();

        return redirect()->route('contracts.index')
            ->with('success', 'Contract deleted successfully.');
    }

    /**
     * Download contract as PDF.
     */
    public function downloadPdf(Contract $contract)
    {
        $contract->load(['lead', 'businessFirm', 'state', 'city', 'area', 'contractMachines']);
        
        // Load related data for machine details
        foreach ($contract->contractMachines as $machine) {
            $machine->load([
                'machineCategory',
                'brand',
                'machineModel',
                'feeder.feederBrand',
                'machineHook',
                'machineERead',
                'color',
                'machineNozzle',
                'machineDropin',
                'machineBeam',
                'machineClothRoller',
                'machineSoftware',
                'hsnCode',
                'wir',
                'machineShaft',
                'machineLever',
                'machineChain',
                'machineHealdWire',
                'deliveryTerm'
            ]);
        }
        
        $pdf = DomPDF::loadView('contracts.pdf', compact('contract'));
        
        return $pdf->download('contract-' . $contract->contract_number . '.pdf');
    }
}
