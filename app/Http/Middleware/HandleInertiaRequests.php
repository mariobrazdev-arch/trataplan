<?php

namespace App\Http\Middleware;

use App\Models\ProductCategory;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Storage;
use Inertia\Middleware;
use Tighten\Ziggy\Ziggy;

class HandleInertiaRequests extends Middleware
{
    protected $rootView = 'app';

    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    public function share(Request $request): array
    {
        return [
            ...parent::share($request),
            'locale' => App::getLocale(),
            'settings' => function () {
                $settings = Setting::all_cached();
                foreach (['logo_transparent', 'logo_white_bg', 'banner_image'] as $key) {
                    if (!empty($settings[$key])) {
                        $settings[$key . '_url'] = Storage::url($settings[$key]);
                    }
                }
                return $settings;
            },
            'flash' => [
                'success'    => fn () => $request->session()->get('success'),
                'error'      => fn () => $request->session()->get('error'),
                'mail_error' => fn () => $request->session()->get('mail_error'),
            ],
            'auth' => [
                'user' => $request->user() ? [
                    'id' => $request->user()->id,
                    'name' => $request->user()->name,
                    'email' => $request->user()->email,
                    'is_admin' => $request->user()->is_admin,
                ] : null,
            ],
            'ziggy' => fn () => [...(new Ziggy)->toArray(), 'location' => $request->url()],
            'catalog_footer' => function () {
                $locale = App::getLocale();
                $prefix = $locale === 'en' ? '/en' : '';
                $catalogBase = $prefix . ($locale === 'en' ? '/catalog' : '/catalogo');

                return ProductCategory::orderBy('name_pt')
                    ->get(['id', 'name_pt', 'name_en'])
                    ->map(fn ($c) => [
                        'label' => $locale === 'en' ? ($c->name_en ?: $c->name_pt) : $c->name_pt,
                        'href'  => $catalogBase,
                    ])
                    ->values();
            },
        ];
    }
}
