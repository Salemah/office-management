<?php

namespace App\Models\Inventory\Suppliers;

use App\Models\CRM\Address\City;
use App\Models\CRM\Address\State;
use App\Models\CRM\Address\Country;
use Illuminate\Database\Eloquent\Model;
use App\Models\Inventory\Area\InventoryArea;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class InventorySupplier extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'inventory_suppliers';
    protected $fillable = [
        'country_id',
        'state_id',
        'city_id',
        'area_id',
        'postal_code',
        'name',
        'company_name',
        'email',
        'phone',
        'tax_number',
        'contact_person',
        'address',
        'description',
        'status',
       ];

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
