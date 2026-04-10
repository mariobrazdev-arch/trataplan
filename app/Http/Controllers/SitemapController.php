<?php

namespace App\Http\Controllers;

use App\Models\Page;
use App\Models\Post;
use Spatie\Sitemap\Sitemap;
use Spatie\Sitemap\Tags\Url;
use Illuminate\Http\Response;

class SitemapController extends Controller
{
    public function index(): Response
    {
        $sitemap = Sitemap::create();

        // Static pages — both locales
        $staticRoutes = [
            ['pt' => '/', 'en' => '/en', 'priority' => 1.0, 'freq' => 'weekly'],
            ['pt' => '/sobre', 'en' => '/en/about', 'priority' => 0.8, 'freq' => 'monthly'],
            ['pt' => '/produtos', 'en' => '/en/products', 'priority' => 0.9, 'freq' => 'monthly'],
            ['pt' => '/blog', 'en' => '/en/blog', 'priority' => 0.8, 'freq' => 'weekly'],
            ['pt' => '/parceiros', 'en' => '/en/partners', 'priority' => 0.6, 'freq' => 'monthly'],
            ['pt' => '/contato', 'en' => '/en/contact', 'priority' => 0.7, 'freq' => 'monthly'],
        ];

        foreach ($staticRoutes as $route) {
            $sitemap->add(
                Url::create(url($route['pt']))
                    ->setPriority($route['priority'])
                    ->setChangeFrequency($route['freq'])
            );
            $sitemap->add(
                Url::create(url($route['en']))
                    ->setPriority($route['priority'])
                    ->setChangeFrequency($route['freq'])
            );
        }

        // Dynamic posts
        Post::published()->each(function (Post $post) use ($sitemap) {
            $sitemap->add(
                Url::create(url("/blog/{$post->slug}"))
                    ->setLastModificationDate($post->updated_at)
                    ->setPriority(0.7)
                    ->setChangeFrequency('monthly')
            );
            $sitemap->add(
                Url::create(url("/en/blog/{$post->slug}"))
                    ->setLastModificationDate($post->updated_at)
                    ->setPriority(0.7)
                    ->setChangeFrequency('monthly')
            );
        });

        return $sitemap->toResponse(request());
    }
}
