<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $data['user'] = Auth::user();
        return view('dashboard', $data);
    }
}
