<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\softDeletes;

class Stuff extends Model
{
    use softDeletes;//optimal digunakan hanya untuk table yang mengunakan fitur softdeletes
    protected $fillable = ["name","category"];

    //mendifinisikan relasi
    //table yang berperan sebasgai Primary Key : hasOne / hasMany / ..
    //table yang berperan sebagai Foreign Key : belongsto
    //nama Functon disarankan mengunakan aturan berikut:
    //1. one to one : nama model yang terhubung versi tunggal
    //2. one to many : nama model yang terhubung versi jama (untuk forseign key nya)

    public function StuffStock()
    {
        return $this->hasone(StuffStock::class);  
    }

    public function inboundStuffs()
    {
        return $this->hasMany(inboundStuff::class); 
    }

    public function stuff(){
        return $this->belongsTo(Stuff::class);
    }

}
