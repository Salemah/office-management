<?php

namespace App\Models\HRM\Settings;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Weekend extends Model
{
    use HasFactory, SoftDeletes;
    protected $fillable = ['status'];
}
