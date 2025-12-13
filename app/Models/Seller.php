<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Seller extends Model
{
    use HasFactory;

    protected $fillable = [
        'country_id',
        'seller_name',
        'email',
        'mobile',
        'address',
        'pi_short_name',
        'gst_no',
        'signature',
    ];

    public function country()
    {
        return $this->belongsTo(Country::class);
    }

    public function machineCategories()
    {
        return $this->belongsToMany(MachineCategory::class, 'seller_machine_category');
    }

    public function bankDetails()
    {
        return $this->hasMany(SellerBankDetail::class);
    }
}
