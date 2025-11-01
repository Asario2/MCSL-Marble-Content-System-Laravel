<?php

namespace App\Http\Controllers;

use Inertia\Inertia;

class DashboardCustomerController extends Controller
{
    public function customer_index()
    {
        return redirect('/');
    }
     public function admin_profile(Request $request)
    {
        return Inertia::render('Admin/Profile', [
            'confirmsTwoFactorAuthentication' => auth()->user()->confirmsTwoFactorAuthentication,
            'sessions' => ApplicationController::sessions($request)->all(),
        ]);
    }
}
