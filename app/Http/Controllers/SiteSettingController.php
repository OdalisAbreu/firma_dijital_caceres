<?php

namespace App\Http\Controllers;

use App\Models\SiteSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class SiteSettingController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Muestra el formulario de configuración visual (logo, favicon, apple touch icon).
     */
    public function edit()
    {
        abort_unless(auth()->user()?->role === 'administrador', 403);

        $settings = SiteSetting::current();

        return Inertia::render('Settings/Visuales', [
            'settings' => [
                'logo_url' => $settings->logo_url,
                'favicon_url' => $settings->favicon_url,
                'apple_touch_icon_url' => $settings->apple_touch_icon_url,
            ],
        ]);
    }

    /**
     * Actualiza el logo, favicon y/o apple touch icon del sitio.
     */
    public function update(Request $request)
    {
        abort_unless(auth()->user()?->role === 'administrador', 403);

        $validated = $request->validate([
            'logo' => 'nullable|image|mimes:png,jpg,jpeg,svg,webp|max:2048',
            'favicon' => 'nullable|mimes:ico,png,svg|max:512',
            'apple_touch_icon' => 'nullable|image|mimes:png,jpg,jpeg|max:1024',
        ]);

        $settings = SiteSetting::current();

        foreach ([
            'logo' => 'logo_path',
            'favicon' => 'favicon_path',
            'apple_touch_icon' => 'apple_touch_icon_path',
        ] as $inputName => $column) {
            if ($request->hasFile($inputName)) {
                if ($settings->{$column}) {
                    Storage::disk('public')->delete($settings->{$column});
                }

                $settings->{$column} = $request->file($inputName)->store('branding', 'public');
            }
        }

        $settings->save();

        return redirect()->route('settings.visuales.edit')->with('success', 'Configuración visual actualizada correctamente.');
    }
}
