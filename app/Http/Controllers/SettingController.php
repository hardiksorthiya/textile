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
}
