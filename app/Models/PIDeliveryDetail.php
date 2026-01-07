<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PIDeliveryDetail extends Model
{
    protected $table = 'pi_delivery_details';

    protected $fillable = [
        'proforma_invoice_id',
        'document_name',
        'date',
        'number',
        'no_of_copies',
        'is_received',
        'sort_order',
    ];

    protected $casts = [
        'date' => 'date',
        'no_of_copies' => 'integer',
        'is_received' => 'boolean',
        'sort_order' => 'integer',
    ];

    public function proformaInvoice()
    {
        return $this->belongsTo(ProformaInvoice::class);
    }

    /**
     * Accessor for document_number (maps to number column)
     */
    public function getDocumentNumberAttribute()
    {
        return $this->attributes['number'] ?? null;
    }

    /**
     * Mutator for document_number (maps to number column)
     */
    public function setDocumentNumberAttribute($value)
    {
        $this->attributes['number'] = $value;
    }
}
