<?php

namespace App\Http\Controllers;

use App\Models\PortfolioService;
use Inertia\Inertia;
use Inertia\Response;

class WorkforceController extends Controller
{
    public function show(PortfolioService $item): Response
    {
        $photos = $item->photos->map(fn ($p) => $p->url)->values()->all();
        $coverUrl = $item->cover_url ?? ($photos[0] ?? null);

        return Inertia::render('WorkforceItem', [
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
