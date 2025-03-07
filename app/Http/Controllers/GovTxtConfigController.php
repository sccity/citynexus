<?php

namespace App\Http\Controllers;

use App\Models\GovTxtAutoResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;

class GovTxtConfigController extends Controller
{
    public function index()
    {
        $autoResponses = GovTxtAutoResponse::orderBy('name')->get();
        
        return Inertia::render('GovTxt/Config', [
            'autoResponses' => $autoResponses
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'terms' => 'required|string|max:255',
            'response' => 'required|string',
            'active' => 'required|boolean'
        ]);

        GovTxtAutoResponse::create($validated);

        return redirect()->back()->with('success', 'Auto response created successfully.');
    }

    public function update(Request $request, GovTxtAutoResponse $autoResponse)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'terms' => 'required|string|max:255',
            'response' => 'required|string',
            'active' => 'required|boolean'
        ]);

        $autoResponse->update($validated);

        return redirect()->back()->with('success', 'Auto response updated successfully.');
    }

    public function destroy(GovTxtAutoResponse $autoResponse)
    {
        $autoResponse->delete();

        return redirect()->back()->with('success', 'Auto response deleted successfully.');
    }
} 