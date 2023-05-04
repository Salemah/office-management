<?php

namespace App\Models\Inventory\Customers;
use App\Models\Inventory\Settings\InventoryWarehouse;
use App\Models\User;
use App\Models\CRM\Address\City;
use App\Models\CRM\Address\State;
use App\Models\CRM\Address\Country;
use App\Models\Inventory\Area\InventoryArea;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class InventoryCustomer extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'inventory_customers';
    protected $fillable = [
        'country_id',
        'warehouse_id',
        'state_id',
        'city_id',
        'area_id',
        'postal_code',
        'name',
        'email',
        'phone',
        'tax_number',
        'contact_person',
        'address',
        'description',
        'status',
       ];

    public function warehouse_rel()
    {
        return $this->belongsTo(InventoryWarehouse::class,'warehouse_id','id');
    }

    // Relation With User Model
    public function createdByUser() {
        return $this->belongsTo(User::class, 'created_by');
    }

    // Relation With User Model
    public function updatedByUser() {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function countries(): BelongsTo
    {
        return $this->belongsTo(Country::class, 'country_id', 'id')->withTrashed();
    }
    public function states(): BelongsTo
    {
        return $this->belongsTo(State::class, 'state_id', 'id')->withTrashed();
    }
    public function cities(): BelongsTo
    {
        return $this->belongsTo(City::class, 'city_id', 'id')->withTrashed();
    }
    public function areas(): BelongsTo
    {
        return $this->belongsTo(InventoryArea::class, 'area_id', 'id')->withTrashed();
    }
}
