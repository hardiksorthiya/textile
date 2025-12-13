<?php

namespace App\Http\Controllers;

use App\Models\State;
use Illuminate\Http\Request;

class StateController extends Controller
{
    public function index()
    {
        $states = State::orderBy('name')->paginate(10);
        return view('states.index', compact('states'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:states,name',
        ]);

        State::create([
            'name' => $request->name,
        ]);

        return redirect()->route('states.index')
            ->with('success', 'State added successfully.');
    }

    public function update(Request $request, State $state)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:states,name,' . $state->id,
        ]);

        $state->update([
            'name' => $request->name,
        ]);

        return redirect()->route('states.index')
            ->with('success', 'State updated successfully.');
    }

    public function destroy(State $state)
    {
        $state->delete();

        return redirect()->route('states.index')
            ->with('success', 'State deleted successfully.');
    }
}
