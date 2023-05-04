<?php

namespace App\Models\Inventory\Purchase;

use App\Models\Inventory\Products\Products;
use App\Models\Inventory\Products\ProductVariant;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PriceManagement extends Model
{
    use HasFactory,SoftDeletes;
    public function createdBy() {
        return $this->belongsTo(User::class, 'created_by');
    }

    // Relation With User Model
    public function updatedBy() {
        return $this->belongsTo(User::class, 'updated_by');
    }
    public function products()
    {
    	return $this->belongsTo(Products::class,'product_id');
    }
    public function variants()
    {
    	return $this->belongsTo(ProductVariant::class,'variant_id');
    }
}
