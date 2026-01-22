<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        // Kita hanya me-render view, data akan diambil via API client-side (Alpine.js)
        // agar dashboard terasa realtime dan cepat tanpa reload halaman.
        return view('dashboard');
    }

    public function history()
    {
        return view('history');
    }
}
