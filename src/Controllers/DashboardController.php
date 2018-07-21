<?php

namespace AoQueue\Controllers;

use Illuminate\Routing\Controller;

class DashboardController extends Controller
{

    public function index()
    {
        return response()->json([
            'alex' => 'oliveira'
        ]);
    }

}