<script setup>
import { ref, computed } from 'vue';
import SeoHead from '@/Components/Shared/SeoHead.vue';
import { Link } from '@inertiajs/vue3';
import { useSettings } from '@/composables/useSettings';
import { useWhatsAppPicker } from '@/composables/useWhatsAppPicker';

const props = defineProps({
    about: Object,
    seo: Object,
});

const { setting } = useSettings();
const { open: openWhatsApp } = useWhatsAppPicker();

// ── Defaults estáticos (fallback quando não há versão ativa) ─────────────────
const DEFAULTS = {
    hero_title_pt:        'Trataplan: A Estrutura da Inovação Sustentável.',
    hero_subtitle_pt:     'Há mais de uma década transformando madeira em durabilidade, no coração do Oeste Catarinense.',
    hero_photo_url:       '/images/defaults/about-hero.jpg',
    hero_badge_value:     '+15',
    hero_badge_label_pt:  'Anos de mercado',
    history_title_pt:     'Raízes profundas no Oeste Catarinense',
    history_content_pt:   'A Trataplan nasceu da visão de oferecer madeira tratada de alta qualidade para o mercado do Oeste Catarinense e de toda a região Sul do Brasil. Nossa missão é entregar durabilidade com responsabilidade ambiental.\n\nUtilizamos exclusivamente madeiras de reflorestamento — Pinus e Eucalipto — tratadas pelo processo de autoclave vácuo-pressão, o mais eficaz para garantir resistência a cupins, fungos e intempéries por décadas.',
    history_photo_url:    '/images/defaults/about-history.jpg',
    history_youtube_url:  null,
    history_items: [
        { value: '15+',  label_pt: 'Anos' },
        { value: '100%', label_pt: 'Reflorestamento' },
        { value: 'SC',   label_pt: 'Santa Catarina' },
    ],
    quality_title_pt:     'Processo e Qualidade',
    quality_subtitle_pt:  'Cada etapa do nosso processo é pensada para entregar a máxima durabilidade e segurança.',
    quality_items: [
        {
            icon:     'park',
            title_pt: '100% Reflorestamento',
            desc_pt:  'Trabalhamos exclusivamente com Eucalipto e Pinus oriundos de florestas plantadas e manejadas de forma sustentável.',
        },
        {
            icon:     'precision_manufacturing',
            title_pt: 'Autoclave Vácuo-Pressão',
            desc_pt:  'O processo de impregnação sob alta pressão garante penetração total do preservativo em cada fibra da madeira.',
        },
        {
            icon:     'verified_user',
            title_pt: 'Garantia de 15 Anos',
            desc_pt:  'Confiança respaldada por certificação e pelo compromisso com a qualidade que só a experiência pode oferecer.',
        },
    ],
    cta_title_pt:    'Madeira 100% de Reflorestamento.',
    cta_subtitle_pt: 'Nosso compromisso com o planeta é tão sólido quanto a madeira que produzimos. Cada peça vendida representa uma árvore de floresta plantada.',
    cta_photo_url:   '/images/defaults/about-hero.jpg',
};

function merge(key) {
    const val = props.about?.[key];
    if (Array.isArray(val)) return val.length ? val : DEFAULTS[key];
    return (val !== null && val !== undefined && val !== '') ? val : DEFAULTS[key];
}

const data = computed(() => ({
    hero_title_pt:        merge('hero_title_pt'),
    hero_subtitle_pt:     merge('hero_subtitle_pt'),
    hero_photo_url:       merge('hero_photo_url'),
    hero_badge_value:     merge('hero_badge_value'),
    hero_badge_label_pt:  merge('hero_badge_label_pt'),
    history_title_pt:     merge('history_title_pt'),
    history_content_pt:   merge('history_content_pt'),
    history_photo_url:    merge('history_photo_url'),
    history_youtube_url:  merge('history_youtube_url'),
    history_items:        merge('history_items'),
    quality_title_pt:     merge('quality_title_pt'),
    quality_subtitle_pt:  merge('quality_subtitle_pt'),
    quality_items:        merge('quality_items'),
    cta_title_pt:         merge('cta_title_pt'),
    cta_subtitle_pt:      merge('cta_subtitle_pt'),
    cta_photo_url:        merge('cta_photo_url'),
}));

// ── YouTube embed ────────────────────────────────────────────────────────────
const youtubeVideo = computed(() => {
    const url = data.value.history_youtube_url;
    if (!url) return null;
    let id = null;
    let isShort = false;
    try {
        const u = new URL(url);
        if (u.hostname === 'youtu.be') {
            id = u.pathname.slice(1).split('?')[0];
        } else if (u.pathname.startsWith('/shorts/')) {
            id = u.pathname.replace('/shorts/', '').split('?')[0];
            isShort = true;
        } else {
            id = u.searchParams.get('v');
        }
    } catch {
        const m = url.match(/(?:v=|youtu\.be\/|shorts\/)([A-Za-z0-9_-]{11})/);
        if (m) id = m[1];
    }
    if (!id) return null;
    return {
        embedUrl: `https://www.youtube.com/embed/${id}?rel=0&modestbranding=1`,
        isShort,
    };
});

// ── Carousel qualidade ───────────────────────────────────────────────────────
const qualitySlideStart = ref(0);
const hasQualityCarousel = computed(() => data.value.quality_items.length > 3);
const visibleQualityItems = computed(() => {
    const items = data.value.quality_items;
    if (items.length <= 3) return items;
    const out = [];
    for (let i = 0; i < 3; i++) {
        out.push(items[(qualitySlideStart.value + i) % items.length]);
    }
    return out;
});
function nextQuality() {
    if (!hasQualityCarousel.value) return;
    qualitySlideStart.value = (qualitySlideStart.value + 1) % data.value.quality_items.length;
}
function prevQuality() {
    if (!hasQualityCarousel.value) return;
    qualitySlideStart.value = (qualitySlideStart.value - 1 + data.value.quality_items.length) % data.value.quality_items.length;
}
</script>

<template>
    <SeoHead v-bind="seo" />

    <!-- ─── HERO ─────────────────────────────────────────────────────────────── -->
    <section class="relative min-h-[480px] md:min-h-[560px] flex items-center overflow-hidden" style="background-color: #012d1d;">
        <div class="absolute inset-0 opacity-5"
             style="background-image: repeating-linear-gradient(45deg, #fff 0, #fff 1px, transparent 0, transparent 50%); background-size: 20px 20px;"></div>

        <div class="container mx-auto px-6 py-24 grid md:grid-cols-2 gap-12 items-center relative z-10">
            <div>
                <span class="inline-block text-xs font-black uppercase tracking-[0.3em] mb-6"
                      style="color: #fbbf24; font-family: Manrope, sans-serif;">
                    Sobre a Empresa
                </span>
                <h1 class="text-white font-headline font-extrabold text-3xl md:text-5xl lg:text-6xl leading-[1.05] tracking-tight mb-8">
                    {{ data.hero_title_pt }}
                </h1>
                <p class="text-lg leading-relaxed mb-10" style="color: rgba(255,255,255,0.6); font-family: Newsreader, serif; font-style: italic;">
                    {{ data.hero_subtitle_pt }}
                </p>
                <button type="button" @click="openWhatsApp()"
                   class="inline-flex items-center gap-2 px-7 py-3.5 rounded-xl font-bold text-sm transition-all active:scale-95 hover:opacity-90"
                   style="background-color: #fbbf24; color: #012d1d; font-family: Manrope, sans-serif;">
                    <svg class="w-4 h-4 shrink-0" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
                    </svg>
                    Falar com a equipe
                </button>
            </div>

            <div class="relative hidden md:block">
                <div class="relative rounded-2xl overflow-hidden shadow-2xl h-64 md:h-[420px]">
                    <img
                        :src="data.hero_photo_url"
                        alt="Floresta de pinus – Trataplan"
                        class="w-full h-full object-cover"
                    />
                    <div class="absolute inset-0" style="background: linear-gradient(135deg, rgba(1,45,29,0.3) 0%, transparent 60%);"></div>
                </div>
                <div class="absolute -bottom-6 -left-6 rounded-2xl px-6 py-5 shadow-xl"
                     style="background-color: #fbbf24;">
                    <p class="font-black text-4xl leading-none" style="color: #012d1d; font-family: Manrope, sans-serif;">
                        {{ data.hero_badge_value }}
                    </p>
                    <p class="text-xs font-bold uppercase tracking-widest mt-1" style="color: #012d1d; font-family: Manrope, sans-serif;">
                        {{ data.hero_badge_label_pt }}
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- ─── NOSSA HISTÓRIA ───────────────────────────────────────────────────── -->
    <section class="py-24" style="background-color: #fcf9f4;">
        <div class="container mx-auto px-6">
            <div class="grid md:grid-cols-2 gap-16 items-center">

                <div class="relative">
                    <!-- Vídeo YouTube -->
                    <div v-if="youtubeVideo" class="flex justify-center">
                        <!-- Short: container portrait centralizado -->
                        <div v-if="youtubeVideo.isShort"
                             class="rounded-2xl shadow-xl overflow-hidden w-full max-w-[280px]"
                             style="aspect-ratio: 9/16;">
                            <iframe
                                :src="youtubeVideo.embedUrl"
                                class="w-full h-full"
                                frameborder="0"
                                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                                allowfullscreen
                                title="Vídeo Trataplan"
                            ></iframe>
                        </div>
                        <!-- Vídeo normal: container landscape -->
                        <div v-else
                             class="w-full rounded-2xl shadow-xl overflow-hidden h-56 md:h-[420px]">
                            <iframe
                                :src="youtubeVideo.embedUrl"
                                class="w-full h-full"
                                frameborder="0"
                                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                                allowfullscreen
                                title="Vídeo Trataplan"
                            ></iframe>
                        </div>
                    </div>
                    <!-- Foto (padrão quando não há vídeo) -->
                    <div v-else>
                        <img
                            :src="data.history_photo_url"
                            alt="Madeira tratada – Trataplan"
                            class="w-full rounded-2xl shadow-xl object-cover h-64 md:h-[420px]"
                        />
                        <div class="absolute bottom-6 right-6 rounded-xl px-5 py-4 shadow-lg"
                             style="background: rgba(1,45,29,0.92); backdrop-filter: blur(8px);">
                            <p class="font-black text-xl text-white leading-none" style="font-family: Manrope, sans-serif;">{{ setting('contact_address', 'SC-283, Planalto Alegre - SC') }}</p>
                        </div>
                    </div>
                </div>

                <div>
                    <span class="text-xs font-black uppercase tracking-[0.3em] mb-4 block"
                          style="color: #fbbf24; font-family: Manrope, sans-serif;">
                        Nossa História
                    </span>
                    <h2 class="font-headline font-extrabold text-4xl mb-6 leading-tight" style="color: #012d1d;">
                        {{ data.history_title_pt }}
                    </h2>

                    <div class="space-y-4 text-lg leading-relaxed" style="color: #4a5568; font-family: Newsreader, serif;">
                        <p v-for="(paragraph, i) in data.history_content_pt.split('\n\n')" :key="i">
                            {{ paragraph }}
                        </p>
                    </div>

                    <div v-if="data.history_items.length"
                         class="grid gap-6 mt-10 pt-8 border-t"
                         :style="`grid-template-columns: repeat(${Math.min(data.history_items.length, 4)}, minmax(0,1fr)); border-color: rgba(1,45,29,0.1);`">
                        <div v-for="item in data.history_items" :key="item.value">
                            <p class="font-black text-3xl" style="color: #012d1d; font-family: Manrope, sans-serif;">
                                {{ item.value }}
                            </p>
                            <p class="text-xs uppercase tracking-widest mt-1" style="color: #717973; font-family: Manrope, sans-serif;">
                                {{ item.label_pt }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ─── PROCESSO E QUALIDADE ─────────────────────────────────────────────── -->
    <section class="py-24" style="background-color: #f0ede8;">
        <div class="container mx-auto px-6">

            <div class="flex items-end justify-between mb-16">
                <div class="max-w-2xl">
                    <span class="text-xs font-black uppercase tracking-[0.3em] mb-4 block"
                          style="color: #fbbf24; font-family: Manrope, sans-serif;">
                        Nossa Qualidade
                    </span>
                    <h2 class="font-headline font-extrabold text-4xl md:text-5xl leading-tight" style="color: #012d1d;">
                        {{ data.quality_title_pt }}
                    </h2>
                    <p class="mt-4 text-lg" style="color: #717973; font-family: Newsreader, serif; font-style: italic;">
                        {{ data.quality_subtitle_pt }}
                    </p>
                </div>

                <div v-if="hasQualityCarousel" class="flex gap-3 flex-shrink-0 ml-8">
                    <button
                        type="button"
                        @click="prevQuality"
                        class="w-11 h-11 rounded-full border flex items-center justify-center transition-colors"
                        style="border-color: rgba(1,45,29,0.2); color: #012d1d;"
                        aria-label="Anterior"
                    >
                        <span class="material-symbols-outlined">arrow_back</span>
                    </button>
                    <button
                        type="button"
                        @click="nextQuality"
                        class="w-11 h-11 rounded-full border flex items-center justify-center transition-colors"
                        style="border-color: rgba(1,45,29,0.2); color: #012d1d;"
                        aria-label="Próximo"
                    >
                        <span class="material-symbols-outlined">arrow_forward</span>
                    </button>
                </div>
            </div>

            <div class="grid sm:grid-cols-2 md:grid-cols-3 gap-8">
                <div
                    v-for="(item, i) in visibleQualityItems"
                    :key="item.title_pt + i"
                    class="rounded-2xl p-8 flex flex-col gap-5 transition-all duration-300 hover:-translate-y-1 hover:shadow-lg"
                    style="background-color: #fff;"
                >
                    <div class="w-14 h-14 rounded-xl flex items-center justify-center shrink-0"
                         style="background-color: #012d1d;">
                        <span class="material-symbols-outlined text-2xl"
                              style="color: #fbbf24; font-variation-settings: 'FILL' 1;">{{ item.icon || 'star' }}</span>
                    </div>
                    <div>
                        <h3 class="font-headline font-bold text-xl mb-3" style="color: #012d1d;">
                            {{ item.title_pt }}
                        </h3>
                        <p class="leading-relaxed" style="color: #717973; font-family: Newsreader, serif;">
                            {{ item.desc_pt }}
                        </p>
                    </div>
                    <span class="text-5xl font-black mt-auto self-end leading-none"
                          style="color: rgba(1,45,29,0.07); font-family: Manrope, sans-serif;">
                        0{{ i + 1 }}
                    </span>
                </div>
            </div>
        </div>
    </section>

    <!-- ─── CTA ──────────────────────────────────────────────────────────────── -->
    <section class="relative overflow-hidden py-24" style="background-color: #012d1d;">
        <div class="absolute top-0 right-0 w-1/2 h-full opacity-10"
             style="background: radial-gradient(circle at 80% 50%, #fbbf24 0%, transparent 70%);"></div>

        <div class="container mx-auto px-6 grid md:grid-cols-2 gap-12 items-center relative z-10">
            <div>
                <h2 class="text-white font-headline font-extrabold text-5xl md:text-6xl leading-[1.05] tracking-tight mb-8">
                    {{ data.cta_title_pt }}
                </h2>
                <p class="text-lg leading-relaxed mb-10" style="color: rgba(255,255,255,0.55); font-family: Newsreader, serif; font-style: italic;">
                    {{ data.cta_subtitle_pt }}
                </p>
                <div class="flex flex-wrap gap-4">
                    <button type="button" @click="openWhatsApp()"
                       class="inline-flex items-center gap-2 px-7 py-3.5 rounded-xl font-bold text-sm transition-all active:scale-95 hover:opacity-90"
                       style="background-color: #fbbf24; color: #012d1d; font-family: Manrope, sans-serif;">
                        Solicitar orçamento
                    </button>
                    <Link href="/portfolio"
                          class="inline-flex items-center gap-2 px-7 py-3.5 rounded-xl font-bold text-sm border transition-all active:scale-95 hover:bg-white/5"
                          style="border-color: rgba(255,255,255,0.25); color: rgba(255,255,255,0.8); font-family: Manrope, sans-serif;">
                        Ver produtos
                    </Link>
                </div>
            </div>

            <div class="hidden md:flex justify-center">
                <div class="relative">
                    <div class="w-52 h-52 md:w-72 md:h-72 rounded-full overflow-hidden border-4 shadow-2xl"
                         style="border-color: rgba(251,191,36,0.4);">
                        <img
                            :src="data.cta_photo_url"
                            alt="Floresta de reflorestamento"
                            class="w-full h-full object-cover"
                        />
                    </div>
                    <div class="absolute -inset-4 rounded-full border opacity-20"
                         style="border-color: #fbbf24;"></div>
                    <div class="absolute -bottom-4 -right-4 w-20 h-20 rounded-full flex flex-col items-center justify-center shadow-xl"
                         style="background-color: #fbbf24;">
                        <span class="material-symbols-outlined text-2xl" style="color: #012d1d; font-variation-settings: 'FILL' 1;">eco</span>
                        <span class="text-[9px] font-black uppercase text-center leading-tight mt-0.5" style="color: #012d1d; font-family: Manrope, sans-serif;">
                            ECO<br>CERT
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </section>
</template>
