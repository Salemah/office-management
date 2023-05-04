<?php

namespace App\Models\Employee\Salary;

use App\Models\Employee\Allowance\Allowance;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Salary extends Model
{
    use HasFactory, SoftDeletes;
    protected $fillable = [
        'status'
    ];

    public function allowance(){
        $this->belongsTo(Allowance::class, 'allowance_id');
    }
}
