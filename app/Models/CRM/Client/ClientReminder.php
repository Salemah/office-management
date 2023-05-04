<?php

namespace App\Models\CRM\Client;

use App\Models\User;
use App\Models\CRM\Client\Client;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ClientReminder extends Model
{
    use HasFactory, SoftDeletes;

    // Relation With User Model
    public function createdBy() {
        return $this->belongsTo(User::class, 'created_by');
    }

    // Relation With User Model
    public function updatedBy() {
        return $this->belongsTo(User::class, 'updated_by');
    }
    public function clients() {
        return $this->belongsTo(Client::class, 'client_id');
    }
}
