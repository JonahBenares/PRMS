<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Items extends Model
{
    use HasFactory;
    protected $table = 'items';
    protected $fillable = [
        'item_code',
        'category_id',
        'category_name',
        'sub_cat_id',
        'sub_cat_name',
        'item_description',
    ];

    public function item_Variants(){
            return $this->hasMany(ItemVariants::class, 'item_id');
    }

}
