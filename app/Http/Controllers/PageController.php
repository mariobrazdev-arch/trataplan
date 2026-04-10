<?php

namespace App\Http\Controllers;

use App\Models\Page;
use Illuminate\Support\Facades\App;
use Inertia\Inertia;
use Inertia\Response;

class PageController extends Controller
{
    public function show(string $slug): Response
    {
        $page = Page::where('slug', $slug)->published()->firstOrFail();
        $locale = App::getLocale();

        $component = $slug === 'about' ? 'About' : 'Page';

        return Inertia::render($component, [
            'page' => [
                'id' => $page->id,
                'slug' => $page->slug,
                'title' => $page->getTitle($locale),
                'content' => $page->getContent($locale),
            ],
            'seo' => [
                'title' => $page->getMetaTitle($locale) ?? $page->getTitle($locale),
                'description' => $page->getMetaDescription($locale),
                'image' => $page->og_image ? asset('storage/' . $page->og_image) : null,
                'canonical' => url()->current(),
            ],
        ]);
    }
}
