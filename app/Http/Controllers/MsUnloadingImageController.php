<?php

namespace App\Http\Controllers;

use App\Models\ProformaInvoice;
use App\Models\MsUnloadingImage;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class MsUnloadingImageController extends Controller
{
    /**
     * Display image uploading index page (list all PIs - with or without images)
     */
    public function index(Request $request)
    {
        $query = ProformaInvoice::with(['msUnloadingImages', 'contract.creator', 'creator', 'seller'])
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

        return view('ms-unloading-images.index', compact('proformaInvoices', 'salesManagers'));
    }

    /**
     * Show the form for uploading images for a proforma invoice
     */
    public function show(ProformaInvoice $proformaInvoice)
    {
        $proformaInvoice->load('msUnloadingImages');
        
        // Get existing images
        $existingImages = $proformaInvoice->msUnloadingImages;

        return view('ms-unloading-images.show', compact('proformaInvoice', 'existingImages'));
    }

    /**
     * Store uploaded images for a proforma invoice
     */
    public function store(Request $request, ProformaInvoice $proformaInvoice)
    {
        $request->validate([
            'images' => 'required|array',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif,webp|max:10240', // Max 10MB per image
        ]);

        try {
            DB::beginTransaction();

            // Handle image uploads
            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $image) {
                    if ($image->isValid()) {
                        $fileName = time() . '_' . uniqid() . '_' . $image->getClientOriginalName();
                        $filePath = $image->storeAs('ms-unloading-images/' . $proformaInvoice->id, $fileName, 'public');
                        
                        MsUnloadingImage::create([
                            'proforma_invoice_id' => $proformaInvoice->id,
                            'file_name' => $image->getClientOriginalName(),
                            'file_path' => $filePath,
                            'file_type' => $image->getMimeType(),
                            'file_size' => $image->getSize(),
                        ]);
                    }
                }
            }

            DB::commit();

            return redirect()->route('ms-unloading-images.show', $proformaInvoice)
                ->with('success', 'Images uploaded successfully.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            return back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error uploading images: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            return back()->withErrors(['error' => 'Failed to upload images: ' . $e->getMessage()])
                ->withInput();
        }
    }

    /**
     * Delete an uploaded image
     */
    public function destroy(MsUnloadingImage $msUnloadingImage)
    {
        try {
            // Delete file from storage
            if (Storage::disk('public')->exists($msUnloadingImage->file_path)) {
                Storage::disk('public')->delete($msUnloadingImage->file_path);
            }

            // Delete record from database
            $msUnloadingImage->delete();

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
