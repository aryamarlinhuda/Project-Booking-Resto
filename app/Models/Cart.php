<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    use HasFactory;

    protected $table = 'table_cart';
    protected $primaryKey = 'id';
    protected $fillable = ['resto_id','total_table','total_price','carted_by'];

    public function resto() {
        return $this->belongsTo('App\Models\Resto', 'resto_id');
    }

    public function user() {
        return $this->belongsTo('App\Models\User', 'ordered_by');
    }
}
