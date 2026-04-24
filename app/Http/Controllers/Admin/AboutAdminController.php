<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AboutPageVersion;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;

class AboutAdminController extends Controller
{
    public function index(): Response
    {
        $versions = AboutPageVersion::latest()->get()->map(fn ($v) => [
            'id'                  => $v->id,
            'version_label'       => $v->version_label,
            'is_active'           => $v->is_active,
            'created_at'          => $v->created_at->format('d/m/Y H:i'),
            // Hero
            'hero_title_pt'       => $v->hero_title_pt,
            'hero_title_en'       => $v->hero_title_en,
            'hero_subtitle_pt'    => $v->hero_subtitle_pt,
            'hero_subtitle_en'    => $v->hero_subtitle_en,
            'hero_photo_url'      => $v->hero_photo_url,
            'hero_badge_value'    => $v->hero_badge_value,
            'hero_badge_label_pt' => $v->hero_badge_label_pt,
            'hero_badge_label_en' => $v->hero_badge_label_en,
            // História
            'history_title_pt'    => $v->history_title_pt,
            'history_title_en'    => $v->history_title_en,
            'history_content_pt'  => $v->history_content_pt,
            'history_content_en'  => $v->history_content_en,
            'history_photo_url'   => $v->history_photo_url,
            'history_youtube_url' => $v->history_youtube_url,
            'history_items'       => $v->history_items ?? [],
            // Qualidade
            'quality_title_pt'    => $v->quality_title_pt,
            'quality_title_en'    => $v->quality_title_en,
            'quality_subtitle_pt' => $v->quality_subtitle_pt,
            'quality_subtitle_en' => $v->quality_subtitle_en,
            'quality_items'       => $v->quality_items ?? [],
            // CTA
            'cta_section_name'    => $v->cta_section_name,
            'cta_title_pt'        => $v->cta_title_pt,
            'cta_title_en'        => $v->cta_title_en,
            'cta_subtitle_pt'     => $v->cta_subtitle_pt,
            'cta_subtitle_en'     => $v->cta_subtitle_en,
            'cta_photo_url'       => $v->cta_photo_url,
        ]);

        return Inertia::render('Admin/About/Index', ['versions' => $versions]);
    }

    public function create(Request $request): Response
    {
        $fromId = $request->query('from');
        $base = $fromId
            ? AboutPageVersion::find($fromId)
            : AboutPageVersion::active()->latest()->first();

        $prefill = $base ? [
            'version_label'       => null,
            'hero_title_pt'       => $base->hero_title_pt,
            'hero_title_en'       => $base->hero_title_en,
            'hero_subtitle_pt'    => $base->hero_subtitle_pt,
            'hero_subtitle_en'    => $base->hero_subtitle_en,
            'hero_badge_value'    => $base->hero_badge_value,
            'hero_badge_label_pt' => $base->hero_badge_label_pt,
            'hero_badge_label_en' => $base->hero_badge_label_en,
            'hero_photo_url'      => $base->hero_photo_url,
            'hero_photo_path'     => $base->hero_photo,
            'history_title_pt'    => $base->history_title_pt,
            'history_title_en'    => $base->history_title_en,
            'history_content_pt'  => $base->history_content_pt,
            'history_content_en'  => $base->history_content_en,
            'history_photo_url'   => $base->history_photo_url,
            'history_photo_path'  => $base->history_photo,
            'history_youtube_url' => $base->history_youtube_url,
            'history_items'       => $base->history_items ?? [],
            'quality_title_pt'    => $base->quality_title_pt,
            'quality_title_en'    => $base->quality_title_en,
            'quality_subtitle_pt' => $base->quality_subtitle_pt,
            'quality_subtitle_en' => $base->quality_subtitle_en,
            'quality_items'       => $base->quality_items ?? [],
            'cta_section_name'    => $base->cta_section_name,
            'cta_title_pt'        => $base->cta_title_pt,
            'cta_title_en'        => $base->cta_title_en,
            'cta_subtitle_pt'     => $base->cta_subtitle_pt,
            'cta_subtitle_en'     => $base->cta_subtitle_en,
            'cta_photo_url'       => $base->cta_photo_url,
            'cta_photo_path'      => $base->cta_photo,
        ] : null;

        return Inertia::render('Admin/About/Form', ['prefill' => $prefill]);
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'version_label'              => ['nullable', 'string', 'max:150'],
            'activate_now'               => ['boolean'],
            'hero_title_pt'              => ['nullable', 'string', 'max:255'],
            'hero_title_en'              => ['nullable', 'string', 'max:255'],
            'hero_subtitle_pt'           => ['nullable', 'string', 'max:1000'],
            'hero_subtitle_en'           => ['nullable', 'string', 'max:1000'],
            'hero_photo'                 => ['nullable', 'image', 'max:3072'],
            'hero_badge_value'           => ['nullable', 'string', 'max:50'],
            'hero_badge_label_pt'        => ['nullable', 'string', 'max:100'],
            'hero_badge_label_en'        => ['nullable', 'string', 'max:100'],
            'history_title_pt'           => ['nullable', 'string', 'max:255'],
            'history_title_en'           => ['nullable', 'string', 'max:255'],
            'history_content_pt'         => ['nullable', 'string'],
            'history_content_en'         => ['nullable', 'string'],
            'history_photo'              => ['nullable', 'image', 'max:3072'],
            'history_youtube_url'        => ['nullable', 'string', 'max:500'],
            'history_items'              => ['nullable', 'array'],
            'history_items.*.value'      => ['nullable', 'string', 'max:50'],
            'history_items.*.label_pt'   => ['nullable', 'string', 'max:100'],
            'history_items.*.label_en'   => ['nullable', 'string', 'max:100'],
            'quality_title_pt'           => ['nullable', 'string', 'max:255'],
            'quality_title_en'           => ['nullable', 'string', 'max:255'],
            'quality_subtitle_pt'        => ['nullable', 'string', 'max:1000'],
            'quality_subtitle_en'        => ['nullable', 'string', 'max:1000'],
            'quality_items'              => ['nullable', 'array'],
            'quality_items.*.icon'       => ['nullable', 'string', 'max:100'],
            'quality_items.*.title_pt'   => ['nullable', 'string', 'max:150'],
            'quality_items.*.title_en'   => ['nullable', 'string', 'max:150'],
            'quality_items.*.desc_pt'    => ['nullable', 'string', 'max:500'],
            'quality_items.*.desc_en'    => ['nullable', 'string', 'max:500'],
            'cta_section_name'           => ['nullable', 'string', 'max:100'],
            'cta_title_pt'               => ['nullable', 'string', 'max:255'],
            'cta_title_en'               => ['nullable', 'string', 'max:255'],
            'cta_subtitle_pt'            => ['nullable', 'string', 'max:1000'],
            'cta_subtitle_en'            => ['nullable', 'string', 'max:1000'],
            'keep_hero_photo'            => ['nullable', 'string'],
            'keep_history_photo'         => ['nullable', 'string'],
            'cta_photo'                  => ['nullable', 'image', 'max:3072'],
            'keep_cta_photo'             => ['nullable', 'string'],
        ]);

        $heroPhoto = null;
        if ($request->hasFile('hero_photo')) {
            $heroPhoto = $request->file('hero_photo')->store('about', 'public');
        } elseif ($request->input('keep_hero_photo')) {
            $heroPhoto = $request->input('keep_hero_photo');
        }

        $historyPhoto = null;
        if ($request->hasFile('history_photo')) {
            $historyPhoto = $request->file('history_photo')->store('about', 'public');
        } elseif ($request->input('keep_history_photo')) {
            $historyPhoto = $request->input('keep_history_photo');
        }

        $ctaPhoto = null;
        if ($request->hasFile('cta_photo')) {
            $ctaPhoto = $request->file('cta_photo')->store('about', 'public');
        } elseif ($request->input('keep_cta_photo')) {
            $ctaPhoto = $request->input('keep_cta_photo');
        }

        DB::transaction(function () use ($request, $heroPhoto, $historyPhoto, $ctaPhoto) {
            $activateNow = filter_var($request->input('activate_now', false), FILTER_VALIDATE_BOOLEAN);

            if ($activateNow) {
                AboutPageVersion::where('is_active', true)->update(['is_active' => false]);
            }

            AboutPageVersion::create([
                'is_active'           => $activateNow,
                'version_label'       => $request->input('version_label'),
                'hero_title_pt'       => $request->input('hero_title_pt'),
                'hero_title_en'       => $request->input('hero_title_en'),
                'hero_subtitle_pt'    => $request->input('hero_subtitle_pt'),
                'hero_subtitle_en'    => $request->input('hero_subtitle_en'),
                'hero_photo'          => $heroPhoto,
                'hero_badge_value'    => $request->input('hero_badge_value'),
                'hero_badge_label_pt' => $request->input('hero_badge_label_pt'),
                'hero_badge_label_en' => $request->input('hero_badge_label_en'),
                'history_title_pt'    => $request->input('history_title_pt'),
                'history_title_en'    => $request->input('history_title_en'),
                'history_content_pt'  => $request->input('history_content_pt'),
                'history_content_en'  => $request->input('history_content_en'),
                'history_photo'       => $historyPhoto,
                'history_youtube_url' => $request->input('history_youtube_url'),
                'history_items'       => $request->input('history_items', []),
                'quality_title_pt'    => $request->input('quality_title_pt'),
                'quality_title_en'    => $request->input('quality_title_en'),
                'quality_subtitle_pt' => $request->input('quality_subtitle_pt'),
                'quality_subtitle_en' => $request->input('quality_subtitle_en'),
                'quality_items'       => $request->input('quality_items', []),
                'cta_section_name'    => $request->input('cta_section_name'),
                'cta_title_pt'        => $request->input('cta_title_pt'),
                'cta_title_en'        => $request->input('cta_title_en'),
                'cta_subtitle_pt'     => $request->input('cta_subtitle_pt'),
                'cta_subtitle_en'     => $request->input('cta_subtitle_en'),
                'cta_photo'           => $ctaPhoto,
            ]);
        });

        return redirect()->route('admin.about.index')->with('success', 'Versão salva com sucesso!');
    }

    public function edit(AboutPageVersion $about): Response
    {
        return Inertia::render('Admin/About/Form', [
            'editId'  => $about->id,
            'prefill' => [
                'version_label'       => $about->version_label,
                'hero_title_pt'       => $about->hero_title_pt,
                'hero_title_en'       => $about->hero_title_en,
                'hero_subtitle_pt'    => $about->hero_subtitle_pt,
                'hero_subtitle_en'    => $about->hero_subtitle_en,
                'hero_badge_value'    => $about->hero_badge_value,
                'hero_badge_label_pt' => $about->hero_badge_label_pt,
                'hero_badge_label_en' => $about->hero_badge_label_en,
                'hero_photo_url'      => $about->hero_photo_url,
                'hero_photo_path'     => $about->hero_photo,
                'history_title_pt'    => $about->history_title_pt,
                'history_title_en'    => $about->history_title_en,
                'history_content_pt'  => $about->history_content_pt,
                'history_content_en'  => $about->history_content_en,
                'history_photo_url'   => $about->history_photo_url,
                'history_photo_path'  => $about->history_photo,
                'history_youtube_url' => $about->history_youtube_url,
                'history_items'       => $about->history_items ?? [],
                'quality_title_pt'    => $about->quality_title_pt,
                'quality_title_en'    => $about->quality_title_en,
                'quality_subtitle_pt' => $about->quality_subtitle_pt,
                'quality_subtitle_en' => $about->quality_subtitle_en,
                'quality_items'       => $about->quality_items ?? [],
                'cta_section_name'    => $about->cta_section_name,
                'cta_title_pt'        => $about->cta_title_pt,
                'cta_title_en'        => $about->cta_title_en,
                'cta_subtitle_pt'     => $about->cta_subtitle_pt,
                'cta_subtitle_en'     => $about->cta_subtitle_en,
                'cta_photo_url'       => $about->cta_photo_url,
                'cta_photo_path'      => $about->cta_photo,
                'activate_now'        => $about->is_active,
            ],
        ]);
    }

    public function update(Request $request, AboutPageVersion $about): RedirectResponse
    {
        $request->validate([
            'version_label'              => ['nullable', 'string', 'max:150'],
            'activate_now'               => ['boolean'],
            'hero_title_pt'              => ['nullable', 'string', 'max:255'],
            'hero_title_en'              => ['nullable', 'string', 'max:255'],
            'hero_subtitle_pt'           => ['nullable', 'string', 'max:1000'],
            'hero_subtitle_en'           => ['nullable', 'string', 'max:1000'],
            'hero_photo'                 => ['nullable', 'image', 'max:3072'],
            'hero_badge_value'           => ['nullable', 'string', 'max:50'],
            'hero_badge_label_pt'        => ['nullable', 'string', 'max:100'],
            'hero_badge_label_en'        => ['nullable', 'string', 'max:100'],
            'history_title_pt'           => ['nullable', 'string', 'max:255'],
            'history_title_en'           => ['nullable', 'string', 'max:255'],
            'history_content_pt'         => ['nullable', 'string'],
            'history_content_en'         => ['nullable', 'string'],
            'history_photo'              => ['nullable', 'image', 'max:3072'],
            'history_youtube_url'        => ['nullable', 'string', 'max:500'],
            'history_items'              => ['nullable', 'array'],
            'history_items.*.value'      => ['nullable', 'string', 'max:50'],
            'history_items.*.label_pt'   => ['nullable', 'string', 'max:100'],
            'history_items.*.label_en'   => ['nullable', 'string', 'max:100'],
            'quality_title_pt'           => ['nullable', 'string', 'max:255'],
            'quality_title_en'           => ['nullable', 'string', 'max:255'],
            'quality_subtitle_pt'        => ['nullable', 'string', 'max:1000'],
            'quality_subtitle_en'        => ['nullable', 'string', 'max:1000'],
            'quality_items'              => ['nullable', 'array'],
            'quality_items.*.icon'       => ['nullable', 'string', 'max:100'],
            'quality_items.*.title_pt'   => ['nullable', 'string', 'max:150'],
            'quality_items.*.title_en'   => ['nullable', 'string', 'max:150'],
            'quality_items.*.desc_pt'    => ['nullable', 'string', 'max:500'],
            'quality_items.*.desc_en'    => ['nullable', 'string', 'max:500'],
            'cta_section_name'           => ['nullable', 'string', 'max:100'],
            'cta_title_pt'               => ['nullable', 'string', 'max:255'],
            'cta_title_en'               => ['nullable', 'string', 'max:255'],
            'cta_subtitle_pt'            => ['nullable', 'string', 'max:1000'],
            'cta_subtitle_en'            => ['nullable', 'string', 'max:1000'],
            'keep_hero_photo'            => ['nullable', 'string'],
            'keep_history_photo'         => ['nullable', 'string'],
            'cta_photo'                  => ['nullable', 'image', 'max:3072'],
            'keep_cta_photo'             => ['nullable', 'string'],
        ]);

        $heroPhoto = $about->hero_photo;
        if ($request->hasFile('hero_photo')) {
            if ($about->hero_photo) Storage::disk('public')->delete($about->hero_photo);
            $heroPhoto = $request->file('hero_photo')->store('about', 'public');
        } elseif ($request->input('keep_hero_photo') === '') {
            if ($about->hero_photo) Storage::disk('public')->delete($about->hero_photo);
            $heroPhoto = null;
        }

        $historyPhoto = $about->history_photo;
        if ($request->hasFile('history_photo')) {
            if ($about->history_photo) Storage::disk('public')->delete($about->history_photo);
            $historyPhoto = $request->file('history_photo')->store('about', 'public');
        } elseif ($request->input('keep_history_photo') === '') {
            if ($about->history_photo) Storage::disk('public')->delete($about->history_photo);
            $historyPhoto = null;
        }

        $ctaPhoto = $about->cta_photo;
        if ($request->hasFile('cta_photo')) {
            if ($about->cta_photo) Storage::disk('public')->delete($about->cta_photo);
            $ctaPhoto = $request->file('cta_photo')->store('about', 'public');
        } elseif ($request->input('keep_cta_photo') === '') {
            if ($about->cta_photo) Storage::disk('public')->delete($about->cta_photo);
            $ctaPhoto = null;
        }

        DB::transaction(function () use ($request, $about, $heroPhoto, $historyPhoto, $ctaPhoto) {
            $activateNow = filter_var($request->input('activate_now', false), FILTER_VALIDATE_BOOLEAN);

            if ($activateNow && !$about->is_active) {
                AboutPageVersion::where('is_active', true)->update(['is_active' => false]);
            }

            $about->update([
                'is_active'           => $activateNow,
                'version_label'       => $request->input('version_label'),
                'hero_title_pt'       => $request->input('hero_title_pt'),
                'hero_title_en'       => $request->input('hero_title_en'),
                'hero_subtitle_pt'    => $request->input('hero_subtitle_pt'),
                'hero_subtitle_en'    => $request->input('hero_subtitle_en'),
                'hero_photo'          => $heroPhoto,
                'hero_badge_value'    => $request->input('hero_badge_value'),
                'hero_badge_label_pt' => $request->input('hero_badge_label_pt'),
                'hero_badge_label_en' => $request->input('hero_badge_label_en'),
                'history_title_pt'    => $request->input('history_title_pt'),
                'history_title_en'    => $request->input('history_title_en'),
                'history_content_pt'  => $request->input('history_content_pt'),
                'history_content_en'  => $request->input('history_content_en'),
                'history_photo'       => $historyPhoto,
                'history_youtube_url' => $request->input('history_youtube_url'),
                'history_items'       => $request->input('history_items', []),
                'quality_title_pt'    => $request->input('quality_title_pt'),
                'quality_title_en'    => $request->input('quality_title_en'),
                'quality_subtitle_pt' => $request->input('quality_subtitle_pt'),
                'quality_subtitle_en' => $request->input('quality_subtitle_en'),
                'quality_items'       => $request->input('quality_items', []),
                'cta_section_name'    => $request->input('cta_section_name'),
                'cta_title_pt'        => $request->input('cta_title_pt'),
                'cta_title_en'        => $request->input('cta_title_en'),
                'cta_subtitle_pt'     => $request->input('cta_subtitle_pt'),
                'cta_subtitle_en'     => $request->input('cta_subtitle_en'),
                'cta_photo'           => $ctaPhoto,
            ]);
        });

        return redirect()->route('admin.about.index')->with('success', 'Versão atualizada com sucesso!');
    }

    public function activate(AboutPageVersion $about): RedirectResponse
    {
        DB::transaction(function () use ($about) {
            AboutPageVersion::where('is_active', true)->update(['is_active' => false]);
            $about->update(['is_active' => true]);
        });

        return back()->with('success', 'Versão ativada!');
    }

    public function destroy(AboutPageVersion $about): RedirectResponse
    {
        if ($about->is_active) {
            return back()->with('error', 'Não é possível excluir a versão ativa. Ative outra versão primeiro.');
        }

        $about->delete();

        return back()->with('success', 'Versão excluída.');
    }
}
