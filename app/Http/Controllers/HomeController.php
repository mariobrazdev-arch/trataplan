<?php

namespace App\Http\Controllers;

use App\Models\Hero;
use App\Models\Post;
use App\Models\PortfolioCategory;
use App\Models\PortfolioService;
use Inertia\Inertia;
use Inertia\Response;

class HomeController extends Controller
{
    public function index(): Response
    {
        $posts = Post::published()
            ->latest('published_at')
            ->take(2)
            ->get()
            ->map(fn ($post) => [
                'id' => $post->id,
                'slug' => $post->slug,
                'title' => $post->getTitle('pt'),
                'excerpt' => $post->getExcerpt('pt'),
                'featured_image_url' => $post->featured_image_url,
                'published_at' => $post->published_at?->format('d/m/Y'),
            ]);

        $categories = PortfolioCategory::active()
            ->with('photos')
            ->get()
            ->map(fn ($c) => [
                'id' => $c->id,
                'name_pt' => $c->name_pt,
                'description_pt' => $c->description_pt,
                'cover_url' => $c->cover_url ?? $c->photos->first()?->url,
                'gallery_urls' => $c->photos->map(fn ($photo) => $photo->url)->values()->all(),
                'product_category_id' => $c->product_category_id,
            ])
            ->values()
            ->all();

        $services = PortfolioService::active()
            ->with('photos')
            ->get()
            ->map(fn ($service) => [
                'id' => $service->id,
                'name_pt' => $service->name_pt,
                'description_pt' => $service->description_pt,
                'cover_url' => $service->cover_url ?? $service->photos->first()?->url,
                'gallery_urls' => $service->photos->map(fn ($photo) => $photo->url)->values()->all(),
            ])
            ->values()
            ->all();

        $fallbackGridPhotos = collect($categories)
            ->pluck('cover_url')
            ->filter()
            ->values();

        if ($fallbackGridPhotos->count() < 6) {
            $extraGalleryPhotos = collect($categories)
                ->flatMap(fn ($c) => $c['gallery_urls'])
                ->reject(fn ($url) => $fallbackGridPhotos->contains($url));

            $fallbackGridPhotos = $fallbackGridPhotos
                ->merge($extraGalleryPhotos)
                ->values();
        }

        $fallbackGridPhotos = $fallbackGridPhotos->take(6)->all();

        $heroes = Hero::active()
            ->with('photos')
            ->get()
            ->map(function (Hero $hero) use ($fallbackGridPhotos) {
                $photos = $hero->photos->map(fn ($p) => $p->url)->values()->all();

                if ($hero->layout === Hero::LAYOUT_PHOTO_GRID && empty($photos)) {
                    $photos = $fallbackGridPhotos;
                }

                return [
                    'id'        => $hero->id,
                    'layout'    => $hero->layout,
                    'title'     => $hero->getTitle('pt'),
                    'subtitle'  => $hero->getSubtitle('pt'),
                    'cta_label' => $hero->getCtaLabel('pt'),
                    'cta_url'   => $hero->getCtaUrl('pt'),
                    'main_image_url' => $hero->main_image_url,
                    'photos'    => $photos,
                ];
            })
            ->values()
            ->all();

        return Inertia::render('Home', [
            'heroes' => $heroes,
            'featuredPosts' => $posts,
            'categories' => $categories,
            'services' => $services,
            'seo' => [
                'title' => 'Madeira Tratada | Decks, Cercas e Construção Civil',
                'description' => 'Trataplan – Referência em madeira tratada no Oeste Catarinense. Eucalipto e Pinus autoclavado com 15 anos de garantia.',
                'canonical' => url('/'),
            ],
        ]);
    }
}
