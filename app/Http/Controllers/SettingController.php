<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SettingController extends Controller
{
    /**
     * Show the settings page.
     */
    public function edit()
    {
        $setting = Setting::firstOrCreate(
            ['id' => 1],
            [
                'primary_color' => '#9f2323',
                'secondary_color' => '#e80202',
            ]
        );

        return view('admin.settings', compact('setting'));
    }

    /**
     * Update branding and theme settings.
     */
    public function update(Request $request)
    {
        $setting = Setting::firstOrCreate(
            ['id' => 1],
            [
                'primary_color' => '#9f2323',
                'secondary_color' => '#e80202',
            ]
        );

        $request->validate([
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:4096',
            'favicon' => 'nullable|image|mimes:jpeg,png,jpg,gif,ico,svg|max:2048',
            'primary_color' => ['required', 'string', 'regex:/^#([A-Fa-f0-9]{3}|[A-Fa-f0-9]{6})$/'],
            'secondary_color' => ['required', 'string', 'regex:/^#([A-Fa-f0-9]{3}|[A-Fa-f0-9]{6})$/'],
        ]);

        $data = [
            'primary_color' => $request->primary_color,
            'secondary_color' => $request->secondary_color,
        ];

        if ($request->hasFile('logo')) {
            if ($setting->logo) {
                Storage::disk('public')->delete($setting->logo);
            }

            $data['logo'] = $request->file('logo')->store('branding', 'public');
        }

        if ($request->hasFile('favicon')) {
            if ($setting->favicon) {
                Storage::disk('public')->delete($setting->favicon);
            }

            $data['favicon'] = $request->file('favicon')->store('branding', 'public');
        }

        $setting->update($data);

        return redirect()->route('settings.edit')->with('success', 'Settings updated successfully.');
    }

    /**
     * Show the global contract details settings page.
     */
    public function contractDetails()
    {
        $setting = Setting::firstOrCreate(['id' => 1]);
        return view('admin.contract-details-settings', compact('setting'));
    }

    /**
     * Update global contract details settings.
     */
    public function updateContractDetails(Request $request)
    {
        $setting = Setting::firstOrCreate(['id' => 1]);

        $request->validate([
            // Other Buyer Expenses Details
            'global_overseas_freight' => 'nullable|string|max:255',
            'global_demurrage_detention_cfs_charges' => 'nullable|string|max:255',
            'global_air_pipe_connection' => 'nullable|string|max:255',
            'global_custom_duty' => 'nullable|string|max:255',
            'global_port_expenses_transport' => 'nullable|string|max:255',
            'global_crane_foundation' => 'nullable|string|max:255',
            'global_humidification' => 'nullable|string|max:255',
            'global_damage' => 'nullable|string|max:255',
            'global_gst_custom_charges' => 'nullable|string|max:255',
            'global_compressor' => 'nullable|string|max:255',
            'global_optional_spares' => 'nullable|string|max:255',
            'global_other_buyer_expenses_in_print' => 'nullable|boolean',
            // Other Details
            'global_payment_terms' => 'nullable|string|max:255',
            'global_quote_validity' => 'nullable|string|max:255',
            'global_loading_terms' => 'nullable|string|max:255',
            'global_warranty' => 'nullable|string|max:255',
            'global_complimentary_spares' => 'nullable|string|max:255',
            'global_other_details_in_print' => 'nullable|boolean',
            // Difference of Specification
            'global_cam_jacquard_chain_jacquard' => 'nullable|string|max:255',
            'global_hooks_5376_to_6144_jacquard' => 'nullable|string|max:255',
            'global_warp_beam' => 'nullable|string|max:255',
            'global_reed_space_380_to_420_cm' => 'nullable|string|max:255',
            'global_color_selector_8_to_12' => 'nullable|string|max:255',
            'global_hooks_5376_to_2688_jacquard' => 'nullable|string|max:255',
            'global_extra_feeder' => 'nullable|string|max:255',
            'global_difference_specification_in_print' => 'nullable|boolean',
        ]);

        $setting->update([
            // Other Buyer Expenses Details
            'global_overseas_freight' => $request->global_overseas_freight,
            'global_demurrage_detention_cfs_charges' => $request->global_demurrage_detention_cfs_charges,
            'global_air_pipe_connection' => $request->global_air_pipe_connection,
            'global_custom_duty' => $request->global_custom_duty,
            'global_port_expenses_transport' => $request->global_port_expenses_transport,
            'global_crane_foundation' => $request->global_crane_foundation,
            'global_humidification' => $request->global_humidification,
            'global_damage' => $request->global_damage,
            'global_gst_custom_charges' => $request->global_gst_custom_charges,
            'global_compressor' => $request->global_compressor,
            'global_optional_spares' => $request->global_optional_spares,
            'global_other_buyer_expenses_in_print' => $request->has('global_other_buyer_expenses_in_print') ? (bool)$request->global_other_buyer_expenses_in_print : true,
            // Other Details
            'global_payment_terms' => $request->global_payment_terms,
            'global_quote_validity' => $request->global_quote_validity,
            'global_loading_terms' => $request->global_loading_terms,
            'global_warranty' => $request->global_warranty,
            'global_complimentary_spares' => $request->global_complimentary_spares,
            'global_other_details_in_print' => $request->has('global_other_details_in_print') ? (bool)$request->global_other_details_in_print : true,
            // Difference of Specification
            'global_cam_jacquard_chain_jacquard' => $request->global_cam_jacquard_chain_jacquard,
            'global_hooks_5376_to_6144_jacquard' => $request->global_hooks_5376_to_6144_jacquard,
            'global_warp_beam' => $request->global_warp_beam,
            'global_reed_space_380_to_420_cm' => $request->global_reed_space_380_to_420_cm,
            'global_color_selector_8_to_12' => $request->global_color_selector_8_to_12,
            'global_hooks_5376_to_2688_jacquard' => $request->global_hooks_5376_to_2688_jacquard,
            'global_extra_feeder' => $request->global_extra_feeder,
            'global_difference_specification_in_print' => $request->has('global_difference_specification_in_print') ? (bool)$request->global_difference_specification_in_print : true,
        ]);

        return redirect()->route('settings.contract-details')->with('success', 'Global contract details updated successfully.');
    }
}
