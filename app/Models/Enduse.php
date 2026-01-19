<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Enduse extends Model
{
    use HasFactory;
    protected $table = 'enduses';
    protected $fillable = ['enduse_name'];
}
