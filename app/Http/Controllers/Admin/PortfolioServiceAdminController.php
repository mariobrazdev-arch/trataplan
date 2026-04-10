<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PortfolioService;
use App\Models\PortfolioServicePhoto;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;

class PortfolioServiceAdminController extends Controller
{
    public function index(): Response
    {
        $services = PortfolioService::with('photos')
            ->orderBy('order')
            ->get()
            ->map(fn ($s) => [
                'id'           => $s->id,
                'name_pt'      => $s->name_pt,
                'name_en'      => $s->name_en,
                'is_active'    => $s->is_active,
                'order'        => $s->order,
                'cover_url'    => $s->cover_url ?? $s->photos->first()?->url,
                'photos_count' => $s->photos->count(),
            ]);

        return Inertia::render('Admin/Portfolio/Services/Index', ['services' => $services]);
    }

    public function create(): Response
    {
        return Inertia::render('Admin/Portfolio/Services/Form', ['service' => null]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name_pt'        => ['required', 'string', 'max:200'],
            'name_en'        => ['required', 'string', 'max:200'],
            'description_pt' => ['nullable', 'string'],
            'description_en' => ['nullable', 'string'],
            'cover_photo'    => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:3072'],
            'order'          => ['integer', 'min:0'],
            'is_active'      => ['boolean'],
            'new_photos'     => ['nullable', 'array'],
            'new_photos.*'   => ['image', 'mimes:jpg,jpeg,png,webp', 'max:3072'],
        ]);

        $service = PortfolioService::create(collect($validated)->except(['cover_photo', 'new_photos'])->all());

        if ($request->hasFile('cover_photo')) {
            $coverPath = $request->file('cover_photo')->store("portfolio/services/{$service->id}", 'public');
            $service->update(['cover_photo' => $coverPath]);
        }

        $firstUploadedPhotoPath = null;
        if ($request->hasFile('new_photos')) {
            foreach ($request->file('new_photos') as $i => $file) {
                $path = $file->store("portfolio/services/{$service->id}", 'public');
                if ($i === 0) {
                    $firstUploadedPhotoPath = $path;
                }
                PortfolioServicePhoto::create(['service_id' => $service->id, 'path' => $path, 'order' => $i]);
            }
        }

        if (!$service->cover_photo && $firstUploadedPhotoPath) {
            $service->update(['cover_photo' => $firstUploadedPhotoPath]);
        }

        return redirect()->route('admin.portfolio.services.index')
            ->with('success', 'Serviço criado!');
    }

    public function edit(PortfolioService $service): Response
    {
        return Inertia::render('Admin/Portfolio/Services/Form', [
            'service' => [
                'id'             => $service->id,
                'name_pt'        => $service->name_pt,
                'name_en'        => $service->name_en,
                'description_pt' => $service->description_pt,
                'description_en' => $service->description_en,
                'cover_url'      => $service->cover_url,
                'order'          => $service->order,
                'is_active'      => $service->is_active,
                'photos'         => $service->photos->map(fn ($ph) => [
                    'id'  => $ph->id,
                    'url' => $ph->url,
                ]),
            ],
        ]);
    }

    public function update(Request $request, PortfolioService $service): RedirectResponse
    {
        $validated = $request->validate([
            'name_pt'        => ['required', 'string', 'max:200'],
            'name_en'        => ['required', 'string', 'max:200'],
            'description_pt' => ['nullable', 'string'],
            'description_en' => ['nullable', 'string'],
            'cover_photo'    => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:3072'],
            'order'          => ['integer', 'min:0'],
            'is_active'      => ['boolean'],
            'new_photos'     => ['nullable', 'array'],
            'new_photos.*'   => ['image', 'mimes:jpg,jpeg,png,webp', 'max:3072'],
        ]);

        $service->update(collect($validated)->except(['cover_photo', 'new_photos'])->all());

        if ($request->hasFile('cover_photo')) {
            if ($service->cover_photo) {
                Storage::disk('public')->delete($service->cover_photo);
            }
            $coverPath = $request->file('cover_photo')->store("portfolio/services/{$service->id}", 'public');
            $service->update(['cover_photo' => $coverPath]);
        }

        $firstUploadedPhotoPath = null;
        if ($request->hasFile('new_photos')) {
            $nextOrder = $service->photos()->max('order') + 1;
            foreach ($request->file('new_photos') as $i => $file) {
                $path = $file->store("portfolio/services/{$service->id}", 'public');
                if ($i === 0) {
                    $firstUploadedPhotoPath = $path;
                }
                PortfolioServicePhoto::create(['service_id' => $service->id, 'path' => $path, 'order' => $nextOrder + $i]);
            }
        }

        if (!$service->cover_photo && $firstUploadedPhotoPath) {
            $service->update(['cover_photo' => $firstUploadedPhotoPath]);
        }

        return back()->with('success', 'Serviço atualizado!');
    }

    public function destroyPhoto(PortfolioServicePhoto $photo): RedirectResponse
    {
        $serviceId = $photo->service_id;
        Storage::disk('public')->delete($photo->path);
        $photo->delete();

        return redirect()->route('admin.portfolio.services.edit', $serviceId)
            ->with('success', 'Foto removida.');
    }

    public function destroy(PortfolioService $service): RedirectResponse
    {
        if ($service->cover_photo) {
            Storage::disk('public')->delete($service->cover_photo);
        }

        foreach ($service->photos as $photo) {
            Storage::disk('public')->delete($photo->path);
        }
        $service->delete();

        return redirect()->route('admin.portfolio.services.index')
            ->with('success', 'Serviço removido.');
    }
}
