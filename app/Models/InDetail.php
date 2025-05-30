<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Str;

class InDetail extends Model
{
    use HasFactory;
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
      'id',
      'in_stock_id',
      'product_id',
      'quantity',
      'expiry_date',
      'manufacturer_id',
      'remaining_stock',
    ];

    public function manufacturer()
    {
        return $this->belongsTo(Manufacturer::class);
    }
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function inStock()
    {
        return $this->belongsTo(InStock::class);
    }

    protected static function boot()
    {
        parent::boot();

  
        static::creating(function ($model) {
            $model->id = (string) Str::uuid();
        });

        static::created(function ($model) {
            $model->product->increment('stock', $model->quantity);
        });
    }
}