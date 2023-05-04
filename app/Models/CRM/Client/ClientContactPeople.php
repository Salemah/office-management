<?php

namespace App\Models\CRM\Client;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ClientContactPeople extends Model
{
    use HasFactory,SoftDeletes;

      // Relation With User Model
      public function createdBy() {
        return $this->belongsTo(User::class, 'created_by');
    }
    public function assignTo() {
        return $this->belongsTo(User::class, 'assign_to');
    }
}
