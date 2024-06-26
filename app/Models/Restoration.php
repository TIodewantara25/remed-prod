<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\softDeletes;

class Restoration extends Model
{
    use softdeletes;

    // protected $table = 'restoration';

    protected $fillable = [
        "usser_id",//table users kolom pk id
        "leding_id",
        "date_time",
        "total_good_stuff",
        "total_defec_stuff"
    ];

    
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function lending()
    {
        return $this->belongsTo(Lending::class);
    }
}
