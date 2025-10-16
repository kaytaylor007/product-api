<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Product extends Model {
    use HasFactory;
    public $incrementing = false;
    protected $keyType = 'string';
    protected $fillable = [
      'id','gtin','title','description','brand',
      'gpc_cat_id','gpc_cat_full_path',
      'unspsc_cat_code','unspsc_cat_full_path',
      'status','created_by'
    ];

    protected static function booted() {
        static::creating(fn($m) => $m->id ??= (string) Str::uuid());
    }

    public function creator() { return $this->belongsTo(User::class,'created_by'); }
    public function images() { return $this->hasMany(ProductImage::class); }
    public function primaryImage() { return $this->hasOne(ProductImage::class)->where('primary', true); }
    public function prices() { return $this->hasMany(PriceRecord::class); }

    // helper to get latest approved price for chain
    public function latestApprovedPriceForChain($chainId) {
        return $this->prices()->where('chain_id',$chainId)->where('status','approved')->orderByDesc('effective_at')->first();
    }
}
