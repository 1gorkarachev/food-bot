<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Menu extends Model
{
    use SoftDeletes;

    protected $table = 'menu';

    protected $fillable = [
        'name',
        'price',
        'weight',
        'category',
        'slug',
    ];

    public function getMenuItemAttribute()
    {
        return "$this->name - $this->weight - $this->price руб.";
    }
}
