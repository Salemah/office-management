<?php

namespace App\Models\Employee\Salary;

use App\Models\Employee\Employee;
use App\Models\Inventory\Settings\InventoryWarehouse;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SalaryGenerate extends Model
{
    use HasFactory, SoftDeletes;

    public function employee()
    {
        return $this->belongsTo(Employee::class,'employee_id','id')->withTrashed();
    }

    public function warehouse_relation()
    {
        return $this->belongsTo(InventoryWarehouse::class,'warehouse','id')->withTrashed();
    }

    public function getDataForMonth($month, $year) {
        return $this->whereMonth('created_at', $month)
            ->whereYear('created_at', $year)
            ->get();
    }
}
