<?php

namespace App\Models\Project;

use App\Models\Documents;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Task extends Model
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
    public function projectDuration() {
        return $this->belongsTo(ProjectDuration::class, 'task_type_id');
    }
    public function project() {
        return $this->belongsTo(Projects::class, 'task_type_id');
    }
    public function document() {
        return $this->hasMany(Documents::class, 'document_id');
    }

}
