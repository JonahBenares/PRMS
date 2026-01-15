<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    use HasFactory;
    protected $table = 'company';
    protected $fillable = [
        'company_name',
        'company_code',
        'company_logo',
    ];

    public function companylocation(){
        return $this->hasMany(CompanyLocation::class, 'company_id');
    }
}
