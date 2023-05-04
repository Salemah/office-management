<?php

namespace App\Models\Inventory\Sales;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Inventory\Customers\InventoryCustomer;
use App\Models\Inventory\Settings\InventoryWarehouse;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Sales extends Model
{
    use HasFactory, SoftDeletes;
    public function createdBy() {
        return $this->belongsTo(User::class, 'created_by');
    }

    // Relation With User Model
    public function updatedBy() {
        return $this->belongsTo(User::class, 'updated_by');
    }
    public function customers() {
        return $this->belongsTo(InventoryCustomer::class, 'customer_id');
    }
    public function getDataForMonth($month, $year) {
        return $this->whereMonth('created_at', $month)
            ->whereYear('created_at', $year)
            ->get();
    }
    
    public function warehouses():BelongsTo
    {
        return $this->belongsTo(InventoryWarehouse::class,'warehouse_id','id')->withTrashed();
    }
}
