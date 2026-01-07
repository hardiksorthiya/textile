<?php

namespace App\Http\Controllers;

use App\Models\ProformaInvoice;
use App\Models\DamageDetail;
use App\Models\DamageImage;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class DamageDetailController extends Controller
{
    /**
     * Display damage details index page (list all PIs - with or without damage details)
     */
    public function index(Request $request)
    {
        $query = ProformaInvoice::with(['damageDetails.images', 'contract.creator', 'creator', 'seller'])
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

        return view('damage-details.index', compact('proformaInvoices', 'salesManagers'));
    }

    /**
     * Show the form for adding/viewing damage details for a proforma invoice
     */
    public function show(ProformaInvoice $proformaInvoice)
    {
        $proformaInvoice->load(['damageDetails.images']);
        
        // Get existing damage details
        $damageDetails = $proformaInvoice->damageDetails;

        return view('damage-details.show', compact('proformaInvoice', 'damageDetails'));
    }

    /**
     * Show the form for editing a damage detail
     */
    public function edit(DamageDetail $damageDetail)
    {
        $damageDetail->load(['images', 'proformaInvoice']);
        return view('damage-details.edit', compact('damageDetail'));
    }

    /**
     * Update a damage detail
     */
    public function update(Request $request, DamageDetail $damageDetail)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'detail' => 'nullable|string',
            'images' => 'nullable|array',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif,webp|max:10240', // Max 10MB per image
        ]);

        try {
            DB::beginTransaction();

            // Update damage detail
            $damageDetail->update([
                'title' => $request->title,
                'detail' => $request->detail,
            ]);

            // Handle new image uploads
            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $image) {
                    if ($image->isValid()) {
                        $fileName = time() . '_' . uniqid() . '_' . $image->getClientOriginalName();
                        $filePath = $image->storeAs('damage-images/' . $damageDetail->id, $fileName, 'public');
                        
                        DamageImage::create([
                            'damage_detail_id' => $damageDetail->id,
                            'file_name' => $image->getClientOriginalName(),
                            'file_path' => $filePath,
                            'file_type' => $image->getMimeType(),
                            'file_size' => $image->getSize(),
                        ]);
                    }
                }
            }

            DB::commit();

            return redirect()->route('damage-details.show', $damageDetail->proformaInvoice)
                ->with('success', 'Damage detail updated successfully.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            return back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating damage detail: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            return back()->withErrors(['error' => 'Failed to update damage detail: ' . $e->getMessage()])
                ->withInput();
        }
    }

    /**
     * Store a new damage detail for a proforma invoice
     */
    public function store(Request $request, ProformaInvoice $proformaInvoice)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'detail' => 'nullable|string',
            'images' => 'nullable|array',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif,webp|max:10240', // Max 10MB per image
        ]);

        try {
            DB::beginTransaction();

            // Create damage detail
            $damageDetail = DamageDetail::create([
                'proforma_invoice_id' => $proformaInvoice->id,
                'title' => $request->title,
                'detail' => $request->detail,
            ]);

            // Handle image uploads
            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $image) {
                    if ($image->isValid()) {
                        $fileName = time() . '_' . uniqid() . '_' . $image->getClientOriginalName();
                        $filePath = $image->storeAs('damage-images/' . $damageDetail->id, $fileName, 'public');
                        
                        DamageImage::create([
                            'damage_detail_id' => $damageDetail->id,
                            'file_name' => $image->getClientOriginalName(),
                            'file_path' => $filePath,
                            'file_type' => $image->getMimeType(),
                            'file_size' => $image->getSize(),
                        ]);
                    }
                }
            }

            DB::commit();

            return redirect()->route('damage-details.show', $proformaInvoice)
                ->with('success', 'Damage detail added successfully.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            return back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error saving damage detail: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            return back()->withErrors(['error' => 'Failed to save damage detail: ' . $e->getMessage()])
                ->withInput();
        }
    }

    /**
     * Delete a damage detail
     */
    public function destroy(DamageDetail $damageDetail)
    {
        try {
            DB::beginTransaction();

            // Delete all associated images
            foreach ($damageDetail->images as $image) {
                if (Storage::disk('public')->exists($image->file_path)) {
                    Storage::disk('public')->delete($image->file_path);
                }
                $image->delete();
            }

            // Delete damage detail
            $damageDetail->delete();

            DB::commit();

            return back()->with('success', 'Damage detail deleted successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error deleting damage detail: ' . $e->getMessage());
            return back()->withErrors(['error' => 'Failed to delete damage detail: ' . $e->getMessage()]);
        }
    }

    /**
     * Delete a damage image
     */
    public function destroyImage(DamageImage $damageImage)
    {
        try {
            // Delete file from storage
            if (Storage::disk('public')->exists($damageImage->file_path)) {
                Storage::disk('public')->delete($damageImage->file_path);
            }

            // Delete record from database
            $damageImage->delete();

            return back()->with('success', 'Image deleted successfully.');
        } catch (\Exception $e) {
            Log::error('Error deleting image: ' . $e->getMessage());
            return back()->withErrors(['error' => 'Failed to delete image: ' . $e->getMessage()]);
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
