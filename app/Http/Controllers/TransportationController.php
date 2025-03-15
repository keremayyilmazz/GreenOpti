<?php

namespace App\Http\Controllers;

use App\Models\Transportation;
use Illuminate\Http\Request;

class TransportationController extends Controller
{
    public function index()
    {
        $transportations = Transportation::all();
        return view('transportation.index', compact('transportations'));
    }

    public function create()
    {
        return view('transportation.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'cost_per_km' => 'required|numeric|min:0'
        ]);

        Transportation::create($validated);
        return redirect()->route('transportations.index')->with('success', 'Taşıma yöntemi başarıyla eklendi.');
    }

    public function edit(Transportation $transportation)
    {
        return view('transportation.edit', compact('transportation'));
    }

    public function update(Request $request, Transportation $transportation)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'cost_per_km' => 'required|numeric|min:0'
        ]);

        $transportation->update($validated);
        return redirect()->route('transportations.index')->with('success', 'Taşıma yöntemi başarıyla güncellendi.');
    }

    public function destroy(Transportation $transportation)
    {
        $transportation->delete();
        return redirect()->route('transportations.index')->with('success', 'Taşıma yöntemi başarıyla silindi.');
    }
}
