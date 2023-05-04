<?php

namespace App\Models\Inventory\Products;

use App\Models\Inventory\Purchase\Purchase;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Inventory\Settings\InventoryWarehouse;
use App\Models\Inventory\Suppliers\InventorySupplier;
use App\Models\Inventory\Wholesale\Wholesale;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class InventoryProductCount extends Model
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
    public function suppliers() {
        return $this->belongsTo(InventorySupplier::class, 'supplier_id');
    }
    public function products() {
        return $this->belongsTo(Products::class, 'product_id');
    }
    public function variant() {
        return $this->belongsTo(ProductVariant::class, 'variant_id');
    }
    public function warehouse() {
        return $this->belongsTo(InventoryWarehouse::class, 'warehouse_id');
    }

    public function purchase(): BelongsTo
    {
        return $this->belongsTo(Purchase::class, 'purchase_id', 'id')->withTrashed();
    }
    public function wholesales(): BelongsTo
    {
        return $this->belongsTo(Wholesale::class, 'whole_sale_id', 'id')->withTrashed();
    }
}
