<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Spare extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'spare_type',
        'quantity',
        'image',
    ];

    protected $casts = [
        'quantity' => 'integer',
    ];

    public function sellers()
    {
        return $this->belongsToMany(Seller::class, 'spare_seller');
    }

    public function machineCategories()
    {
        return $this->belongsToMany(MachineCategory::class, 'spare_machine_category');
    }
}
