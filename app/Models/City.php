<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class City extends Model
{
    use HasFactory;

    protected $table = 'table_city';
    Protected $primaryKey = 'id';
    protected $fillable = ['name','province_id'];
    protected $hidden = ['province_id','province_name'];

    public function province() {
        return $this->belongsTo('App\Models\Province', 'province_id');
    }
}
