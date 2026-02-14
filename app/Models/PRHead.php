<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PRHead extends Model
{
    use HasFactory;
    protected $table = 'pr_head';
    protected $fillable = [
        'company_id',
        'company_name',
        'pr_no',
        'location_id',
        'location_name',
        'date_prepared',
        'department_id',
        'department_name',
        'department_code',
        'requestor_id',
        'requestor_name',
        'urgency_id',
        'urgency_name',
        'purpose_id',
        'purpose_name',
        'enduse_id',
        'enduse_name',
        'notes',
        'status',
        'user_id',
    ];

    public function pr_items(){
        return $this->hasMany(PRItems::class, 'pr_head_id');
    }

    public function company()
    {
        return $this->belongsTo(Company::class, 'company_id');
    }
}
