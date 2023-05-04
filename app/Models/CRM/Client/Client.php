<?php

namespace App\Models\CRM\Client;

use App\Models\CRM\Address\City;
use App\Models\CRM\Address\Country;
use App\Models\CRM\Address\State;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Client extends Model
{
    use HasFactory, SoftDeletes;

    // Relation With User Model
    public function createdBy() {
        return $this->belongsTo(User::class, 'created_by');
    }
    public function assignTo() {
        return $this->belongsTo(User::class, 'assign_to');
    }

    // Relation With User Model
    public function updatedBy() {
        return $this->belongsTo(User::class, 'updated_by');
    }
    public function comments() {
        return $this->hasMany(ClientComment::class);
    }
    public function reminders() {
        return $this->hasMany(ClientReminder::class);
    }
    public function contactThrough() {
        return $this->belongsTo(ContactThrough::class,'contact_through');
    }
    public function interestedOn() {
        return $this->belongsTo(InterestedOn::class,'interested_on');
    }
    public function	clientType() {
        return $this->belongsTo(ClientType::class,'client_type');
    }
    public function	busniessCategory() {
        return $this->belongsTo(ClientBusinessCategory::class,'client_business_category_id');
    }
    public function	country() {
        return $this->belongsTo(Country::class,'country_id');
    }
    public function	state() {
        return $this->belongsTo(State::class,'state_id');
    }
    public function	city() {
        return $this->belongsTo(City::class,'city_id');
    }
    public function	countryOther() {
        return $this->belongsTo(Country::class,'country_id_others');
    }
    public function	stateOther() {
        return $this->belongsTo(State::class,'state_id_others');
    }
    public function	cityOther() {
        return $this->belongsTo(City::class,'city_id_others');
    }
}
