<?php

namespace App\Models\Inventory\Products;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class InventoryProductCategory extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'inventory_product_categories';

    // Relation With User Model
    public function createdByUser() {
        return $this->belongsTo(User::class, 'created_by');
    }

    // Relation With User Model
    public function updatedByUser() {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
