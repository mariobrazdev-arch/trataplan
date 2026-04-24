<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\PostCategory;
use Illuminate\Support\Facades\App;
use Inertia\Inertia;
use Inertia\Response;

class PostController extends Controller
{
    public function index(): Response
    {
        $locale = App::getLocale();

        $posts = Post::published()
            ->with('postCategory')
            ->latest('published_at')
            ->paginate(9)
            ->through(fn ($post) => [
                'id' => $post->id,
                'slug' => $post->slug,
                'title' => $post->getTitle($locale),
                'excerpt' => $post->getExcerpt($locale),
                'featured_image_url' => $post->featured_image_url,
                'published_at' => $post->published_at?->format('d/m/Y'),
                'post_category_id' => $post->post_category_id,
                'category_label' => $post->postCategory
                    ? ($locale === 'en' ? ($post->postCategory->name_en ?: $post->postCategory->name_pt) : $post->postCategory->name_pt)
                    : null,
            ]);

        $postCategories = PostCategory::orderBy('order')->get(['id', 'name_pt', 'name_en'])
            ->map(fn ($c) => [
                'id'   => $c->id,
                'name' => $locale === 'en' ? ($c->name_en ?: $c->name_pt) : $c->name_pt,
            ]);

        return Inertia::render('Blog/Index', [
            'posts' => $posts,
            'postCategories' => $postCategories,
            'seo' => [
                'title' => $locale === 'pt' ? 'Blog – Dicas e Novidades' : 'Blog – Tips & News',
                'description' => $locale === 'pt'
                    ? 'Artigos e novidades sobre madeira tratada, construção, decks e paisagismo.'
                    : 'Articles and news about treated wood, construction, decks and landscaping.',
                'canonical' => url()->current(),
            ],
        ]);
    }

    public function show(string $slug): Response
    {
        $locale = App::getLocale();

        // Post deletado → 410 Gone (mais rápido de desindexar pelo Google que 404)
        if (Post::withTrashed()->where('slug', $slug)->whereNotNull('deleted_at')->exists()) {
            abort(410);
        }

        $post = Post::where('slug', $slug)->published()->with(['postCategory', 'photos'])->firstOrFail();

        $prev = Post::published()
            ->where('published_at', '<', $post->published_at)
            ->latest('published_at')
            ->first();

        $next = Post::published()
            ->where('published_at', '>', $post->published_at)
            ->oldest('published_at')
            ->first();

        return Inertia::render('Blog/Show', [
            'post' => [
                'id' => $post->id,
                'slug' => $post->slug,
                'title' => $post->getTitle($locale),
                'content' => $post->getContent($locale),
                'excerpt' => $post->getExcerpt($locale),
                'featured_image_url' => $post->featured_image_url,
                'published_at' => $post->published_at?->format('d/m/Y'),
                'author' => $post->user?->name,
                'category_label' => $post->postCategory
                    ? ($locale === 'en' ? ($post->postCategory->name_en ?: $post->postCategory->name_pt) : $post->postCategory->name_pt)
                    : null,
                'photos' => $post->photos->map(fn ($p) => [
                    'id'  => $p->id,
                    'url' => $p->url,
                ])->values()->all(),
            ],
            'prev' => $prev ? ['slug' => $prev->slug, 'title' => $prev->getTitle($locale)] : null,
            'next' => $next ? ['slug' => $next->slug, 'title' => $next->getTitle($locale)] : null,
            'seo' => [
                'title' => $post->getMetaTitle($locale) ?? $post->getTitle($locale),
                'description' => $post->getMetaDescription($locale) ?? $post->getExcerpt($locale),
                'image' => $post->og_image ? asset('storage/' . $post->og_image) : $post->featured_image_url,
                'canonical' => url()->current(),
            ],
        ]);
    }
}
