<?php

namespace App\Models\Account\Loan;

use App\Models\Account\Transaction;
use App\Models\Employee\Employee;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Loan extends Model
{
    use HasFactory,SoftDeletes;

    // Relation With User Model
    public function createdByUser() {
        return $this->belongsTo(User::class, 'created_by');
    }

    // Relation With User Model
    public function updatedByUser() {
        return $this->belongsTo(User::class, 'updated_by');
    }
	public function author(){
        return $this->belongsTo(Loan_Authority::class,'loan_author_id','id');
    }
    public function transaction(){
        return $this->hasMany(Transaction::class,'loan_id')->withTrashed();
    }
    public function negotiator(){
        return $this->belongsTo(Employee::class,'negotiator_id');
    }
}
