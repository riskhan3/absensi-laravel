<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\GeneralSetting;
use Illuminate\Http\Request;

class GeneralSettingController extends Controller
{
    /** Halaman pengaturan sistem */
    public function index()
    {
        $settings = GeneralSetting::instance();
        return view('admin.settings.index', compact('settings'));
    }

    /** Simpan perubahan pengaturan */
    public function update(Request $request)
    {
        $validated = $request->validate([
            'school_name'              => ['required', 'string', 'max:255'],
            'school_year'              => ['required', 'string', 'max:20'],
            'school_latitude'          => ['required', 'numeric', 'between:-90,90'],
            'school_longitude'         => ['required', 'numeric', 'between:-180,180'],
            'geofence_radius_meters'   => ['required', 'integer', 'min:50', 'max:5000'],
            'geofencing_enabled'       => ['boolean'],
            'whatsapp_token'           => ['nullable', 'string', 'max:255'],
            'whatsapp_enabled'         => ['boolean'],
            'copyright'                => ['nullable', 'string', 'max:255'],
        ]);

        // Checkbox → boolean (tidak terkirim jika unchecked)
        $validated['geofencing_enabled'] = $request->boolean('geofencing_enabled');
        $validated['whatsapp_enabled']   = $request->boolean('whatsapp_enabled');

        $settings = GeneralSetting::instance();

        if ($settings) {
            $settings->update($validated);
        } else {
            GeneralSetting::create($validated);
        }

        return redirect()->route('admin.settings.index')
            ->with('success', 'Pengaturan sistem berhasil disimpan.');
    }
}
