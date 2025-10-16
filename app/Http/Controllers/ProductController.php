<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\ProductImage;

class ProductController extends Controller {
    public function index(Request $r) {
        $q = Product::query();
        if ($r->query('q')) {
            $q->where('title','like','%'.$r->query('q').'%')
              ->orWhere('gtin','like','%'.$r->query('q').'%');
        }
        return $q->with('primaryImage')->paginate(20);
    }

    public function showByGtin(Request $r, $gtin) {
        $product = Product::where('gtin',$gtin)->with(['primaryImage','images'])->first();
        if (!$product) return response()->json(['exists'=>false],404);

        $ret = ['exists'=>true,'product'=>$product];
        if ($r->query('chain_id')) {
            $ret['latest_price_for_chain'] = $product->latestApprovedPriceForChain($r->query('chain_id'));
        }
        return response()->json($ret);
    }

    public function store(Request $r) {
        $validated = $r->validate([
            'gtin' => 'required|string|unique:products,gtin',
            'title' => 'required|string|max:150',
            'description' => 'nullable|string',
            'brand' => 'nullable|string',
            'gpc_cat_id' => 'nullable|string',
            'unspsc_cat_code' => 'nullable|string',
            'image_urls' => 'nullable|array'
        ]);

        $product = Product::create(array_merge($validated, ['created_by'=>$r->user()->id]));

        if (!empty($validated['image_urls'])) {
            foreach ($validated['image_urls'] as $idx=>$url) {
                ProductImage::create([
                  'product_id'=>$product->id,
                  'uploader_id'=>$r->user()->id,
                  'image_url'=>$url,
                  'primary'=> $idx===0 ? true : false
                ]);
            }
        }

        return response()->json(['product'=>$product],201);
    }

    public function update(Request $r,$id) {
        $product = Product::findOrFail($id);
        $validated = $r->validate([
            'title'=>'sometimes|string|max:150',
            'description'=>'nullable|string',
            'brand'=>'nullable|string',
            'gpc_cat_id'=>'nullable|string',
            'unspsc_cat_code'=>'nullable|string',
            'status'=>'nullable|in:pending,approved,rejected'
        ]);
        $product->update($validated);
        return response()->json(['product'=>$product]);
    }
}