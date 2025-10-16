<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ProductImage extends Model {
    use HasFactory;
    public $incrementing = false;
    protected $keyType = 'string';
    protected $fillable = ['id','product_id','uploader_id','image_url','thumb_url','approved','primary'];

    protected static function booted() {
        static::creating(fn($m) => $m->id ??= (string) Str::uuid());
    }

    public function product() { return $this->belongsTo(Product::class); }
    public function uploader() { return $this->belongsTo(User::class,'uploader_id'); }
}
