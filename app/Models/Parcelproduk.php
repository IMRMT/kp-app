<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Parcelproduk extends Model
{
    use SoftDeletes;
    use HasFactory;

    protected $table = 'parcelproduks';
    protected $fillable = [
        'parcels_id',
        'produks_id',
        'quantity',
    ];

    public function produk(): BelongsTo
    {
        return $this->belongsTo(produk::class, 'produks_id');
    }

    public function parcel(): BelongsTo
    {
        return $this->belongsTo(Parcel::class, 'parcels_id');
    }
}
