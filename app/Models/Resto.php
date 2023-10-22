<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Resto extends Model
{
    use HasFactory;

    protected $table = 'table_resto';
    protected $primaryKey = 'id';
    protected $fillable = ['name','description','category_id','address','city_id','province_id','latitude','longitude'];

    public function category() {
        return $this->belongsTo('App\Models\Category', 'category_id');
    }

    public function city() {
        return $this->belongsTo('App\Models\City', 'city_id');
    }

    public function province() {
        return $this->belongsTo('App\Models\Province', 'province_id');
    }
}
