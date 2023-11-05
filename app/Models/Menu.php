<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Menu extends Model
{
    use HasFactory;

    protected $table = 'table_menu';
    protected $primaryKey = 'id';
    protected $fillable = ['name','photo','description','price','resto_id'];
    protected $hidden = ['photo','resto_id'];

    public function resto() {
        return $this->belongsTo('App\Models\Resto', 'resto_id');
    }
}
