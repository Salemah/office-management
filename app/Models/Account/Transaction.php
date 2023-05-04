<?php

namespace App\Models\Account;

use App\Models\User;
use App\Models\Expense\Expense;
use App\Models\Account\BankAccount;
use App\Models\Inventory\Sales\Sales;
use Illuminate\Database\Eloquent\Model;
use App\Models\Account\Investment\Investor;
use App\Models\Inventory\Purchase\Purchase;
use App\Models\Inventory\Settings\InventoryWarehouse;
use App\Models\Inventory\Wholesale\Wholesale;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Transaction extends Model
{
    use HasFactory,SoftDeletes;

    public function createdByUser() {
        return $this->belongsTo(User::class, 'created_by');
    }
    // Relation With User Model
    public function updatedByUser() {
        return $this->belongsTo(User::class, 'updated_by');
    }
     // Relation With Bank-Account Model
     public function bankAccount() {
        return $this->belongsTo(BankAccount::class, 'account_id')->withTrashed();
    }
    public function investor() {
        return $this->belongsTo(Investor::class, 'investor_id', 'id');
    }
    public function expense() {
        return $this->belongsTo(Expense::class, 'expense_id', 'id');
    }

    public function purchases(): BelongsTo {
        return $this->belongsTo(Purchase::class, 'purchase_id', 'id')->withTrashed();
    }

    public function sales(): BelongsTo{
        return $this->belongsTo(Sales::class, 'sale_id', 'id')->withTrashed();
    }
    public function wholesales(): BelongsTo{
        return $this->belongsTo(Wholesale::class, 'whole_sale_id', 'id')->withTrashed();
    }
    public function warehouse(){
        return $this->belongsTo(InventoryWarehouse::class, 'warehouse_id', 'id');
    }

}
