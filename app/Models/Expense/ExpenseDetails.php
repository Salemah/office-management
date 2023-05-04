<?php

namespace App\Models\Expense;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ExpenseDetails extends Model
{
    use HasFactory,SoftDeletes;
    public function createdBy() {
        return $this->belongsTo(User::class, 'created_by');
    }
    // Relation With User Model
    public function updatedBy() {
        return $this->belongsTo(User::class, 'updated_by');
    }
    public function expense(){
        return $this->belongsTo(Expense::class,'expense_id')->withTrashed();
    }
    public function expenseCategory(){
        return $this->belongsTo(ExpenseCategory::class,'expense_category')->withTrashed();
    }
}
