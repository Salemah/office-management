<?php

namespace App\Models\Expense;

use App\Models\Account\Transaction;
use App\Models\Employee\Employee;
use App\Models\Inventory\Settings\InventoryWarehouse;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Expense extends Model
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
    public function expenseBy(){
        return $this->belongsTo(Employee::class,'expense_by')->withTrashed();
    }

    public function getDataForMonth($month, $year) {
        return $this->whereMonth('created_at', $month)
            ->whereYear('created_at', $year)
            ->get();
    }
    public function warehouse(){
        return $this->belongsTo(InventoryWarehouse::class,'warehouse_id');
    }
}
