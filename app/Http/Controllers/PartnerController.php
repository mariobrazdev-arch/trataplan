<?php

namespace App\Http\Controllers;

use App\Models\Partner;
use Illuminate\Support\Facades\App;
use Inertia\Inertia;
use Inertia\Response;

class PartnerController extends Controller
{
    public function index(): Response
    {
        $locale = App::getLocale();

        $partners = Partner::active()
            ->get()
            ->map(fn ($partner) => [
                'id' => $partner->id,
                'name' => $partner->name,
                'logo_url' => $partner->logo_url,
                'website' => $partner->website,
                'description' => $partner->getDescription($locale),
            ]);

        return Inertia::render('Partners', [
            'partners' => $partners,
            'seo' => [
                'title' => $locale === 'pt' ? 'Nossos Parceiros' : 'Our Partners',
                'description' => $locale === 'pt'
                    ? 'Conheça os parceiros e fornecedores da Trataplan.'
                    : 'Meet Trataplan\'s partners and suppliers.',
                'canonical' => url()->current(),
            ],
        ]);
    }
}
