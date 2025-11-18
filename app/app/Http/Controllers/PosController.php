<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class PosController extends Controller
{
    /**
     * Display the POS interface
     */
    public function index()
    {
        return view('pos.index');
    }
}
