<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Models\PostPhoto;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class PostAdminController extends Controller
{
    public function index(): Response
    {
        $posts = Post::with('user')
            ->latest()
            ->paginate(15)
            ->through(fn ($post) => [
                'id' => $post->id,
                'slug' => $post->slug,
                'title_pt' => $post->title_pt,
                'title_en' => $post->title_en,
                'featured_image_url' => $post->featured_image_url,
                'photos_count' => $post->photos()->count(),
                'is_published' => $post->is_published,
                'published_at' => $post->published_at?->format('d/m/Y H:i'),
                'author' => $post->user?->name,
                'created_at' => $post->created_at->format('d/m/Y'),
            ]);

        return Inertia::render('Admin/Posts/Index', ['posts' => $posts]);
    }

    public function create(): Response
    {
        return Inertia::render('Admin/Posts/Form', ['post' => null]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title_pt' => ['required', 'string', 'max:255'],
            'title_en' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', 'unique:posts,slug'],
            'content_pt' => ['nullable', 'string'],
            'content_en' => ['nullable', 'string'],
            'excerpt_pt' => ['nullable', 'string', 'max:300'],
            'excerpt_en' => ['nullable', 'string', 'max:300'],
            'meta_title_pt' => ['nullable', 'string', 'max:60'],
            'meta_title_en' => ['nullable', 'string', 'max:60'],
            'meta_description_pt' => ['nullable', 'string', 'max:160'],
            'meta_description_en' => ['nullable', 'string', 'max:160'],
            'featured_image' => ['nullable', 'image', 'max:5120'],
            'new_photos' => ['nullable', 'array'],
            'new_photos.*' => ['image', 'mimes:jpg,jpeg,png,webp', 'max:3072'],
            'published_at' => ['nullable', 'date'],
        ]);

        $slug = $validated['slug'] ?? Str::slug($validated['title_pt']);

        if (Post::where('slug', $slug)->exists()) {
            $slug = $slug . '-' . time();
        }

        $imagePath = null;
        if ($request->hasFile('featured_image')) {
            $imagePath = $request->file('featured_image')->store('posts', 'public');
        }

        $post = Post::create([
            ...$validated,
            'slug' => $slug,
            'featured_image' => $imagePath,
            'user_id' => auth()->id(),
        ]);

        if ($request->hasFile('new_photos')) {
            foreach ($request->file('new_photos') as $i => $file) {
                $path = $file->store("posts/{$post->id}", 'public');
                PostPhoto::create([
                    'post_id' => $post->id,
                    'path' => $path,
                    'order' => $i,
                ]);
            }
        }

        return redirect()->route('admin.posts.index')->with('success', 'Post criado com sucesso!');
    }

    public function edit(Post $post): Response
    {
        $post->load('photos');

        return Inertia::render('Admin/Posts/Form', [
            'post' => [
                'id' => $post->id,
                'slug' => $post->slug,
                'title_pt' => $post->title_pt,
                'title_en' => $post->title_en,
                'content_pt' => $post->content_pt,
                'content_en' => $post->content_en,
                'excerpt_pt' => $post->excerpt_pt,
                'excerpt_en' => $post->excerpt_en,
                'meta_title_pt' => $post->meta_title_pt,
                'meta_title_en' => $post->meta_title_en,
                'meta_description_pt' => $post->meta_description_pt,
                'meta_description_en' => $post->meta_description_en,
                'featured_image_url' => $post->featured_image_url,
                'photos' => $post->photos->map(fn ($photo) => [
                    'id' => $photo->id,
                    'url' => $photo->url,
                ]),
                'published_at' => $post->published_at?->format('Y-m-d\TH:i'),
            ],
        ]);
    }

    public function update(Request $request, Post $post): RedirectResponse
    {
        $validated = $request->validate([
            'title_pt' => ['required', 'string', 'max:255'],
            'title_en' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', "unique:posts,slug,{$post->id}"],
            'content_pt' => ['nullable', 'string'],
            'content_en' => ['nullable', 'string'],
            'excerpt_pt' => ['nullable', 'string', 'max:300'],
            'excerpt_en' => ['nullable', 'string', 'max:300'],
            'meta_title_pt' => ['nullable', 'string', 'max:60'],
            'meta_title_en' => ['nullable', 'string', 'max:60'],
            'meta_description_pt' => ['nullable', 'string', 'max:160'],
            'meta_description_en' => ['nullable', 'string', 'max:160'],
            'featured_image' => ['nullable', 'image', 'max:5120'],
            'new_photos' => ['nullable', 'array'],
            'new_photos.*' => ['image', 'mimes:jpg,jpeg,png,webp', 'max:3072'],
            'published_at' => ['nullable', 'date'],
        ]);

        if ($request->hasFile('featured_image')) {
            if ($post->featured_image) {
                Storage::disk('public')->delete($post->featured_image);
            }
            $validated['featured_image'] = $request->file('featured_image')->store('posts', 'public');
        }

        $post->update($validated);

        if ($request->hasFile('new_photos')) {
            $nextOrder = $post->photos()->max('order') + 1;
            foreach ($request->file('new_photos') as $i => $file) {
                $path = $file->store("posts/{$post->id}", 'public');
                PostPhoto::create([
                    'post_id' => $post->id,
                    'path' => $path,
                    'order' => $nextOrder + $i,
                ]);
            }
        }

        return back()->with('success', 'Post atualizado com sucesso!');
    }

    public function destroyPhoto(PostPhoto $photo): RedirectResponse
    {
        $postId = $photo->post_id;
        Storage::disk('public')->delete($photo->path);
        $photo->delete();

        return redirect()->route('admin.posts.edit', $postId)
            ->with('success', 'Foto removida com sucesso!');
    }

    public function destroy(Post $post): RedirectResponse
    {
        if ($post->featured_image) {
            Storage::disk('public')->delete($post->featured_image);
        }

        foreach ($post->photos as $photo) {
            Storage::disk('public')->delete($photo->path);
        }

        $post->delete();

        return redirect()->route('admin.posts.index')->with('success', 'Post excluído.');
    }
}
