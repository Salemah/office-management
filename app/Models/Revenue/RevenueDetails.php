<?php

namespace App\Models\Revenue;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RevenueDetails extends Model
{
    use HasFactory,SoftDeletes;
    public function createdBy() {
        return $this->belongsTo(User::class, 'created_by');
    }
    // Relation With User Model
    public function updatedBy() {
        return $this->belongsTo(User::class, 'updated_by');
    }
    public function revenue(){
        return $this->belongsTo(Revenue::class,'revenue_id')->withTrashed();
    }
    public function revenueCategory(){
        return $this->belongsTo(RevenueCategory::class,'revenue_category')->withTrashed();
    }
}
