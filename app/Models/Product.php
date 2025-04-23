<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $table = 'products'; // Ensure this matches the migration table name
    protected $primaryKey = 'product_id'; // Set the primary key if different from 'id'
    public $incrementing = true;

    protected $fillable = [
        'brand_id', // Add brand_id for mass assignment
        'category_id', // Add category_id for mass assignment
        'name',
        'description',
        'price',
        'sale_price',
        'images',
        'status',
        'quantity',
        'user_id',
    ];

    protected $casts = [
        'images' => 'array',
    ];

    public function brands()
    {
        return $this->belongsToMany(Brand::class, 'product_brand','product_id', 'brand_id');
    }

    public function categories()
    {
        return $this->belongsToMany(Category::class, 'product_category','product_id', 'category_id');
    }
   

}
