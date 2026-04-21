<?php

namespace App\Http\Controllers;

use App\Models\LandingPage;
use Illuminate\View\View;

class LandingPageController extends Controller
{
    public function __invoke(): View
    {
        return view('landing', [
            'page' => LandingPage::home(),
        ]);
    }
}
