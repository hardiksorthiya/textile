<?php

namespace App\Http\Controllers;

use App\Models\Country;
use Illuminate\Http\Request;

class CountryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $countries = Country::orderBy('name')->paginate(10);
        return view('countries.index', compact('countries'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:countries,name',
            'currency' => 'nullable|string|max:10',
        ]);

        $country = Country::create([
            'name' => $request->name,
            'currency' => $request->currency,
        ]);

        // Return JSON response for AJAX requests (from sellers page)
        if ($request->wantsJson() || $request->expectsJson()) {
            return response()->json([
                'success' => true,
                'country' => $country,
                'message' => 'Country added successfully.'
            ]);
        }

        // Redirect for form submissions (from countries index page)
        return redirect()->route('countries.index')
            ->with('success', 'Country added successfully.');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Country $country)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:countries,name,' . $country->id,
            'currency' => 'nullable|string|max:10',
        ]);

        $country->update([
            'name' => $request->name,
            'currency' => $request->currency,
        ]);

        return redirect()->route('countries.index')
            ->with('success', 'Country updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Country $country)
    {
        $country->delete();

        return redirect()->route('countries.index')
            ->with('success', 'Country deleted successfully.');
    }
}
