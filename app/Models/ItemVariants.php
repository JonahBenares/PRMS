<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ItemVariants extends Model
{
    use HasFactory;
    protected $table = 'item_Variants';
    protected $fillable = [
        'item_id',
        'variant_item_code',
        'brand',
        'type',
        'model',
        'part_no',
        'model',
        'size',
        'color',
        'material',
        'uom',
        'img1',
        'img2',
        'img3',
    ];

    public function items(){
        return $this->belongsTo(Items::class);
    }
}
