<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    use HasFactory;

    protected $fillable = [
        'logo',
        'favicon',
        'primary_color',
        'secondary_color',
        // Global Contract Details - Other Buyer Expenses
        'global_overseas_freight',
        'global_demurrage_detention_cfs_charges',
        'global_air_pipe_connection',
        'global_custom_duty',
        'global_port_expenses_transport',
        'global_crane_foundation',
        'global_humidification',
        'global_damage',
        'global_gst_custom_charges',
        'global_compressor',
        'global_optional_spares',
        'global_other_buyer_expenses_in_print',
        // Global Contract Details - Other Details
        'global_payment_terms',
        'global_quote_validity',
        'global_loading_terms',
        'global_warranty',
        'global_complimentary_spares',
        'global_other_details_in_print',
        // Global Contract Details - Difference of Specification
        'global_cam_jacquard_chain_jacquard',
        'global_hooks_5376_to_6144_jacquard',
        'global_warp_beam',
        'global_reed_space_380_to_420_cm',
        'global_color_selector_8_to_12',
        'global_hooks_5376_to_2688_jacquard',
        'global_extra_feeder',
        'global_difference_specification_in_print',
    ];

    protected $casts = [
        'global_other_buyer_expenses_in_print' => 'boolean',
        'global_other_details_in_print' => 'boolean',
        'global_difference_specification_in_print' => 'boolean',
    ];
}
