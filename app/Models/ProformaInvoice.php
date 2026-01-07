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

    public function deliveryDetails()
    {
        return $this->hasMany(PIDeliveryDetail::class)->orderBy('sort_order');
    }

    public function documents()
    {
        return $this->hasMany(PIDocument::class)->orderBy('row_number');
    }

    public function machineStatus()
    {
        return $this->hasOne(MachineStatus::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function preErectionDetails()
    {
        return $this->hasMany(PreErectionDetail::class)->orderBy('sort_order');
    }

    public function msUnloadingImages()
    {
        return $this->hasMany(MsUnloadingImage::class)->orderBy('created_at', 'desc');
    }

    public function damageDetails()
    {
        return $this->hasMany(DamageDetail::class)->orderBy('created_at', 'desc');
    }

    public function serialNumbers()
    {
        return $this->hasMany(SerialNumber::class)->orderBy('created_at', 'desc');
    }

    public function machineErectionDetails()
    {
        return $this->hasMany(MachineErectionDetail::class)->orderBy('machine_category_id')->orderBy('sort_order')->orderBy('machine_number');
    }

    public function iaFittingDetails()
    {
        return $this->hasMany(IAFittingDetail::class)->orderBy('machine_category_id')->orderBy('machine_number')->orderBy('sort_order');
    }
}
