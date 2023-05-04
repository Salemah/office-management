<?php

namespace App\Models\Employee;

use App\Models\Settings\Reference;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EmployeeReference extends Model
{
    use HasFactory, SoftDeletes;
    public function reference() {
        return $this->belongsTo(Reference::class, 'reference_id', 'id');
    }
    public function createdBy() {
        return $this->belongsTo(User::class, 'created_by');
    }

    // Relation With User Model
    public function updatedBy() {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
