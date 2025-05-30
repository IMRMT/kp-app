<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Notajual extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['pegawai_id','jenis_pembayaran'];

    /**
     * Get the user that owns the Transaction
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(user::class, 'pegawai_id');
    }

    /**
     * Get the user that customer the Transaction
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */


    public function notaJualProduks()
    {
        return $this->hasMany(NotaJualProduk::class, 'notajuals_id');
    }

    public function notaJualParcels()
    {
        return $this->hasMany(NotajualParcel::class, 'notajuals_id');
    }
}
