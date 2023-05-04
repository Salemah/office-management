<?php

namespace App\Models\Account\Investment;

use App\Models\Identity;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class InvestorIdentity extends Model
{
    use HasFactory,SoftDeletes;
    // Relation With User Model
    public function createdBy() {
        return $this->belongsTo(User::class, 'created_by');
    }

    // Relation With User Model
    public function updatedBy() {
        return $this->belongsTo(User::class, 'updated_by');
    }

    // Relation With User Model
    public function investor() {
        return $this->belongsTo(Investor::class, 'investor_id', 'id');
    }
    public function identity() {
        return $this->belongsTo(Identity::class, 'id_type_id', 'id');
    }
}
