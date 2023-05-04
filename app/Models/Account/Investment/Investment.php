<?php

namespace App\Models\Account\Investment;

use App\Models\Account\Transaction;
use App\Models\Employee\Employee;
use App\Models\Inventory\Settings\InventoryWarehouse;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Investment extends Model
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
    public function investor()
    {
        return $this->belongsTo(Investor::class,"investor_id");
    }
    public function transaction(){
        return $this->hasMany(Transaction::class,'investment_id')->withTrashed();
    }
    public function negotiator(){
        return $this->belongsTo(Employee::class,'negotiator_id');
    }
    public function warehouse(){
        return $this->belongsTo(InventoryWarehouse::class,'warehouse_id');
    }
}
