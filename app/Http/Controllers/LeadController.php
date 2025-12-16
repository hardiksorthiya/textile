<?php

namespace App\Http\Controllers;

use App\Models\Lead;
use App\Models\Business;
use App\Models\State;
use App\Models\City;
use App\Models\Area;
use App\Models\Status;
use App\Models\Brand;
use App\Models\MachineCategory;
use App\Models\BusinessFirm;
use App\Models\Contract;
use App\Models\ContractMachine;
use App\Models\MachineModel;
use App\Models\Setting;
use Illuminate\Http\Request;

class LeadController extends Controller
{
    public function index(Request $request)
    {
        $query = Lead::with(['business', 'state', 'city', 'area', 'status', 'brand', 'machineCategories', 'contract'])
            ->whereDoesntHave('contract', function($q) {
                $q->whereNotNull('approval_status');
            });

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('phone_number', 'like', "%{$search}%")
                  ->orWhereHas('business', function($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%");
                  })
                  ->orWhereHas('brand', function($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%");
                  })
                  ->orWhereHas('state', function($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%");
                  })
                  ->orWhereHas('city', function($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%");
                  })
                  ->orWhereHas('area', function($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%");
                  });
            });
        }

        // Filter by type
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        // Filter by status
        if ($request->filled('status_id')) {
            $query->where('status_id', $request->status_id);
        }

        // Filter by state
        if ($request->filled('state_id')) {
            $query->where('state_id', $request->state_id);
        }

        // Filter by city
        if ($request->filled('city_id')) {
            $query->where('city_id', $request->city_id);
        }

        // Filter by business
        if ($request->filled('business_id')) {
            $query->where('business_id', $request->business_id);
        }

        // Filter by brand
        if ($request->filled('brand_id')) {
            $query->where('brand_id', $request->brand_id);
        }

        $leads = $query->orderBy('created_at', 'desc')->paginate(50)->withQueryString();

        $businesses = Business::orderBy('name')->get();
        $states = State::orderBy('name')->get();
        $statuses = Status::orderBy('name')->get();
        $brands = Brand::orderBy('name')->get();

        return view('leads.index', compact('leads', 'businesses', 'states', 'statuses', 'brands'));
    }

    public function show(Lead $lead)
    {
        $lead->load(['business', 'state', 'city', 'area', 'status', 'brand', 'machineCategories']);
        return view('leads.show', compact('lead'));
    }

    public function create()
    {
        $businesses = Business::orderBy('name')->get();
        $states = State::orderBy('name')->get();
        $statuses = Status::orderBy('name')->get();
        $brands = Brand::orderBy('name')->get();
        $categories = MachineCategory::orderBy('name')->get();
        return view('leads.create', compact('businesses', 'states', 'statuses', 'brands', 'categories'));
    }

    public function store(Request $request)
    {
        $rules = [
            'type' => 'required|in:new,old',
            'name' => 'required|string|max:255',
            'phone_number' => 'required|string|max:20|unique:leads,phone_number',
            'state_id' => 'required|exists:states,id',
            'city_id' => 'required|exists:cities,id',
            'area_id' => 'required|exists:areas,id',
            'quantity' => 'required|integer|min:1',
            'status_id' => 'required|exists:statuses,id',
            'categories' => 'required|array',
            'categories.*' => 'exists:machine_categories,id',
            'needs_scheduling' => 'nullable|boolean',
            'scheduled_date' => 'nullable|date|required_if:needs_scheduling,1',
            'scheduled_time' => 'nullable|date_format:H:i|required_if:needs_scheduling,1',
        ];

        if ($request->type === 'new') {
            $rules['business_id'] = 'required|exists:businesses,id';
        } else {
            $rules['brand_id'] = 'required|exists:brands,id';
            $rules['machine_quantity'] = 'required|integer|min:1';
            $rules['running_since'] = 'required|string|max:255';
        }

        $request->validate($rules);

        $leadData = [
            'type' => $request->type,
            'name' => $request->name,
            'phone_number' => $request->phone_number,
            'state_id' => $request->state_id,
            'city_id' => $request->city_id,
            'area_id' => $request->area_id,
            'quantity' => $request->quantity,
            'status_id' => $request->status_id,
            'needs_scheduling' => $request->has('needs_scheduling') ? (bool)$request->needs_scheduling : false,
            'scheduled_date' => $request->needs_scheduling ? $request->scheduled_date : null,
            'scheduled_time' => $request->needs_scheduling ? $request->scheduled_time : null,
        ];

        if ($request->type === 'new') {
            $leadData['business_id'] = $request->business_id;
        } else {
            $leadData['brand_id'] = $request->brand_id;
            $leadData['machine_quantity'] = $request->machine_quantity;
            $leadData['running_since'] = $request->running_since;
        }

        $lead = Lead::create($leadData);

        // Attach categories
        $lead->machineCategories()->attach($request->categories);

        return redirect()->route('leads.index')
            ->with('success', 'Lead created successfully.');
    }

    public function edit(Lead $lead)
    {
        $businesses = Business::orderBy('name')->get();
        $states = State::orderBy('name')->get();
        $cities = City::where('state_id', $lead->state_id)->orderBy('name')->get();
        $areas = Area::where('city_id', $lead->city_id)->orderBy('name')->get();
        $statuses = Status::orderBy('name')->get();
        $brands = Brand::orderBy('name')->get();
        $categories = MachineCategory::orderBy('name')->get();
        return view('leads.edit', compact('lead', 'businesses', 'states', 'cities', 'areas', 'statuses', 'brands', 'categories'));
    }

    public function update(Request $request, Lead $lead)
    {
        $rules = [
            'type' => 'required|in:new,old',
            'name' => 'required|string|max:255',
            'phone_number' => 'required|string|max:20|unique:leads,phone_number,' . $lead->id,
            'state_id' => 'required|exists:states,id',
            'city_id' => 'required|exists:cities,id',
            'area_id' => 'required|exists:areas,id',
            'quantity' => 'required|integer|min:1',
            'status_id' => 'required|exists:statuses,id',
            'categories' => 'required|array',
            'categories.*' => 'exists:machine_categories,id',
            'needs_scheduling' => 'nullable|boolean',
            'scheduled_date' => 'nullable|date|required_if:needs_scheduling,1',
            'scheduled_time' => 'nullable|date_format:H:i|required_if:needs_scheduling,1',
        ];

        if ($request->type === 'new') {
            $rules['business_id'] = 'required|exists:businesses,id';
        } else {
            $rules['brand_id'] = 'required|exists:brands,id';
            $rules['machine_quantity'] = 'required|integer|min:1';
            $rules['running_since'] = 'required|string|max:255';
        }

        $request->validate($rules);

        $leadData = [
            'type' => $request->type,
            'name' => $request->name,
            'phone_number' => $request->phone_number,
            'state_id' => $request->state_id,
            'city_id' => $request->city_id,
            'area_id' => $request->area_id,
            'quantity' => $request->quantity,
            'status_id' => $request->status_id,
            'needs_scheduling' => $request->has('needs_scheduling') ? (bool)$request->needs_scheduling : false,
            'scheduled_date' => $request->needs_scheduling ? $request->scheduled_date : null,
            'scheduled_time' => $request->needs_scheduling ? $request->scheduled_time : null,
        ];

        if ($request->type === 'new') {
            $leadData['business_id'] = $request->business_id;
            $leadData['brand_id'] = null;
            $leadData['machine_quantity'] = null;
            $leadData['running_since'] = null;
        } else {
            $leadData['business_id'] = null;
            $leadData['brand_id'] = $request->brand_id;
            $leadData['machine_quantity'] = $request->machine_quantity;
            $leadData['running_since'] = $request->running_since;
        }

        $lead->update($leadData);

        // Sync categories
        $lead->machineCategories()->sync($request->categories);

        return redirect()->route('leads.index')
            ->with('success', 'Lead updated successfully.');
    }

    public function destroy(Lead $lead)
    {
        $lead->delete();

        return redirect()->route('leads.index')
            ->with('success', 'Lead deleted successfully.');
    }

    public function getCities($state_id)
    {
        $cities = City::where('state_id', $state_id)->orderBy('name')->get();
        return response()->json($cities);
    }

    public function getAreas($city_id)
    {
        $areas = Area::where('city_id', $city_id)->orderBy('name')->get();
        return response()->json($areas);
    }

    public function convertToContract(Lead $lead)
    {
        // Check if this lead already has a contract
        $existingContract = Contract::where('lead_id', $lead->id)->first();
        if ($existingContract) {
            return redirect()->route('contracts.index')
                ->with('error', 'This lead already has a contract. You can view it in the contracts list.');
        }

        $lead->load(['business', 'state', 'city', 'area', 'status', 'brand', 'machineCategories']);
        $businessFirms = BusinessFirm::orderBy('name')->get();
        $states = State::orderBy('name')->get();
        $cities = City::where('state_id', $lead->state_id)->orderBy('name')->get();
        $areas = Area::where('city_id', $lead->city_id)->orderBy('name')->get();
        $categories = MachineCategory::orderBy('name')->get();
        $brands = Brand::orderBy('name')->get();
        
        // Get global contract details settings
        $settings = Setting::firstOrCreate(['id' => 1]);
        
        // Generate contract number
        $lastContract = Contract::orderBy('id', 'desc')->first();
        $contractNumber = 'CNT-' . str_pad(($lastContract ? $lastContract->id : 0) + 1, 6, '0', STR_PAD_LEFT);
        
        return view('leads.convert-to-contract', compact('lead', 'businessFirms', 'states', 'cities', 'areas', 'categories', 'brands', 'contractNumber', 'settings'));
    }

    public function storeContract(Request $request, Lead $lead)
    {
        // Check if this lead already has a contract
        $existingContract = Contract::where('lead_id', $lead->id)->first();
        if ($existingContract) {
            return redirect()->route('contracts.index')
                ->with('error', 'This lead already has a contract. You cannot create multiple contracts for the same lead.');
        }

        $request->validate([
            'business_firm_id' => 'required|exists:business_firms,id',
            'contract_number' => 'required|string|max:255|unique:contracts,contract_number',
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
            'machines' => 'required|array|min:1',
            'machines.*.machine_category_id' => 'required|exists:machine_categories,id',
            'machines.*.brand_id' => 'nullable|exists:brands,id',
            'machines.*.machine_model_id' => 'nullable|exists:machine_models,id',
            'machines.*.quantity' => 'required|integer|min:1',
            'machines.*.amount' => 'required|numeric|min:0',
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
        ]);

        $contract = Contract::create([
            'lead_id' => $lead->id,
            'business_firm_id' => $request->business_firm_id,
            'contract_number' => $request->contract_number,
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
            'other_buyer_expenses_in_print' => $request->has('other_buyer_expenses_in_print') ? (bool)$request->other_buyer_expenses_in_print : true,
            // Other Details
            'payment_terms' => $request->payment_terms,
            'quote_validity' => $request->quote_validity,
            'loading_terms' => $request->loading_terms,
            'warranty' => $request->warranty,
            'complimentary_spares' => $request->complimentary_spares,
            'other_details_in_print' => $request->has('other_details_in_print') ? (bool)$request->other_details_in_print : true,
            // Difference of Specification
            'cam_jacquard_chain_jacquard' => $request->cam_jacquard_chain_jacquard,
            'hooks_5376_to_6144_jacquard' => $request->hooks_5376_to_6144_jacquard,
            'warp_beam' => $request->warp_beam,
            'reed_space_380_to_420_cm' => $request->reed_space_380_to_420_cm,
            'color_selector_8_to_12' => $request->color_selector_8_to_12,
            'hooks_5376_to_2688_jacquard' => $request->hooks_5376_to_2688_jacquard,
            'extra_feeder' => $request->extra_feeder,
            'difference_specification_in_print' => $request->has('difference_specification_in_print') ? (bool)$request->difference_specification_in_print : true,
        ]);

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
            ];
            
            $machineDetails[] = $machineDetail;
            
            // Also store in contract_machines table for relational queries
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
            ]);
        }

        // Update contract with total amount and machine details
        $contract->update([
            'total_amount' => $totalAmount,
            'machine_details' => $machineDetails
        ]);

        // Redirect to signature page
        return redirect()->route('contracts.signature', $contract)
            ->with('success', 'Contract created successfully. Please sign the contract.');
    }

    public function getMachineModels($brand_id)
    {
        $models = MachineModel::where('brand_id', $brand_id)->orderBy('model_no')->get();
        return response()->json($models);
    }

    public function getCategoryItems($category_id)
    {
        $category = MachineCategory::with([
            'brands', 'feeders.feederBrand', 'machineHooks', 'machineEReads', 'colors',
            'machineNozzles', 'machineDropins', 'machineBeams',
            'machineClothRollers', 'machineSoftwares', 'hsnCodes',
            'wirs', 'machineShafts', 'machineLevers', 'machineChains',
            'machineHealdWires'
        ])->findOrFail($category_id);

        return response()->json([
            'brands' => $category->brands->map(function($brand) {
                return ['id' => $brand->id, 'name' => $brand->name];
            })->values(),
            'feeders' => $category->feeders->map(function($feeder) {
                return [
                    'id' => $feeder->id,
                    'feeder' => $feeder->feeder . ($feeder->feederBrand ? ' (' . $feeder->feederBrand->name . ')' : '')
                ];
            }),
            'machine_hooks' => $category->machineHooks->map(function($hook) {
                return ['id' => $hook->id, 'hook' => $hook->hook];
            }),
            'machine_e_reads' => $category->machineEReads->map(function($eread) {
                return ['id' => $eread->id, 'name' => $eread->name];
            }),
            'colors' => $category->colors->map(function($color) {
                return ['id' => $color->id, 'name' => $color->name];
            }),
            'machine_nozzles' => $category->machineNozzles->map(function($nozzle) {
                return ['id' => $nozzle->id, 'nozzle' => $nozzle->nozzle];
            }),
            'machine_dropins' => $category->machineDropins->map(function($dropin) {
                return ['id' => $dropin->id, 'name' => $dropin->name];
            }),
            'machine_beams' => $category->machineBeams->map(function($beam) {
                return ['id' => $beam->id, 'name' => $beam->name];
            }),
            'machine_cloth_rollers' => $category->machineClothRollers->map(function($roller) {
                return ['id' => $roller->id, 'name' => $roller->name];
            }),
            'machine_softwares' => $category->machineSoftwares->map(function($software) {
                return ['id' => $software->id, 'name' => $software->name];
            }),
            'hsn_codes' => $category->hsnCodes->map(function($hsn) {
                return ['id' => $hsn->id, 'name' => $hsn->name];
            }),
            'wirs' => $category->wirs->map(function($wir) {
                return ['id' => $wir->id, 'name' => $wir->name];
            }),
            'machine_shafts' => $category->machineShafts->map(function($shaft) {
                return ['id' => $shaft->id, 'name' => $shaft->name];
            }),
            'machine_levers' => $category->machineLevers->map(function($lever) {
                return ['id' => $lever->id, 'name' => $lever->name];
            }),
            'machine_chains' => $category->machineChains->map(function($chain) {
                return ['id' => $chain->id, 'name' => $chain->name];
            }),
            'machine_heald_wires' => $category->machineHealdWires->map(function($wire) {
                return ['id' => $wire->id, 'name' => $wire->name];
            }),
        ]);
    }
}
