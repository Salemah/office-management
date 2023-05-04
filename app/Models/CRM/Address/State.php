<?php

namespace App\Models\CRM\Address;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class State extends Model
{
    use HasFactory,SoftDeletes;

    // Relation With Country Table
     public function district() {
       return $this->belongsTo(District::class, 'district_id')->withTrashed();
    }
     public function countries() {
       return $this->belongsTo(Country::class, 'country_id ')->withTrashed();
    }

    // Relation With User Model
    public function createdBy() {
        return $this->belongsTo(User::class, 'created_by');
    }

    // Relation With User Model
    public function updatedBy() {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
