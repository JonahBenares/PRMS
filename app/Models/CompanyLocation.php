<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CompanyLocation extends Model
{
    use HasFactory;
    protected $table = 'company_location';
    protected $fillable = [
        'company_id',
        'location',
        'address',
        'telephone',
        'telefax',
    ];
    
    public function company(){
        return $this->belongsTo(Company::class, 'company_id');
    }
}
