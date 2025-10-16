<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class RetailChain extends Model {
    use HasFactory;
    public $incrementing = false;
    protected $keyType = 'string';
    protected $fillable = ['id','name','website','country_code'];

    protected static function booted() {
        static::creating(fn($m)=> $m->id ??= (string) Str::uuid());
    }

    public function prices() { return $this->hasMany(PriceRecord::class,'chain_id'); }
}
