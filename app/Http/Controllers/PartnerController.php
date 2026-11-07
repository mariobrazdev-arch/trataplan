<?php

namespace App\Http\Controllers;

use App\Models\Partner;
use Inertia\Inertia;
use Inertia\Response;

class PartnerController extends Controller
{
    public function index(): Response
    {
        $partners = Partner::active()
            ->get()
            ->map(fn ($partner) => [
                'id' => $partner->id,
                'name' => $partner->name,
                'logo_url' => $partner->logo_url,
                'website' => $partner->website,
                'description' => $partner->getDescription('pt'),
            ]);

        return Inertia::render('Partners', [
            'partners' => $partners,
            'seo' => [
                'title' => 'Nossos Parceiros',
                'description' => 'Conheça os parceiros e fornecedores da Trataplan.',
                'canonical' => url()->current(),
            ],
        ]);
    }
}
