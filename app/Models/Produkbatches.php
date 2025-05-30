<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Produkbatches extends Model
{
    use SoftDeletes;
    use HasFactory;

    protected $table = 'produkbatches';
    protected $fillable = [
        'produks_id',
        'stok',
        'unitprice',
        'diskon',
        'status',
        'distributors_id',
        'gudangs_id',
        'tgl_kadaluarsa',
        'tgl_datang',
    ];

    public function distributor(): BelongsTo
    {
        return $this->belongsTo(Distributor::class, 'distributors_id');
    }

    public function gudang(): BelongsTo
    {
        return $this->belongsTo(Gudang::class, 'gudangs_id');
    }

    public function produks(): BelongsTo
    {
        return $this->belongsTo(produk::class, 'produks_id');
    }

    public function notaJualProduks()
    {
        return $this->hasMany(NotaJualProduk::class, 'produkbatches_id');
    }

    public function notaBeliProduks()
    {
        return $this->hasMany(NotaBeliProduk::class, 'produkbatches_id');
    }

    public function terimaBatches()
    {
        return $this->hasMany(Terimabatches::class, 'produkbatches_id');
    }
}
