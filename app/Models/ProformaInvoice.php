<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProformaInvoice extends Model
{
    protected $fillable = [
        'contract_id',
        'seller_id',
        'proforma_invoice_number',
        'created_by',
        'total_amount',
        'notes',
        'type_of_sale',
        'currency',
        'usd_rate',
        'commission',
        'buyer_company_name',
        'pan',
        'gst',
        'phone_number',
        'phone_number_2',
        'ifc_certificate_number',
        'billing_address',
        'shipping_address',
        'overseas_freight',
        'port_expenses_clearing',
        'gst_percentage',
        'gst_amount',
        'final_amount_with_gst',
    ];

    protected $casts = [
        'total_amount' => 'decimal:2',
        'usd_rate' => 'decimal:2',
        'commission' => 'decimal:2',
        'overseas_freight' => 'decimal:2',
        'port_expenses_clearing' => 'decimal:2',
        'gst_percentage' => 'decimal:2',
        'gst_amount' => 'decimal:2',
        'final_amount_with_gst' => 'decimal:2',
    ];

    public function contract()
    {
        return $this->belongsTo(Contract::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function proformaInvoiceMachines()
    {
        return $this->hasMany(ProformaInvoiceMachine::class);
    }

    public function seller()
    {
        return $this->belongsTo(Seller::class);
    }
}
