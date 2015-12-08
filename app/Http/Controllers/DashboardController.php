<?php

namespace app\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Client;
use App\Product;

class DashboardController extends Controller
{
    public function index() {
        return view('index', [
            'products'=> Product::all(),
        ]);
    }
}
