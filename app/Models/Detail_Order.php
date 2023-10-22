<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Detail_Order extends Model
{
    use HasFactory;

    protected $table = 'detail_order';
    protected $primaryKey = 'id';
    protected $fillable = ['table_id', 'order_id'];
    
    public function table() {
        return $this->belongsTo('App\Models\Table', 'table_id');
    }

    public function order() {
        return $this->belongsTo('App\Models\Order', 'order_id');
    }
}
