<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Str;

class Product extends Model
{
    use HasFactory;
    public $incrementing = false;
    protected $keyType = 'string';
    protected $fillable = ['category_id', 'stock', 'min_stock', 'pcs_per_box', 'name', 'code'];
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function inDetails()
    {
        return $this->hasMany(InDetail::class);
    }

    public function outDetails()
    {
        return $this->hasMany(OutDetail::class);
    }
    protected static function boot()
{
    parent::boot();

    static::creating(function ($model) {
        if (empty($model->id)) {
            $model->id = (string) Str::uuid();
        }
    });
}

}
