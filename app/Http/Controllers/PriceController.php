<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PriceRecord;

class PriceController extends Controller {
    // list latest approved price per chain for product
    public function listForProduct($productId) {
        $rows = PriceRecord::where('product_id',$productId)
            ->where('status','approved')
            ->orderByDesc('effective_at')
            ->get()
            ->groupBy('chain_id')
            ->map(fn($g)=> $g->first());
        return response()->json(array_values($rows->toArray()));
    }

    // store a price report (pending/approved depending on user role)
    public function store(Request $r) {
        $v = $r->validate([
            'product_id'=>'required|exists:products,id',
            'chain_id'=>'required|exists:retail_chains,id',
            'price_amount'=>'required|numeric|min:0',
            'currency'=>'required|string|size:3',
            'unit'=>'nullable|string',
            'effective_at'=>'nullable|date',
            'photo_url'=>'nullable|url',
            'notes'=>'nullable|string'
        ]);

        $price = PriceRecord::create([
            'product_id'=>$v['product_id'],
            'chain_id'=>$v['chain_id'],
            'price_amount'=>$v['price_amount'],
            'currency'=>$v['currency'],
            'unit'=>$v['unit'] ?? 'each',
            'effective_at'=>$v['effective_at'] ?? now(),
            'reported_by'=>$r->user()->id,
            'photo_url'=>$v['photo_url'] ?? null,
            'notes'=>$v['notes'] ?? null,
            'status' => $r->user()->role === 'trusted_collector' ? 'approved' : 'pending'
        ]);

        return response()->json(['price'=>$price],201);
    }
}