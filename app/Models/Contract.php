<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Contract extends Model
{
    use HasFactory;

    protected $fillable = [
        'lead_id',
        'business_firm_id',
        'contract_number',
        'buyer_name',
        'company_name',
        'contact_address',
        'state_id',
        'city_id',
        'area_id',
        'email',
        'phone_number',
        'phone_number_2',
        'gst',
        'pan',
        'total_amount',
        'machine_details',
    ];

    protected $casts = [
        'machine_details' => 'array',
    ];

    public function lead()
    {
        return $this->belongsTo(Lead::class);
    }

    public function businessFirm()
    {
        return $this->belongsTo(BusinessFirm::class);
    }

    public function state()
    {
        return $this->belongsTo(State::class);
    }

    public function city()
    {
        return $this->belongsTo(City::class);
    }

    public function area()
    {
        return $this->belongsTo(Area::class);
    }

    public function contractMachines()
    {
        return $this->hasMany(ContractMachine::class);
    }
}
