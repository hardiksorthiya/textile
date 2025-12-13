<x-app-layout> 
    <div class="mb-4 d-flex justify-content-between align-items-center">
        <div>
            <h1 class="h2 fw-bold mb-1" style="color: #1f2937;">Seller Management</h1>
            <p class="text-muted mb-0">Manage sellers and their details</p>
        </div>
    </div>

    <div class="row g-4"
        x-data="{
            editingSeller: null,
            isEditing: false,
            bankDetails: [{ bank_name: '', account_number: '', ifsc_code: '', branch_name: '', bank_address: '', account_holder_name: '' }],
            selectedCategories: [],
            categoryDropdownOpen: false,
            signaturePreview: null,

            addBankDetail() {
                this.bankDetails.push({ bank_name: '', account_number: '', ifsc_code: '', branch_name: '', bank_address: '', account_holder_name: '' });
            },

            removeBankDetail(index) {
                this.bankDetails.splice(index, 1);
            },

            toggleCategory(id) {
                id = String(id);
                const index = this.selectedCategories.indexOf(id);
                index > -1 ? this.selectedCategories.splice(index, 1) : this.selectedCategories.push(id);
            },

            isCategorySelected(id) {
                return this.selectedCategories.includes(String(id));
            },

            editSeller(seller) {
                this.editingSeller = seller;
                this.isEditing = true;

                this.selectedCategories = Array.isArray(seller.categories)
                    ? seller.categories.map(c => String(c.id ?? c.pivot?.machine_category_id ?? c.pivot?.machine_category_id)).filter(Boolean)
                    : [];

                this.bankDetails = seller.bank_details && seller.bank_details.length > 0
                    ? seller.bank_details.map(b => ({
                        bank_name: b.bank_name || '',
                        account_number: b.account_number || '',
                        ifsc_code: b.ifsc_code || '',
                        branch_name: b.branch_name || '',
                        bank_address: b.bank_address || '',
                        account_holder_name: b.account_holder_name || ''
                    }))
                    : [{ bank_name: '', account_number: '', ifsc_code: '', branch_name: '', bank_address: '', account_holder_name: '' }];

                this.signaturePreview = seller.signature && seller.signature.trim() !== ''
                    ? (seller.signature.startsWith('http') || seller.signature.startsWith('/')
                        ? seller.signature
                        : `/storage/${seller.signature}`)
                    : null;

                window.scrollTo({ top: 0, behavior: 'smooth' });
            },

            cancelEdit() {
                this.editingSeller = null;
                this.isEditing = false;
                this.selectedCategories = [];
                this.bankDetails = [{ bank_name: '', account_number: '', ifsc_code: '', branch_name: '', bank_address: '', account_holder_name: '' }];
                this.signaturePreview = null;
            }
        }">

        <!-- Left Side: Add/Edit Seller Form (30%) -->
        <div class="col-lg-7 col-md-12">
            <div class="card shadow-sm border-0 h-100" style="background: linear-gradient(to bottom, #ffffff 0%, #faf5ff 100%); border-radius: 12px;">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center mb-4 pb-3 border-bottom" style="border-color: rgba(139, 92, 246, 0.2) !important;">
                        <div class="rounded-circle bg-primary d-flex align-items-center justify-content-center me-3" style="width: 48px; height: 48px; background: linear-gradient(45deg, #8b5cf6, #a78bfa) !important;">
                            <i class="fas text-white" :class="isEditing ? 'fa-edit' : 'fa-user-tie'"></i>
                        </div>
                        <h2 class="h5 fw-bold mb-0" style="color: #1f2937;" x-text="isEditing ? 'Edit Seller' : 'Add Seller'"></h2>
                    </div>

                   <!-- Add Form -->
                   <div x-show="!isEditing">
                       <form action="{{ route('sellers.store') }}" method="POST" enctype="multipart/form-data">
                           @csrf
                           <div class="mb-3">
                               <label class="form-label fw-semibold" style="color: #374151;">Country</label>
                               <select name="country_id" required
                                       class="form-select @error('country_id') is-invalid @enderror"
                                       style="border-radius: 8px; border: 1px solid #e5e7eb;">
                                   <option value="">Select Country</option>
                                   @forelse($countries ?? [] as $country)
                                       <option value="{{ $country->id }}" {{ old('country_id') == $country->id ? 'selected' : '' }}>
                                           {{ $country->name }}
                                       </option>
                                   @empty
                                       <option value="" disabled>No countries available. Add one first.</option>
                                   @endforelse
                               </select>
                               @error('country_id')
                                   <div class="invalid-feedback d-block">{{ $message }}</div>
                               @enderror
                           </div>

                           <div class="mb-3">
                               <label class="form-label fw-semibold" style="color: #374151;">Category <small class="text-muted">(Multiple Select)</small></label>
                               <div class="position-relative" @click.away="categoryDropdownOpen = false">
                                   <button type="button" 
                                           @click="categoryDropdownOpen = !categoryDropdownOpen"
                                           class="form-control text-start d-flex justify-content-between align-items-center"
                                           style="border-radius: 8px; border: 1px solid #e5e7eb; background: white; min-height: 38px;">
                                       <span x-text="selectedCategories.length > 0 ? selectedCategories.length + ' category(ies) selected' : 'Select Categories'"></span>
                                       <i class="fas fa-chevron-down" :class="{ 'rotate-180': categoryDropdownOpen }"></i>
                                   </button>
                                   <div x-show="categoryDropdownOpen" 
                                        x-cloak
                                        class="position-absolute w-100 bg-white border rounded shadow-lg mt-1"
                                        style="z-index: 1000; max-height: 200px; overflow-y: auto; border-color: #e5e7eb !important;"
                                        @click.stop>
                                       @forelse($categories ?? [] as $category)
                                           <div class="d-flex align-items-center py-2 px-3" 
                                                x-data="{ hovered: false }"
                                                :class="isCategorySelected({{ $category->id }}) ? 'bg-purple-50' : ''"
                                                :style="isCategorySelected({{ $category->id }}) || hovered ? 'background-color: #f3e8ff;' : 'background-color: white;'"
                                                style="cursor: pointer; transition: background 0.2s; border-radius: 4px; margin: 2px;" 
                                                @mouseenter="hovered = true"
                                                @mouseleave="hovered = false"
                                                @click="toggleCategory({{ $category->id }})">
                                               <input class="form-check-input me-3" 
                                                      type="checkbox" 
                                                      :checked="isCategorySelected({{ $category->id }})"
                                                      style="cursor: pointer; margin-top: 0; flex-shrink: 0;"
                                                      @click.stop="toggleCategory({{ $category->id }})">
                                               <label class="flex-grow-1 mb-0" style="cursor: pointer; margin: 0;">
                                                   {{ $category->name }}
                                               </label>
                                               <i class="fas fa-check text-primary ms-2" x-show="isCategorySelected({{ $category->id }})" style="font-size: 0.875rem;"></i>
                                           </div>
                                       @empty
                                           <div class="p-3 text-center text-muted">
                                               <small>No categories available. Add categories first.</small>
                                           </div>
                                       @endforelse
                                   </div>
                                   <template x-for="categoryId in selectedCategories" :key="categoryId">
                                       <input type="hidden" :name="`categories[]`" :value="categoryId">
                                   </template>
                               </div>
                               @error('categories')
                                   <div class="invalid-feedback d-block">{{ $message }}</div>
                               @enderror
                           </div>

                           <div class="mb-3">
                               <label class="form-label fw-semibold" style="color: #374151;">Seller Name</label>
                               <input type="text" name="seller_name" required
                                      value="{{ old('seller_name') }}"
                                      class="form-control @error('seller_name') is-invalid @enderror"
                                      placeholder="Enter seller name"
                                      style="border-radius: 8px; border: 1px solid #e5e7eb;">
                               @error('seller_name')
                                   <div class="invalid-feedback">{{ $message }}</div>
                               @enderror
                           </div>

                           <div class="mb-3">
                               <label class="form-label fw-semibold" style="color: #374151;">Email</label>
                               <input type="email" name="email" required
                                      value="{{ old('email') }}"
                                      class="form-control @error('email') is-invalid @enderror"
                                      placeholder="Enter email"
                                      style="border-radius: 8px; border: 1px solid #e5e7eb;">
                               @error('email')
                                   <div class="invalid-feedback">{{ $message }}</div>
                               @enderror
                           </div>

                           <div class="mb-3">
                               <label class="form-label fw-semibold" style="color: #374151;">Mobile</label>
                               <input type="text" name="mobile" required
                                      value="{{ old('mobile') }}"
                                      class="form-control @error('mobile') is-invalid @enderror"
                                      placeholder="Enter mobile number"
                                      style="border-radius: 8px; border: 1px solid #e5e7eb;">
                               @error('mobile')
                                   <div class="invalid-feedback">{{ $message }}</div>
                               @enderror
                           </div>

                           <div class="mb-3">
                               <label class="form-label fw-semibold" style="color: #374151;">Address</label>
                               <textarea name="address" required rows="3"
                                         class="form-control @error('address') is-invalid @enderror"
                                         placeholder="Enter address"
                                         style="border-radius: 8px; border: 1px solid #e5e7eb;">{{ old('address') }}</textarea>
                               @error('address')
                                   <div class="invalid-feedback">{{ $message }}</div>
                               @enderror
                           </div>

                           <div class="mb-3">
                               <label class="form-label fw-semibold" style="color: #374151;">PI Short Name</label>
                               <input type="text" name="pi_short_name" required
                                      value="{{ old('pi_short_name') }}"
                                      class="form-control @error('pi_short_name') is-invalid @enderror"
                                      placeholder="Enter PI short name"
                                      style="border-radius: 8px; border: 1px solid #e5e7eb;">
                               @error('pi_short_name')
                                   <div class="invalid-feedback">{{ $message }}</div>
                               @enderror
                           </div>

                           <div class="mb-3">
                               <label class="form-label fw-semibold" style="color: #374151;">GST No</label>
                               <input type="text" name="gst_no"
                                      value="{{ old('gst_no') }}"
                                      class="form-control @error('gst_no') is-invalid @enderror"
                                      placeholder="Enter GST number"
                                      style="border-radius: 8px; border: 1px solid #e5e7eb;">
                               @error('gst_no')
                                   <div class="invalid-feedback">{{ $message }}</div>
                               @enderror
                           </div>

                           <div class="mb-3">
                               <label class="form-label fw-semibold" style="color: #374151;">Signature <small class="text-muted">(Image Upload)</small></label>
                               <input type="file" name="signature" accept="image/*"
                                      class="form-control @error('signature') is-invalid @enderror"
                                      style="border-radius: 8px; border: 1px solid #e5e7eb;">
                               <small class="text-muted">Max size: 2MB, Formats: JPEG, PNG, JPG, GIF</small>
                               @error('signature')
                                   <div class="invalid-feedback">{{ $message }}</div>
                               @enderror
                           </div>

                           <!-- Bank Details Section -->
                           <div class="mb-4">
                               <div class="d-flex justify-content-between align-items-center mb-2">
                                   <label class="form-label fw-semibold mb-0" style="color: #374151;">Bank Details</label>
                                   <button type="button" @click="addBankDetail()" class="btn btn-sm btn-outline-primary">
                                       <i class="fas fa-plus me-1"></i>Add Bank
                                   </button>
                               </div>
                               <div style="max-height: 250px; overflow-y: auto;">
                                   <template x-for="(bank, index) in bankDetails" :key="index">
                                       <div class="border rounded p-3 mb-2" style="border-color: rgba(139, 92, 246, 0.2) !important; background: white;">
                                           <div class="d-flex justify-content-between align-items-center mb-2">
                                               <small class="text-muted fw-semibold">Bank Detail <span x-text="index + 1"></span></small>
                                               <button type="button" @click="removeBankDetail(index)" class="btn btn-sm btn-outline-danger" x-show="bankDetails.length > 1">
                                                   <i class="fas fa-trash"></i>
                                               </button>
                                           </div>
                                           <input type="hidden" :name="`bank_details[${index}][bank_name]`" x-model="bank.bank_name">
                                           <input type="hidden" :name="`bank_details[${index}][account_number]`" x-model="bank.account_number">
                                           <input type="hidden" :name="`bank_details[${index}][ifsc_code]`" x-model="bank.ifsc_code">
                                           <input type="hidden" :name="`bank_details[${index}][branch_name]`" x-model="bank.branch_name">
                                           <input type="hidden" :name="`bank_details[${index}][bank_address]`" x-model="bank.bank_address">
                                           <input type="hidden" :name="`bank_details[${index}][account_holder_name]`" x-model="bank.account_holder_name">
                                           
                                           <div class="mb-2">
                                               <input type="text" x-model="bank.bank_name" placeholder="Bank Name" class="form-control form-control-sm" style="border-radius: 6px;">
                                           </div>
                                           <div class="mb-2">
                                               <input type="text" x-model="bank.account_number" placeholder="Account Number" class="form-control form-control-sm" style="border-radius: 6px;">
                                           </div>
                                           <div class="mb-2">
                                               <input type="text" x-model="bank.ifsc_code" placeholder="IFSC Code" class="form-control form-control-sm" style="border-radius: 6px;">
                                           </div>
                                           <div class="mb-2">
                                               <input type="text" x-model="bank.branch_name" placeholder="Branch Name" class="form-control form-control-sm" style="border-radius: 6px;">
                                           </div>
                                           <div class="mb-2">
                                               <textarea x-model="bank.bank_address" placeholder="Bank Address" rows="2" class="form-control form-control-sm" style="border-radius: 6px;"></textarea>
                                           </div>
                                           <div>
                                               <input type="text" x-model="bank.account_holder_name" placeholder="Account Holder Name" class="form-control form-control-sm" style="border-radius: 6px;">
                                           </div>
                                       </div>
                                   </template>
                               </div>
                           </div>

                           <button type="submit" class="btn btn-primary w-100 py-2 fw-semibold" style="border-radius: 8px; background: linear-gradient(45deg, #8b5cf6, #a78bfa) !important; border: none;">
                               <i class="fas fa-plus me-2"></i>Add Seller
                           </button>
                       </form>
                   </div>

                   <!-- Edit Form -->
                   <div x-show="isEditing" x-cloak>
                       <template x-if="editingSeller">
                           <form :action="`{{ url('sellers') }}/${editingSeller.id}`" method="POST" enctype="multipart/form-data">
                               @csrf
                               @method('PUT')
                               <div class="mb-3">
                                   <label class="form-label fw-semibold" style="color: #374151;">Country</label>
                                   <select name="country_id" required
                                           class="form-select"
                                           style="border-radius: 8px; border: 1px solid #e5e7eb;"
                                           x-model="editingSeller.country_id">
                                       <option value="">Select Country</option>
                                       @forelse($countries ?? [] as $country)
                                           <option value="{{ $country->id }}">
                                               {{ $country->name }}
                                           </option>
                                       @empty
                                           <option value="" disabled>No countries available</option>
                                       @endforelse
                                   </select>
                               </div>

                               <div class="mb-3">
                                   <label class="form-label fw-semibold" style="color: #374151;">Category <small class="text-muted">(Multiple Select)</small></label>
                                   <div class="position-relative" @click.away="categoryDropdownOpen = false">
                                       <button type="button" 
                                               @click="categoryDropdownOpen = !categoryDropdownOpen"
                                               class="form-control text-start d-flex justify-content-between align-items-center"
                                               style="border-radius: 8px; border: 1px solid #e5e7eb; background: white; min-height: 38px;">
                                           <span x-text="selectedCategories.length > 0 ? selectedCategories.length + ' category(ies) selected' : 'Select Categories'"></span>
                                           <i class="fas fa-chevron-down" :class="{ 'rotate-180': categoryDropdownOpen }"></i>
                                       </button>
                                       <div x-show="categoryDropdownOpen" 
                                            x-cloak
                                            class="position-absolute w-100 bg-white border rounded shadow-lg mt-1"
                                            style="z-index: 1000; max-height: 200px; overflow-y: auto; border-color: #e5e7eb !important;"
                                            @click.stop>
                                           @forelse($categories ?? [] as $category)
                                               <div class="d-flex align-items-center py-2 px-3" 
                                                    x-data="{ hovered: false }"
                                                    :class="isCategorySelected({{ $category->id }}) ? 'bg-purple-50' : ''"
                                                    :style="isCategorySelected({{ $category->id }}) || hovered ? 'background-color: #f3e8ff;' : 'background-color: white;'"
                                                    style="cursor: pointer; transition: background 0.2s; border-radius: 4px; margin: 2px;" 
                                                    @mouseenter="hovered = true"
                                                    @mouseleave="hovered = false"
                                                    @click="toggleCategory({{ $category->id }})">
                                                   <input class="form-check-input me-3" 
                                                          type="checkbox" 
                                                          :checked="isCategorySelected({{ $category->id }})"
                                                          style="cursor: pointer; margin-top: 0; flex-shrink: 0;"
                                                          @click.stop="toggleCategory({{ $category->id }})">
                                                   <label class="flex-grow-1 mb-0" style="cursor: pointer; margin: 0;">
                                                       {{ $category->name }}
                                                   </label>
                                                   <i class="fas fa-check text-primary ms-2" x-show="isCategorySelected({{ $category->id }})" style="font-size: 0.875rem;"></i>
                                               </div>
                                           @empty
                                               <div class="p-3 text-center text-muted">
                                                   <small>No categories available</small>
                                               </div>
                                           @endforelse
                                       </div>
                                       <template x-for="categoryId in selectedCategories" :key="categoryId">
                                           <input type="hidden" :name="`categories[]`" :value="categoryId">
                                       </template>
                                   </div>
                               </div>

                               <div class="mb-3">
                                   <label class="form-label fw-semibold" style="color: #374151;">Seller Name</label>
                                   <input type="text" name="seller_name" required
                                          x-model="editingSeller.seller_name"
                                          class="form-control"
                                          placeholder="Enter seller name"
                                          style="border-radius: 8px; border: 1px solid #e5e7eb;">
                               </div>

                               <div class="mb-3">
                                   <label class="form-label fw-semibold" style="color: #374151;">Email</label>
                                   <input type="email" name="email" required
                                          x-model="editingSeller.email"
                                          class="form-control"
                                          placeholder="Enter email"
                                          style="border-radius: 8px; border: 1px solid #e5e7eb;">
                               </div>

                               <div class="mb-3">
                                   <label class="form-label fw-semibold" style="color: #374151;">Mobile</label>
                                   <input type="text" name="mobile" required
                                          x-model="editingSeller.mobile"
                                          class="form-control"
                                          placeholder="Enter mobile number"
                                          style="border-radius: 8px; border: 1px solid #e5e7eb;">
                               </div>

                               <div class="mb-3">
                                   <label class="form-label fw-semibold" style="color: #374151;">Address</label>
                                   <textarea name="address" required rows="3"
                                             x-model="editingSeller.address"
                                             class="form-control"
                                             placeholder="Enter address"
                                             style="border-radius: 8px; border: 1px solid #e5e7eb;"></textarea>
                               </div>

                               <div class="mb-3">
                                   <label class="form-label fw-semibold" style="color: #374151;">PI Short Name</label>
                                   <input type="text" name="pi_short_name" required
                                          x-model="editingSeller.pi_short_name"
                                          class="form-control"
                                          placeholder="Enter PI short name"
                                          style="border-radius: 8px; border: 1px solid #e5e7eb;">
                               </div>

                               <div class="mb-3">
                                   <label class="form-label fw-semibold" style="color: #374151;">GST No</label>
                                   <input type="text" name="gst_no"
                                          x-model="editingSeller.gst_no"
                                          class="form-control"
                                          placeholder="Enter GST number"
                                          style="border-radius: 8px; border: 1px solid #e5e7eb;">
                               </div>

                               <div class="mb-3">
                                   <label class="form-label fw-semibold" style="color: #374151;">Signature <small class="text-muted">(Image Upload)</small></label>
                                   <template x-if="signaturePreview">
                                       <div class="mb-2">
                                           <img :src="signaturePreview" alt="Signature Preview" class="img-thumbnail" style="max-height: 100px;">
                                       </div>
                                   </template>
                                   <input type="file" name="signature" accept="image/*"
                                          @change="(e) => { const file = e.target.files[0]; if (file) { const reader = new FileReader(); reader.onload = (e) => { signaturePreview = e.target.result; }; reader.readAsDataURL(file); } else { signaturePreview = editingSeller.signature ? (editingSeller.signature.startsWith('http') || editingSeller.signature.startsWith('/') ? editingSeller.signature : `/storage/${editingSeller.signature}`) : null; } }"
                                          class="form-control"
                                          style="border-radius: 8px; border: 1px solid #e5e7eb;">
                                   <small class="text-muted">Max size: 2MB, Formats: JPEG, PNG, JPG, GIF. Leave empty to keep current image.</small>
                               </div>

                               <!-- Bank Details Section -->
                               <div class="mb-4">
                                   <div class="d-flex justify-content-between align-items-center mb-2">
                                       <label class="form-label fw-semibold mb-0" style="color: #374151;">Bank Details</label>
                                       <button type="button" @click="addBankDetail()" class="btn btn-sm btn-outline-primary">
                                           <i class="fas fa-plus me-1"></i>Add Bank
                                       </button>
                                   </div>
                                   <div style="max-height: 250px; overflow-y: auto;">
                                       <template x-for="(bank, index) in bankDetails" :key="index">
                                           <div class="border rounded p-3 mb-2" style="border-color: rgba(139, 92, 246, 0.2) !important; background: white;">
                                               <div class="d-flex justify-content-between align-items-center mb-2">
                                                   <small class="text-muted fw-semibold">Bank Detail <span x-text="index + 1"></span></small>
                                                   <button type="button" @click="removeBankDetail(index)" class="btn btn-sm btn-outline-danger" x-show="bankDetails.length > 1">
                                                       <i class="fas fa-trash"></i>
                                                   </button>
                                               </div>
                                               <input type="hidden" :name="`bank_details[${index}][bank_name]`" x-model="bank.bank_name">
                                               <input type="hidden" :name="`bank_details[${index}][account_number]`" x-model="bank.account_number">
                                               <input type="hidden" :name="`bank_details[${index}][ifsc_code]`" x-model="bank.ifsc_code">
                                               <input type="hidden" :name="`bank_details[${index}][branch_name]`" x-model="bank.branch_name">
                                               <input type="hidden" :name="`bank_details[${index}][bank_address]`" x-model="bank.bank_address">
                                               <input type="hidden" :name="`bank_details[${index}][account_holder_name]`" x-model="bank.account_holder_name">
                                               
                                               <div class="mb-2">
                                                   <input type="text" x-model="bank.bank_name" placeholder="Bank Name" class="form-control form-control-sm" style="border-radius: 6px;">
                                               </div>
                                               <div class="mb-2">
                                                   <input type="text" x-model="bank.account_number" placeholder="Account Number" class="form-control form-control-sm" style="border-radius: 6px;">
                                               </div>
                                               <div class="mb-2">
                                                   <input type="text" x-model="bank.ifsc_code" placeholder="IFSC Code" class="form-control form-control-sm" style="border-radius: 6px;">
                                               </div>
                                               <div class="mb-2">
                                                   <input type="text" x-model="bank.branch_name" placeholder="Branch Name" class="form-control form-control-sm" style="border-radius: 6px;">
                                               </div>
                                               <div class="mb-2">
                                                   <textarea x-model="bank.bank_address" placeholder="Bank Address" rows="2" class="form-control form-control-sm" style="border-radius: 6px;"></textarea>
                                               </div>
                                               <div>
                                                   <input type="text" x-model="bank.account_holder_name" placeholder="Account Holder Name" class="form-control form-control-sm" style="border-radius: 6px;">
                                               </div>
                                           </div>
                                       </template>
                                   </div>
                               </div>

                               <div class="d-flex gap-2">
                                   <button type="button" @click="cancelEdit()" class="btn btn-outline-secondary flex-grow-1" style="border-radius: 8px;">
                                       Cancel
                                   </button>
                                   <button type="submit" class="btn btn-primary flex-grow-1" style="border-radius: 8px; background: linear-gradient(45deg, #8b5cf6, #a78bfa) !important; border: none;">
                                       <i class="fas fa-save me-2"></i>Update
                                   </button>
                               </div>
                           </form>
                       </template>
                   </div>
                </div>
            </div>
        </div>

        <!-- Right Side: Seller List Table (70%) -->
        <div class="col-lg-5 col-md-12">
            <div class="card shadow-sm border-0 h-100" style="background: linear-gradient(to bottom, #ffffff 0%, #faf5ff 100%); border-radius: 12px;">
                <div class="card-header border-0 pb-0" style="background: transparent;">
                    <div class="d-flex align-items-center py-3 border-bottom" style="border-color: rgba(139, 92, 246, 0.2) !important;">
                        <div class="rounded-circle bg-primary d-flex align-items-center justify-content-center me-3" style="width: 48px; height: 48px; background: linear-gradient(45deg, #8b5cf6, #a78bfa) !important;">
                            <i class="fas fa-list text-white"></i>
                        </div>
                        <h2 class="h5 fw-bold mb-0" style="color: #1f2937;">Seller List</h2>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive" style="max-height: calc(100vh - 350px); overflow-y: auto; overflow-x: hidden;">
                        <table class="table table-hover mb-0 align-middle">
                            <thead class="sticky-top" style="background: linear-gradient(to right, #f3e8ff, #e9d5ff) !important;">
                                <tr>
                                    <th class="px-4 py-3 text-uppercase small fw-bold" style="color: #4c1d95 !important; border-bottom: 1px solid #d8b4fe !important;">Seller</th>
                                    <th class="px-4 py-3 text-uppercase small fw-bold" style="color: #4c1d95 !important; border-bottom: 1px solid #d8b4fe !important;">Country</th>
                                    <th class="px-4 py-3 text-uppercase small fw-bold" style="color: #4c1d95 !important; border-bottom: 1px solid #d8b4fe !important;">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($sellers as $seller)
                                    <tr class="border-bottom" style="transition: all 0.2s ease;">
                                        <td class="px-4 py-3">
                                            <div class="d-flex align-items-center">
                                                <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center me-3 shadow-sm" 
                                                     style="width: 45px; height: 45px; font-weight: 600; font-size: 16px; background: linear-gradient(45deg, #8b5cf6, #a78bfa) !important; box-shadow: 0 2px 8px rgba(139, 92, 246, 0.3);">
                                                    {{ strtoupper(substr($seller->seller_name, 0, 1)) }}
                                                </div>
                                                <div class="fw-semibold" style="color: #1f2937; font-size: 0.95rem;">{{ $seller->seller_name }}</div>
                                            </div>
                                        </td>
                                        <td class="px-4 py-3">
                                            <div class="d-flex align-items-center">
                                                <i class="fas fa-globe me-2 text-muted" style="font-size: 0.75rem;"></i>
                                                <small class="text-muted" style="font-size: 0.85rem;">{{ $seller->country->name ?? 'N/A' }}</small>
                                            </div>
                                        </td>
                                        <td class="px-4 py-3">
                                            <div class="d-flex gap-2" role="group">
                                                <button type="button" 
                                                        @click="editSeller({
                                                            id: {{ $seller->id }},
                                                            country_id: {{ $seller->country_id ?? 'null' }},
                                                            seller_name: '{{ addslashes($seller->seller_name) }}',
                                                            email: '{{ addslashes($seller->email) }}',
                                                            mobile: '{{ addslashes($seller->mobile) }}',
                                                            address: '{{ addslashes($seller->address) }}',
                                                            pi_short_name: '{{ addslashes($seller->pi_short_name) }}',
                                                            gst_no: '{{ addslashes($seller->gst_no ?? '') }}',
                                                            signature: '{{ addslashes($seller->signature ?? '') }}',
                                                            categories: @js($seller->machineCategories),
                                                            bank_details: @js($seller->bankDetails)
                                                        })"
                                                        class="btn btn-sm btn-outline-primary" 
                                                        title="Edit Seller"
                                                        style="border-radius: 6px; border-color: #8b5cf6; color: #8b5cf6; transition: all 0.2s ease;"
                                                        onmouseover="this.style.background='#8b5cf6'; this.style.color='white';"
                                                        onmouseout="this.style.background='transparent'; this.style.color='#8b5cf6';">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <form action="{{ route('sellers.destroy', $seller) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this seller?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" 
                                                            class="btn btn-sm btn-outline-danger" 
                                                            title="Delete Seller"
                                                            style="border-radius: 6px; border-color: #ef4444; color: #ef4444; transition: all 0.2s ease;"
                                                            onmouseover="this.style.background='#ef4444'; this.style.color='white';"
                                                            onmouseout="this.style.background='transparent'; this.style.color='#ef4444';">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="text-center text-muted py-5">
                                            <div class="d-flex flex-column align-items-center">
                                                <i class="fas fa-user-tie fa-3x mb-3" style="color: #d1d5db; opacity: 0.5;"></i>
                                                <p class="mb-0" style="font-size: 0.9rem;">No sellers found.</p>
                                                <small class="text-muted mt-1">Add your first seller to get started</small>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                @if($sellers->hasPages())
                    <div class="card-footer bg-transparent border-top" style="border-color: rgba(139, 92, 246, 0.2) !important;">
                        <div class="d-flex justify-content-center">
                            {{ $sellers->links() }}
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Success/Error Message -->
    @if(session('success'))
        <div x-data="{ show: true }" 
             x-show="show" 
             x-init="setTimeout(() => show = false, 3000)"
             class="position-fixed bottom-0 end-0 m-4 rounded shadow-lg" 
             style="z-index: 1050; background: linear-gradient(135deg, #10b981 0%, #059669 100%); color: white; padding: 1rem 1.5rem; border-radius: 10px; animation: slideIn 0.3s ease; box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);">
            <div class="d-flex align-items-center">
                <i class="fas fa-check-circle me-2 fs-5"></i>
                <span class="fw-semibold">{{ session('success') }}</span>
            </div>
        </div>
        <style>
            @keyframes slideIn {
                from {
                    transform: translateX(100%);
                    opacity: 0;
                }
                to {
                    transform: translateX(0);
                    opacity: 1;
                }
            }
        </style>
    @endif
    @if(session('error'))
        <div x-data="{ show: true }"
             x-show="show"
             x-init="setTimeout(() => show = false, 3000)"
             class="position-fixed bottom-0 end-0 m-4 bg-danger text-white px-4 py-3 rounded shadow-lg" 
             style="z-index: 1050; background: linear-gradient(45deg, #ef4444, #f87171) !important; box-shadow: 0 4px 12px rgba(239, 68, 68, 0.3);">
            <div class="d-flex align-items-center">
                <i class="fas fa-times-circle me-2"></i>
                <span>{{ session('error') }}</span>
            </div>
        </div>
    @endif

    <style>
        .table-hover tbody tr:hover {
            background-color: #f3e8ff !important;
            transform: scale(1.01);
            transition: all 0.2s ease;
        }
        .card {
            transition: all 0.3s ease;
        }
        .card:hover {
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
        }
    </style>
</x-app-layout>
