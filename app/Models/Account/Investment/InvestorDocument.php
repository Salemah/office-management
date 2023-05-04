<?php

namespace App\Models\Account\Investment;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class InvestorDocument extends Model
{
    use HasFactory,SoftDeletes;
}
