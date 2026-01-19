<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    use HasFactory;

    protected $table = 'items';

    protected $fillable = [
        'sku',
        'name',
        'description',
        'unit',
        'current_stock',
        'cost_price',
        'min_stock_manual',
        'created_at',
        'updated_at',
    ];



}
