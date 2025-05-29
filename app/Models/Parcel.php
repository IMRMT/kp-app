<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Parcel extends Model
{
    use SoftDeletes;
    use HasFactory;

    public function parcelProduks()
    {
        return $this->hasMany(Parcelproduk::class, 'parcels_id');
    }

    public function notaJualParcels()
    {
        return $this->hasMany(Notajualparcel::class, 'parcels_id');
    }

    public function produks()
    {
        return $this->belongsToMany(
            Produk::class,
            'parcelproduks',    
            'parcels_id',       
            'produks_id'         
        )
            ->withPivot('quantity')
            ->withTimestamps();
    }
}
