<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class DamagedDetail extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'damaged_details';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'damaged_stock_id',
        'product_id',
        'quantity',
        'expiry_date',
        'in_stock_id',
        'in_detail_id',
        'information',
    ];

    public function damagedStock()
    {
        return $this->belongsTo(DamagedStock::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function inStock()
    {
        return $this->belongsTo(InStock::class);
    }

    public function inDetail()
    {
        return $this->belongsTo(InDetail::class);
    }

        protected static function boot()
{
    parent::boot();

    static::creating(function ($model) {
        if (empty($model->id)) {
            $model->id = (string) \Illuminate\Support\Str::uuid();
        }
    });
}
}
