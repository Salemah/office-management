<?php

namespace App\Models\Account;

use App\Models\Inventory\Settings\InventoryWarehouse;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BankAccount extends Model
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
    public function bank() {
        return $this->belongsTo(Bank::class, 'bank_id')->withTrashed();
    }

    public function warehouse_rel()
    {
        return $this->belongsTo(InventoryWarehouse::class,'warehouse','id');
    }
}
