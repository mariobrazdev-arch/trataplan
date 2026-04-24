<script setup>
import SeoHead from '@/Components/Shared/SeoHead.vue';
import { Link, useForm } from '@inertiajs/vue3';
import { useLocale } from '@/composables/useLocale';
import { useSettings } from '@/composables/useSettings';
import { useWhatsAppPicker } from '@/composables/useWhatsAppPicker';
import { computed, ref } from 'vue';

const props = defineProps({
    featuredPosts: Array,
    seo: Object,
    categories: Array,
    services: Array,
});

const { locale, t } = useLocale();
const { settings, setting, logoTransparentUrl, companyUnits } = useSettings();
const { open: openWhatsApp } = useWhatsAppPicker();

// Unidades: usa company_units do banco ou monta uma a partir dos campos legados
const units = computed(() => {
    if (companyUnits.value?.length) return companyUnits.value;
    const addr  = setting('contact_address', 'SC-283, Planalto Alegre - SC, 89882-000');
    const phone = setting('contact_phone',   '+55 49 9960-0329');
    const email = setting('contact_email',   'trataplan@trataplan.com.br');
    return [{ label: 'Sede', tipo: 'matriz', address: addr, phone, email }];
});

const activeIdx  = ref(0);
const activeUnit = computed(() => units.value[activeIdx.value] ?? units.value[0]);
const unitPhone  = computed(() => activeUnit.value?.phone  || setting('contact_phone', '+55 49 9960-0329'));
const unitEmail  = computed(() => activeUnit.value?.email  || setting('contact_email', 'trataplan@trataplan.com.br'));
const unitAddr   = computed(() => activeUnit.value?.address || setting('contact_address', 'SC-283, Planalto Alegre - SC, 89882-000'));
const unitWaNum  = computed(() => (activeUnit.value?.whatsapp || setting('whatsapp_number', '554999600329')).replace(/\D/g,''));
const unitWaUrl  = computed(() => `https://wa.me/${unitWaNum.value}`);

const bannerBg = computed(() => {
    const img = settings.value['banner_image'];
    if (!img) return 'https://images.unsplash.com/photo-1558618666-fcd25c85cd64?w=1920&q=80';
    return img.startsWith('http') ? img : `/storage/${img}`;
});

const bannerTitle = computed(() =>
    locale.value === 'en'
        ? (settings.value['banner_title_en'] || 'Treated Wood that Lasts Generations.')
        : (settings.value['banner_title_pt'] || 'Madeira Tratada que Dura Gerações.')
);

const bannerSubtitle = computed(() =>
    locale.value === 'en'
        ? (settings.value['banner_subtitle_en'] || 'Eucalyptus and Autoclaved Pine solutions for Construction, Fencing and Leisure with the robustness your project demands.')
        : (settings.value['banner_subtitle_pt'] || 'Soluções em Eucalipto e Pinus Autoclavado para Construção, Cercas e Lazer com a robustez que seu projeto exige.')
);

const segmentFallbackImgs = [
    'https://images.unsplash.com/photo-1555041469-a586c61ea9bc?w=600&q=80',
    'https://images.unsplash.com/photo-1504307651254-35680f356dfd?w=600&q=80',
    'https://images.unsplash.com/photo-1597200381847-30ec200eeb9a?w=600&q=80',
    'https://images.unsplash.com/photo-1416879595882-3373a0480b5b?w=600&q=80',
];
const segmentLabels = [
    { pt: 'Segmento', en: 'Segment' },
    { pt: 'Estrutural', en: 'Structural' },
    { pt: 'Rural', en: 'Rural' },
    { pt: 'Externo', en: 'Outdoor' },
];
const displayCategories = computed(() => {
    const source = props.categories || [];
    return source.slice(0, 4).map((cat, i) => ({
        ...cat,
        description: locale.value === 'en' ? (cat.description_en || '') : (cat.description_pt || ''),
        label: segmentLabels[i] ?? segmentLabels[0],
    }));
});

const contactAddress = computed(() => setting('contact_address', 'SC-283, Planalto Alegre - SC, 89882-000'));
const contactPhone = computed(() => setting('contact_phone', '+55 49 9960-0329'));
const contactEmail = computed(() => setting('contact_email', 'trataplan@trataplan.com.br'));

const contactForm = useForm({
    name: '',
    email: '',
    message: '',
    subject: 'orcamento',
    unit_email: '',
});

const defaultUnitEmail = computed(() => {
    const u = units.value.find(u => u.email) ?? units.value[0];
    return u?.email || '';
});

function fieldStyle(hasError) {
    return hasError
        ? 'background-color: #fff0f0; border: 2px solid #ba1a1a;'
        : 'background-color: #edeae5; border: 2px solid transparent;';
}

const submitContact = () => {
    if (!contactForm.unit_email && defaultUnitEmail.value) {
        contactForm.unit_email = defaultUnitEmail.value;
    }
    contactForm.post(locale.value === 'en' ? '/en/contact' : '/contato', {
        preserveScroll: true,
        onSuccess: () => contactForm.reset(),
    });
};

const serviceFallbackImgs = [
    'https://images.unsplash.com/photo-1449158743715-0a90ebb6d2d8?w=1200&q=80',
    'https://images.unsplash.com/photo-1597200381847-30ec200eeb9a?w=1200&q=80',
    'https://images.unsplash.com/photo-1555041469-a586c61ea9bc?w=1200&q=80',
    'https://images.unsplash.com/photo-1504307651254-35680f356dfd?w=1200&q=80',
];

const servicesForShowcase = computed(() => {
    return (props.services || []).map((service, i) => ({
        id: service.id,
        title: locale.value === 'en' ? service.name_en : service.name_pt,
        tag: locale.value === 'en' ? 'Specialized Workforce' : 'Mão de Obra Especializada',
        description: locale.value === 'en' ? service.description_en : service.description_pt,
        image: service.cover_url || service.gallery_urls?.[0] || serviceFallbackImgs[i % serviceFallbackImgs.length],
    }));
});

const serviceSlideStart = ref(0);
const hasServiceCarousel = computed(() => servicesForShowcase.value.length > 3);
const visibleServices = computed(() => {
    const items = servicesForShowcase.value;
    if (items.length <= 3) return items;

    const out = [];
    for (let i = 0; i < 3; i += 1) {
        out.push(items[(serviceSlideStart.value + i) % items.length]);
    }
    return out;
});

function nextServices() {
    if (!hasServiceCarousel.value) return;
    serviceSlideStart.value = (serviceSlideStart.value + 1) % servicesForShowcase.value.length;
}

function prevServices() {
    if (!hasServiceCarousel.value) return;
    serviceSlideStart.value = (serviceSlideStart.value - 1 + servicesForShowcase.value.length) % servicesForShowcase.value.length;
}
</script>

<template>
    <SeoHead v-bind="seo" />

    <!-- ──────────────────────────────── HERO ──────────────────────────────────── -->
    <section class="relative min-h-[560px] md:h-[921px] flex items-center overflow-hidden">
        <div class="absolute inset-0 z-0">
            <img
                class="w-full h-full object-cover"
                :src="bannerBg"
                :alt="t('Madeira tratada premium – Trataplan', 'Premium treated wood – Trataplan')"
            />
            <div class="absolute inset-0 bg-gradient-to-r from-primary/90 via-primary/40 to-transparent"></div>
        </div>
        <div class="container mx-auto px-6 relative z-10 grid md:grid-cols-2">
            <div class="max-w-2xl">
                <h2 class="text-emerald-50 font-headline font-extrabold text-4xl md:text-5xl lg:text-7xl tracking-tight leading-[1.1] mb-6">
                    {{ bannerTitle }}
                </h2>
                <p class="text-emerald-50/80 text-xl font-body mb-10 max-w-lg leading-relaxed italic">
                    {{ bannerSubtitle }}
                </p>
                <div class="flex flex-wrap gap-4 items-center">
                    <button
                        type="button"
                        @click="openWhatsApp()"
                        class="bg-tertiary hover:bg-tertiary/90 text-on-tertiary px-8 py-4 rounded-xl font-headline font-bold uppercase tracking-wider transition-transform active:scale-95 shadow-xl shadow-primary/20"
                    >
                        {{ t('Falar no WhatsApp', 'Chat on WhatsApp') }}
                    </button>
                    <div class="bg-secondary-container/20 backdrop-blur-md border border-white/10 px-6 py-4 rounded-xl flex items-center gap-3">
                        <span class="material-symbols-outlined text-secondary-container" style="font-variation-settings: 'FILL' 1;">verified</span>
                        <span class="text-emerald-50 font-headline font-bold text-sm uppercase tracking-widest">
                            {{ t('15 Anos de Garantia', '15-Year Guarantee') }}
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ──────────────────────────── SEGMENTS GRID ─────────────────────────────── -->
    <section class="py-24 bg-surface-container-low wood-texture-overlay">
        <div class="container mx-auto px-6">
            <div class="max-w-4xl mb-20">
                <h3 class="text-primary font-headline font-extrabold text-4xl mb-6 tracking-tight">
                    {{ t('Especialistas em Madeira Autoclavada', 'Specialists in Autoclaved Wood') }}
                </h3>
                <p class="text-on-surface-variant font-body text-xl leading-relaxed">
                    {{ t(
                        'A Trataplan possui madeiras tratadas para os mais diversos segmentos. Decks, pergolados, palanques para cercas, mangueiras, madeira para construção de casas (parede, forro, barrotes, assoalho), construção de barracões, quiosques, chalés e muito mais.',
                        'Trataplan offers treated wood for the most diverse segments. Decks, pergolas, fence stakes, handling pens, wood for house construction (walls, ceilings, joists, flooring), sheds, kiosks, chalets and much more.'
                    ) }}
                </p>
            </div>

            <div v-if="displayCategories.length" class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <a
                    v-for="(item, i) in displayCategories"
                    :key="item.id ?? i"
                    :href="item.product_category_id
                        ? (locale === 'en' ? '/en/catalog' : '/catalogo') + '?cat=' + item.product_category_id
                        : (locale === 'en' ? '/en/catalog' : '/catalogo')"
                    class="group relative aspect-[3/4] overflow-hidden rounded-xl bg-surface-container-highest block"
                    :class="{ 'md:mt-12': i === 1 || i === 3 }"
                >
                    <img
                        :src="item.cover_url || item.gallery_urls?.[0] || segmentFallbackImgs[i % segmentFallbackImgs.length]"
                        :alt="locale === 'en' ? item.name_en : item.name_pt"
                        class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110"
                    />
                    <div class="absolute inset-0 bg-gradient-to-t from-primary via-transparent to-transparent opacity-80"></div>
                    <div class="absolute bottom-0 left-0 p-8">
                        <span class="text-amber-200 font-headline text-xs font-bold tracking-[0.2em] uppercase">
                            {{ locale === 'en' ? item.label.en : item.label.pt }}
                        </span>
                        <h4 class="text-white font-headline font-extrabold text-2xl">
                            {{ locale === 'en' ? item.name_en : item.name_pt }}
                        </h4>
                        <p v-if="item.description" class="text-white/85 font-body text-sm mt-2 line-clamp-2">
                            {{ item.description }}
                        </p>
                    </div>
                </a>
            </div>
            <div v-else class="rounded-2xl p-10 text-center" style="background-color: #f0ede8;">
                <p class="font-headline font-bold text-xl text-primary mb-2">{{ t('Nenhum produto cadastrado', 'No products registered') }}</p>
                <p class="font-body text-on-surface-variant">{{ t('Cadastre em Admin > Portfólio > Produtos para exibir aqui.', 'Register items in Admin > Portfolio > Products to show them here.') }}</p>
            </div>
        </div>
    </section>

    <!-- ──────────────────────────── PORTFOLIO SHOWCASE ────────────────────────── -->
    <section class="py-24 bg-surface overflow-hidden">
        <div class="container mx-auto px-6">
            <div class="flex flex-col md:flex-row items-end justify-between mb-16 gap-8">
                <div class="max-w-2xl">
                    <span class="text-secondary font-headline text-xs font-black tracking-[0.3em] uppercase">
                        {{ t('Portfólio Trataplan', 'Trataplan Portfolio') }}
                    </span>
                    <h3 class="text-primary font-headline font-extrabold text-5xl mt-4 tracking-tighter">
                        {{ t('Projetos que Inspiram Confiança', 'Projects that Inspire Confidence') }}
                    </h3>
                </div>
                <p class="font-body text-xl italic text-on-surface-variant max-w-sm">
                    {{ t(
                        '"A excelência no tratamento da madeira é o alicerce de obras que desafiam o tempo."',
                        '"Excellence in wood treatment is the foundation of works that defy time."'
                    ) }}
                </p>
            </div>

            <div v-if="hasServiceCarousel" class="flex justify-end gap-3 mb-8">
                <button
                    type="button"
                    @click="prevServices"
                    class="w-11 h-11 rounded-full border border-primary/20 text-primary hover:bg-primary hover:text-white transition-colors flex items-center justify-center"
                    :aria-label="t('Anterior', 'Previous')"
                >
                    <span class="material-symbols-outlined">arrow_back</span>
                </button>
                <button
                    type="button"
                    @click="nextServices"
                    class="w-11 h-11 rounded-full border border-primary/20 text-primary hover:bg-primary hover:text-white transition-colors flex items-center justify-center"
                    :aria-label="t('Próximo', 'Next')"
                >
                    <span class="material-symbols-outlined">arrow_forward</span>
                </button>
            </div>

            <div v-if="visibleServices.length" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-8 md:gap-12">
                <a
                    v-for="(item, i) in visibleServices"
                    :key="item.id"
                    :href="locale === 'en' ? '/en/catalog?cat=service' : '/catalogo?cat=service'"
                    class="space-y-6 block group"
                    :class="{ 'md:pt-24': i === 1 && visibleServices.length === 3 }"
                >
                    <div class="overflow-hidden rounded-xl shadow-lg">
                        <img :src="item.image" :alt="item.title" class="w-full h-[280px] md:h-[500px] object-cover transition-transform duration-700 group-hover:scale-105" />
                    </div>
                    <h5 class="text-primary font-headline font-bold text-xl group-hover:underline">{{ item.title }}</h5>
                    <p class="text-on-surface-variant text-sm font-label uppercase tracking-widest">{{ item.tag }}</p>
                    <p v-if="item.description" class="text-on-surface-variant font-body text-base leading-relaxed">{{ item.description }}</p>
                </a>
            </div>
            <div v-else class="rounded-2xl p-10 text-center" style="background-color: #f0ede8;">
                <p class="font-headline font-bold text-xl text-primary mb-2">{{ t('Nenhuma mão de obra cadastrada', 'No workforce items registered') }}</p>
                <p class="font-body text-on-surface-variant">{{ t('Cadastre em Admin > Portfólio > Mão de Obra para exibir aqui.', 'Register items in Admin > Portfolio > Workforce to show them here.') }}</p>
            </div>
        </div>
    </section>

    <!-- ──────────────────────────── SUSTAINABILITY & TECH ─────────────────────── -->
    <section class="py-32 bg-primary text-emerald-50 relative overflow-hidden">
        <div class="absolute top-0 right-0 w-1/3 h-full bg-surface-container-highest/10 -skew-x-12 translate-x-1/2"></div>
        <div class="container mx-auto px-6 relative z-10">
            <div class="grid md:grid-cols-2 gap-20 items-center">
                <div>
                    <h3 class="font-headline font-extrabold text-5xl mb-8 tracking-tight">
                        {{ t('O Processo que Garante Durabilidade Imbatível', 'The Process that Guarantees Unbeatable Durability') }}
                    </h3>
                    <div class="space-y-12">
                        <div
                            v-for="feature in [
                                {
                                    icon: 'eco',
                                    title: t('100% Reflorestamento', '100% Reforestation'),
                                    desc: t('Trabalhamos exclusivamente com madeiras de fontes renováveis, respeitando o ecossistema e garantindo o futuro do planeta.', 'We work exclusively with wood from renewable sources, respecting the ecosystem and ensuring the planet\'s future.'),
                                },
                                {
                                    icon: 'precision_manufacturing',
                                    title: t('Vácuo-Pressão (Autoclave)', 'Vacuum-Pressure (Autoclave)'),
                                    desc: t('Nossas madeiras são impregnadas com preservativos químicos sob alta pressão, tornando-as imunes a cupins e fungos.', 'Our wood is impregnated with chemical preservatives under high pressure, making it immune to termites and fungi.'),
                                },
                                {
                                    icon: 'shield_with_heart',
                                    title: t('Garantia de 15 Anos', '15-Year Guarantee'),
                                    desc: t('A confiança na qualidade do nosso processo nos permite oferecer uma das maiores garantias do mercado nacional.', 'Confidence in the quality of our process allows us to offer one of the highest guarantees in the national market.'),
                                },
                            ]"
                            :key="feature.icon"
                            class="flex gap-6"
                        >
                            <div class="w-16 h-16 shrink-0 rounded-full bg-tertiary flex items-center justify-center">
                                <span class="material-symbols-outlined text-3xl" style="font-variation-settings: 'FILL' 1;">{{ feature.icon }}</span>
                            </div>
                            <div>
                                <h4 class="font-headline font-bold text-xl mb-2">{{ feature.title }}</h4>
                                <p class="text-emerald-50/70 font-body text-lg leading-relaxed">{{ feature.desc }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="relative">
                    <img
                        src="https://images.unsplash.com/photo-1504307651254-35680f356dfd?w=800&q=80"
                        :alt="t('Processo de tratamento em autoclave', 'Autoclave treatment process')"
                        class="rounded-2xl shadow-2xl"
                    />
                    <div class="hidden md:block absolute -bottom-10 -left-10 bg-secondary px-10 py-12 rounded-xl shadow-2xl max-w-xs">
                        <p class="text-white font-headline font-black text-6xl leading-none">15</p>
                        <p class="text-amber-200 font-headline font-bold text-xl uppercase tracking-tighter">
                            {{ t('Anos de Tranquilidade', 'Years of Peace of Mind') }}
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ──────────────────────────── BLOG / RECENT PROJECTS ────────────────────── -->
    <section class="py-24 bg-surface">
        <div class="container mx-auto px-6 text-center mb-16">
            <span class="text-tertiary font-headline text-xs font-bold tracking-[0.4em] uppercase">
                {{ t('Inovação e Projetos', 'Innovation and Projects') }}
            </span>
            <h3 class="text-primary font-headline font-extrabold text-5xl mt-4">
                {{ t('Últimas Execuções', 'Latest Executions') }}
            </h3>
        </div>
        <div class="container mx-auto px-6 grid grid-cols-1 md:grid-cols-2 gap-8">
            <template v-if="featuredPosts?.length">
                <div
                    v-for="post in featuredPosts"
                    :key="post.id"
                    class="group cursor-pointer"
                >
                    <Link :href="(locale === 'en' ? '/en/blog/' : '/blog/') + post.slug">
                        <div class="relative h-[220px] md:h-[400px] rounded-2xl overflow-hidden mb-6">
                            <img
                                v-if="post.featured_image_url"
                                :src="post.featured_image_url"
                                :alt="post.title"
                                class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
                            />
                            <div v-else class="w-full h-full bg-surface-container-high flex items-center justify-center">
                                <span class="material-symbols-outlined text-6xl text-outline-variant">article</span>
                            </div>
                            <div class="absolute top-6 left-6 bg-white/20 backdrop-blur-md px-4 py-2 rounded-lg">
                                <span class="text-white font-headline text-xs font-bold uppercase tracking-widest">{{ post.published_at }}</span>
                            </div>
                        </div>
                        <h4 class="text-primary font-headline font-extrabold text-2xl group-hover:text-tertiary transition-colors">{{ post.title }}</h4>
                        <p class="text-on-surface-variant font-body text-lg mt-2">{{ post.excerpt }}</p>
                    </Link>
                </div>
            </template>
            <template v-else>
                <div class="md:col-span-2 rounded-2xl p-10 text-center" style="background-color: #f0ede8;">
                    <p class="font-headline font-bold text-xl text-primary mb-2">{{ t('Nenhum trabalho publicado', 'No published work yet') }}</p>
                    <p class="font-body text-on-surface-variant">{{ t('Cadastre e publique postagens em Admin > Postagens para exibir aqui.', 'Create and publish posts in Admin > Posts to display them here.') }}</p>
                </div>
            </template>
        </div>
    </section>

    <!-- ──────────────────────────── CONTACT / MAP ─────────────────────────────── -->
    <section class="py-24 bg-surface-container-low border-t border-primary/5" id="contato">
        <div class="container mx-auto px-6">

            <!-- Cabeçalho da seção -->
            <div class="mb-10">
                <span class="text-xs font-black uppercase tracking-[0.3em] text-amber-600"
                      style="font-family: Manrope, sans-serif;">
                    {{ t('Onde Estamos', 'Where We Are') }}
                </span>
                <h3 class="text-primary font-headline font-extrabold text-4xl mt-2">
                    {{ units.length > 1
                        ? t('Nossas Unidades', 'Our Locations')
                        : t('Visite Nossa Unidade', 'Visit Our Location') }}
                </h3>
            </div>

            <!-- Seletor de unidades (só aparece se houver mais de uma) -->
            <div v-if="units.length > 1" class="flex flex-wrap gap-2 mb-8">
                <button
                    v-for="(unit, i) in units"
                    :key="i"
                    @click="activeIdx = i"
                    class="flex items-center gap-2 px-5 py-2.5 rounded-full text-sm font-bold transition-all duration-200"
                    :style="activeIdx === i
                        ? 'background:#012d1d; color:#fff;'
                        : 'background:#fff; color:#012d1d; border: 1.5px solid #012d1d33;'"
                    style="font-family: Manrope, sans-serif;"
                >
                    <span class="w-2 h-2 rounded-full shrink-0"
                          :style="activeIdx === i ? 'background:#fbbf24;' : 'background:#012d1d44;'"></span>
                    {{ unit.label || (unit.tipo === 'matriz' ? t('Sede', 'HQ') : `${t('Unidade','Branch')} ${i + 1}`) }}
                </button>
            </div>

            <!-- Grid: card + mapa -->
            <div class="grid md:grid-cols-[320px_1fr] lg:grid-cols-[380px_1fr] gap-6 items-stretch">

                <!-- Card de contato -->
                <div class="rounded-2xl overflow-hidden flex flex-col shadow-xl" style="background-color: #012d1d;">

                    <!-- Área de marca: logo centralizada num bloco mais escuro -->
                    <div class="flex flex-col items-center justify-center py-10 px-8 text-center"
                         style="background-color: #011a10; min-height: 180px;">
                        <img
                            v-if="logoTransparentUrl"
                            :src="logoTransparentUrl"
                            alt="Trataplan"
                            class="h-24 w-auto object-contain mb-3"
                        />
                        <template v-else>
                            <span class="material-symbols-outlined text-amber-400 text-4xl mb-2"
                                  style="font-variation-settings:'FILL' 1;">forest</span>
                            <span class="text-2xl font-black text-white tracking-tight"
                                  style="font-family: Manrope, sans-serif;">TRATAPLAN</span>
                        </template>
                        <!-- Badge tipo unidade -->
                        <span v-if="units.length > 1"
                              class="mt-3 px-3 py-1 rounded-full text-xs font-bold uppercase tracking-widest"
                              style="background: rgba(251,191,36,0.15); color: #fbbf24; font-family: Manrope, sans-serif;">
                            {{ activeUnit.tipo === 'matriz' ? t('Sede', 'Headquarters') : t('Filial', 'Branch') }}
                        </span>
                        <p v-if="activeUnit.label" class="mt-1 text-sm" style="color:rgba(255,255,255,0.4); font-family: Manrope, sans-serif;">
                            {{ activeUnit.label }}
                        </p>
                    </div>

                    <!-- Dados de contato -->
                    <div class="flex-1 px-8 py-7 space-y-5 border-b" style="border-color: rgba(255,255,255,0.06);">
                        <div class="flex items-start gap-4">
                            <span class="material-symbols-outlined text-amber-400 text-xl mt-0.5 shrink-0">location_on</span>
                            <div>
                                <p class="text-xs font-bold uppercase tracking-widest mb-1"
                                   style="color: rgba(255,255,255,0.3); font-family: Manrope, sans-serif;">
                                    {{ t('Endereço', 'Address') }}
                                </p>
                                <p class="text-white/85 text-sm leading-snug" style="font-family: Newsreader, serif;">
                                    {{ unitAddr }}
                                </p>
                            </div>
                        </div>

                        <div class="flex items-center gap-4">
                            <span class="material-symbols-outlined text-amber-400 text-xl shrink-0">phone_iphone</span>
                            <div>
                                <p class="text-xs font-bold uppercase tracking-widest mb-1"
                                   style="color: rgba(255,255,255,0.3); font-family: Manrope, sans-serif;">
                                    WhatsApp
                                </p>
                                <a :href="unitWaUrl" target="_blank" rel="noopener"
                                   class="text-white/85 text-sm font-semibold hover:text-white transition-colors"
                                   style="font-family: Manrope, sans-serif;">
                                    {{ unitPhone }}
                                </a>
                            </div>
                        </div>

                        <div class="flex items-center gap-4">
                            <span class="material-symbols-outlined text-amber-400 text-xl shrink-0">mail</span>
                            <div>
                                <p class="text-xs font-bold uppercase tracking-widest mb-1"
                                   style="color: rgba(255,255,255,0.3); font-family: Manrope, sans-serif;">
                                    Email
                                </p>
                                <a :href="'mailto:' + unitEmail"
                                   class="text-white/85 text-sm hover:text-white transition-colors"
                                   style="font-family: Manrope, sans-serif;">
                                    {{ unitEmail }}
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- CTA WhatsApp -->
                    <div class="px-8 py-6">
                        <a
                            :href="unitWaUrl"
                            target="_blank" rel="noopener noreferrer"
                            class="flex items-center justify-center gap-2.5 w-full py-3.5 rounded-xl font-bold text-sm transition-all duration-200 hover:opacity-90 active:scale-95"
                            style="background-color: #25D366; color: #fff; font-family: Manrope, sans-serif;"
                        >
                            <svg class="w-5 h-5 shrink-0" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
                            </svg>
                            {{ t('Falar pelo WhatsApp', 'Chat on WhatsApp') }}
                        </a>
                    </div>
                </div>

                <!-- Mapa + formulário lado direito -->
                <div class="flex flex-col gap-6">
                    <!-- Mapa -->
                    <div class="rounded-2xl overflow-hidden shadow-lg flex-1 min-h-[340px]">
                        <iframe
                            v-if="activeUnit.maps_link"
                            :key="activeUnit.maps_link"
                            :src="activeUnit.maps_link"
                            class="w-full h-full min-h-[340px]"
                            style="border:0;"
                            allowfullscreen=""
                            loading="lazy"
                            :title="t('Localização Trataplan', 'Trataplan Location')"
                        ></iframe>
                        <div v-else class="w-full h-full min-h-[340px] flex flex-col items-center justify-center gap-3"
                             style="background: #f0f4f2;">
                            <span class="material-symbols-outlined text-4xl" style="color: #c8d8cf;">map</span>
                            <p class="text-xs font-bold uppercase tracking-widest text-center px-6"
                               style="color: #a0b8ad; font-family: Manrope, sans-serif;">
                                {{ t('Configure o link do mapa nas configurações', 'Configure the map link in settings') }}
                            </p>
                        </div>
                    </div>

                    <!-- Formulário de orçamento -->
                    <div class="rounded-2xl p-7 border bg-white" style="border-color: rgba(1,45,29,0.07);">
                        <h4 class="font-headline font-bold text-lg mb-5 text-primary">
                            {{ t('Solicite um Orçamento', 'Request a Quote') }}
                        </h4>
                        <!-- Sucesso -->
                        <div v-if="contactForm.wasSuccessful"
                             class="flex items-start gap-3 rounded-xl p-4 mb-4"
                             style="background-color: #c1ecd4;">
                            <span class="material-symbols-outlined text-xl shrink-0"
                                  style="color: #012d1d; font-variation-settings:'FILL' 1;">check_circle</span>
                            <p class="text-sm font-bold" style="color: #012d1d; font-family: Manrope, sans-serif;">
                                {{ t('Mensagem enviada com sucesso!', 'Message sent successfully!') }}
                            </p>
                        </div>

                        <form v-else class="space-y-3" @submit.prevent="submitContact">

                            <!-- Unidade de Atendimento -->
                            <div v-if="units.length > 1">
                                <div class="relative">
                                    <select v-model="contactForm.unit_email"
                                        class="w-full px-4 py-3.5 rounded-xl outline-none appearance-none transition-all focus:ring-2 cursor-pointer"
                                        :style="fieldStyle(contactForm.errors.unit_email)"
                                        style="font-family: Manrope, sans-serif; font-size: 0.9rem; color: #1c1c19;">
                                        <option v-for="(unit, i) in units" :key="i" :value="unit.email || ''">
                                            {{ unit.label || (unit.tipo === 'matriz' ? t('Matriz (Sede)', 'Headquarters') : t('Filial', 'Branch')) }}
                                            {{ unit.address ? ' — ' + unit.address.split(',')[0] : '' }}
                                        </option>
                                    </select>
                                    <span class="material-symbols-outlined absolute right-4 top-1/2 -translate-y-1/2 pointer-events-none text-xl"
                                          style="color: #9a8b7a;">expand_more</span>
                                </div>
                            </div>

                            <!-- Nome + Email -->
                            <div class="grid sm:grid-cols-2 gap-3">
                                <div>
                                    <input
                                        v-model="contactForm.name"
                                        class="w-full px-4 py-3.5 rounded-xl outline-none transition-all focus:ring-2"
                                        :style="fieldStyle(contactForm.errors.name)"
                                        :placeholder="t('Seu Nome', 'Your Name')"
                                        type="text"
                                    />
                                    <p v-if="contactForm.errors.name" class="mt-1 text-xs font-semibold" style="color:#ba1a1a; font-family: Manrope, sans-serif;">{{ contactForm.errors.name }}</p>
                                </div>
                                <div>
                                    <input
                                        v-model="contactForm.email"
                                        class="w-full px-4 py-3.5 rounded-xl outline-none transition-all focus:ring-2"
                                        :style="fieldStyle(contactForm.errors.email)"
                                        :placeholder="t('E-mail', 'Email')"
                                        type="email"
                                    />
                                    <p v-if="contactForm.errors.email" class="mt-1 text-xs font-semibold" style="color:#ba1a1a; font-family: Manrope, sans-serif;">{{ contactForm.errors.email }}</p>
                                </div>
                            </div>

                            <!-- Mensagem -->
                            <div>
                                <textarea
                                    v-model="contactForm.message"
                                    class="w-full px-4 py-3.5 rounded-xl outline-none resize-none transition-all focus:ring-2"
                                    :style="fieldStyle(contactForm.errors.message)"
                                    :placeholder="t('Fale sobre seu projeto...', 'Tell us about your project...')"
                                    rows="4"
                                ></textarea>
                                <p v-if="contactForm.errors.message" class="mt-1 text-xs font-semibold" style="color:#ba1a1a; font-family: Manrope, sans-serif;">{{ contactForm.errors.message }}</p>
                            </div>

                            <button
                                type="submit"
                                :disabled="contactForm.processing"
                                class="w-full py-3.5 rounded-xl font-headline font-bold uppercase tracking-widest text-sm text-white hover:opacity-90 transition-opacity disabled:opacity-60"
                                style="background-color: #012d1d;"
                            >
                                <span v-if="contactForm.processing" class="material-symbols-outlined text-base animate-spin align-middle mr-1">autorenew</span>
                                {{ contactForm.processing ? t('Enviando...', 'Sending...') : t('Enviar Mensagem', 'Send Message') }}
                            </button>
                        </form>
                    </div>
                </div>
            </div>

        </div>
    </section>
</template>
