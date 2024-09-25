<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductBill extends Model
{
    use HasFactory;
    protected $fillable = [
        'bill_id',
        'price',
        'quantity',
        'feature_product_id',
        'created_at',
        'updated_at',
    ];
}
