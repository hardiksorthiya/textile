<x-app-layout>
    <div class="mb-4 d-flex justify-content-between align-items-center">
        <div>
            <h1 class="h2 fw-bold mb-1" style="color: #1f2937;">Return Payment</h1>
            <p class="text-muted mb-0">Return payment for an approved contract or proforma invoice</p>
        </div>
        <a href="{{ route('payments.return-payment') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-2"></i>Back to Search
        </a>
    </div>

    <div x-data="{
        selectedSalesManager: '{{ request('sales_manager') ?? '' }}',
        selectedContractId: null,
        selectedProformaInvoiceId: null,
        contractSearch: '',
        proformaInvoiceSearch: '',
        contractDropdownOpen: false,
        proformaInvoiceDropdownOpen: false,
        contracts: @js($contracts ?? []),
        proformaInvoices: @js($proformaInvoices ?? []),
        selectedPayeeCountry: '',
        selectedSellerId: null,
        selectedBankId: null,
        sellerSearch: '',
        sellerDropdownOpen: false,
        sellers: [],
        bankDetails: [],
        selectedCurrency: '',
        selectedPaymentMode: '',
        countries: @js(collect($countries ?? [])->map(function($c) { return ['id' => $c->id, 'name' => $c->name, 'currency' => $c->currency ?? '$']; })->values()->toArray()),
        
        get filteredContracts() {
            if (!this.contractSearch) return this.contracts;
            const search = this.contractSearch.toLowerCase();
            return this.contracts.filter(c => 
                (c.contract_number && c.contract_number.toLowerCase().includes(search)) ||
                (c.buyer_name && c.buyer_name.toLowerCase().includes(search)) ||
                (c.company_name && c.company_name.toLowerCase().includes(search))
            );
        },
        
        get filteredProformaInvoices() {
            if (!this.proformaInvoiceSearch) return this.proformaInvoices;
            const search = this.proformaInvoiceSearch.toLowerCase();
            return this.proformaInvoices.filter(pi => 
                (pi.proforma_invoice_number && pi.proforma_invoice_number.toLowerCase().includes(search)) ||
                (pi.buyer_company_name && pi.buyer_company_name.toLowerCase().includes(search))
            );
        },
        
        selectContract(contractId) {
            this.selectedContractId = contractId;
            this.selectedProformaInvoiceId = null;
            this.contractDropdownOpen = false;
            window.location.href = '{{ route('payments.return-payment') }}?contract_id=' + contractId + '&sales_manager=' + this.selectedSalesManager;
        },
        
        selectProformaInvoice(piId) {
            this.selectedProformaInvoiceId = piId;
            this.selectedContractId = null;
            this.proformaInvoiceDropdownOpen = false;
            window.location.href = '{{ route('payments.return-payment') }}?proforma_invoice_id=' + piId + '&sales_manager=' + this.selectedSalesManager;
        },
        
        loadSellers() {
            if (!this.selectedPayeeCountry) {
                this.sellers = [];
                this.selectedSellerId = null;
                this.selectedBankId = null;
                this.bankDetails = [];
                this.selectedCurrency = '';
                return;
            }

            // Get currency from selected country
            const selectedCountry = this.countries.find(c => c.id == this.selectedPayeeCountry);
            this.selectedCurrency = selectedCountry ? (selectedCountry.currency || '$') : '';
            
            fetch('{{ route('payments.get-sellers-by-country') }}?country_id=' + this.selectedPayeeCountry)
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.json();
                })
                .then(data => {
                    this.sellers = data || [];
                    this.selectedSellerId = null;
                    this.selectedBankId = null;
                    this.bankDetails = [];
                })
                .catch(error => {
                    console.error('Error loading sellers:', error);
                    this.sellers = [];
                });
        },
        
        get filteredSellers() {
            if (!this.sellerSearch) return this.sellers;
            const search = this.sellerSearch.toLowerCase();
            return this.sellers.filter(s => 
                (s.seller_name && s.seller_name.toLowerCase().includes(search)) ||
                (s.pi_short_name && s.pi_short_name.toLowerCase().includes(search))
            );
        },
        
        selectSeller(sellerId) {
            this.selectedSellerId = sellerId;
            this.selectedBankId = null;
            this.sellerDropdownOpen = false;
            this.loadBankDetails();
        },
        
        loadBankDetails() {
            if (!this.selectedSellerId) {
                this.bankDetails = [];
                this.selectedBankId = null;
                return;
            }
            fetch('{{ route('payments.get-bank-details-by-seller') }}?seller_id=' + this.selectedSellerId)
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.json();
                })
                .then(data => {
                    this.bankDetails = data || [];
                })
                .catch(error => {
                    console.error('Error loading bank details:', error);
                    this.bankDetails = [];
                });
        },
        
        loadContracts() {
            if (!this.selectedSalesManager) {
                this.contracts = [];
                this.proformaInvoices = [];
                return;
            }
            fetch('{{ route('payments.get-contracts') }}?sales_manager_id=' + this.selectedSalesManager)
                .then(response => response.json())
                .then(data => {
                    this.contracts = data;
                });
            
            fetch('{{ route('payments.get-proforma-invoices') }}?sales_manager_id=' + this.selectedSalesManager)
                .then(response => response.json())
                .then(data => {
                    this.proformaInvoices = data;
                });
        }
    }" x-init="if(selectedSalesManager) loadContracts()">
        <!-- Search Section -->
        <div class="card shadow-sm border-0 mb-4" style="background: linear-gradient(to bottom, #ffffff 0%, color-mix(in srgb, var(--primary-color) 6%, #ffffff) 100%); border-radius: 12px;">
            <div class="card-header border-0 pb-0" style="background: transparent;">
                <div class="d-flex align-items-center py-3 border-bottom" style="border-color: color-mix(in srgb, var(--primary-color) 20%, transparent) !important;">
                    <div class="rounded-circle bg-primary d-flex align-items-center justify-content-center me-3" style="width: 48px; height: 48px; background: linear-gradient(45deg, var(--primary-color), var(--primary-light)) !important;">
                        <i class="fas fa-search text-white"></i>
                    </div>
                    <h2 class="h5 fw-bold mb-0" style="color: #1f2937;">Search</h2>
                </div>
            </div>
            <div class="card-body p-4">
                <form method="GET" action="{{ route('payments.return-payment') }}" id="searchForm">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label fw-semibold" style="color: #374151;">Sales Manager</label>
                            <select name="sales_manager" x-model="selectedSalesManager" @change="loadContracts()" class="form-select" style="border-radius: 8px; border: 1px solid #e5e7eb;">
                                <option value="">All Sales Managers</option>
                                @foreach($salesManagers as $manager)
                                    <option value="{{ $manager->id }}" {{ request('sales_manager') == $manager->id ? 'selected' : '' }}>{{ $manager->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold" style="color: #374151;">Proforma Invoice</label>
                            <div class="position-relative" @click.away="proformaInvoiceDropdownOpen = false">
                                <button type="button" 
                                        @click="proformaInvoiceDropdownOpen = !proformaInvoiceDropdownOpen"
                                        class="form-control text-start d-flex justify-content-between align-items-center"
                                        style="border-radius: 8px; border: 1px solid #e5e7eb; background: white; min-height: 38px;"
                                        :disabled="!selectedSalesManager">
                                    <span x-text="selectedProformaInvoiceId ? proformaInvoices.find(pi => pi.id == selectedProformaInvoiceId)?.proforma_invoice_number || 'Select Proforma Invoice' : 'Select Proforma Invoice'"></span>
                                    <i class="fas fa-chevron-down" :class="{ 'rotate-180': proformaInvoiceDropdownOpen }"></i>
                                </button>
                                <div x-show="proformaInvoiceDropdownOpen" 
                                     x-cloak
                                     class="position-absolute w-100 bg-white border rounded shadow-lg mt-1"
                                     style="z-index: 1000; max-height: 300px; overflow-y: auto; border-color: #e5e7eb !important;"
                                     @click.stop>
                                    <div class="p-2 border-bottom">
                                        <input type="text" 
                                               x-model="proformaInvoiceSearch" 
                                               @click.stop
                                               placeholder="Search proforma invoice..."
                                               class="form-control form-control-sm">
                                    </div>
                                    <template x-if="filteredProformaInvoices.length === 0">
                                        <div class="p-3 text-center text-muted">No proforma invoices found</div>
                                    </template>
                                    <template x-for="pi in filteredProformaInvoices" :key="pi.id">
                                        <div class="d-flex align-items-center py-2 px-3 cursor-pointer hover:bg-gray-100" 
                                             @click="selectProformaInvoice(pi.id)"
                                             style="cursor: pointer;"
                                             :class="{ 'bg-primary text-white': selectedProformaInvoiceId == pi.id }">
                                            <div class="flex-grow-1">
                                                <div class="fw-semibold" x-text="pi.proforma_invoice_number"></div>
                                                <small x-text="pi.buyer_company_name"></small>
                                            </div>
                                        </div>
                                    </template>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold" style="color: #374151;">Contract</label>
                            <div class="position-relative" @click.away="contractDropdownOpen = false">
                                <button type="button" 
                                        @click="contractDropdownOpen = !contractDropdownOpen"
                                        class="form-control text-start d-flex justify-content-between align-items-center"
                                        style="border-radius: 8px; border: 1px solid #e5e7eb; background: white; min-height: 38px;"
                                        :disabled="!selectedSalesManager">
                                    <span x-text="selectedContractId ? contracts.find(c => c.id == selectedContractId)?.contract_number || 'Select Contract' : 'Select Contract'"></span>
                                    <i class="fas fa-chevron-down" :class="{ 'rotate-180': contractDropdownOpen }"></i>
                                </button>
                                <div x-show="contractDropdownOpen" 
                                     x-cloak
                                     class="position-absolute w-100 bg-white border rounded shadow-lg mt-1"
                                     style="z-index: 1000; max-height: 300px; overflow-y: auto; border-color: #e5e7eb !important;"
                                     @click.stop>
                                    <div class="p-2 border-bottom">
                                        <input type="text" 
                                               x-model="contractSearch" 
                                               @click.stop
                                               placeholder="Search contract..."
                                               class="form-control form-control-sm">
                                    </div>
                                    <template x-if="filteredContracts.length === 0">
                                        <div class="p-3 text-center text-muted">No contracts found</div>
                                    </template>
                                    <template x-for="contract in filteredContracts" :key="contract.id">
                                        <div class="d-flex align-items-center py-2 px-3 cursor-pointer hover:bg-gray-100" 
                                             @click="selectContract(contract.id)"
                                             style="cursor: pointer;"
                                             :class="{ 'bg-primary text-white': selectedContractId == contract.id }">
                                            <div class="flex-grow-1">
                                                <div class="fw-semibold" x-text="contract.contract_number"></div>
                                                <small x-text="contract.buyer_name + (contract.company_name ? ' (' + contract.company_name + ')' : '')"></small>
                                            </div>
                                        </div>
                                    </template>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Payment Form (shown when contract or proforma invoice is selected) -->
        @if(isset($contract) || isset($proformaInvoice))
        <div class="card shadow-sm border-0" style="background: linear-gradient(to bottom, #ffffff 0%, color-mix(in srgb, var(--primary-color) 6%, #ffffff) 100%); border-radius: 12px;">
            <div class="card-header border-0 pb-0" style="background: transparent;">
                <div class="d-flex align-items-center py-3 border-bottom" style="border-color: color-mix(in srgb, var(--primary-color) 20%, transparent) !important;">
                    <div class="rounded-circle bg-primary d-flex align-items-center justify-content-center me-3" style="width: 48px; height: 48px; background: linear-gradient(45deg, var(--primary-color), var(--primary-light)) !important;">
                        <i class="fas fa-undo text-white"></i>
                    </div>
                    <h2 class="h5 fw-bold mb-0" style="color: #1f2937;">Return Payment Details</h2>
                </div>
            </div>
            <div class="card-body p-4">
                <form action="{{ route('payments.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="type" value="return">
                    @if(isset($contract))
                        <input type="hidden" name="contract_id" value="{{ $contract->id }}">
                    @endif
                    @if(isset($proformaInvoice))
                        <input type="hidden" name="proforma_invoice_id" value="{{ $proformaInvoice->id }}">
                    @endif

                    <div class="row g-3">
                        <!-- PI Number (if proforma invoice is selected) -->
                        @if(isset($proformaInvoice))
                        <div class="col-md-4">
                            <label class="form-label">PI Number</label>
                            <input type="text" class="form-control" value="{{ $proformaInvoice->proforma_invoice_number }}" readonly style="background-color: #f3f4f6;">
                        </div>
                        @endif

                        <!-- Contract Number -->
                        <div class="col-md-4">
                            <label class="form-label">Contract Number</label>
                            <input type="text" class="form-control" 
                                   value="{{ isset($contract) ? $contract->contract_number : (isset($proformaInvoice) && $proformaInvoice->contract ? $proformaInvoice->contract->contract_number : 'N/A') }}" 
                                   readonly style="background-color: #f3f4f6;">
                        </div>

                        <!-- Buyer Name -->
                        <div class="col-md-4">
                            <label class="form-label">Buyer Name</label>
                            <input type="text" class="form-control" 
                                   value="{{ isset($contract) ? $contract->buyer_name . ($contract->company_name ? ' (' . $contract->company_name . ')' : '') : (isset($proformaInvoice) ? $proformaInvoice->buyer_company_name : 'N/A') }}" 
                                   readonly style="background-color: #f3f4f6;">
                        </div>

                        <div class="col-md-6">
                            <label for="payment_date" class="form-label">Payment Date <span class="text-danger">*</span></label>
                            <input type="date" name="payment_date" id="payment_date" class="form-control" value="{{ date('Y-m-d') }}" required>
                        </div>
                        <div class="col-md-6">
                            <label for="payment_method" class="form-label">Payment Mode</label>
                            <select name="payment_method" id="payment_method" x-model="selectedPaymentMode" class="form-select">
                                <option value="">Select Payment Mode</option>
                                <option value="UPI">UPI</option>
                                <option value="NEFT">NEFT</option>
                                <option value="CHEQUE">CHEQUE</option>
                                <option value="CASH">CASH</option>
                                <option value="TT">TT</option>
                                <option value="LC">LC</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="payment_by" class="form-label">Payment By</label>
                            <input type="text" name="payment_by" id="payment_by" class="form-control" placeholder="Enter payment by">
                        </div>
                        <div class="col-md-6">
                            <label for="payee_country_id" class="form-label">Payee</label>
                            <select name="payee_country_id" id="payee_country_id" x-model="selectedPayeeCountry" @change="loadSellers()" class="form-select">
                                <option value="">Select Payee (Country)</option>
                                @foreach($countries as $country)
                                    <option value="{{ $country->id }}">{{ $country->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="payment_to_seller_id" class="form-label">Payment To</label>
                            <div class="position-relative" @click.away="sellerDropdownOpen = false">
                                <button type="button" 
                                        @click="sellerDropdownOpen = !sellerDropdownOpen"
                                        class="form-control text-start d-flex justify-content-between align-items-center"
                                        style="border-radius: 8px; border: 1px solid #e5e7eb; background: white; min-height: 38px;"
                                        :disabled="!selectedPayeeCountry">
                                    <span x-text="selectedSellerId ? sellers.find(s => s.id == selectedSellerId)?.seller_name || 'Select Seller' : 'Select Seller'"></span>
                                    <i class="fas fa-chevron-down" :class="{ 'rotate-180': sellerDropdownOpen }"></i>
                                </button>
                                <div x-show="sellerDropdownOpen" 
                                     x-cloak
                                     class="position-absolute w-100 bg-white border rounded shadow-lg mt-1"
                                     style="z-index: 1000; max-height: 300px; overflow-y: auto; border-color: #e5e7eb !important;"
                                     @click.stop>
                                    <div class="p-2 border-bottom">
                                        <input type="text" 
                                               x-model="sellerSearch" 
                                               @click.stop
                                               placeholder="Search seller..."
                                               class="form-control form-control-sm">
                                    </div>
                                    <template x-if="filteredSellers.length === 0">
                                        <div class="p-3 text-center text-muted">No sellers found</div>
                                    </template>
                                    <template x-for="seller in filteredSellers" :key="seller.id">
                                        <div class="d-flex align-items-center py-2 px-3 cursor-pointer hover:bg-gray-100" 
                                             @click="selectSeller(seller.id)"
                                             style="cursor: pointer;"
                                             :class="{ 'bg-primary text-white': selectedSellerId == seller.id }">
                                            <div class="flex-grow-1">
                                                <div class="fw-semibold" x-text="seller.seller_name"></div>
                                                <small x-text="seller.pi_short_name"></small>
                                            </div>
                                        </div>
                                    </template>
                                </div>
                            </div>
                            <input type="hidden" name="payment_to_seller_id" x-model="selectedSellerId">
                        </div>
                        <div class="col-md-6">
                            <label for="bank_detail_id" class="form-label">Bank Name</label>
                            <select name="bank_detail_id" id="bank_detail_id" x-model="selectedBankId" class="form-select" :disabled="!selectedSellerId">
                                <option value="">Select Bank</option>
                                <template x-for="bank in bankDetails" :key="bank.id">
                                    <option :value="bank.id" x-text="bank.bank_name"></option>
                                </template>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="transaction_id" class="form-label">Transaction ID</label>
                            <input type="text" name="transaction_id" id="transaction_id" class="form-control" placeholder="Enter transaction ID">
                        </div>
                        <div class="col-md-6" x-show="selectedPayeeCountry && selectedCurrency === '$'" x-cloak>
                            <label for="swift_copy" class="form-label">SWIFT Copy <span class="text-muted">(Image)</span></label>
                            <input type="file" name="swift_copy" id="swift_copy" accept="image/*" class="form-control">
                            <small class="text-muted">Upload SWIFT copy image (JPG, PNG, etc.)</small>
                        </div>
                        <div class="col-md-6">
                            <label for="amount" class="form-label">Amount <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text" style="background-color: #f3f4f6; border: 1px solid #e5e7eb; border-right: none; border-radius: 8px 0 0 8px; font-weight: 600; min-width: 50px; justify-content: center;" x-text="selectedCurrency || '$'"></span>
                                <input type="number" step="0.01" name="amount" id="amount" class="form-control" required style="border-radius: 0 8px 8px 0;">
                            </div>
                        </div>
                        <div class="col-12">
                            <label for="notes" class="form-label">Notes</label>
                            <textarea name="notes" id="notes" class="form-control" rows="3"></textarea>
                        </div>
                        <div class="col-12">
                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-danger">
                                    <i class="fas fa-save me-2"></i>Return Payment
                                </button>
                                <a href="{{ route('payments.return-payment') }}" class="btn btn-outline-secondary">
                                    <i class="fas fa-times me-2"></i>Cancel</a>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        @endif
    </div>
</x-app-layout>
