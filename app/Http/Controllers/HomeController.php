<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\PortfolioCategory;
use App\Models\PortfolioService;
use Illuminate\Support\Facades\App;
use Inertia\Inertia;
use Inertia\Response;

class HomeController extends Controller
{
    public function index(): Response
    {
        $locale = App::getLocale();
        $posts = Post::published()
            ->latest('published_at')
            ->take(2)
            ->get()
            ->map(fn ($post) => [
                'id' => $post->id,
                'slug' => $post->slug,
                'title' => $post->getTitle($locale),
                'excerpt' => $post->getExcerpt($locale),
                'featured_image_url' => $post->featured_image_url,
                'published_at' => $post->published_at?->format('d/m/Y'),
            ]);

        $categories = PortfolioCategory::active()
            ->with('photos')
            ->get()
            ->map(fn ($c) => [
                'id' => $c->id,
                'name_pt' => $c->name_pt,
                'name_en' => $c->name_en,
                'description_pt' => $c->description_pt,
                'description_en' => $c->description_en,
                'cover_url' => $c->cover_url ?? $c->photos->first()?->url,
                'gallery_urls' => $c->photos->map(fn ($photo) => $photo->url)->values()->all(),
            ])
            ->values()
            ->all();

        $services = PortfolioService::active()
            ->with('photos')
            ->get()
            ->map(fn ($service) => [
                'id' => $service->id,
                'name_pt' => $service->name_pt,
                'name_en' => $service->name_en,
                'description_pt' => $service->description_pt,
                'description_en' => $service->description_en,
                'cover_url' => $service->cover_url ?? $service->photos->first()?->url,
                'gallery_urls' => $service->photos->map(fn ($photo) => $photo->url)->values()->all(),
            ])
            ->values()
            ->all();

        return Inertia::render('Home', [
            'featuredPosts' => $posts,
            'categories' => $categories,
            'services' => $services,
            'seo' => [
                'title' => $locale === 'pt'
                    ? 'Madeira Tratada | Decks, Cercas e Construção Civil'
                    : 'Treated Wood | Decks, Fences and Civil Construction',
                'description' => $locale === 'pt'
                    ? 'Trataplan – Referência em madeira tratada no Oeste Catarinense. Eucalipto e Pinus autoclavado com 15 anos de garantia.'
                    : 'Trataplan – Reference in treated wood in Western Santa Catarina. Autoclaved eucalyptus and pine with 15-year guarantee.',
                'canonical' => $locale === 'pt' ? url('/') : url('/en'),
            ],
        ]);
    }
}
