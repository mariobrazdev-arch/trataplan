<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Partner;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;

class PartnerAdminController extends Controller
{
    public function index(): Response
    {
        $partners = Partner::orderBy('order')->get()->map(fn ($p) => [
            'id' => $p->id,
            'name' => $p->name,
            'logo_url' => $p->logo_url,
            'website' => $p->website,
            'is_active' => $p->is_active,
            'order' => $p->order,
        ]);

        return Inertia::render('Admin/Partners/Index', ['partners' => $partners]);
    }

    public function create(): Response
    {
        return Inertia::render('Admin/Partners/Form', ['partner' => null]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'website' => ['nullable', 'url', 'max:255'],
            'description_pt' => ['nullable', 'string', 'max:500'],
            'description_en' => ['nullable', 'string', 'max:500'],
            'order' => ['integer', 'min:0'],
            'is_active' => ['boolean'],
            'logo' => ['nullable', 'image', 'max:2048'],
        ]);

        $logoPath = null;
        if ($request->hasFile('logo')) {
            $logoPath = $request->file('logo')->store('partners', 'public');
        }

        Partner::create([...$validated, 'logo' => $logoPath]);

        return redirect()->route('admin.partners.index')->with('success', 'Parceiro adicionado!');
    }

    public function edit(Partner $partner): Response
    {
        return Inertia::render('Admin/Partners/Form', [
            'partner' => [
                'id' => $partner->id,
                'name' => $partner->name,
                'logo_url' => $partner->logo_url,
                'website' => $partner->website,
                'description_pt' => $partner->description_pt,
                'description_en' => $partner->description_en,
                'order' => $partner->order,
                'is_active' => $partner->is_active,
            ],
        ]);
    }

    public function update(Request $request, Partner $partner): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'website' => ['nullable', 'url', 'max:255'],
            'description_pt' => ['nullable', 'string', 'max:500'],
            'description_en' => ['nullable', 'string', 'max:500'],
            'order' => ['integer', 'min:0'],
            'is_active' => ['boolean'],
            'logo' => ['nullable', 'image', 'max:2048'],
        ]);

        if ($request->hasFile('logo')) {
            if ($partner->logo) Storage::disk('public')->delete($partner->logo);
            $validated['logo'] = $request->file('logo')->store('partners', 'public');
        }

        $partner->update($validated);

        return back()->with('success', 'Parceiro atualizado!');
    }

    public function destroy(Partner $partner): RedirectResponse
    {
        if ($partner->logo) Storage::disk('public')->delete($partner->logo);
        $partner->delete();

        return redirect()->route('admin.partners.index')->with('success', 'Parceiro removido.');
    }

    public function reorder(Request $request): \Illuminate\Http\JsonResponse
    {
        $request->validate(['order' => ['required', 'array']]);

        foreach ($request->order as $item) {
            Partner::where('id', $item['id'])->update(['order' => $item['order']]);
        }

        return response()->json(['success' => true]);
    }
}
