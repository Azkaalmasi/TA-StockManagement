<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class OutDetail extends Model
{
    use HasFactory;
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'out_stock_id',
        'product_id',
        'quantity',
        'in_detail_id',
        'expiry_date',     
        'in_stock_id'      
    ];
    


    public function product()
    {
        return $this->belongsTo(Product::class);
    }
    public function inDetail()
    {
    return $this->belongsTo(InDetail::class);
    }

    public function outStock()
    {
        return $this->belongsTo(OutStock::class);
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
