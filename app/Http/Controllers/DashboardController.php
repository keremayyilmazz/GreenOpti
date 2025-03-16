<?php

namespace App\Http\Controllers;

use App\Models\Factory;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $factories = Factory::all();
        return view('dashboard', compact('factories'));
    }
}