<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\View\View;

class ClientProfileDashboardDemoController extends Controller
{
    public function __invoke(): View
    {
        return view('demo.client-profile-dashboard');
    }
}
