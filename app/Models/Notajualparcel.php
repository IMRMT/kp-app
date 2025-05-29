<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Notajualparcel extends Model
{
    use HasFactory, SoftDeletes;
    
    protected $table = 'notajuals_has_parcels';
    protected $fillable = [
        'notajuals_id',
        'parcels_id',
        'quantity',
        'subtotal',
    ];

    public function parcel()
    {
        return $this->belongsTo(Parcel::class, 'parcels_id');
    }

    public function notajual()
    {
        return $this->belongsTo(Notajual::class, 'notajuals_id');
    }
}
