<?php

namespace App\Http\Controllers;

use App\Models\PortfolioCategory;
use App\Models\PortfolioService;
use Inertia\Inertia;
use Inertia\Response;

class PortfolioController extends Controller
{
    public function index(): Response
    {
        $items = PortfolioCategory::active()
            ->with('photos')
            ->get()
            ->map(fn (PortfolioCategory $item) => [
                'id'           => $item->id,
                'name'         => $item->name_pt,
                'cover_url'    => $item->cover_url ?? $item->photos->first()?->url,
                'photos_count' => $item->photos->count(),
            ]);

        $workforceItems = PortfolioService::active()
            ->with('photos')
            ->get()
            ->map(fn (PortfolioService $item) => [
                'id'           => $item->id,
                'name'         => $item->name_pt,
                'cover_url'    => $item->cover_url ?? $item->photos->first()?->url,
                'photos_count' => $item->photos->count(),
            ]);

        return Inertia::render('Portfolio', [
            'items'          => $items,
            'workforceItems' => $workforceItems,
            'seo' => [
                'title'       => 'Portfólio — Trataplan',
                'description' => 'Conheça o portfólio de produtos da Trataplan.',
                'canonical'   => url()->current(),
            ],
        ]);
    }

    public function show(PortfolioCategory $item): Response
    {
        $photos = $item->photos->map(fn ($p) => $p->url)->values()->all();
        $coverUrl = $item->cover_url ?? ($photos[0] ?? null);

        return Inertia::render('PortfolioItem', [
            'item' => [
                'id'             => $item->id,
                'name'           => $item->name_pt,
                'description'    => $item->description_pt,
                'cover_url'      => $coverUrl,
                'gallery_urls'   => $photos,
                'back_url'       => '/portfolio',
                'detail_url'     => url()->current(),
            ],
            'seo' => [
                'title'       => $item->name_pt . ' — Trataplan',
                'description' => $item->description_pt ?: '',
                'image'     => $coverUrl,
                'canonical' => url()->current(),
            ],
        ]);
    }
}
