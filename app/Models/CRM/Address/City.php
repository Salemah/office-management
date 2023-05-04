<?php

namespace App\Models\CRM\Address;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class City extends Model
{
    use HasFactory, SoftDeletes;
    public function states() {
        return $this->belongsTo(State::class, 'state_id')->withTrashed();
     }
}
