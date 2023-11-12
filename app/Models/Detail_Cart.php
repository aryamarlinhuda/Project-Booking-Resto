<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Detail_Cart extends Model
{
    use HasFactory;

    protected $table = 'detail_cart';
    protected $primaryKey = 'id';
    protected $fillable = ['table_id','cart_id'];
    protected $hidden = ['table_id','cart_id'];
    
    public function table() {
        return $this->belongsTo('App\Models\Table', 'table_id');
    }

    public function cart() {
        return $this->belongsTo('App\Models\Cart', 'cart_id');
    }
}
