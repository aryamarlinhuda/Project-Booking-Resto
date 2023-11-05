<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Table extends Model
{
    use HasFactory;

    protected $table = 'resto_table';
    protected $primaryKey = 'id';
    protected $fillable = ['name','description','laod','price','ordered','resto_id'];
    protected $hidden =['ordered','resto_id'];

    public function resto() {
        return $this->belongsTo('App\Models\Resto', 'resto_id');
    }

    public function ordered() {
        return $this->belongsTo('App\Models\Order', 'ordered');
    }
}
