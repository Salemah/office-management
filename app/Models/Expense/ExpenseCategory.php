<?php

namespace App\Models\Expense;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ExpenseCategory extends Model
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
}
