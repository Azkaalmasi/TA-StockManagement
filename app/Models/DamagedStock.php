<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class DamagedStock extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'damaged_stocks';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'user_id',
        'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function damagedDetails()
    {
        return $this->hasMany(DamagedDetail::class);
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
