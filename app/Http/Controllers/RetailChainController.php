<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\RetailChain;

class RetailChainController extends Controller {
    public function index() {
        return RetailChain::orderBy('name')->get();
    }
}