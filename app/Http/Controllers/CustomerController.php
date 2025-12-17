<?php

namespace App\Http\Controllers;

use App\Models\Contract;
use App\Models\State;
use App\Models\City;
use App\Models\BusinessFirm;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    /**
     * Display a listing of customers (from approved contracts).
     */
    public function index(Request $request)
    {
        $this->authorize('view customers');
        
        $query = Contract::with(['businessFirm', 'state', 'city', 'area', 'creator', 'approver'])
            ->where('approval_status', 'approved');
        
        // If user is not Admin or Super Admin, show only customers from contracts they created or contracts from their team members
        if (!auth()->user()->hasAnyRole(['Admin', 'Super Admin'])) {
            $teamMemberIds = \App\Models\User::where('created_by', auth()->id())->pluck('id')->toArray();
            $query->where(function($q) use ($teamMemberIds) {
                $q->where('created_by', auth()->id())
                  ->orWhereIn('created_by', $teamMemberIds);
            });
        }

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('contract_number', 'like', "%{$search}%")
                  ->orWhere('buyer_name', 'like', "%{$search}%")
                  ->orWhere('company_name', 'like', "%{$search}%")
                  ->orWhere('phone_number', 'like', "%{$search}%")
                  ->orWhere('phone_number_2', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhereHas('state', function($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%");
                  })
                  ->orWhereHas('city', function($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%");
                  })
                  ->orWhereHas('area', function($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%");
                  })
                  ->orWhereHas('businessFirm', function($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%");
                  });
            });
        }

        // Filter by state
        if ($request->filled('state_id')) {
            $query->where('state_id', $request->state_id);
        }

        // Filter by city
        if ($request->filled('city_id')) {
            $query->where('city_id', $request->city_id);
        }

        // Filter by business firm
        if ($request->filled('business_firm_id')) {
            $query->where('business_firm_id', $request->business_firm_id);
        }
        
        $customers = $query->orderBy('approved_at', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(20)
            ->withQueryString();

        $states = State::orderBy('name')->get();
        $businessFirms = BusinessFirm::orderBy('name')->get();
        $cities = $request->filled('state_id') 
            ? City::where('state_id', $request->state_id)->orderBy('name')->get()
            : collect([]);
        
        return view('customers.index', compact('customers', 'states', 'cities', 'businessFirms'));
    }

    /**
     * Remove the specified customer (contract) and all related data.
     */
    public function destroy(Contract $contract)
    {
        $this->authorize('delete customers');
        
        // Ensure this is an approved contract (customer)
        if ($contract->approval_status !== 'approved') {
            return redirect()->route('customers.index')
                ->with('error', 'Only approved contracts can be deleted as customers.');
        }

        // Delete all related contract machines
        $contract->contractMachines()->delete();

        // Delete the contract (customer)
        $contract->delete();

        return redirect()->route('customers.index')
            ->with('success', 'Customer and all related data deleted successfully.');
    }
}
