<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\PostCategory;
use Inertia\Inertia;
use Inertia\Response;

class PostController extends Controller
{
    public function index(): Response
    {
        $posts = Post::published()
            ->with('postCategory')
            ->latest('published_at')
            ->paginate(9)
            ->through(fn ($post) => [
                'id' => $post->id,
                'slug' => $post->slug,
                'title' => $post->getTitle('pt'),
                'excerpt' => $post->getExcerpt('pt'),
                'featured_image_url' => $post->featured_image_url,
                'published_at' => $post->published_at?->format('d/m/Y'),
                'post_category_id' => $post->post_category_id,
                'category_label' => $post->postCategory?->name_pt,
            ]);

        $postCategories = PostCategory::orderBy('order')->get(['id', 'name_pt'])
            ->map(fn ($c) => [
                'id'   => $c->id,
                'name' => $c->name_pt,
            ]);

        return Inertia::render('Blog/Index', [
            'posts' => $posts,
            'postCategories' => $postCategories,
            'seo' => [
                'title' => 'Blog – Dicas e Novidades',
                'description' => 'Artigos e novidades sobre madeira tratada, construção, decks e paisagismo.',
                'canonical' => url()->current(),
            ],
        ]);
    }

    public function show(string $slug): Response
    {
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
                'title' => $post->getTitle('pt'),
                'content' => $post->getContent('pt'),
                'excerpt' => $post->getExcerpt('pt'),
                'featured_image_url' => $post->featured_image_url,
                'published_at' => $post->published_at?->format('d/m/Y'),
                'author' => $post->user?->name,
                'category_label' => $post->postCategory?->name_pt,
                'photos' => $post->photos->map(fn ($p) => [
                    'id'  => $p->id,
                    'url' => $p->url,
                ])->values()->all(),
            ],
            'prev' => $prev ? ['slug' => $prev->slug, 'title' => $prev->getTitle('pt')] : null,
            'next' => $next ? ['slug' => $next->slug, 'title' => $next->getTitle('pt')] : null,
            'seo' => [
                'title' => $post->getMetaTitle('pt') ?? $post->getTitle('pt'),
                'description' => $post->getMetaDescription('pt') ?? $post->getExcerpt('pt'),
                'image' => $post->og_image ? asset('storage/' . $post->og_image) : $post->featured_image_url,
                'canonical' => url()->current(),
            ],
        ]);
    }
}
