<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    use HasFactory;

    protected $table = 'table_review';
    protected $primaryKey = 'id';
    protected $fillable = ['rating','review','resto_id','created_by'];
    protected $hidden = ['resto_id','created_by','resto','user'];

    public function resto() {
        return $this->belongsTo('App\Models\Resto', 'resto_id');
    }

    public function user() {
        return $this->belongsTo('App\Models\User', 'created_by');
    }
}
