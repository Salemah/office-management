<?php

namespace App\Models\Employee\Salary;

use App\Models\Account\BankAccount;
use App\Models\Employee\Employee;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SalaryReport extends Model
{
    use HasFactory,SoftDeletes;

    public function employee()
    {
        return $this->belongsTo(Employee::class,'employee_id','id')->withTrashed();
    }

    public function account()
    {
        return $this->belongsTo(BankAccount::class,'account_id','id')->withTrashed();
    }
}
