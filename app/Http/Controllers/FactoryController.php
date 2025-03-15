<?php

namespace App\Http\Controllers;

use App\Models\Factory;
use Illuminate\Http\Request;

class FactoryController extends Controller
{
    public function index()
    {
        $factories = Factory::where('user_id', auth()->id())->get();
        return response()->json($factories);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
        ]);

        $factory = Factory::create([
            'name' => $validated['name'],
            'latitude' => $validated['latitude'],
            'longitude' => $validated['longitude'],
            'user_id' => auth()->id()
        ]);

        return response()->json($factory);
    }

    public function show(Factory $factory)
    {
        // Kullanıcının kendi fabrikasını görüntülemesini sağla
        if ($factory->user_id !== auth()->id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }
        return response()->json($factory);
    }

    public function update(Request $request, Factory $factory)
    {
        // Kullanıcının kendi fabrikasını güncellemesini sağla
        if ($factory->user_id !== auth()->id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
        ]);

        $factory->update($validated);
        return response()->json($factory);
    }

    public function destroy(Factory $factory)
    {
        // Kullanıcının kendi fabrikasını silmesini sağla
        if ($factory->user_id !== auth()->id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $factory->delete();
        return response()->json(['message' => 'Factory deleted successfully']);
    }

    public function mapData()
    {
        $factories = Factory::where('user_id', auth()->id())->get();
        return response()->json($factories);
    }

    public function updateLocation(Request $request, Factory $factory)
    {
        // Kullanıcının kendi fabrikasının konumunu güncellemesini sağla
        if ($factory->user_id !== auth()->id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
        ]);

        $factory->update($validated);
        return response()->json($factory);
    }
}