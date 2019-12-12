<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;

class ShowHome extends Controller
{
    public function __invoke(Request $request)
    {
        return Inertia::render('Home')->with(
            'user',
            $request->user()->loadMissing('latestTickets', 'latestRequestedChanges', 'agencies', 'accounts')
        );
    }
}
