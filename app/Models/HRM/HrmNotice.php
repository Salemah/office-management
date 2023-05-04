<?php

namespace App\Models\HRM;
use App\Models\User;
use App\Models\Employee\Employee;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class HrmNotice extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'hrm_notices';

    // Relation With User Model
    public function createdByUser() {
        return $this->belongsTo(User::class, 'created_by');
    }

    // Relation With User Model
    public function updatedByUser() {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function createdBy() {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function expenseBy(){
        return $this->belongsTo(Employee::class, 'employee_id')->withTrashed();
    }
}
