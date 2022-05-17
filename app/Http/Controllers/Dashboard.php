<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

class Dashboard extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return View
     */
    public function __invoke(): View
    {
        return view('dashboard');
    }
}
