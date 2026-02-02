<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PRItems extends Model
{
     use HasFactory;
    protected $table = 'pr_items';
    protected $fillable = [
        'pr_head_id',
        'qty',
        'item_variant_code',
        'item_variant_id',
        'uom',
        'description',
        'wh_stocks',
        'date_needed',
    ];

    public function pr_head(){
        return $this->belongsTo(PRHead::class, 'pr_head_id');
    }
}
