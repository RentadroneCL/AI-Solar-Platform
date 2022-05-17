<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    /**
     * Create a new instance.
     */
    public function __construct()
    {
        $this->middleware('role_or_permission:administrator');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(): View
    {
        return view('users.index', [
            'users' => User::all()->except(['id' => Auth::id()]),
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     *
     * @return View
     */
    public function edit(int $id = null): View
    {
        return view('users.edit', [
            'user' => User::query()->with(['roles', 'sites'])->findOrFail($id),
        ]);
    }
}
