<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class Contract extends Model
{
    protected $fillable = [
        'lead_id',
        'created_by',
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
        'token_amount',
        'machine_details',
        // Other Buyer Expenses Details
        'overseas_freight',
        'demurrage_detention_cfs_charges',
        'air_pipe_connection',
        'custom_duty',
        'port_expenses_transport',
        'crane_foundation',
        'humidification',
        'damage',
        'gst_custom_charges',
        'compressor',
        'optional_spares',
        'other_buyer_expenses_in_print',
        // Other Details
        'payment_terms',
        'quote_validity',
        'loading_terms',
        'warranty',
        'complimentary_spares',
        'other_details_in_print',
        // Difference of Specification
        'cam_jacquard_chain_jacquard',
        'hooks_5376_to_6144_jacquard',
        'warp_beam',
        'reed_space_380_to_420_cm',
        'color_selector_8_to_12',
        'hooks_5376_to_2688_jacquard',
        'extra_feeder',
        'difference_specification_in_print',
        'customer_signature',
        'approval_status',
        'approved_by',
        'approved_at',
        'approval_notes',
    ];

    protected $casts = [
        'machine_details' => 'array',
        'other_buyer_expenses_in_print' => 'boolean',
        'other_details_in_print' => 'boolean',
        'difference_specification_in_print' => 'boolean',
        'approved_at' => 'datetime',
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

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function machineStatus()
    {
        return $this->hasOne(MachineStatus::class);
    }

    public function proformaInvoices()
    {
        return $this->hasMany(ProformaInvoice::class);
    }
}
