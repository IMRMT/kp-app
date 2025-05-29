<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Tipeuser extends Model
{
    use SoftDeletes;
    use HasFactory;

    protected $table = 'tipe_users';
    protected $fillable = [
        'tipe',
        'deskripsi'
    ];

    public function user(): HasMany
    {
        return $this->hasMany(User::class);
    }
}
