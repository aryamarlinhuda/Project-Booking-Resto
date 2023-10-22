<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Saved extends Model
{
    use HasFactory;

    protected $table = 'saved_resto';
    protected $primaryKey = 'id';
    protected $fillable = ['resto_id','saved_by'];

    protected $hidden = ['resto_id','saved_by'];

    public function resto() {
        return $this->belongsTo('App\Models\Resto', 'resto_id');
    }

    public function user() {
        return $this->belongsTo('App\Models\User', 'saved_by');
    }
}
