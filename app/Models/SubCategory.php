<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subcategory extends Model
{
    use HasFactory;
    protected $table = 'sub_category';
    protected $fillable = [
        'category_id',
        'sub_cat_name',
    ];
    
    public function categories(){
        return $this->belongsTo(Category::class, 'category_id');
    }
}
