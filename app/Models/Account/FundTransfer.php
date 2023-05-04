<?php

namespace App\Models\Account;

use App\Models\User;
use App\Models\Account\Transaction;
use App\Models\Inventory\Settings\InventoryWarehouse;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class FundTransfer extends Model
{
    use HasFactory,SoftDeletes;
    public function createdBy(){
        return $this->belongsTo(User::class, 'created_by');
    }
    // Relation With User Model
    public function updatedBy(){
        return $this->belongsTo(User::class, 'updated_by');
    }
    // Relation With cash-in Bank-Account Model
    public function cashInBankAccount(){
        return $this->belongsTo(BankAccount::class, 'cash_in_account')->withTrashed();
    }
    // Relation With cash-out Bank-Account Model
    public function cashOutBankAccount(){
        return $this->belongsTo(BankAccount::class, 'cash_out_account')->withTrashed();
    }
    public function balanceTransfer(){
        return $this->hasOne(Transaction::class, 'id','cash_in_transaction')->withTrashed();
    }
     public function warehouses()
    {
        return $this->belongsTo(InventoryWarehouse::class,'warehouse_id','id')->withTrashed();
    }
}
