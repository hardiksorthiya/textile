<?php

namespace App\Http\Controllers;

use App\Models\Business;
use Illuminate\Http\Request;

class BusinessController extends Controller
{
    public function index()
    {
        $businesses = Business::orderBy('name')->paginate(10);
        return view('businesses.index', compact('businesses'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:businesses,name',
        ]);

        Business::create([
            'name' => $request->name,
        ]);

        return redirect()->route('businesses.index')
            ->with('success', 'Business added successfully.');
    }

    public function update(Request $request, Business $business)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:businesses,name,' . $business->id,
        ]);

        $business->update([
            'name' => $request->name,
        ]);

        return redirect()->route('businesses.index')
            ->with('success', 'Business updated successfully.');
    }

    public function destroy(Business $business)
    {
        $business->delete();

        return redirect()->route('businesses.index')
            ->with('success', 'Business deleted successfully.');
    }
}
