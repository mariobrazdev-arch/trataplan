<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;

class SettingsAdminController extends Controller
{
    public function index(): Response
    {
        $settings = Setting::all_cached();

        foreach (['logo_transparent', 'logo_white_bg', 'banner_image'] as $key) {
            if (!empty($settings[$key])) {
                $settings[$key . '_url'] = Storage::url($settings[$key]);
            }
        }

        // Decode company_units JSON → array for the form
        if (!empty($settings['company_units'])) {
            $settings['company_units_decoded'] = json_decode($settings['company_units'], true) ?? [];
        } else {
            // Migrate old fields on first load
            $units = [];
            if (!empty($settings['contact_address'])) {
                $units[] = [
                    'tipo' => 'matriz', 'label' => 'Matriz (Sede)',
                    'address' => $settings['contact_address'],
                    'phone'   => $settings['contact_phone'] ?? '',
                    'email'   => $settings['contact_email'] ?? '',
                    'whatsapp'=> $settings['whatsapp_number'] ?? '',
                    'telegram'=> $settings['telegram_link'] ?? '',
                ];
            }
            if (!empty($settings['contact_address_branch'])) {
                $units[] = [
                    'tipo' => 'filial', 'label' => 'Filial',
                    'address' => $settings['contact_address_branch'],
                    'phone' => '', 'email' => '', 'whatsapp' => '', 'telegram' => '',
                ];
            }
            $settings['company_units_decoded'] = $units;
        }

        return Inertia::render('Admin/Settings', [
            'settings' => $settings,
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'whatsapp_number'    => ['nullable', 'string', 'max:20'],
            'telegram_link'      => ['nullable', 'url', 'max:255'],
            'contact_email'      => ['nullable', 'email', 'max:150'],
            'contact_phone'      => ['nullable', 'string', 'max:20'],
            'company_units'      => ['nullable', 'string', 'max:10000'],
            'facebook_url'       => ['nullable', 'url', 'max:255'],
            'instagram_url'      => ['nullable', 'url', 'max:255'],
            'linkedin_url'       => ['nullable', 'url', 'max:255'],
            'company_tagline_pt' => ['nullable', 'string', 'max:200'],
            'footer_text_pt'     => ['nullable', 'string', 'max:500'],
            // SMTP
            'mail_from_address'  => ['nullable', 'email', 'max:150'],
            'mail_from_name'     => ['nullable', 'string', 'max:100'],
            'mail_host'          => ['nullable', 'string', 'max:255'],
            'mail_port'          => ['nullable', 'integer', 'min:1', 'max:65535'],
            'mail_username'      => ['nullable', 'string', 'max:150'],
            'mail_password'      => ['nullable', 'string', 'max:255'],
            'mail_encryption'    => ['nullable', 'in:tls,ssl,none'],
            'banner_title_pt'    => ['nullable', 'string', 'max:200'],
            'banner_subtitle_pt' => ['nullable', 'string', 'max:300'],
            'banner_image'       => ['nullable', 'file', 'mimes:png,jpg,jpeg,webp', 'max:5120'],
            'logo_transparent'   => ['nullable', 'file', 'mimes:png,jpg,jpeg,webp,svg', 'max:2048'],
            'logo_white_bg'      => ['nullable', 'file', 'mimes:png,jpg,jpeg,webp', 'max:2048'],
        ]);

        foreach (['banner_image', 'logo_transparent', 'logo_white_bg'] as $logoKey) {
            if ($request->hasFile($logoKey)) {
                $old = Setting::get($logoKey);
                if ($old && Storage::disk('public')->exists($old)) {
                    Storage::disk('public')->delete($old);
                }
                $validated[$logoKey] = $request->file($logoKey)->store('logos', 'public');
            } else {
                unset($validated[$logoKey]);
            }
        }

        foreach ($validated as $key => $value) {
            Setting::set($key, $value);
        }

        // Keep contact_address in sync with the first unit (backward compat for footer/home)
        if (!empty($validated['company_units'])) {
            $units = json_decode($validated['company_units'], true) ?? [];
            // Find the Matriz unit (first one of tipo=matriz, or first unit)
            $matriz = collect($units)->firstWhere('tipo', 'matriz') ?? ($units[0] ?? null);
            if ($matriz) {
                if (!empty($matriz['address']))  Setting::set('contact_address',  $matriz['address']);
                if (!empty($matriz['phone']))    Setting::set('contact_phone',    $matriz['phone']);
                if (!empty($matriz['email']))    Setting::set('contact_email',    $matriz['email']);
                if (!empty($matriz['whatsapp'])) Setting::set('whatsapp_number', $matriz['whatsapp']);
                Setting::set('telegram_link', $matriz['telegram'] ?? '');
            }
        }

        return back()->with('success', 'Configurações salvas!');
    }
}
