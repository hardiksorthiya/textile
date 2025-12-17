<x-app-layout>
    <div class="mb-4 d-flex justify-content-between align-items-center">
        <div>
            <h1 class="h2 fw-bold mb-1" style="color: #1f2937;">Create Proforma Invoice</h1>
            <p class="text-muted mb-0">Create a proforma invoice from an approved contract</p>
        </div>
        <a href="{{ route('contracts.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-2"></i>Back to Contracts
        </a>
    </div>

    <div x-data="proformaInvoiceForm({{ $selectedContractId ?? 'null' }})" x-init="init()">
        <!-- Search Section -->
        <div class="card shadow-sm border-0 mb-4" style="background: linear-gradient(to bottom, #ffffff 0%, color-mix(in srgb, var(--primary-color) 6%, #ffffff) 100%); border-radius: 12px;">
            <div class="card-header border-0 pb-0" style="background: transparent;">
                <div class="d-flex align-items-center py-3 border-bottom" style="border-color: color-mix(in srgb, var(--primary-color) 20%, transparent) !important;">
                    <div class="rounded-circle bg-primary d-flex align-items-center justify-content-center me-3" style="width: 48px; height: 48px; background: linear-gradient(45deg, var(--primary-color), var(--primary-light)) !important;">
                        <i class="fas fa-search text-white"></i>
                    </div>
                    <h2 class="h5 fw-bold mb-0" style="color: #1f2937;">Search Contract</h2>
                </div>
            </div>
            <div class="card-body p-4">
                <form method="GET" action="{{ route('proforma-invoices.create') }}" id="searchForm">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label fw-semibold" style="color: #374151;">Sales Manager</label>
                            <select name="sales_manager_id" class="form-select" style="border-radius: 8px; border: 1px solid #e5e7eb;" onchange="document.getElementById('searchForm').submit();">
                                <option value="">All Sales Managers</option>
                                @foreach($salesManagers as $manager)
                                    <option value="{{ $manager->id }}" {{ request('sales_manager_id') == $manager->id ? 'selected' : '' }}>{{ $manager->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold" style="color: #374151;">Contract Number</label>
                            <input type="text" name="contract_number" value="{{ request('contract_number') }}" 
                                   class="form-control" placeholder="Enter contract number" 
                                   style="border-radius: 8px; border: 1px solid #e5e7eb;">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold" style="color: #374151;">Customer Name</label>
                            <input type="text" name="customer_name" value="{{ request('customer_name') }}" 
                                   class="form-control" placeholder="Enter customer name" 
                                   style="border-radius: 8px; border: 1px solid #e5e7eb;">
                        </div>
                        <div class="col-12">
                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-search me-2"></i>Search
                                </button>
                                <a href="{{ route('proforma-invoices.create') }}" class="btn btn-outline-secondary">
                                    <i class="fas fa-redo me-2"></i>Reset
                                </a>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Contracts List -->
        @if(request()->hasAny(['sales_manager_id', 'contract_number', 'customer_name']) || $contracts->count() > 0)
        <div class="card shadow-sm border-0 mb-4" style="background: linear-gradient(to bottom, #ffffff 0%, color-mix(in srgb, var(--primary-color) 6%, #ffffff) 100%); border-radius: 12px;">
            <div class="card-header border-0 pb-0" style="background: transparent;">
                <div class="d-flex align-items-center py-3 border-bottom" style="border-color: color-mix(in srgb, var(--primary-color) 20%, transparent) !important;">
                    <div class="rounded-circle bg-primary d-flex align-items-center justify-content-center me-3" style="width: 48px; height: 48px; background: linear-gradient(45deg, var(--primary-color), var(--primary-light)) !important;">
                        <i class="fas fa-file-contract text-white"></i>
                    </div>
                    <h2 class="h5 fw-bold mb-0" style="color: #1f2937;">Select Contract</h2>
                </div>
            </div>
            <div class="card-body p-4">
                @forelse($contracts as $contract)
                    <div class="card mb-3" style="cursor: pointer; border: 2px solid #e5e7eb; transition: all 0.2s;" 
                         :class="{ 'border-primary': selectedContractId === {{ $contract->id }} }"
                         @click="selectContract({{ $contract->id }})"
                         onmouseover="this.style.borderColor='var(--primary-color)'" 
                         onmouseout="this.style.borderColor='#e5e7eb'">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h5 class="fw-bold mb-1" style="color: #1f2937;">{{ $contract->contract_number }}</h5>
                                    <p class="mb-1" style="color: #6b7280;">
                                        <strong>Customer:</strong> {{ $contract->buyer_name }}
                                        @if($contract->company_name)
                                            ({{ $contract->company_name }})
                                        @endif
                                    </p>
                                    <p class="mb-0 small text-muted">
                                        <strong>Sales Manager:</strong> {{ $contract->creator->name ?? 'N/A' }} | 
                                        <strong>Amount:</strong> ${{ number_format($contract->total_amount ?? 0, 2) }}
                                    </p>
                                </div>
                                <div>
                                    <i class="fas fa-chevron-right text-primary"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="text-center text-muted py-4">
                        <i class="fas fa-file-contract fa-3x mb-3" style="opacity: 0.3;"></i>
                        <p>No contracts found. Please adjust your search criteria.</p>
                    </div>
                @endforelse

                @if($contracts->hasPages())
                    <div class="mt-3">
                        {{ $contracts->links() }}
                    </div>
                @endif
            </div>
        </div>
        @endif

        <!-- Proforma Invoice Form (shown when contract is selected) -->
        <div x-show="selectedContractId" x-cloak>
            <div class="card shadow-sm border-0" style="background: linear-gradient(to bottom, #ffffff 0%, color-mix(in srgb, var(--primary-color) 6%, #ffffff) 100%); border-radius: 12px;">
                <div class="card-header border-0 pb-0" style="background: transparent;">
                    <div class="d-flex align-items-center py-3 border-bottom" style="border-color: color-mix(in srgb, var(--primary-color) 20%, transparent) !important;">
                        <div class="rounded-circle bg-primary d-flex align-items-center justify-content-center me-3" style="width: 48px; height: 48px; background: linear-gradient(45deg, var(--primary-color), var(--primary-light)) !important;">
                            <i class="fas fa-file-invoice text-white"></i>
                        </div>
                        <h2 class="h5 fw-bold mb-0" style="color: #1f2937;">Proforma Invoice Details</h2>
                    </div>
                </div>
                <div class="card-body p-4">
                    <form action="{{ route('proforma-invoices.store') }}" method="POST" id="proformaInvoiceForm">
                        @csrf
                        <input type="hidden" name="contract_id" x-model="selectedContractId">

                        <!-- Contract Info Display -->
                        <div x-show="contractData" class="alert alert-info mb-4">
                            <div class="row">
                                <div class="col-md-6">
                                    <strong>Contract:</strong> <span x-text="contractData?.contract?.contract_number"></span><br>
                                    <strong>Customer:</strong> <span x-text="contractData?.contract?.buyer_name"></span>
                                </div>
                                <div class="col-md-6">
                                    <strong>Sales Manager:</strong> <span x-text="contractData?.contract?.creator?.name"></span><br>
                                    <strong>Total Contract Amount:</strong> $<span x-text="contractData?.contract?.total_amount ? parseFloat(contractData.contract.total_amount).toFixed(2) : '0.00'"></span>
                                </div>
                            </div>
                        </div>

                        <!-- Buyer Details -->
                        <div class="mb-4">
                            <h5 class="fw-bold mb-3" style="color: #1f2937;">Buyer Details</h5>
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold" style="color: #374151;">Buyer Company Name <span class="text-danger">*</span></label>
                                    <input type="text" name="buyer_company_name" required 
                                           x-model="buyerCompanyName"
                                           class="form-control" placeholder="Enter Buyer Company Name" 
                                           style="border-radius: 8px; border: 1px solid #e5e7eb;">
                                    @error('buyer_company_name')
                                        <div class="text-danger small mt-1">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold" style="color: #374151;">PAN No</label>
                                    <input type="text" name="pan" 
                                           x-model="pan"
                                           class="form-control" placeholder="Enter PAN No" 
                                           style="border-radius: 8px; border: 1px solid #e5e7eb;">
                                    @error('pan')
                                        <div class="text-danger small mt-1">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold" style="color: #374151;">GST No</label>
                                    <input type="text" name="gst" 
                                           x-model="gst"
                                           class="form-control" placeholder="Enter GST No" 
                                           style="border-radius: 8px; border: 1px solid #e5e7eb;">
                                    @error('gst')
                                        <div class="text-danger small mt-1">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold" style="color: #374151;">Phone Number <span class="text-danger">*</span></label>
                                    <input type="text" name="phone_number" required 
                                           x-model="phoneNumber"
                                           class="form-control" placeholder="Enter Phone Number" 
                                           style="border-radius: 8px; border: 1px solid #e5e7eb;">
                                    @error('phone_number')
                                        <div class="text-danger small mt-1">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold" style="color: #374151;">Phone Number 2</label>
                                    <input type="text" name="phone_number_2" 
                                           x-model="phoneNumber2"
                                           class="form-control" placeholder="Enter Phone Number 2" 
                                           style="border-radius: 8px; border: 1px solid #e5e7eb;">
                                    @error('phone_number_2')
                                        <div class="text-danger small mt-1">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold" style="color: #374151;">IFC Certificate Number</label>
                                    <input type="text" name="ifc_certificate_number" 
                                           x-model="ifcCertificateNumber"
                                           class="form-control" placeholder="Enter IFC Certificate Number" 
                                           style="border-radius: 8px; border: 1px solid #e5e7eb;">
                                    @error('ifc_certificate_number')
                                        <div class="text-danger small mt-1">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Billing and Shipping Address -->
                        <div class="mb-4">
                            <h5 class="fw-bold mb-3" style="color: #1f2937;">Address Information</h5>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold" style="color: #374151;">Billing Address</label>
                                    <textarea name="billing_address" rows="3" 
                                              x-model="billingAddress"
                                              @input="if(copyBillingToShipping) { shippingAddress = billingAddress; }"
                                              class="form-control" placeholder="Enter Billing Address" 
                                              style="border-radius: 8px; border: 1px solid #e5e7eb;"></textarea>
                                    @error('billing_address')
                                        <div class="text-danger small mt-1">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <label class="form-label fw-semibold mb-0" style="color: #374151;">Shipping Address</label>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="copyBillingAddress" 
                                                   x-model="copyBillingToShipping"
                                                   @change="copyBillingAddressToShipping()"
                                                   style="cursor: pointer;">
                                            <label class="form-check-label" for="copyBillingAddress" style="cursor: pointer; color: #6b7280; font-size: 0.875rem;">
                                                <i class="fas fa-copy me-1"></i>Copy from Billing
                                            </label>
                                        </div>
                                    </div>
                                    <textarea name="shipping_address" rows="3" 
                                              x-model="shippingAddress"
                                              class="form-control" placeholder="Enter Shipping Address" 
                                              style="border-radius: 8px; border: 1px solid #e5e7eb;"></textarea>
                                    @error('shipping_address')
                                        <div class="text-danger small mt-1">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Type of Sale and Related Fields -->
                        <div class="mb-4">
                            <h5 class="fw-bold mb-3" style="color: #1f2937;">Sale Information</h5>
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold" style="color: #374151;">Type Of Sale <span class="text-danger">*</span></label>
                                    <select name="type_of_sale" required x-model="typeOfSale" @change="updateCurrencyAndFields()" 
                                            class="form-select" style="border-radius: 8px; border: 1px solid #e5e7eb;">
                                        <option value="import" selected>Import</option>
                                        <option value="local">Local</option>
                                        <option value="high_seas">High Seas</option>
                                    </select>
                                    @error('type_of_sale')
                                        <div class="text-danger small mt-1">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold" style="color: #374151;">Currency <span class="text-danger">*</span></label>
                                    <input type="text" name="currency" required x-model="currency" readonly
                                           class="form-control" style="border-radius: 8px; border: 1px solid #e5e7eb;">
                                    @error('currency')
                                        <div class="text-danger small mt-1">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="col-md-4" x-show="typeOfSale === 'local' || typeOfSale === 'high_seas'">
                                    <label class="form-label fw-semibold" style="color: #374151;">Today Rate of USD <span class="text-danger" x-show="typeOfSale === 'local' || typeOfSale === 'high_seas'">*</span></label>
                                    <input type="number" name="usd_rate" step="0.01" min="0" 
                                           x-model="usdRate" @input="calculateTotal()"
                                           :required="typeOfSale === 'local' || typeOfSale === 'high_seas'"
                                           class="form-control" placeholder="Enter USD Rate" 
                                           style="border-radius: 8px; border: 1px solid #e5e7eb;">
                                    @error('usd_rate')
                                        <div class="text-danger small mt-1">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="col-md-4" x-show="typeOfSale === 'high_seas'" x-cloak>
                                    <label class="form-label fw-semibold" style="color: #374151;">High Seas Commission (%) <span class="text-danger">*</span></label>
                                    <input type="number" name="commission" step="0.01" min="0" max="100"
                                           x-model="commission" @input="calculateTotal()"
                                           :required="typeOfSale === 'high_seas'"
                                           class="form-control" placeholder="Enter Commission %" 
                                           style="border-radius: 8px; border: 1px solid #e5e7eb;">
                                    @error('commission')
                                        <div class="text-danger small mt-1">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Machine Details Section -->
                        <div class="mb-4" x-show="contractData && contractData.machinesByCategory && contractData.machinesByCategory.length > 0">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h5 class="fw-bold mb-0" style="color: #1f2937;">Machine Details</h5>
                                <button type="button" @click="addMachine()" class="btn btn-sm btn-primary">
                                    <i class="fas fa-plus me-2"></i>Add Machine
                                </button>
                            </div>

                            <!-- Added Machines List -->
                            <div x-show="addedMachines.length > 0">
                                <template x-for="(machine, index) in addedMachines" :key="machine.tempId">
                                    <div class="card shadow-sm border-0 mb-3" style="border-radius: 8px; border: 1px solid #e5e7eb;">
                                        <div class="card-body p-4">
                                            <div class="d-flex justify-content-between align-items-center mb-3">
                                                <h6 class="mb-0 fw-semibold" style="color: #374151;">
                                                    Machine <span x-text="index + 1"></span>
                                                    <span x-show="machine.machine_category_id" class="text-muted ms-2">
                                                        (<span x-text="getCategoryName(machine.machine_category_id)"></span>)
                                                    </span>
                                                </h6>
                                                <button type="button" @click="removeMachine(machine.tempId)" class="btn btn-sm btn-outline-danger">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>

                                            <div class="row g-3">
                                                <!-- Machine Category Selection -->
                                                <div class="col-md-4">
                                                    <label class="form-label fw-semibold" style="color: #374151;">Machine Category <span class="text-danger">*</span></label>
                                                    <select 
                                                        :name="`machines[${index}][machine_category_id]`"
                                                        x-model="machine.machine_category_id"
                                                        @change="loadMachinesForCategory(machine.tempId, $event.target.value)"
                                                        required
                                                        class="form-select" style="border-radius: 8px; border: 1px solid #e5e7eb;">
                                                        <option value="">Select Category</option>
                                                        <template x-for="category in contractData?.machinesByCategory || []" :key="category.category_id">
                                                            <option :value="category.category_id" x-text="category.category_name"></option>
                                                        </template>
                                                    </select>
                                                </div>

                                                <!-- Hidden field for contract machine ID (auto-selected) -->
                                                <input type="hidden" 
                                                       :name="`machines[${index}][contract_machine_id]`"
                                                       x-model="machine.contract_machine_id">

                                                <!-- Quantity -->
                                                <div class="col-md-4" x-show="machine.contract_machine_id">
                                                    <label class="form-label fw-semibold" style="color: #374151;">Quantity <span class="text-danger">*</span></label>
                                                    <input type="number" 
                                                           :name="`machines[${index}][quantity]`"
                                                           x-model="machine.quantity"
                                                           @input="updateMachineQuantity(machine.tempId, machine.amount, $event.target.value)"
                                                           :max="machine.maxQuantity"
                                                           min="1"
                                                           required
                                                           class="form-control" 
                                                           style="border-radius: 8px; border: 1px solid #e5e7eb;">
                                                    <small class="text-muted" x-show="machine.maxQuantity">
                                                        Contract Quantity: <span x-text="machine.maxQuantity"></span>
                                                    </small>
                                                </div>

                                                <!-- Contract Machine Amount (read-only) -->
                                                <div class="col-md-3" x-show="machine.contract_machine_id && machine.contractAmount > 0">
                                                    <label class="form-label fw-semibold" style="color: #374151;">Contract Machine Amount</label>
                                                    <div class="form-control bg-light" style="border-radius: 8px; border: 1px solid #e5e7eb;">
                                                        $<span x-text="(machine.contractAmount * machine.maxQuantity).toFixed(2)"></span>
                                                    </div>
                                                    <small class="text-muted">Total: $<span x-text="machine.contractAmount.toFixed(2)"></span> × <span x-text="machine.maxQuantity"></span> Machines</small>
                                                </div>

                                                <!-- Amount per unit -->
                                                <div class="col-md-3" x-show="machine.contract_machine_id">
                                                    <label class="form-label fw-semibold" style="color: #374151;">PI Price <span class="text-danger">*</span></label>
                                                    <input type="number" 
                                                           :name="`machines[${index}][amount]`"
                                                           x-model="machine.amount"
                                                           @input="updateMachineAmount(machine.tempId, $event.target.value); calculateTotal()"
                                                           step="0.01"
                                                           min="0"
                                                           class="form-control" 
                                                           style="border-radius: 8px; border: 1px solid #e5e7eb;">
                                                    <small class="text-muted">Can be modified</small>
                                                </div>

                                                <!-- AMC Price -->
                                                <div class="col-md-3" x-show="machine.contract_machine_id">
                                                    <label class="form-label fw-semibold" style="color: #374151;">AMC Price</label>
                                                    <input type="number" 
                                                           :name="`machines[${index}][amc_price]`"
                                                           x-model="machine.amc_price"
                                                           @input="calculateMachineTotals(machine.tempId); calculateTotal()"
                                                           step="0.01"
                                                           min="0"
                                                           class="form-control" 
                                                           style="border-radius: 8px; border: 1px solid #e5e7eb;">
                                                </div>

                                                <!-- PI Price + AMC Price (calculated) -->
                                                <div class="col-md-3" x-show="machine.contract_machine_id">
                                                    <label class="form-label fw-semibold" style="color: #374151;">PI Price + AMC Price</label>
                                                    <div class="form-control bg-light" style="border-radius: 8px; border: 1px solid #e5e7eb;">
                                                        <span x-text="currencySymbol"></span><span x-text="((machine.amount || 0) + (machine.amc_price || 0)).toFixed(2)"></span>
                                                    </div>
                                                </div>

                                                <!-- Total PI Price (PI Price + AMC) × Quantity -->
                                                <div class="col-md-3" x-show="machine.contract_machine_id && machine.quantity > 0">
                                                    <label class="form-label fw-semibold" style="color: #374151;">Total PI Price</label>
                                                    <div class="form-control bg-light" style="border-radius: 8px; border: 1px solid #e5e7eb;">
                                                        <span x-text="currencySymbol"></span><span x-text="(((machine.amount || 0) + (machine.amc_price || 0)) * (machine.quantity || 0)).toFixed(2)"></span>
                                                    </div>
                                                </div>

                                                <!-- Total for this machine -->
                                                <div class="col-md-3" x-show="machine.contract_machine_id && machine.quantity > 0">
                                                    <label class="form-label fw-semibold" style="color: #374151;">Final Total Price</label>
                                                    <div class="form-control bg-light" style="border-radius: 8px; border: 1px solid #e5e7eb;">
                                                        <span x-text="currencySymbol"></span><span x-text="getMachineDisplayAmount(machine.tempId, (machine.amount || 0) + (machine.amc_price || 0)).toFixed(2)"></span>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Machine Specifications (shown when machine is selected and category items are loaded) -->
                                            <div class="row g-3 mt-3" x-show="machine.contract_machine_id && machine.categoryItems">
                                                <div class="col-12">
                                                    <hr>
                                                    <h6 class="fw-semibold mb-3" style="color: #374151;">Machine Specifications</h6>
                                                </div>

                                                <!-- Brand -->
                                                <template x-if="machine.categoryItems && machine.categoryItems.brands && machine.categoryItems.brands.length > 0">
                                                    <div class="col-md-4">
                                                        <label class="form-label fw-semibold" style="color: #374151;">Brand</label>
                                                        <select 
                                                            :name="`machines[${index}][brand_id]`"
                                                            x-model="machine.brand_id"
                                                            @change="async () => { await loadMachineModelsForPI(machine.tempId, machine.brand_id); machine.machine_model_id = ''; }"
                                                            class="form-select" style="border-radius: 8px; border: 1px solid #e5e7eb;">
                                                            <option value="">Select Brand</option>
                                                            <template x-for="brand in machine.categoryItems.brands" :key="brand.id">
                                                                <option :value="String(brand.id)" x-text="brand.name"></option>
                                                            </template>
                                                        </select>
                                                    </div>
                                                </template>

                                                <!-- Model -->
                                                <template x-if="machine.brand_id && machine.machineModels && machine.machineModels.length > 0">
                                                    <div class="col-md-4">
                                                        <label class="form-label fw-semibold" style="color: #374151;">Model</label>
                                                        <select 
                                                            :name="`machines[${index}][machine_model_id]`"
                                                            x-model="machine.machine_model_id"
                                                            class="form-select" style="border-radius: 8px; border: 1px solid #e5e7eb;">
                                                            <option value="">Select Model</option>
                                                            <template x-for="model in machine.machineModels" :key="model.id">
                                                                <option :value="String(model.id)" x-text="model.model_no"></option>
                                                            </template>
                                                        </select>
                                                        
                                                    </div>
                                                </template>

                                                <!-- Feeder -->
                                                <template x-if="machine.categoryItems && machine.categoryItems.feeders && machine.categoryItems.feeders.length > 0">
                                                    <div class="col-md-4">
                                                        <label class="form-label fw-semibold" style="color: #374151;">Feeder</label>
                                                        <select 
                                                            :name="`machines[${index}][feeder_id]`"
                                                            x-model="machine.feeder_id"
                                                            class="form-select" style="border-radius: 8px; border: 1px solid #e5e7eb;">
                                                            <option value="">Select Feeder</option>
                                                            <template x-for="feeder in machine.categoryItems.feeders" :key="feeder.id">
                                                                <option :value="feeder.id" x-text="feeder.feeder"></option>
                                                            </template>
                                                        </select>
                                                    </div>
                                                </template>

                                                <!-- Machine Hook -->
                                                <template x-if="machine.categoryItems && machine.categoryItems.machine_hooks && machine.categoryItems.machine_hooks.length > 0">
                                                    <div class="col-md-4">
                                                        <label class="form-label fw-semibold" style="color: #374151;">Machine Hook</label>
                                                        <select 
                                                            :name="`machines[${index}][machine_hook_id]`"
                                                            x-model="machine.machine_hook_id"
                                                            class="form-select" style="border-radius: 8px; border: 1px solid #e5e7eb;">
                                                            <option value="">Select Hook</option>
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
                                                        <select 
                                                            :name="`machines[${index}][machine_e_read_id]`"
                                                            x-model="machine.machine_e_read_id"
                                                            class="form-select" style="border-radius: 8px; border: 1px solid #e5e7eb;">
                                                            <option value="">Select E-Read</option>
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
                                                        <select 
                                                            :name="`machines[${index}][color_id]`"
                                                            x-model="machine.color_id"
                                                            class="form-select" style="border-radius: 8px; border: 1px solid #e5e7eb;">
                                                            <option value="">Select Color</option>
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
                                                        <select 
                                                            :name="`machines[${index}][machine_nozzle_id]`"
                                                            x-model="machine.machine_nozzle_id"
                                                            class="form-select" style="border-radius: 8px; border: 1px solid #e5e7eb;">
                                                            <option value="">Select Nozzle</option>
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
                                                        <select 
                                                            :name="`machines[${index}][machine_dropin_id]`"
                                                            x-model="machine.machine_dropin_id"
                                                            class="form-select" style="border-radius: 8px; border: 1px solid #e5e7eb;">
                                                            <option value="">Select Dropin</option>
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
                                                        <select 
                                                            :name="`machines[${index}][machine_beam_id]`"
                                                            x-model="machine.machine_beam_id"
                                                            class="form-select" style="border-radius: 8px; border: 1px solid #e5e7eb;">
                                                            <option value="">Select Beam</option>
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
                                                        <select 
                                                            :name="`machines[${index}][machine_cloth_roller_id]`"
                                                            x-model="machine.machine_cloth_roller_id"
                                                            class="form-select" style="border-radius: 8px; border: 1px solid #e5e7eb;">
                                                            <option value="">Select Cloth Roller</option>
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
                                                        <select 
                                                            :name="`machines[${index}][machine_software_id]`"
                                                            x-model="machine.machine_software_id"
                                                            class="form-select" style="border-radius: 8px; border: 1px solid #e5e7eb;">
                                                            <option value="">Select Software</option>
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
                                                        <select 
                                                            :name="`machines[${index}][hsn_code_id]`"
                                                            x-model="machine.hsn_code_id"
                                                            class="form-select" style="border-radius: 8px; border: 1px solid #e5e7eb;">
                                                            <option value="">Select HSN Code</option>
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
                                                        <select 
                                                            :name="`machines[${index}][wir_id]`"
                                                            x-model="machine.wir_id"
                                                            class="form-select" style="border-radius: 8px; border: 1px solid #e5e7eb;">
                                                            <option value="">Select WIR</option>
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
                                                        <select 
                                                            :name="`machines[${index}][machine_shaft_id]`"
                                                            x-model="machine.machine_shaft_id"
                                                            class="form-select" style="border-radius: 8px; border: 1px solid #e5e7eb;">
                                                            <option value="">Select Shaft</option>
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
                                                        <select 
                                                            :name="`machines[${index}][machine_lever_id]`"
                                                            x-model="machine.machine_lever_id"
                                                            class="form-select" style="border-radius: 8px; border: 1px solid #e5e7eb;">
                                                            <option value="">Select Lever</option>
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
                                                        <select 
                                                            :name="`machines[${index}][machine_chain_id]`"
                                                            x-model="machine.machine_chain_id"
                                                            class="form-select" style="border-radius: 8px; border: 1px solid #e5e7eb;">
                                                            <option value="">Select Chain</option>
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
                                                        <select 
                                                            :name="`machines[${index}][machine_heald_wire_id]`"
                                                            x-model="machine.machine_heald_wire_id"
                                                            class="form-select" style="border-radius: 8px; border: 1px solid #e5e7eb;">
                                                            <option value="">Select Heald Wire</option>
                                                            <template x-for="wire in machine.categoryItems.machine_heald_wires" :key="wire.id">
                                                                <option :value="wire.id" x-text="wire.name"></option>
                                                            </template>
                                                        </select>
                                                    </div>
                                                </template>

                                                <!-- Delivery Term -->
                                                <template x-if="machine.categoryItems && machine.categoryItems.delivery_terms && machine.categoryItems.delivery_terms.length > 0">
                                                    <div class="col-md-4">
                                                        <label class="form-label fw-semibold" style="color: #374151;">Delivery Term</label>
                                                        <select 
                                                            :name="`machines[${index}][delivery_term_id]`"
                                                            x-model="machine.delivery_term_id"
                                                            class="form-select" style="border-radius: 8px; border: 1px solid #e5e7eb;">
                                                            <option value="">Select Delivery Term</option>
                                                            <template x-for="term in machine.categoryItems.delivery_terms" :key="term.id">
                                                                <option :value="term.id" x-text="term.name"></option>
                                                            </template>
                                                        </select>
                                                    </div>
                                                </template>

                                                <!-- Description -->
                                                <div class="col-md-12">
                                                    <label class="form-label fw-semibold" style="color: #374151;">Description</label>
                                                    <textarea 
                                                        :name="`machines[${index}][description]`"
                                                        x-model="machine.description"
                                                        rows="2"
                                                        class="form-control" 
                                                        placeholder="Additional description"
                                                        style="border-radius: 8px; border: 1px solid #e5e7eb;"></textarea>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </div>

                        <!-- Additional Expenses and Calculations -->
                        <div class="mb-4" x-show="addedMachines.length > 0">
                            <h5 class="fw-bold mb-3" style="color: #1f2937;">Additional Expenses & Calculations</h5>
                            <div class="row g-3">
                                <!-- Overseas Freight -->
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold" style="color: #374151;">Overseas Freight Approx.</label>
                                    <div class="input-group">
                                        <span class="input-group-text">$</span>
                                        <input type="number" 
                                               name="overseas_freight"
                                               x-model="overseasFreight"
                                               @input="calculateFinalAmounts()"
                                               step="0.01"
                                               min="0"
                                               class="form-control" 
                                               placeholder="Enter Overseas Freight (At Actual)"
                                               style="border-radius: 8px; border: 1px solid #e5e7eb;">
                                    </div>
                                </div>

                                <!-- Port Expenses + Clearing Expenses -->
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold" style="color: #374151;">Port Exp + Clearing Exp Approx.</label>
                                    <div class="input-group">
                                        <span class="input-group-text">$</span>
                                        <input type="number" 
                                               name="port_expenses_clearing"
                                               x-model="portExpensesClearing"
                                               @input="calculateFinalAmounts()"
                                               step="0.01"
                                               min="0"
                                               class="form-control" 
                                               placeholder="Enter Port Exp + Clearing Exp"
                                               style="border-radius: 8px; border: 1px solid #e5e7eb;">
                                    </div>
                                </div>

                                <!-- GST Percentage -->
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold" style="color: #374151;">GST Per</label>
                                    <div class="input-group">
                                        <input type="number" 
                                               name="gst_percentage"
                                               x-model="gstPercentage"
                                               @input="calculateFinalAmounts()"
                                               step="0.01"
                                               min="0"
                                               max="100"
                                               class="form-control" 
                                               style="border-radius: 8px; border: 1px solid #e5e7eb;">
                                        <span class="input-group-text">%</span>
                                    </div>
                                </div>

                                <!-- GST Amount (calculated) -->
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold" style="color: #374151;">GST Amount</label>
                                    <div class="form-control bg-light" style="border-radius: 8px; border: 1px solid #e5e7eb;">
                                        <span x-text="currencySymbol"></span><span x-text="gstAmount.toFixed(2)"></span>
                                    </div>
                                </div>

                                <!-- Final Amount + GST Amount -->
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold" style="color: #374151;">Final Amount + GST Amount</label>
                                    <div class="form-control bg-light" style="border-radius: 8px; border: 1px solid #e5e7eb;">
                                        <span x-text="currencySymbol"></span><span x-text="finalAmountWithGST.toFixed(2)"></span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Notes -->
                        <div class="mb-4">
                            <label class="form-label fw-semibold" style="color: #374151;">Notes</label>
                            <textarea name="notes" rows="3" class="form-control" 
                                      placeholder="Additional notes for proforma invoice" 
                                      style="border-radius: 8px; border: 1px solid #e5e7eb;"></textarea>
                        </div>

                        <!-- Total Amount Display -->
                        <div class="alert alert-primary d-flex justify-content-between align-items-center">
                            <span class="fw-bold">Total Proforma Invoice Amount:</span>
                            <span class="fw-bold fs-4"><span x-text="currencySymbol"></span><span x-text="displayTotalAmount.toFixed(2)"></span></span>
                        </div>

                        <!-- Submit Button -->
                        <div class="d-flex gap-2 justify-content-end">
                            <button type="button" @click="cancelSelection()" class="btn btn-outline-secondary">
                                <i class="fas fa-times me-2"></i>Cancel
                            </button>
                            <button type="submit" class="btn btn-primary" :disabled="totalAmount <= 0 || !typeOfSale">
                                <i class="fas fa-save me-2"></i>Create Proforma Invoice
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        function proformaInvoiceForm(initialContractId = null) {
            return {
                selectedContractId: initialContractId,
                contractData: null,
                selectedMachines: {},
                selectedCategories: [],
                addedMachines: [],
                tempMachineIdCounter: 0,
                totalAmount: 0,
                typeOfSale: 'import',
                currency: 'USD',
                currencySymbol: '$',
                usdRate: null,
                commission: null,
                displayTotalAmount: 0,
                buyerCompanyName: '',
                pan: '',
                gst: '',
                phoneNumber: '',
                phoneNumber2: '',
                ifcCertificateNumber: '',
                billingAddress: '',
                shippingAddress: '',
                copyBillingToShipping: false,
                amcPrice: 0,
                overseasFreight: 0,
                portExpensesClearing: 0,
                gstPercentage: 18,
                gstAmount: 0,
                finalAmountWithGST: 0,

                async init() {
                    // Initialize default values
                    this.updateCurrencyAndFields();
                    this.calculateFinalAmounts(); // Initialize calculations
                    
                    if (this.selectedContractId) {
                        await this.selectContract(this.selectedContractId);
                    }
                },

                updateCurrencyAndFields() {
                    if (this.typeOfSale === 'import') {
                        this.currency = 'USD';
                        this.currencySymbol = '$';
                        this.usdRate = null;
                        this.commission = null;
                    } else if (this.typeOfSale === 'local') {
                        this.currency = 'INR';
                        this.currencySymbol = '₹';
                        this.commission = null;
                    } else if (this.typeOfSale === 'high_seas') {
                        this.currency = 'USD';
                        this.currencySymbol = '$';
                    }
                    this.calculateTotal();
                },

                async selectContract(contractId) {
                    this.selectedContractId = contractId;
                    this.selectedMachines = {};
                    this.addedMachines = [];
                    this.totalAmount = 0;

                    try {
                        const response = await fetch(`/contracts/${contractId}/contract-details`);
                        const data = await response.json();
                        this.contractData = data;

                        // Populate buyer details from contract
                        if (data.contract) {
                            this.buyerCompanyName = data.contract.company_name || '';
                            this.pan = data.contract.pan || '';
                            this.gst = data.contract.gst || '';
                            this.phoneNumber = data.contract.phone_number || '';
                            this.phoneNumber2 = data.contract.phone_number_2 || '';
                            this.ifcCertificateNumber = '';
                            // Populate addresses from contract contact_address
                            this.billingAddress = data.contract.contact_address || '';
                            this.shippingAddress = data.contract.contact_address || '';
                        }
                    } catch (error) {
                        console.error('Error loading contract details:', error);
                        alert('Failed to load contract details. Please try again.');
                    }
                },


                async addMachine() {
                    const tempId = 'temp_' + (++this.tempMachineIdCounter);
                    const newMachine = {
                        tempId: tempId,
                        machine_category_id: '', // User will select category
                        contract_machine_id: '',
                        availableMachines: [], // Will be populated by loadMachinesForCategory
                        categoryItems: null, // Will be populated by loadCategoryItemsForMachine
                        machineModels: [], // Will be populated by loadMachineModelsForPI
                        quantity: 0,
                        amount: 0,
                        amc_price: 0,
                        contractAmount: 0, // Original contract amount
                        maxQuantity: 0,
                        brand_id: '',
                        machine_model_id: '',
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
                        machine_heald_wire_id: '',
                        delivery_term_id: '',
                        delivery_term_text: '',
                        description: ''
                    };
                    
                    this.addedMachines.push(newMachine);
                },

                removeMachine(tempId) {
                    this.addedMachines = this.addedMachines.filter(m => m.tempId !== tempId);
                    this.calculateTotal();
                },

                getMachinesForCategory(categoryId) {
                    if (!this.contractData || !this.contractData.machinesByCategory || !categoryId) {
                        return [];
                    }
                    // Convert both to strings for comparison to handle type mismatches
                    const categoryIdStr = String(categoryId);
                    const category = this.contractData.machinesByCategory.find(cat => {
                        const catIdStr = String(cat.category_id);
                        return catIdStr === categoryIdStr;
                    });
                    
                    if (category && category.machines && Array.isArray(category.machines) && category.machines.length > 0) {
                        return category.machines;
                    }
                    return [];
                },

                async loadMachinesForCategory(tempId, categoryId) {
                    if (!categoryId) {
                        // If no category, clear available machines
                        const machine = this.addedMachines.find(m => m.tempId === tempId);
                        if (machine) {
                            machine.availableMachines = [];
                            machine.categoryItems = null;
                            machine.contract_machine_id = '';
                        }
                        return;
                    }
                    
                    const machine = this.addedMachines.find(m => m.tempId === tempId);
                    if (machine) {
                        const categoryIdStr = String(categoryId);
                        machine.machine_category_id = categoryIdStr;
                        
                        // Store available machines for this category - create new array for reactivity
                        const machines = this.getMachinesForCategory(categoryIdStr);
                        // Force reactivity by creating a completely new array reference
                        if (machines && machines.length > 0) {
                            machine.availableMachines = machines.map(m => ({...m})); // Deep copy for reactivity
                            
                            // Automatically select the first machine from this category
                            const firstMachine = machines[0];
                            if (firstMachine) {
                                machine.contract_machine_id = String(firstMachine.id);
                                // Automatically load all details from the contract machine
                                await this.loadMachineDetails(tempId, firstMachine.id);
                            }
                        } else {
                            machine.availableMachines = [];
                            machine.contract_machine_id = '';
                        }
                    }
                },

                async loadMachineDetails(tempId, contractMachineId) {
                    const machine = this.addedMachines.find(m => m.tempId === tempId);
                    if (!machine || !contractMachineId) {
                        console.log('loadMachineDetails: Missing machine or contractMachineId', { tempId, contractMachineId, machine });
                        return;
                    }
                    
                    // Find the contract machine details - try multiple comparison methods
                    const contractMachineIdNum = parseInt(contractMachineId);
                    const contractMachineIdStr = String(contractMachineId);
                    
                    // Get all machines from all categories
                    const allMachines = this.contractData.machinesByCategory.flatMap(cat => cat.machines);
                    
                    // Try to find the machine using multiple comparison methods
                    let contractMachine = allMachines.find(m => {
                        return m.id == contractMachineIdNum || 
                               String(m.id) === contractMachineIdStr ||
                               m.id === contractMachineIdNum ||
                               String(m.id) === String(contractMachineIdNum);
                    });
                    
                    if (!contractMachine) {
                        console.log('loadMachineDetails: Contract machine not found', {
                            contractMachineId,
                            contractMachineIdNum,
                            contractMachineIdStr,
                            allMachineIds: allMachines.map(m => ({ id: m.id, type: typeof m.id, brand: m.brand, model: m.model }))
                        });
                        return;
                    }
                    
                    // Set contract machine ID
                    machine.contract_machine_id = String(contractMachine.id);
                    machine.contractAmount = parseFloat(contractMachine.amount) || 0; // Store original contract amount
                    machine.amount = parseFloat(contractMachine.amount) || 0; // Set editable amount (can be changed)
                    machine.amc_price = 0; // Initialize AMC price
                    machine.maxQuantity = parseInt(contractMachine.quantity) || 0;
                    machine.quantity = parseInt(contractMachine.quantity) || 0; // Set quantity from contract
                    
                    // Load category items for this machine's category FIRST
                    if (machine.machine_category_id) {
                        await this.loadCategoryItemsForMachine(tempId, machine.machine_category_id);
                        // Wait for Alpine.js to process the category items
                        await this.$nextTick();
                    }
                    
                    // Store contract values - convert to strings to match option values
                    const brandId = (contractMachine.brand_id != null && contractMachine.brand_id !== '') ? String(contractMachine.brand_id) : '';
                    const modelId = (contractMachine.machine_model_id != null && contractMachine.machine_model_id !== '') ? String(contractMachine.machine_model_id) : '';
                    
                    // Set brand_id FIRST (but NOT model_id yet)
                    machine.brand_id = brandId;
                    machine.machine_model_id = ''; // Clear model_id initially
                    
                    // Set all other specification values from contract
                    machine.feeder_id = (contractMachine.feeder_id != null && contractMachine.feeder_id !== '') ? String(contractMachine.feeder_id) : '';
                    machine.machine_hook_id = (contractMachine.machine_hook_id != null && contractMachine.machine_hook_id !== '') ? String(contractMachine.machine_hook_id) : '';
                    machine.machine_e_read_id = (contractMachine.machine_e_read_id != null && contractMachine.machine_e_read_id !== '') ? String(contractMachine.machine_e_read_id) : '';
                    machine.color_id = (contractMachine.color_id != null && contractMachine.color_id !== '') ? String(contractMachine.color_id) : '';
                    machine.machine_nozzle_id = (contractMachine.machine_nozzle_id != null && contractMachine.machine_nozzle_id !== '') ? String(contractMachine.machine_nozzle_id) : '';
                    machine.machine_dropin_id = (contractMachine.machine_dropin_id != null && contractMachine.machine_dropin_id !== '') ? String(contractMachine.machine_dropin_id) : '';
                    machine.machine_beam_id = (contractMachine.machine_beam_id != null && contractMachine.machine_beam_id !== '') ? String(contractMachine.machine_beam_id) : '';
                    machine.machine_cloth_roller_id = (contractMachine.machine_cloth_roller_id != null && contractMachine.machine_cloth_roller_id !== '') ? String(contractMachine.machine_cloth_roller_id) : '';
                    machine.machine_software_id = (contractMachine.machine_software_id != null && contractMachine.machine_software_id !== '') ? String(contractMachine.machine_software_id) : '';
                    machine.hsn_code_id = (contractMachine.hsn_code_id != null && contractMachine.hsn_code_id !== '') ? String(contractMachine.hsn_code_id) : '';
                    machine.wir_id = (contractMachine.wir_id != null && contractMachine.wir_id !== '') ? String(contractMachine.wir_id) : '';
                    machine.machine_shaft_id = (contractMachine.machine_shaft_id != null && contractMachine.machine_shaft_id !== '') ? String(contractMachine.machine_shaft_id) : '';
                    machine.machine_lever_id = (contractMachine.machine_lever_id != null && contractMachine.machine_lever_id !== '') ? String(contractMachine.machine_lever_id) : '';
                    machine.machine_chain_id = (contractMachine.machine_chain_id != null && contractMachine.machine_chain_id !== '') ? String(contractMachine.machine_chain_id) : '';
                    machine.machine_heald_wire_id = (contractMachine.machine_heald_wire_id != null && contractMachine.machine_heald_wire_id !== '') ? String(contractMachine.machine_heald_wire_id) : '';
                    machine.delivery_term_id = (contractMachine.delivery_term_id != null && contractMachine.delivery_term_id !== '') ? String(contractMachine.delivery_term_id) : '';
                    machine.description = contractMachine.description || '';
                    
                    // Wait for Alpine.js to process brand_id change
                    await this.$nextTick();
                    
                    // Load machine models if brand is selected
                    if (brandId) {
                        await this.loadMachineModelsForPI(tempId, brandId);
                        // Wait for models to load and Alpine to update
                        await this.$nextTick();
                        await this.$nextTick(); // Extra tick to ensure DOM is updated
                        
                        // AFTER models are loaded, set the model_id from contract
                        if (modelId && machine.machineModels && machine.machineModels.length > 0) {
                            // Check if the model from contract exists in the loaded models
                            const modelExists = machine.machineModels.some(m => String(m.id) === String(modelId));
                            if (modelExists) {
                                // Set the model_id - this should now work because models are loaded
                                machine.machine_model_id = String(modelId);
                                // Force another update to ensure reactivity
                                await this.$nextTick();
                                console.log('Model set from contract:', modelId, 'Available models:', machine.machineModels.map(m => ({id: m.id, model_no: m.model_no})));
                            } else {
                                console.warn('Model from contract not found in loaded models:', modelId, 'Available models:', machine.machineModels.map(m => ({id: m.id, model_no: m.model_no})));
                                // Still try to set it in case it's a display issue
                                machine.machine_model_id = String(modelId);
                            }
                        } else if (modelId) {
                            // If we have a modelId but no models loaded, still set it
                            console.warn('No models loaded but contract has model_id:', modelId);
                            machine.machine_model_id = String(modelId);
                        }
                    }
                    
                    // Force reactivity update
                    await this.$nextTick();
                    this.calculateTotal();
                },

                async loadCategoryItemsForMachine(tempId, categoryId) {
                    const machine = this.addedMachines.find(m => m.tempId === tempId);
                    if (!machine || !categoryId) {
                        return;
                    }
                    
                    try {
                        const response = await fetch(`{{ url('leads/category-items') }}/${categoryId}`);
                        const items = await response.json();
                        machine.categoryItems = items;
                    } catch (error) {
                        console.error('Error loading category items:', error);
                        machine.categoryItems = null;
                    }
                },

                async loadMachineModelsForPI(tempId, brandId) {
                    const machine = this.addedMachines.find(m => m.tempId === tempId);
                    if (!machine || !brandId) {
                        console.log('loadMachineModelsForPI: Missing machine or brandId', { tempId, brandId, machine });
                        return;
                    }
                    
                    try {
                        const brandIdStr = String(brandId);
                        console.log('Loading machine models for brand:', brandIdStr);
                        const response = await fetch(`{{ url('leads/machine-models') }}/${brandIdStr}`);
                        if (!response.ok) {
                            throw new Error(`HTTP error! status: ${response.status}`);
                        }
                        const models = await response.json();
                        // Ensure we have an array and models have proper structure
                        machine.machineModels = Array.isArray(models) ? models : [];
                        console.log('Loaded machine models for brand', brandIdStr, ':', machine.machineModels.length, 'models', machine.machineModels);
                        
                        // Force reactivity by creating new array reference
                        machine.machineModels = [...machine.machineModels];
                    } catch (error) {
                        console.error('Error loading machine models:', error);
                        machine.machineModels = [];
                    }
                },

                getCategoryName(categoryId) {
                    if (!this.contractData || !this.contractData.machinesByCategory || !categoryId) {
                        return '';
                    }
                    const category = this.contractData.machinesByCategory.find(cat => String(cat.category_id) === String(categoryId));
                    return category ? category.category_name : '';
                },

                getSelectedMachineInfo(contractMachineId) {
                    if (!contractMachineId || !this.contractData || !this.contractData.machinesByCategory) {
                        return 'No machine selected';
                    }
                    
                    // Get all machines from all categories
                    const allMachines = this.contractData.machinesByCategory.flatMap(cat => cat.machines);
                    const machine = allMachines.find(m => String(m.id) === String(contractMachineId));
                    
                    if (machine) {
                        return (machine.brand || 'N/A') + ' - ' + (machine.model || 'N/A');
                    }
                    return 'Machine not found';
                },

                updateMachineQuantity(tempId, unitAmount, quantity) {
                    quantity = parseInt(quantity) || 0;
                    const machine = this.addedMachines.find(m => m.tempId === tempId);
                    
                    if (machine && machine.maxQuantity && quantity > machine.maxQuantity) {
                        alert(`Quantity cannot exceed contract quantity of ${machine.maxQuantity}`);
                        machine.quantity = 0;
                        quantity = 0;
                    }

                    if (machine) {
                        machine.quantity = quantity;
                    }
                    this.calculateTotal();
                },

                getMachineDisplayAmount(tempId, unitAmountUSD) {
                    const machine = this.addedMachines.find(m => m.tempId === tempId);
                    if (!machine) return 0;
                    
                    const quantity = machine.quantity || 0;
                    const piPrice = parseFloat(unitAmountUSD || machine.amount || 0);
                    const amcPrice = parseFloat(machine.amc_price || 0);
                    let amount = quantity * (piPrice + amcPrice);
                    
                    // Only convert currency for local sales
                    if (this.typeOfSale === 'local' && this.usdRate) {
                        amount = amount * parseFloat(this.usdRate);
                    }
                    
                    return amount;
                },

                calculateTotal() {
                    // Calculate total from added machines (PI Price + AMC Price)
                    this.totalAmount = this.addedMachines.reduce((sum, machine) => {
                        const quantity = machine.quantity || 0;
                        const piPrice = machine.amount || 0;
                        const amcPrice = machine.amc_price || 0;
                        return sum + (quantity * (piPrice + amcPrice));
                    }, 0);
                    
                    // Calculate display amount based on type of sale
                    this.displayTotalAmount = this.totalAmount;
                    
                    if (this.typeOfSale === 'local' && this.usdRate) {
                        // Convert USD to INR for local sales
                        this.displayTotalAmount = this.totalAmount * parseFloat(this.usdRate);
                    } else if (this.typeOfSale === 'high_seas') {
                        // For high seas, calculate in USD first
                        this.displayTotalAmount = this.totalAmount;
                        if (this.commission) {
                            // Add commission percentage
                            const commissionAmount = (this.displayTotalAmount * parseFloat(this.commission)) / 100;
                            this.displayTotalAmount = this.displayTotalAmount + commissionAmount;
                        }
                    }
                    
                    // Calculate final amounts including expenses and GST
                    this.calculateFinalAmounts();
                },

                calculateFinalAmounts() {
                    // Base total (PI Price + AMC Price for all machines)
                    let baseTotal = this.totalAmount;
                    
                    // Add overseas freight and port expenses
                    const expenses = (parseFloat(this.overseasFreight) || 0) + (parseFloat(this.portExpensesClearing) || 0);
                    const subtotal = baseTotal + expenses;
                    
                    // Calculate GST
                    const gstPercent = parseFloat(this.gstPercentage) || 0;
                    this.gstAmount = (subtotal * gstPercent) / 100;
                    
                    // Final amount with GST
                    this.finalAmountWithGST = subtotal + this.gstAmount;
                },

                calculateMachineTotals(tempId) {
                    const machine = this.addedMachines.find(m => m.tempId === tempId);
                    if (machine) {
                        // Calculate PI Price + AMC Price
                        const piPlusAmc = (machine.amount || 0) + (machine.amc_price || 0);
                        machine.pi_price_plus_amc = piPlusAmc;
                        
                        // Calculate Total PI Price (PI + AMC) × Quantity
                        machine.total_pi_price = piPlusAmc * (machine.quantity || 0);
                    }
                    this.calculateTotal();
                },

                copyBillingAddressToShipping() {
                    if (this.copyBillingToShipping) {
                        this.shippingAddress = this.billingAddress;
                    }
                },

                toggleCategory(categoryId) {
                    const index = this.selectedCategories.indexOf(categoryId);
                    if (index > -1) {
                        this.selectedCategories.splice(index, 1);
                    } else {
                        this.selectedCategories.push(categoryId);
                    }
                },

                isCategorySelected(categoryId) {
                    return this.selectedCategories.includes(categoryId);
                },

                getSelectedCategories() {
                    if (!this.contractData || !this.contractData.machinesByCategory) {
                        return [];
                    }
                    return this.contractData.machinesByCategory.filter(category => 
                        this.selectedCategories.includes(category.category_id)
                    );
                },

                getMachineIndex(categoryId, machineIndex) {
                    let index = 0;
                    for (let cat of this.getSelectedCategories()) {
                        if (cat.category_id == categoryId) {
                            return index + machineIndex;
                        }
                        index += cat.machines.length;
                    }
                    return index;
                },

                removeMachineFromSelection(machineId) {
                    // Remove machine from selected categories by unselecting its category if it's the only machine
                    // For now, just remove from selectedMachines
                    delete this.selectedMachines[machineId];
                    this.calculateTotal();
                },

                updateMachineAmount(tempId, amount) {
                    const machine = this.addedMachines.find(m => m.tempId === tempId);
                    if (machine) {
                        machine.amount = parseFloat(amount) || 0;
                        this.calculateTotal();
                    }
                },

                cancelSelection() {
                    this.selectedContractId = null;
                    this.contractData = null;
                    this.selectedMachines = {};
                    this.selectedCategories = [];
                    this.addedMachines = [];
                    this.totalAmount = 0;
                }
            }
        }
    </script>

    <style>
        [x-cloak] {
            display: none !important;
        }
    </style>
</x-app-layout>