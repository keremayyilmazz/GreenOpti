<?php

namespace App\Http\Controllers;

use App\Models\Factory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class FactoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $factories = Factory::all();
            return view('factories.index', compact('factories'));
        } catch (\Exception $e) {
            Log::error('Fabrika listeleme hatası:', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
            return back()->with('error', 'Fabrikalar listelenirken bir hata oluştu');
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            Log::info('Gelen fabrika verisi:', $request->all());

            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'latitude' => 'required|numeric|between:-90,90',
                'longitude' => 'required|numeric|between:-180,180',
            ]);

            $factory = Factory::create($validated);

            if ($request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Fabrika başarıyla eklendi',
                    'factory' => $factory
                ]);
            }

            return redirect()
                ->route('dashboard')
                ->with('success', 'Fabrika başarıyla eklendi');

        } catch (ValidationException $e) {
            Log::warning('Fabrika ekleme validasyon hatası:', [
                'errors' => $e->errors()
            ]);

            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validasyon hatası',
                    'errors' => $e->errors()
                ], 422);
            }

            return back()
                ->withErrors($e->errors())
                ->withInput();

        } catch (\Exception $e) {
            Log::error('Fabrika ekleme hatası:', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);

            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Fabrika eklenirken bir hata oluştu'
                ], 500);
            }

            return back()
                ->with('error', 'Fabrika eklenirken bir hata oluştu')
                ->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Factory $factory)
    {
        try {
            return view('factories.show', compact('factory'));
        } catch (\Exception $e) {
            Log::error('Fabrika görüntüleme hatası:', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
            return back()->with('error', 'Fabrika görüntülenirken bir hata oluştu');
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Factory $factory)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'latitude' => 'required|numeric|between:-90,90',
                'longitude' => 'required|numeric|between:-180,180',
            ]);

            $factory->update($validated);

            if ($request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Fabrika başarıyla güncellendi',
                    'factory' => $factory
                ]);
            }

            return redirect()
                ->route('dashboard')
                ->with('success', 'Fabrika başarıyla güncellendi');

        } catch (ValidationException $e) {
            Log::warning('Fabrika güncelleme validasyon hatası:', [
                'errors' => $e->errors()
            ]);

            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validasyon hatası',
                    'errors' => $e->errors()
                ], 422);
            }

            return back()
                ->withErrors($e->errors())
                ->withInput();

        } catch (\Exception $e) {
            Log::error('Fabrika güncelleme hatası:', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);

            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Fabrika güncellenirken bir hata oluştu'
                ], 500);
            }

            return back()
                ->with('error', 'Fabrika güncellenirken bir hata oluştu')
                ->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Factory $factory)
    {
        try {
            $factory->delete();

            if (request()->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Fabrika başarıyla silindi'
                ]);
            }

            return redirect()
                ->route('dashboard')
                ->with('success', 'Fabrika başarıyla silindi');

        } catch (\Exception $e) {
            Log::error('Fabrika silme hatası:', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);

            if (request()->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Fabrika silinirken bir hata oluştu'
                ], 500);
            }

            return back()->with('error', 'Fabrika silinirken bir hata oluştu');
        }
    }
}