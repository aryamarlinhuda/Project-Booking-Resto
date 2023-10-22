<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Image extends Model
{
    use HasFactory;

    protected $table = 'image_resto';
    protected $primaryKey = 'id';
    protected $fillable = ['image','resto_id'];
    protected $hidden = ['image','resto_id'];

    public function resto() {
        return $this->belongsTo('App\Models\resto', 'resto_id');
    }
}
