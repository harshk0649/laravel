<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Brand extends Model
{
    protected $fillable = [
        'name', // Add name to fillable properties
        'logo',
        'contact_mail',
        'brand_web',
        'joindate',
    ];
    use HasFactory;

    protected $table = 'brands';
    protected $primaryKey = 'brand_id';
    public $incrementing = true;


    public function products()
    {
        return $this->belongsToMany(Product::class, 'product_brand','product_id', 'brand_id');
    }
}
