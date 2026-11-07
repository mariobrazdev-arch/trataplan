<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Hero;
use App\Models\HeroPhoto;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;

class HeroAdminController extends Controller
{
    private const MAX_HEROES = 3;

    private const PHOTO_LIMITS = [
        Hero::LAYOUT_FULL_IMAGE   => 0,
        Hero::LAYOUT_SPLIT_SCREEN => 3,
        Hero::LAYOUT_PHOTO_GRID   => 6,
    ];

    public function index(): Response
    {
        $heroes = Hero::with('photos')->orderBy('order')->get()->map(fn (Hero $h) => [
            'id'            => $h->id,
            'layout'        => $h->layout,
            'title_pt'      => $h->title_pt,
            'is_active'     => $h->is_active,
            'order'         => $h->order,
            'main_image_url' => $h->main_image_url,
            'photos_count'  => $h->photos->count(),
            'cover_preview' => $h->main_image_url ?? $h->photos->first()?->url,
        ]);

        return Inertia::render('Admin/Heroes/Index', [
            'heroes'    => $heroes,
            'canCreate' => $heroes->count() < self::MAX_HEROES,
        ]);
    }

    public function create(): Response
    {
        if (Hero::count() >= self::MAX_HEROES) {
            abort(403, 'Limite de ' . self::MAX_HEROES . ' heroes atingido.');
        }

        return Inertia::render('Admin/Heroes/Form', [
            'hero' => null,
            'ctaLinkOptions' => array_keys(Hero::CTA_LINKS),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        if (Hero::count() >= self::MAX_HEROES) {
            return back()->with('error', 'Limite de ' . self::MAX_HEROES . ' heroes atingido.');
        }

        $validated = $this->validateHero($request);

        $validated['order'] = (int) Hero::max('order') + 1;
        $validated['is_active'] = $request->boolean('is_active') || Hero::count() === 0;

        $hero = Hero::create(collect($validated)->except(['main_image', 'new_photos'])->all());

        $this->handleUploads($request, $hero);

        return redirect()->route('admin.heroes.index')->with('success', 'Hero criado!');
    }

    public function edit(Hero $hero): Response
    {
        return Inertia::render('Admin/Heroes/Form', [
            'hero' => [
                'id'             => $hero->id,
                'layout'         => $hero->layout,
                'title_pt'       => $hero->title_pt,
                'subtitle_pt'    => $hero->subtitle_pt,
                'cta_label_pt'   => $hero->cta_label_pt,
                'cta_link'       => $hero->cta_link,
                'main_image_url' => $hero->main_image_url,
                'is_active'      => $hero->is_active,
                'photos' => $hero->photos->map(fn ($ph) => [
                    'id'  => $ph->id,
                    'url' => $ph->url,
                ]),
            ],
            'ctaLinkOptions' => array_keys(Hero::CTA_LINKS),
        ]);
    }

    public function update(Request $request, Hero $hero): RedirectResponse
    {
        $validated = $this->validateHero($request, $hero);
        $willBeActive = $request->boolean('is_active');

        if ($hero->is_active && !$willBeActive && !$this->hasOtherActive($hero)) {
            return back()->with('error', 'É necessário manter ao menos 1 hero ativo.');
        }

        $validated['is_active'] = $willBeActive;

        $hero->update(collect($validated)->except(['main_image', 'new_photos'])->all());

        $this->handleUploads($request, $hero);

        return back()->with('success', 'Hero atualizado!');
    }

    public function toggleActive(Hero $hero): RedirectResponse
    {
        if ($hero->is_active && !$this->hasOtherActive($hero)) {
            return back()->with('error', 'É necessário manter ao menos 1 hero ativo.');
        }

        $hero->update(['is_active' => !$hero->is_active]);

        return back()->with('success', 'Hero atualizado!');
    }

    public function reorder(Request $request): JsonResponse
    {
        $request->validate(['order' => ['required', 'array']]);

        foreach ($request->order as $item) {
            Hero::where('id', $item['id'])->update(['order' => $item['order']]);
        }

        return response()->json(['ok' => true]);
    }

    public function destroyPhoto(HeroPhoto $photo): RedirectResponse
    {
        $heroId = $photo->hero_id;
        Storage::disk('public')->delete($photo->path);
        $photo->delete();

        return redirect()->route('admin.heroes.edit', $heroId)->with('success', 'Foto removida.');
    }

    public function destroy(Hero $hero): RedirectResponse
    {
        if ($hero->is_active && !$this->hasOtherActive($hero)) {
            return back()->with('error', 'Não é possível remover o único hero ativo. Ative outro antes de remover este.');
        }

        if ($hero->main_image) {
            Storage::disk('public')->delete($hero->main_image);
        }

        foreach ($hero->photos as $photo) {
            Storage::disk('public')->delete($photo->path);
        }

        $hero->delete();

        return redirect()->route('admin.heroes.index')->with('success', 'Hero removido.');
    }

    private function validateHero(Request $request, ?Hero $hero = null): array
    {
        $layout = $hero?->layout ?? $request->input('layout');
        $existingPhotosCount = $hero?->photos()->count() ?? 0;
        $photoLimit = self::PHOTO_LIMITS[$layout] ?? 0;
        $maxNewPhotos = max(0, $photoLimit - $existingPhotosCount);

        $rules = [
            'title_pt'     => ['required', 'string', 'max:150'],
            'subtitle_pt'  => ['nullable', 'string', 'max:300'],
            'cta_label_pt' => ['nullable', 'string', 'max:60'],
            'cta_link'     => ['required', 'string', 'in:' . implode(',', array_keys(Hero::CTA_LINKS))],
            'main_image'   => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'is_active'    => ['boolean'],
        ];

        if ($photoLimit > 0) {
            $rules['new_photos'] = ['nullable', 'array', 'max:' . $maxNewPhotos];
            $rules['new_photos.*'] = ['image', 'mimes:jpg,jpeg,png,webp', 'max:5120'];
        }

        if (!$hero) {
            $rules['layout'] = ['required', 'string', 'in:' . implode(',', array_keys(self::PHOTO_LIMITS))];
        }

        return $request->validate($rules, [
            'new_photos.max' => "Este layout permite no máximo {$photoLimit} fotos.",
        ]);
    }

    private function handleUploads(Request $request, Hero $hero): void
    {
        if ($request->hasFile('main_image')) {
            if ($hero->main_image) {
                Storage::disk('public')->delete($hero->main_image);
            }
            $path = $request->file('main_image')->store("heroes/{$hero->id}", 'public');
            $hero->update(['main_image' => $path]);
        }

        if ($request->hasFile('new_photos')) {
            $nextOrder = (int) $hero->photos()->max('order') + 1;
            foreach ($request->file('new_photos') as $i => $file) {
                $path = $file->store("heroes/{$hero->id}", 'public');
                HeroPhoto::create([
                    'hero_id' => $hero->id,
                    'path'    => $path,
                    'order'   => $nextOrder + $i,
                ]);
            }
        }
    }

    private function hasOtherActive(Hero $exceptHero): bool
    {
        return Hero::where('is_active', true)->where('id', '!=', $exceptHero->id)->exists();
    }
}
