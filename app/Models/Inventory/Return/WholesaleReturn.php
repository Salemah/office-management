<?php

namespace App\Models\Inventory\Return;

use App\Models\Inventory\Settings\InventoryWarehouse;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class WholesaleReturn extends Model
{
    use HasFactory,SoftDeletes;
    
    public function createdBy() {
        return $this->belongsTo(User::class, 'created_by');
    }

    // Relation With User Model
    public function updatedBy() {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function warehouse() {
        return $this->belongsTo(InventoryWarehouse::class, 'warehouse_id');
    }
}
