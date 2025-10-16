<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PriceRecord extends Model {
    use HasFactory;
    public $incrementing = false;
    protected $keyType = 'string';
    protected $fillable = [
      'id','product_id','chain_id','price_amount','currency','unit',
      'effective_at','reported_by','status','source_type','photo_url','notes'
    ];

    protected $casts = ['effective_at'=>'datetime','price_amount'=>'decimal:2'];

    protected static function booted() {
        static::creating(fn($m)=> $m->id ??= (string) Str::uuid());
    }

    public function product() { return $this->belongsTo(Product::class); }
    public function chain() { return $this->belongsTo(RetailChain::class,'chain_id'); }
    public function reporter() { return $this->belongsTo(User::class,'reported_by'); }
}
