<?php

namespace App\Models\Employee;

use App\Models\Inventory\Settings\InventoryWarehouse;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Employee extends Model
{
    use HasFactory, SoftDeletes;
    protected $fillable=['current_salary','gross_salary'];

    // Relation With User Model
    public function createdBy() {
        return $this->belongsTo(User::class, 'created_by');
    }

    // Relation With User Model
    public function updatedBy() {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function departments(): BelongsTo
    {
        return $this->belongsTo(Department::class,'department');
    }
    public function designations(): BelongsTo
    {
        return $this->belongsTo(Designation::class,'designation');
    }

    public function identities() {
        return $this->belongsTo(EmployeeIdentity::class, 'employee_id', 'id');
    }
    public function ware_house(){
        return $this->belongsTo(InventoryWarehouse::class,'warehouse','id');
    }
}
