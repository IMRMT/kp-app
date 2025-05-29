<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Produk extends Model
{
    use SoftDeletes;
    use HasFactory;

    public function produkbatches(): HasMany
    {
        return $this->hasMany(Produkbatches::class, 'produks_id');
    }

    public function parcelProduks(): HasMany
    {
        return $this->hasMany(Parcelproduk::class, 'produks_id');
    }

    public function satuan(): BelongsTo
    {
        return $this->belongsTo(Satuan::class, 'satuans_id');
    }

    public function tipeProduk(): BelongsTo
    {
        return $this->belongsTo(TipeProduk::class, 'tipe_produks_id');
    }
}
