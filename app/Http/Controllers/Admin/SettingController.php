<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SettingController extends Controller
{
    /**
     * Keys that can be managed from the settings page.
     *
     * @var list<string>
     */
    protected const KEYS = [
        'site_name',
        'site_description',
        'contact_email',
        'contact_phone',
        'footer_text',
    ];

    /**
     * Show the site settings form.
     */
    public function index(): View
    {
        $settings = [
            'site_name' => Setting::value('site_name', config('app.name')),
            'site_description' => Setting::value('site_description'),
            'contact_email' => Setting::value('contact_email'),
            'contact_phone' => Setting::value('contact_phone'),
            'footer_text' => Setting::value('footer_text'),
        ];

        return view('admin.settings.index', compact('settings'));
    }

    /**
     * Persist the site settings.
     */
    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'site_name' => ['required', 'string', 'max:100'],
            'site_description' => ['nullable', 'string', 'max:255'],
            'contact_email' => ['nullable', 'email', 'max:150'],
            'contact_phone' => ['nullable', 'string', 'max:30'],
            'footer_text' => ['nullable', 'string', 'max:255'],
        ]);

        Setting::setMany(array_intersect_key($validated, array_flip(self::KEYS)));

        return redirect()
            ->route('admin.settings.index')
            ->with('status', 'Pengaturan berhasil disimpan.');
    }
}
