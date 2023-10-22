<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $table = 'table_order';
    protected $primaryKey = 'id';
    protected $fillable = ['order_id','name','resto_id','total_table','total_price','ordered_by'];

    public function resto() {
        return $this->belongsTo('App\Models\Resto', 'resto_id');
    }

    public function user() {
        return $this->belongsTo('App\Models\User', 'ordered_by');
    }
}
