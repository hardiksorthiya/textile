<?php

namespace App\Http\Controllers;

use App\Models\Status;
use Illuminate\Http\Request;

class StatusController extends Controller
{
    public function index()
    {
        $statuses = Status::orderBy('name')->paginate(10);
        return view('statuses.index', compact('statuses'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:statuses,name',
        ]);

        Status::create([
            'name' => $request->name,
        ]);

        return redirect()->route('statuses.index')
            ->with('success', 'Status added successfully.');
    }

    public function update(Request $request, Status $status)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:statuses,name,' . $status->id,
        ]);

        $status->update([
            'name' => $request->name,
        ]);

        return redirect()->route('statuses.index')
            ->with('success', 'Status updated successfully.');
    }

    public function destroy(Status $status)
    {
        $status->delete();

        return redirect()->route('statuses.index')
            ->with('success', 'Status deleted successfully.');
    }
}
