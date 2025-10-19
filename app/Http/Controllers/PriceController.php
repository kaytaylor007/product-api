<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PriceRecord;

class PriceController extends Controller {

    protected function authorizeModerator()
    {
        $user = auth()->user();
        if (!in_array($user->role, ['moderator', 'admin'])) {
            abort(403, 'Forbidden');
        }
    }

    public function pendingList()
    {
        $this->authorizeModerator();

        return PriceRecord::where('status', 'pending')
            ->with(['product:id,title,gtin', 'chain:id,name'])
            ->orderByDesc('created_at')
            ->paginate(20);
    }

    public function approve($id)
    {
        $this->authorizeModerator();

        $price = PriceRecord::findOrFail($id);
        $price->update([
            'status' => 'approved',
            'approved_by' => auth()->id(),
            'approved_at' => now(),
        ]);
        return response()->json(['message' => 'Approved', 'price' => $price]);
    }

    public function reject($id)
    {
        $this->authorizeModerator();

        $price = PriceRecord::findOrFail($id);
        $price->update(['status' => 'rejected']);
        return response()->json(['message' => 'Rejected', 'price' => $price]);
    }

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

        // $status = match($request->user()->role) {
        //     'trusted_collector', 'moderator', 'admin' => 'approved',
        //     default => 'pending',
        // };

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
            'status' => in_array($r->user()->role, ['trusted_collector','moderator','admin']) ? 'approved' : 'pending'
        ]);

        return response()->json(['price'=>$price],201);
    }
}