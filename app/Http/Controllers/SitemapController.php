<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Spatie\Sitemap\Sitemap;
use Spatie\Sitemap\Tags\Url;
use Illuminate\Http\Response;

class SitemapController extends Controller
{
    public function index(): Response
    {
        $sitemap = Sitemap::create();

        // Static pages — both locales with hreflang alternates
        $staticRoutes = [
            ['pt' => '/',          'en' => '/en',           'priority' => 1.0, 'freq' => 'weekly'],
            ['pt' => '/sobre',     'en' => '/en/about',     'priority' => 0.8, 'freq' => 'monthly'],
            ['pt' => '/catalogo',  'en' => '/en/catalog',   'priority' => 0.9, 'freq' => 'monthly'],
            ['pt' => '/blog',      'en' => '/en/blog',      'priority' => 0.8, 'freq' => 'weekly'],
            ['pt' => '/parceiros', 'en' => '/en/partners',  'priority' => 0.6, 'freq' => 'monthly'],
            ['pt' => '/contato',   'en' => '/en/contact',   'priority' => 0.7, 'freq' => 'monthly'],
        ];

        foreach ($staticRoutes as $route) {
            $ptUrl = url($route['pt']);
            $enUrl = url($route['en']);

            $sitemap->add(
                Url::create($ptUrl)
                    ->setPriority($route['priority'])
                    ->setChangeFrequency($route['freq'])
                    ->addAlternate($ptUrl, 'pt-BR')
                    ->addAlternate($enUrl, 'en')
                    ->addAlternate($ptUrl, 'x-default')
            );
            $sitemap->add(
                Url::create($enUrl)
                    ->setPriority($route['priority'])
                    ->setChangeFrequency($route['freq'])
                    ->addAlternate($ptUrl, 'pt-BR')
                    ->addAlternate($enUrl, 'en')
                    ->addAlternate($ptUrl, 'x-default')
            );
        }

        // Dynamic posts
        Post::published()->each(function (Post $post) use ($sitemap) {
            $ptUrl = url("/blog/{$post->slug}");
            $enUrl = url("/en/blog/{$post->slug}");

            $sitemap->add(
                Url::create($ptUrl)
                    ->setLastModificationDate($post->updated_at)
                    ->setPriority(0.7)
                    ->setChangeFrequency('monthly')
                    ->addAlternate($ptUrl, 'pt-BR')
                    ->addAlternate($enUrl, 'en')
                    ->addAlternate($ptUrl, 'x-default')
            );
            $sitemap->add(
                Url::create($enUrl)
                    ->setLastModificationDate($post->updated_at)
                    ->setPriority(0.7)
                    ->setChangeFrequency('monthly')
                    ->addAlternate($ptUrl, 'pt-BR')
                    ->addAlternate($enUrl, 'en')
                    ->addAlternate($ptUrl, 'x-default')
            );
        });

        return $sitemap->toResponse(request());
    }
}
