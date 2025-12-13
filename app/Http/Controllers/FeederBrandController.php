<?php

namespace App\Http\Controllers;

use App\Models\FeederBrand;
use Illuminate\Http\Request;

class FeederBrandController extends Controller
{
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:feeder_brands,name',
        ]);

        $feederBrand = FeederBrand::create([
            'name' => $request->name,
        ]);

        // Return JSON response for AJAX requests
        if ($request->wantsJson() || $request->expectsJson()) {
            return response()->json([
                'success' => true,
                'feederBrand' => $feederBrand,
                'message' => 'Feeder brand added successfully.'
            ]);
        }

        // Redirect for form submissions
        return redirect()->back()
            ->with('success', 'Feeder brand added successfully.');
    }
}
