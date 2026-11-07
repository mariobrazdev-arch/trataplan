<?php

namespace App\Http\Controllers;

use App\Models\PortfolioCategory;
use App\Models\PortfolioService;
use App\Models\Post;
use Spatie\Sitemap\Sitemap;
use Spatie\Sitemap\Tags\Url;
use Illuminate\Http\Response;

class SitemapController extends Controller
{
    public function index(): Response
    {
        $sitemap = Sitemap::create();

        $staticRoutes = [
            ['path' => '/',          'priority' => 1.0, 'freq' => 'weekly'],
            ['path' => '/sobre',     'priority' => 0.8, 'freq' => 'monthly'],
            ['path' => '/portfolio', 'priority' => 0.9, 'freq' => 'monthly'],
            ['path' => '/blog',      'priority' => 0.8, 'freq' => 'weekly'],
            ['path' => '/parceiros', 'priority' => 0.6, 'freq' => 'monthly'],
            ['path' => '/contato',   'priority' => 0.7, 'freq' => 'monthly'],
        ];

        foreach ($staticRoutes as $route) {
            $sitemap->add(
                Url::create(url($route['path']))
                    ->setPriority($route['priority'])
                    ->setChangeFrequency($route['freq'])
            );
        }

        // Dynamic portfolio items (products)
        PortfolioCategory::active()->each(function (PortfolioCategory $item) use ($sitemap) {
            $sitemap->add(
                Url::create(url("/portfolio/{$item->id}"))
                    ->setLastModificationDate($item->updated_at)
                    ->setPriority(0.7)
                    ->setChangeFrequency('monthly')
            );
        });

        // Dynamic workforce items (specialized services)
        PortfolioService::active()->each(function (PortfolioService $item) use ($sitemap) {
            $sitemap->add(
                Url::create(url("/mao-de-obra/{$item->id}"))
                    ->setLastModificationDate($item->updated_at)
                    ->setPriority(0.6)
                    ->setChangeFrequency('monthly')
            );
        });

        // Dynamic posts
        Post::published()->each(function (Post $post) use ($sitemap) {
            $sitemap->add(
                Url::create(url("/blog/{$post->slug}"))
                    ->setLastModificationDate($post->updated_at)
                    ->setPriority(0.7)
                    ->setChangeFrequency('monthly')
            );
        });

        return $sitemap->toResponse(request());
    }
}
