<?php

namespace App\Models\Inventory\Products;

use App\Models\Inventory\Settings\InventoryBrand;
use App\Models\Inventory\Settings\InventoryUnit;
use App\Models\Inventory\Settings\Taxes;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Products extends Model
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
    public function category() {
        return $this->belongsTo(InventoryProductCategory::class, 'category_id');
    }
    public function brands() {
        return $this->belongsTo(InventoryBrand::class, 'brand_id');
    }
    public function units() {
        return $this->belongsTo(InventoryUnit::class, 'unit_id');
    }
    public function taxs() {
        return $this->belongsTo(Taxes::class, 'tax_id');
    }
    public function productVarients() {
        return $this->hasMany(ProductVariant::class, 'product_id');
    }
}
