<x-app-layout>
    <div class="mb-4 d-flex justify-content-between align-items-center">
        <div>
            <h1 class="h2 fw-bold mb-1" style="color: #1f2937;">Convert Lead to Contract</h1>
            <p class="text-muted mb-0">Convert lead #{{ $lead->id }} to a contract</p>
        </div>
        <a href="{{ route('leads.index') }}" class="btn btn-outline-secondary" style="border-radius: 8px;">
            <i class="fas fa-arrow-left me-2"></i>Back to Leads
        </a>
    </div>

    <div class="row g-4" x-data="contractForm()">
        <div class="col-12">
            <div class="card shadow-sm border-0" style="background: linear-gradient(to bottom, #ffffff 0%, #faf5ff 100%); border-radius: 12px;">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center mb-4 pb-3 border-bottom" style="border-color: rgba(139, 92, 246, 0.2) !important;">
                        <div class="rounded-circle bg-primary d-flex align-items-center justify-content-center me-3" style="width: 48px; height: 48px; background: linear-gradient(45deg, #8b5cf6, #a78bfa) !important;">
                            <i class="fas fa-file-contract text-white"></i>
                        </div>
                        <h2 class="h5 fw-bold mb-0" style="color: #1f2937;">Contract Information</h2>
                    </div>

                    <form action="{{ route('leads.store-contract', $lead) }}" method="POST" id="contractForm">
                        @csrf

                        <!-- Basic Information Section -->
                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold" style="color: #374151;">Business Firm <span class="text-danger">*</span></label>
                                <select name="business_firm_id" required class="form-select @error('business_firm_id') is-invalid @enderror" style="border-radius: 8px; border: 1px solid #e5e7eb;">
                                    <option value="">Select Business Firm</option>
                                    @foreach($businessFirms as $firm)
                                        <option value="{{ $firm->id }}" {{ old('business_firm_id') == $firm->id ? 'selected' : '' }}>{{ $firm->name }}</option>
                                    @endforeach
                                </select>
                                @error('business_firm_id')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold" style="color: #374151;">Contract Number <span class="text-danger">*</span></label>
                                <input type="text" name="contract_number" required value="{{ old('contract_number', $contractNumber) }}" 
                                       class="form-control @error('contract_number') is-invalid @enderror" 
                                       placeholder="Contract Number" style="border-radius: 8px; border: 1px solid #e5e7eb;" readonly>
                                @error('contract_number')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">Auto-generated serial number</small>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold" style="color: #374151;">Buyer Name <span class="text-danger">*</span></label>
                                <input type="text" name="buyer_name" required value="{{ old('buyer_name', $lead->name) }}" 
                                       class="form-control @error('buyer_name') is-invalid @enderror" 
                                       placeholder="Enter buyer name" style="border-radius: 8px; border: 1px solid #e5e7eb;">
                                @error('buyer_name')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold" style="color: #374151;">Company Name</label>
                                <input type="text" name="company_name" value="{{ old('company_name') }}" 
                                       class="form-control @error('company_name') is-invalid @enderror" 
                                       placeholder="Enter company name" style="border-radius: 8px; border: 1px solid #e5e7eb;">
                                @error('company_name')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-12">
                                <label class="form-label fw-semibold" style="color: #374151;">Contact Address</label>
                                <textarea name="contact_address" rows="3"
                                          class="form-control @error('contact_address') is-invalid @enderror"
                                          placeholder="Enter contact address" style="border-radius: 8px; border: 1px solid #e5e7eb;">{{ old('contact_address') }}</textarea>
                                @error('contact_address')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-semibold" style="color: #374151;">State <span class="text-danger">*</span></label>
                                <select name="state_id" required id="state_id" class="form-select @error('state_id') is-invalid @enderror" style="border-radius: 8px; border: 1px solid #e5e7eb;" @change="loadCities($event.target.value)">
                                    <option value="">Select State</option>
                                    @foreach($states as $state)
                                        <option value="{{ $state->id }}" {{ old('state_id', $lead->state_id) == $state->id ? 'selected' : '' }}>{{ $state->name }}</option>
                                    @endforeach
                                </select>
                                @error('state_id')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-semibold" style="color: #374151;">City <span class="text-danger">*</span></label>
                                <select name="city_id" required id="city_id" class="form-select @error('city_id') is-invalid @enderror" style="border-radius: 8px; border: 1px solid #e5e7eb;" @change="loadAreas($event.target.value)">
                                    <option value="">Select City</option>
                                    @foreach($cities as $city)
                                        <option value="{{ $city->id }}" {{ old('city_id', $lead->city_id) == $city->id ? 'selected' : '' }}>{{ $city->name }}</option>
                                    @endforeach
                                </select>
                                @error('city_id')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-semibold" style="color: #374151;">Area <span class="text-danger">*</span></label>
                                <select name="area_id" required id="area_id" class="form-select @error('area_id') is-invalid @enderror" style="border-radius: 8px; border: 1px solid #e5e7eb;">
                                    <option value="">Select Area</option>
                                    @foreach($areas as $area)
                                        <option value="{{ $area->id }}" {{ old('area_id', $lead->area_id) == $area->id ? 'selected' : '' }}>{{ $area->name }}</option>
                                    @endforeach
                                </select>
                                @error('area_id')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold" style="color: #374151;">Email</label>
                                <input type="email" name="email" value="{{ old('email') }}" 
                                       class="form-control @error('email') is-invalid @enderror" 
                                       placeholder="Enter email" style="border-radius: 8px; border: 1px solid #e5e7eb;">
                                @error('email')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold" style="color: #374151;">Phone Number <span class="text-danger">*</span></label>
                                <input type="text" name="phone_number" required value="{{ old('phone_number', $lead->phone_number) }}" 
                                       class="form-control @error('phone_number') is-invalid @enderror" 
                                       placeholder="Enter phone number" style="border-radius: 8px; border: 1px solid #e5e7eb;">
                                @error('phone_number')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold" style="color: #374151;">Phone Number 2</label>
                                <input type="text" name="phone_number_2" value="{{ old('phone_number_2') }}" 
                                       class="form-control @error('phone_number_2') is-invalid @enderror" 
                                       placeholder="Enter alternate phone number" style="border-radius: 8px; border: 1px solid #e5e7eb;">
                                @error('phone_number_2')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-3">
                                <label class="form-label fw-semibold" style="color: #374151;">GST</label>
                                <input type="text" name="gst" value="{{ old('gst') }}" 
                                       class="form-control @error('gst') is-invalid @enderror" 
                                       placeholder="Enter GST" style="border-radius: 8px; border: 1px solid #e5e7eb;">
                                @error('gst')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-3">
                                <label class="form-label fw-semibold" style="color: #374151;">PAN</label>
                                <input type="text" name="pan" value="{{ old('pan') }}" 
                                       class="form-control @error('pan') is-invalid @enderror" 
                                       placeholder="Enter PAN" style="border-radius: 8px; border: 1px solid #e5e7eb;">
                                @error('pan')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Machine Details Section -->
                        <div class="border-top pt-4 mt-4">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h5 class="fw-bold mb-0" style="color: #1f2937;">Machine Details</h5>
                                <button type="button" @click="addMachine()" class="btn btn-sm btn-primary" style="border-radius: 8px;">
                                    <i class="fas fa-plus me-2"></i>Add Machine
                                </button>
                            </div>

                            <div id="machines-container">
                                <template x-for="(machine, index) in machines" :key="index">
                                    <div class="card mb-3" style="border-radius: 8px; border: 1px solid #e5e7eb;">
                                        <div class="card-body">
                                            <div class="d-flex justify-content-between align-items-center mb-3">
                                                <h6 class="mb-0 fw-semibold" style="color: #374151;">Machine <span x-text="index + 1"></span></h6>
                                                <button type="button" @click="removeMachine(index)" class="btn btn-sm btn-outline-danger" style="border-radius: 6px;">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>
                                            <div class="row g-3">
                                                <div class="col-md-4">
                                                    <label class="form-label fw-semibold" style="color: #374151;">Machine Category <span class="text-danger">*</span></label>
                                                    <select :name="`machines[${index}][machine_category_id]`" required x-model="machine.machine_category_id" @change="loadCategoryItems(index, $event.target.value)" class="form-select" style="border-radius: 8px; border: 1px solid #e5e7eb;">
                                                        <option value="">Select Category</option>
                                                        @foreach($categories as $category)
                                                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <!-- Brand - only show when category is selected -->
                                                <template x-if="machine.categoryItems">
                                                    <div class="col-md-4">
                                                        <label class="form-label fw-semibold" style="color: #374151;">Brand</label>
                                                        <select :name="`machines[${index}][brand_id]`" x-model="machine.brand_id" @change="loadMachineModels(index, $event.target.value)" :id="`brand_${index}`" class="form-select" style="border-radius: 8px; border: 1px solid #e5e7eb;">
                                                            <option value="">Select Brand</option>
                                                            <template x-for="brand in (machine.categoryItems?.brands || [])" :key="brand.id">
                                                                <option :value="String(brand.id)" x-text="brand.name"></option>
                                                            </template>
                                                        </select>
                                                    </div>
                                                </template>
                                                <!-- Model - only show when brand is selected -->
                                                <template x-if="machine.brand_id">
                                                    <div class="col-md-4">
                                                        <label class="form-label fw-semibold" style="color: #374151;">Model</label>
                                                        <select :name="`machines[${index}][machine_model_id]`" x-model="machine.machine_model_id" :id="`machine_model_${index}`" class="form-select" style="border-radius: 8px; border: 1px solid #e5e7eb;">
                                                            <option value="">Select Model</option>
                                                            <template x-for="model in (machine.machineModels || [])" :key="model.id">
                                                                <option :value="String(model.id)" x-text="model.model_no"></option>
                                                            </template>
                                                        </select>
                                                    </div>
                                                </template>
                                                
                                                <!-- Category-related items (shown dynamically based on category) -->
                                                <template x-if="machine.categoryItems && machine.categoryItems.feeders && machine.categoryItems.feeders.length > 0">
                                                                    <div class="col-md-4">
                                                                        <label class="form-label fw-semibold" style="color: #374151;">Feeder</label>
                                                                        <select :name="`machines[${index}][feeder_id]`" x-model="machine.feeder_id" :id="`feeder_${index}`" class="form-select" style="border-radius: 8px; border: 1px solid #e5e7eb;">
                                                                            <template x-for="(feeder, feederIndex) in machine.categoryItems.feeders" :key="feeder.id">
                                                                                <option :value="feeder.id" :selected="feederIndex === 0" x-text="feeder.feeder"></option>
                                                                            </template>
                                                                        </select>
                                                                    </div>
                                                                </template>
                                                                
                                                <!-- Machine Hook -->
                                                <template x-if="machine.categoryItems && machine.categoryItems.machine_hooks && machine.categoryItems.machine_hooks.length > 0">
                                                                    <div class="col-md-4">
                                                                        <label class="form-label fw-semibold" style="color: #374151;">Machine Hook</label>
                                                                        <select :name="`machines[${index}][machine_hook_id]`" x-model="machine.machine_hook_id" :id="`machine_hook_${index}`" class="form-select" style="border-radius: 8px; border: 1px solid #e5e7eb;">
                                                                            <template x-for="hook in machine.categoryItems.machine_hooks" :key="hook.id">
                                                                                <option :value="hook.id" x-text="hook.hook"></option>
                                                                            </template>
                                                                        </select>
                                                                    </div>
                                                                </template>
                                                                
                                                <!-- Machine E-Read -->
                                                <template x-if="machine.categoryItems && machine.categoryItems.machine_e_reads && machine.categoryItems.machine_e_reads.length > 0">
                                                                    <div class="col-md-4">
                                                                        <label class="form-label fw-semibold" style="color: #374151;">Machine E-Read</label>
                                                                        <select :name="`machines[${index}][machine_e_read_id]`" x-model="machine.machine_e_read_id" :id="`machine_e_read_${index}`" class="form-select" style="border-radius: 8px; border: 1px solid #e5e7eb;">
                                                                            <template x-for="eread in machine.categoryItems.machine_e_reads" :key="eread.id">
                                                                                <option :value="eread.id" x-text="eread.name"></option>
                                                                            </template>
                                                                        </select>
                                                                    </div>
                                                                </template>
                                                                
                                                <!-- Color -->
                                                <template x-if="machine.categoryItems && machine.categoryItems.colors && machine.categoryItems.colors.length > 0">
                                                                    <div class="col-md-4">
                                                                        <label class="form-label fw-semibold" style="color: #374151;">Color</label>
                                                                        <select :name="`machines[${index}][color_id]`" x-model="machine.color_id" :id="`color_${index}`" class="form-select" style="border-radius: 8px; border: 1px solid #e5e7eb;">
                                                                            <template x-for="color in machine.categoryItems.colors" :key="color.id">
                                                                                <option :value="color.id" x-text="color.name"></option>
                                                                            </template>
                                                                        </select>
                                                                    </div>
                                                                </template>
                                                                
                                                <!-- Machine Nozzle -->
                                                <template x-if="machine.categoryItems && machine.categoryItems.machine_nozzles && machine.categoryItems.machine_nozzles.length > 0">
                                                                    <div class="col-md-4">
                                                                        <label class="form-label fw-semibold" style="color: #374151;">Machine Nozzle</label>
                                                                        <select :name="`machines[${index}][machine_nozzle_id]`" x-model="machine.machine_nozzle_id" :id="`machine_nozzle_${index}`" class="form-select" style="border-radius: 8px; border: 1px solid #e5e7eb;">
                                                                            <template x-for="nozzle in machine.categoryItems.machine_nozzles" :key="nozzle.id">
                                                                                <option :value="nozzle.id" x-text="nozzle.nozzle"></option>
                                                                            </template>
                                                                        </select>
                                                                    </div>
                                                                </template>
                                                                
                                                <!-- Machine Dropin -->
                                                <template x-if="machine.categoryItems && machine.categoryItems.machine_dropins && machine.categoryItems.machine_dropins.length > 0">
                                                                    <div class="col-md-4">
                                                                        <label class="form-label fw-semibold" style="color: #374151;">Machine Dropin</label>
                                                                        <select :name="`machines[${index}][machine_dropin_id]`" x-model="machine.machine_dropin_id" :id="`machine_dropin_${index}`" class="form-select" style="border-radius: 8px; border: 1px solid #e5e7eb;">
                                                                            <template x-for="dropin in machine.categoryItems.machine_dropins" :key="dropin.id">
                                                                                <option :value="dropin.id" x-text="dropin.name"></option>
                                                                            </template>
                                                                        </select>
                                                                    </div>
                                                                </template>
                                                                
                                                <!-- Machine Beam -->
                                                <template x-if="machine.categoryItems && machine.categoryItems.machine_beams && machine.categoryItems.machine_beams.length > 0">
                                                                    <div class="col-md-4">
                                                                        <label class="form-label fw-semibold" style="color: #374151;">Machine Beam</label>
                                                                        <select :name="`machines[${index}][machine_beam_id]`" x-model="machine.machine_beam_id" :id="`machine_beam_${index}`" class="form-select" style="border-radius: 8px; border: 1px solid #e5e7eb;">
                                                                            <template x-for="beam in machine.categoryItems.machine_beams" :key="beam.id">
                                                                                <option :value="beam.id" x-text="beam.name"></option>
                                                                            </template>
                                                                        </select>
                                                                    </div>
                                                                </template>
                                                                
                                                <!-- Machine Cloth Roller -->
                                                <template x-if="machine.categoryItems && machine.categoryItems.machine_cloth_rollers && machine.categoryItems.machine_cloth_rollers.length > 0">
                                                                    <div class="col-md-4">
                                                                        <label class="form-label fw-semibold" style="color: #374151;">Machine Cloth Roller</label>
                                                                        <select :name="`machines[${index}][machine_cloth_roller_id]`" x-model="machine.machine_cloth_roller_id" :id="`machine_cloth_roller_${index}`" class="form-select" style="border-radius: 8px; border: 1px solid #e5e7eb;">
                                                                            <template x-for="roller in machine.categoryItems.machine_cloth_rollers" :key="roller.id">
                                                                                <option :value="roller.id" x-text="roller.name"></option>
                                                                            </template>
                                                                        </select>
                                                                    </div>
                                                                </template>
                                                                
                                                <!-- Machine Software -->
                                                <template x-if="machine.categoryItems && machine.categoryItems.machine_softwares && machine.categoryItems.machine_softwares.length > 0">
                                                                    <div class="col-md-4">
                                                                        <label class="form-label fw-semibold" style="color: #374151;">Machine Software</label>
                                                                        <select :name="`machines[${index}][machine_software_id]`" x-model="machine.machine_software_id" :id="`machine_software_${index}`" class="form-select" style="border-radius: 8px; border: 1px solid #e5e7eb;">
                                                                            <template x-for="software in machine.categoryItems.machine_softwares" :key="software.id">
                                                                                <option :value="software.id" x-text="software.name"></option>
                                                                            </template>
                                                                        </select>
                                                                    </div>
                                                                </template>
                                                                
                                                <!-- HSN Code -->
                                                <template x-if="machine.categoryItems && machine.categoryItems.hsn_codes && machine.categoryItems.hsn_codes.length > 0">
                                                                    <div class="col-md-4">
                                                                        <label class="form-label fw-semibold" style="color: #374151;">HSN Code</label>
                                                                        <select :name="`machines[${index}][hsn_code_id]`" x-model="machine.hsn_code_id" :id="`hsn_code_${index}`" class="form-select" style="border-radius: 8px; border: 1px solid #e5e7eb;">
                                                                            <template x-for="hsn in machine.categoryItems.hsn_codes" :key="hsn.id">
                                                                                <option :value="hsn.id" x-text="hsn.name"></option>
                                                                            </template>
                                                                        </select>
                                                                    </div>
                                                                </template>
                                                                
                                                <!-- WIR -->
                                                <template x-if="machine.categoryItems && machine.categoryItems.wirs && machine.categoryItems.wirs.length > 0">
                                                                    <div class="col-md-4">
                                                                        <label class="form-label fw-semibold" style="color: #374151;">WIR</label>
                                                                        <select :name="`machines[${index}][wir_id]`" x-model="machine.wir_id" :id="`wir_${index}`" class="form-select" style="border-radius: 8px; border: 1px solid #e5e7eb;">
                                                                            <template x-for="wir in machine.categoryItems.wirs" :key="wir.id">
                                                                                <option :value="wir.id" x-text="wir.name"></option>
                                                                            </template>
                                                                        </select>
                                                                    </div>
                                                                </template>
                                                                
                                                <!-- Machine Shaft -->
                                                <template x-if="machine.categoryItems && machine.categoryItems.machine_shafts && machine.categoryItems.machine_shafts.length > 0">
                                                                    <div class="col-md-4">
                                                                        <label class="form-label fw-semibold" style="color: #374151;">Machine Shaft</label>
                                                                        <select :name="`machines[${index}][machine_shaft_id]`" x-model="machine.machine_shaft_id" :id="`machine_shaft_${index}`" class="form-select" style="border-radius: 8px; border: 1px solid #e5e7eb;">
                                                                            <template x-for="shaft in machine.categoryItems.machine_shafts" :key="shaft.id">
                                                                                <option :value="shaft.id" x-text="shaft.name"></option>
                                                                            </template>
                                                                        </select>
                                                                    </div>
                                                                </template>
                                                                
                                                <!-- Machine Lever -->
                                                <template x-if="machine.categoryItems && machine.categoryItems.machine_levers && machine.categoryItems.machine_levers.length > 0">
                                                                    <div class="col-md-4">
                                                                        <label class="form-label fw-semibold" style="color: #374151;">Machine Lever</label>
                                                                        <select :name="`machines[${index}][machine_lever_id]`" x-model="machine.machine_lever_id" :id="`machine_lever_${index}`" class="form-select" style="border-radius: 8px; border: 1px solid #e5e7eb;">
                                                                            <template x-for="lever in machine.categoryItems.machine_levers" :key="lever.id">
                                                                                <option :value="lever.id" x-text="lever.name"></option>
                                                                            </template>
                                                                        </select>
                                                                    </div>
                                                                </template>
                                                                
                                                <!-- Machine Chain -->
                                                <template x-if="machine.categoryItems && machine.categoryItems.machine_chains && machine.categoryItems.machine_chains.length > 0">
                                                                    <div class="col-md-4">
                                                                        <label class="form-label fw-semibold" style="color: #374151;">Machine Chain</label>
                                                                        <select :name="`machines[${index}][machine_chain_id]`" x-model="machine.machine_chain_id" :id="`machine_chain_${index}`" class="form-select" style="border-radius: 8px; border: 1px solid #e5e7eb;">
                                                                            <template x-for="chain in machine.categoryItems.machine_chains" :key="chain.id">
                                                                                <option :value="chain.id" x-text="chain.name"></option>
                                                                            </template>
                                                                        </select>
                                                                    </div>
                                                                </template>
                                                                
                                                <!-- Machine Heald Wire -->
                                                <template x-if="machine.categoryItems && machine.categoryItems.machine_heald_wires && machine.categoryItems.machine_heald_wires.length > 0">
                                                                    <div class="col-md-4">
                                                                        <label class="form-label fw-semibold" style="color: #374151;">Machine Heald Wire</label>
                                                                        <select :name="`machines[${index}][machine_heald_wire_id]`" x-model="machine.machine_heald_wire_id" :id="`machine_heald_wire_${index}`" class="form-select" style="border-radius: 8px; border: 1px solid #e5e7eb;">
                                                                            <template x-for="wire in machine.categoryItems.machine_heald_wires" :key="wire.id">
                                                                                <option :value="wire.id" x-text="wire.name"></option>
                                                                            </template>
                                                                        </select>
                                                                    </div>
                                                </template>
                                                
                                                <!-- Quantity, Amount and Description at the end - only show when category is selected -->
                                                <template x-if="machine.categoryItems">
                                                    <div class="col-md-3">
                                                        <label class="form-label fw-semibold" style="color: #374151;">Quantity <span class="text-danger">*</span></label>
                                                        <input type="number" :name="`machines[${index}][quantity]`" required x-model="machine.quantity" min="1" class="form-control" placeholder="Quantity" style="border-radius: 8px; border: 1px solid #e5e7eb;">
                                                    </div>
                                                </template>
                                                <template x-if="machine.categoryItems">
                                                    <div class="col-md-3">
                                                        <label class="form-label fw-semibold" style="color: #374151;">Amount ($) <span class="text-danger">*</span></label>
                                                        <input type="number" :name="`machines[${index}][amount]`" required x-model="machine.amount" step="0.01" min="0" class="form-control" placeholder="0.00" style="border-radius: 8px; border: 1px solid #e5e7eb;">
                                                    </div>
                                                </template>
                                                <template x-if="machine.categoryItems">
                                                    <div class="col-md-3">
                                                        <label class="form-label fw-semibold" style="color: #374151;">Machine Amount</label>
                                                        <div class="form-control bg-light" style="border-radius: 8px; border: 1px solid #e5e7eb; padding: 0.375rem 0.75rem; display: flex; align-items: center; min-height: 38px;">
                                                            <span class="fw-bold" style="color: #8b5cf6;" x-text="'$' + ((parseFloat(machine.quantity) || 0) * (parseFloat(machine.amount) || 0)).toFixed(2)"></span>
                                                        </div>
                                                    </div>
                                                </template>
                                                <template x-if="machine.categoryItems">
                                                    <div class="col-md-3">
                                                        <label class="form-label fw-semibold" style="color: #374151;">Description</label>
                                                        <input type="text" :name="`machines[${index}][description]`" x-model="machine.description" class="form-control" placeholder="Machine description (optional)" style="border-radius: 8px; border: 1px solid #e5e7eb;">
                                                    </div>
                                                </template>
                                            </div>
                                        </div>
                                    </div>
                                </template>
                            </div>
                            
                            <!-- Total Machine Amount Summary -->
                            <div class="mt-4 pt-3 border-top">
                                <div class="row">
                                    <div class="col-md-6 offset-md-6">
                                        <div class="card bg-light" style="border-radius: 8px; border: 1px solid #e5e7eb;">
                                            <div class="card-body">
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <span class="fw-bold" style="color: #374151; font-size: 1.1rem;">Total Machine Amount:</span>
                                                    <span class="fw-bold" style="color: #8b5cf6; font-size: 1.2rem;" x-text="'$' + getTotalMachineAmount().toFixed(2)"></span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row mt-4">
                            <div class="col-12">
                                <button type="submit" class="btn btn-primary w-100 py-2 fw-semibold" style="border-radius: 8px; background: linear-gradient(45deg, #8b5cf6, #a78bfa) !important; border: none;">
                                    <i class="fas fa-save me-2"></i>Create Contract
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        function contractForm() {
            return {
                machines: [{
                    machine_category_id: '',
                    brand_id: '',
                    machine_model_id: '',
                    machineModels: [],
                    quantity: 1,
                    description: '',
                    categoryItems: null,
                    amount: 0,
                    feeder_id: '',
                    machine_hook_id: '',
                    machine_e_read_id: '',
                    color_id: '',
                    machine_nozzle_id: '',
                    machine_dropin_id: '',
                    machine_beam_id: '',
                    machine_cloth_roller_id: '',
                    machine_software_id: '',
                    hsn_code_id: '',
                    wir_id: '',
                    machine_shaft_id: '',
                    machine_lever_id: '',
                    machine_chain_id: '',
                    machine_heald_wire_id: ''
                }],

                getTotalMachineAmount() {
                    return this.machines.reduce((total, machine) => {
                        const quantity = parseFloat(machine.quantity) || 0;
                        const amount = parseFloat(machine.amount) || 0;
                        const machineTotal = quantity * amount;
                        return total + machineTotal;
                    }, 0);
                },

                addMachine() {
                    this.machines.push({
                        machine_category_id: '',
                        brand_id: '',
                        machine_model_id: '',
                        machineModels: [],
                        quantity: 1,
                        description: '',
                        categoryItems: null,
                        amount: 0,
                        feeder_id: '',
                        machine_hook_id: '',
                        machine_e_read_id: '',
                        color_id: '',
                        machine_nozzle_id: '',
                        machine_dropin_id: '',
                        machine_beam_id: '',
                        machine_cloth_roller_id: '',
                        machine_software_id: '',
                        hsn_code_id: '',
                        wir_id: '',
                        machine_shaft_id: '',
                        machine_lever_id: '',
                        machine_chain_id: '',
                        machine_heald_wire_id: ''
                    });
                },


                removeMachine(index) {
                    if (this.machines.length > 1) {
                        this.machines.splice(index, 1);
                    } else {
                        alert('At least one machine is required.');
                    }
                },

                async loadCities(stateId) {
                    if (!stateId) {
                        document.getElementById('city_id').innerHTML = '<option value="">Select City</option>';
                        document.getElementById('area_id').innerHTML = '<option value="">Select Area</option>';
                        return;
                    }
                    try {
                        const response = await fetch(`{{ url('leads/cities') }}/${stateId}`);
                        const cities = await response.json();
                        const citySelect = document.getElementById('city_id');
                        citySelect.innerHTML = '<option value="">Select City</option>';
                        cities.forEach(city => {
                            citySelect.innerHTML += `<option value="${city.id}">${city.name}</option>`;
                        });
                        document.getElementById('area_id').innerHTML = '<option value="">Select Area</option>';
                    } catch (error) {
                        console.error('Error loading cities:', error);
                    }
                },

                async loadAreas(cityId) {
                    if (!cityId) {
                        document.getElementById('area_id').innerHTML = '<option value="">Select Area</option>';
                        return;
                    }
                    try {
                        const response = await fetch(`{{ url('leads/areas') }}/${cityId}`);
                        const areas = await response.json();
                        const areaSelect = document.getElementById('area_id');
                        areaSelect.innerHTML = '<option value="">Select Area</option>';
                        areas.forEach(area => {
                            areaSelect.innerHTML += `<option value="${area.id}">${area.name}</option>`;
                        });
                    } catch (error) {
                        console.error('Error loading areas:', error);
                    }
                },

                async loadMachineModels(index, brandId) {
                    if (!brandId) {
                        this.machines[index].machine_model_id = '';
                        this.machines[index].machineModels = [];
                        return;
                    }
                    try {
                        const response = await fetch(`{{ url('leads/machine-models') }}/${brandId}`);
                        const models = await response.json();
                        this.machines[index].machineModels = models;
                        
                        // Auto-select first model if available - ensure it's a string to match option values
                        if (models && models.length > 0) {
                            this.machines[index].machine_model_id = String(models[0].id);
                        } else {
                            this.machines[index].machine_model_id = '';
                        }
                    } catch (error) {
                        console.error('Error loading models:', error);
                        this.machines[index].machineModels = [];
                        this.machines[index].machine_model_id = '';
                    }
                },

                async loadCategoryItems(index, categoryId) {
                    if (!categoryId) {
                        this.machines[index].categoryItems = null;
                        this.machines[index].brand_id = '';
                        this.machines[index].machine_model_id = '';
                        return;
                    }
                    try {
                        const response = await fetch(`{{ url('leads/category-items') }}/${categoryId}`);
                        const items = await response.json();
                        console.log('Category items loaded:', items); // Debug log
                        this.machines[index].categoryItems = items;
                        
                        // Reset brand and model when category changes
                        this.machines[index].brand_id = '';
                        this.machines[index].machine_model_id = '';
                        this.machines[index].machineModels = [];
                        
                        // Auto-select first brand if available - use $nextTick to ensure dropdown is rendered
                        if (items.brands && items.brands.length > 0) {
                            await this.$nextTick();
                            // Ensure brand_id is set as string to match option values
                            this.machines[index].brand_id = String(items.brands[0].id);
                            // Load models for the first brand (which will auto-select first model)
                            await this.loadMachineModels(index, items.brands[0].id);
                        }
                        
                        // Auto-select first value in category-related dropdowns
                        this.$nextTick(() => {
                            // Auto-select first feeder
                            if (items.feeders && items.feeders.length > 0) {
                                this.machines[index].feeder_id = items.feeders[0].id;
                            }
                            // Auto-select first hook
                            if (items.machine_hooks && items.machine_hooks.length > 0) {
                                this.machines[index].machine_hook_id = items.machine_hooks[0].id;
                            }
                            // Auto-select first e-read
                            if (items.machine_e_reads && items.machine_e_reads.length > 0) {
                                this.machines[index].machine_e_read_id = items.machine_e_reads[0].id;
                            }
                            // Auto-select first color
                            if (items.colors && items.colors.length > 0) {
                                this.machines[index].color_id = items.colors[0].id;
                            }
                            // Auto-select first nozzle
                            if (items.machine_nozzles && items.machine_nozzles.length > 0) {
                                this.machines[index].machine_nozzle_id = items.machine_nozzles[0].id;
                            }
                            // Auto-select first dropin
                            if (items.machine_dropins && items.machine_dropins.length > 0) {
                                this.machines[index].machine_dropin_id = items.machine_dropins[0].id;
                            }
                            // Auto-select first beam
                            if (items.machine_beams && items.machine_beams.length > 0) {
                                this.machines[index].machine_beam_id = items.machine_beams[0].id;
                            }
                            // Auto-select first cloth roller
                            if (items.machine_cloth_rollers && items.machine_cloth_rollers.length > 0) {
                                this.machines[index].machine_cloth_roller_id = items.machine_cloth_rollers[0].id;
                            }
                            // Auto-select first software
                            if (items.machine_softwares && items.machine_softwares.length > 0) {
                                this.machines[index].machine_software_id = items.machine_softwares[0].id;
                            }
                            // Auto-select first HSN code
                            if (items.hsn_codes && items.hsn_codes.length > 0) {
                                this.machines[index].hsn_code_id = items.hsn_codes[0].id;
                            }
                            // Auto-select first WIR
                            if (items.wirs && items.wirs.length > 0) {
                                this.machines[index].wir_id = items.wirs[0].id;
                            }
                            // Auto-select first shaft
                            if (items.machine_shafts && items.machine_shafts.length > 0) {
                                this.machines[index].machine_shaft_id = items.machine_shafts[0].id;
                            }
                            // Auto-select first lever
                            if (items.machine_levers && items.machine_levers.length > 0) {
                                this.machines[index].machine_lever_id = items.machine_levers[0].id;
                            }
                            // Auto-select first chain
                            if (items.machine_chains && items.machine_chains.length > 0) {
                                this.machines[index].machine_chain_id = items.machine_chains[0].id;
                            }
                            // Auto-select first heald wire
                            if (items.machine_heald_wires && items.machine_heald_wires.length > 0) {
                                this.machines[index].machine_heald_wire_id = items.machine_heald_wires[0].id;
                            }
                        });
                    } catch (error) {
                        console.error('Error loading category items:', error);
                    }
                }
            }
        }

        // Initialize cities and areas if state/city is pre-selected
        document.addEventListener('DOMContentLoaded', function() {
            const stateSelect = document.getElementById('state_id');
            if (stateSelect && stateSelect.value) {
                const event = new Event('change');
                stateSelect.dispatchEvent(event);
            }
        });
    </script>

    @if(session('success'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)"
             class="position-fixed bottom-0 end-0 m-4 rounded shadow-lg" 
             style="z-index: 1050; background: linear-gradient(135deg, #10b981 0%, #059669 100%); color: white; padding: 1rem 1.5rem; border-radius: 10px;">
            <div class="d-flex align-items-center">
                <i class="fas fa-check-circle me-2"></i>
                <span>{{ session('success') }}</span>
            </div>
        </div>
    @endif
</x-app-layout>
