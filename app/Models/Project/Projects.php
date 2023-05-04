<?php

namespace App\Models\Project;

use App\Models\CRM\Client\Client;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Projects extends Model
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
    public function projectCategory() {
        return $this->belongsTo(ProjectCategory::class, 'project_category');
    }
    public function projectDuration() {
        return $this->hasMany(ProjectDuration::class, 'project_id');
    }
    public function client() {
        return $this->hasMany(Client::class, 'client_id');
    }
}
