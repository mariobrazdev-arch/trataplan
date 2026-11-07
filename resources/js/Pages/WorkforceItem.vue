<script setup>
import SeoHead from '@/Components/Shared/SeoHead.vue';
import Lightbox from '@/Components/Shared/Lightbox.vue';
import { Link } from '@inertiajs/vue3';
import { useWhatsAppPicker } from '@/composables/useWhatsAppPicker';
import { computed, ref } from 'vue';

const props = defineProps({
    item: Object,
    seo:  Object,
});

const { open: openWhatsApp } = useWhatsAppPicker();

// ── Galeria ──────────────────────────────────────────────────────
const allPhotos = computed(() => {
    const gallery = props.item?.gallery_urls ?? [];
    const cover   = props.item?.cover_url;
    if (cover && !gallery.includes(cover)) return [cover, ...gallery];
    return gallery.length ? gallery : (cover ? [cover] : []);
});

const fallback = 'https://images.unsplash.com/photo-1558618666-fcd25c85cd64?w=1200&q=80';

const lightboxOpen = ref(false);
const lightboxIndex = ref(0);

function openLightbox(idx) {
    lightboxIndex.value = idx;
    lightboxOpen.value = true;
}

// ── WhatsApp CTA ──────────────────────────────────────────────────
const waRawText = computed(() =>
    `Olá! Tenho interesse em saber mais sobre "${props.item?.name}". Podem me ajudar?`
);

// ── Short description paragraphs ────────────────────────────────
const paragraphs = computed(() => {
    const desc = props.item?.description ?? '';
    return desc.split(/\n+/).filter(p => p.trim().length > 0);
});
</script>

<template>
    <SeoHead v-bind="seo" />

    <div class="min-h-screen" style="background-color: #fcf9f4;">

        <!-- ── Breadcrumb bar ──────────────────────────────────── -->
        <div class="fixed top-0 left-0 right-0 z-40 pt-[72px]" style="background-color: #fcf9f4;">
            <div class="max-w-7xl mx-auto px-6 py-2.5 flex items-center gap-2 border-b" style="border-color: #e8e4de;">
                <Link href="/"
                    class="text-xs font-bold uppercase tracking-widest transition-colors hover:underline"
                    style="font-family: Manrope, sans-serif; color: #9e9e9e;">
                    {{ 'Início' }}
                </Link>
                <span class="material-symbols-outlined text-xs" style="color: #c1c8c2; font-size: 14px;">chevron_right</span>
                <Link :href="item.back_url"
                    class="text-xs font-bold uppercase tracking-widest transition-colors hover:underline"
                    style="font-family: Manrope, sans-serif; color: #9e9e9e;">
                    {{ 'Mão de Obra' }}
                </Link>
                <span class="material-symbols-outlined text-xs" style="color: #c1c8c2; font-size: 14px;">chevron_right</span>
                <span class="text-xs font-bold uppercase tracking-widest truncate max-w-[180px]"
                    style="font-family: Manrope, sans-serif; color: #012d1d;">
                    {{ item.name }}
                </span>

                <div class="ml-auto">
                    <Link :href="item.back_url"
                        class="flex items-center gap-1.5 px-4 py-1.5 rounded-full text-xs font-bold uppercase tracking-widest transition-all hover:-translate-x-0.5"
                        style="font-family: Manrope, sans-serif; background-color: #ebe8e3; color: #414844;">
                        <span class="material-symbols-outlined text-sm">arrow_back</span>
                        {{ 'Voltar à Mão de Obra' }}
                    </Link>
                </div>
            </div>
        </div>

        <!-- ── Hero header ────────────────────────────────────── -->
        <div class="max-w-7xl mx-auto px-6 pt-36 pb-8">
            <div class="flex flex-col md:flex-row md:items-start md:justify-between gap-4">
                <div class="flex-1">
                    <p class="text-xs font-bold uppercase tracking-[0.25em] mb-3"
                        style="font-family: Manrope, sans-serif; color: #6a5e33;">
                        {{ 'MÃO DE OBRA' }}
                    </p>
                    <h1 class="text-4xl md:text-5xl lg:text-6xl font-black leading-[1.05] tracking-tight mb-4"
                        style="font-family: Manrope, sans-serif; color: #012d1d;">
                        {{ item.name }}
                    </h1>
                    <p v-if="paragraphs.length"
                        class="text-lg leading-relaxed max-w-xl"
                        style="font-family: Newsreader, serif; color: #414844; font-style: italic;">
                        {{ paragraphs[0] }}
                    </p>
                </div>
            </div>
        </div>

        <!-- ── Galeria em grid (click → lightbox) ──────────────── -->
        <div class="max-w-7xl mx-auto px-6 mb-12">
            <div v-if="allPhotos.length" class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-3">
                <button
                    v-for="(url, idx) in allPhotos"
                    :key="idx"
                    type="button"
                    @click="openLightbox(idx)"
                    class="relative rounded-2xl overflow-hidden group aspect-square"
                    style="background-color: #1a1a1a;">
                    <img
                        :src="url"
                        :alt="`${item.name} — foto ${idx + 1}`"
                        class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-105"
                        loading="lazy"
                    />
                    <div class="absolute inset-0 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center"
                        style="background-color: rgba(0,0,0,0.35);">
                        <span class="material-symbols-outlined text-white text-3xl">zoom_in</span>
                    </div>
                </button>
            </div>

            <div v-else class="py-24 text-center rounded-2xl" style="background-color: #f0ede8;">
                <img :src="fallback" :alt="item.name" class="w-full h-64 object-cover rounded-2xl opacity-60" />
            </div>
        </div>

        <Lightbox
            v-model:open="lightboxOpen"
            v-model:start-index="lightboxIndex"
            :photos="allPhotos"
        />

        <!-- ── Conteúdo principal ──────────────────────────────── -->
        <div class="max-w-7xl mx-auto px-6 mb-20">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-10">

                <!-- Coluna esquerda: descrição completa + quote -->
                <div class="lg:col-span-7 space-y-8">

                    <div v-if="paragraphs.length > 0">
                        <h2 class="text-2xl font-black mb-5"
                            style="font-family: Manrope, sans-serif; color: #012d1d;">
                            {{ 'O Conceito' }}
                        </h2>
                        <div class="space-y-4">
                            <p v-for="(para, i) in paragraphs"
                                :key="i"
                                class="text-base leading-relaxed"
                                style="font-family: Newsreader, serif; font-size: 1.05rem; color: #414844;">
                                {{ para }}
                            </p>
                        </div>
                    </div>

                    <div v-else class="py-10 rounded-2xl text-center" style="background-color: #f0ede8;">
                        <span class="material-symbols-outlined text-4xl mb-2 block" style="color: #c1c8c2;">description</span>
                        <p class="italic text-sm" style="font-family: Newsreader, serif; color: #9e9e9e;">
                            {{ 'Descrição em breve.' }}
                        </p>
                    </div>

                    <blockquote class="relative pl-6 py-2 border-l-4" style="border-color: #6a5e33;">
                        <p class="text-lg italic leading-relaxed"
                            style="font-family: Newsreader, serif; color: #414844;">
                            "{{ 'Qualidade e durabilidade são a base de cada solução que entregamos.' }}"
                        </p>
                        <footer class="mt-3 text-xs font-bold uppercase tracking-widest" style="font-family: Manrope, sans-serif; color: #9e9e9e;">
                            — Trataplan
                        </footer>
                    </blockquote>
                </div>

                <!-- Coluna direita: info cards -->
                <div class="lg:col-span-5 space-y-5">

                    <div class="rounded-2xl p-6" style="background-color: #ffffff; box-shadow: 0 2px 20px rgba(0,0,0,0.06);">
                        <h3 class="text-sm font-black uppercase tracking-[0.15em] mb-5"
                            style="font-family: Manrope, sans-serif; color: #012d1d;">
                            {{ 'Informações' }}
                        </h3>
                        <dl class="space-y-3 divide-y" style="border-color: #f0ede8;">
                            <div v-if="allPhotos.length" class="flex justify-between items-center pt-3 first:pt-0">
                                <dt class="text-[10px] font-bold uppercase tracking-widest" style="font-family: Manrope, sans-serif; color: #9e9e9e;">
                                    {{ 'FOTOS' }}
                                </dt>
                                <dd class="text-sm font-bold text-right" style="font-family: Manrope, sans-serif; color: #1c1c19;">
                                    {{ allPhotos.length }}
                                </dd>
                            </div>
                        </dl>
                    </div>

                    <div class="rounded-2xl p-6 flex items-start gap-4"
                        style="background-color: #012d1d;">
                        <span class="material-symbols-outlined mt-0.5 flex-shrink-0" style="color: #c1ecd4; font-variation-settings: 'FILL' 1;">verified</span>
                        <div>
                            <p class="font-black text-sm mb-1" style="font-family: Manrope, sans-serif; color: #c1ecd4;">
                                {{ 'Excelência Certificada' }}
                            </p>
                            <p class="text-xs leading-relaxed" style="font-family: Newsreader, serif; color: rgba(255,255,255,0.65); font-style: italic;">
                                {{ 'Produtos e serviços com processos rigorosos de qualidade e rastreabilidade.' }}
                            </p>
                        </div>
                    </div>

                    <button type="button" @click="openWhatsApp(waRawText.value)"
                        class="flex items-center justify-center gap-3 px-5 py-4 rounded-2xl font-bold text-sm uppercase tracking-widest transition-all hover:shadow-xl hover:-translate-y-0.5 active:scale-95 w-full"
                        style="background-color: #25d366; color: #fff; font-family: Manrope, sans-serif;">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347z"/>
                            <path d="M12 0C5.373 0 0 5.373 0 12c0 2.123.553 4.116 1.522 5.845L.057 23.617a.5.5 0 0 0 .614.634l5.563-1.459A11.945 11.945 0 0 0 12 24c6.627 0 12-5.373 12-12S18.627 0 12 0zm0 21.9a9.874 9.874 0 0 1-5.027-1.373l-.361-.214-3.729.978.996-3.638-.235-.374A9.862 9.862 0 0 1 2.1 12C2.1 6.523 6.523 2.1 12 2.1S21.9 6.523 21.9 12 17.477 21.9 12 21.9z"/>
                        </svg>
                        {{ 'Solicitar Orçamento' }}
                    </button>
                </div>
            </div>
        </div>

        <!-- ── CTA Final ──────────────────────────────────────── -->
        <div class="relative overflow-hidden mx-4 md:mx-6 mb-10 rounded-3xl"
            style="background-color: #1a1a1a;">
            <div class="absolute inset-0 opacity-30"
                style="background-image: radial-gradient(circle at 20% 50%, #012d1d 0%, transparent 60%), radial-gradient(circle at 80% 50%, #4a3728 0%, transparent 60%);"></div>

            <div class="relative z-10 max-w-2xl mx-auto text-center py-20 px-6">
                <p class="text-xs font-bold uppercase tracking-[0.3em] mb-4"
                    style="font-family: Manrope, sans-serif; color: rgba(255,255,255,0.4);">
                    {{ 'PRÓXIMO PASSO' }}
                </p>
                <h2 class="text-3xl md:text-4xl font-black text-white mb-4"
                    style="font-family: Manrope, sans-serif;">
                    {{ 'Inicie seu Projeto' }}
                </h2>
                <p class="text-base mb-10 leading-relaxed"
                    style="font-family: Newsreader, serif; color: rgba(255,255,255,0.65); font-style: italic;">
                    {{ 'Nossa equipe técnica está pronta para transformar sua visão em uma estrutura tangível e duradoura.' }}
                </p>
                <div class="flex flex-col sm:flex-row items-center justify-center gap-4">
                    <button type="button" @click="openWhatsApp(waRawText.value)"
                        class="flex items-center gap-2.5 px-8 py-4 rounded-full font-black text-sm uppercase tracking-widest transition-all hover:shadow-2xl hover:-translate-y-0.5 active:scale-95"
                        style="font-family: Manrope, sans-serif; background-color: #25d366; color: #fff;">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347z"/>
                            <path d="M12 0C5.373 0 0 5.373 0 12c0 2.123.553 4.116 1.522 5.845L.057 23.617a.5.5 0 0 0 .614.634l5.563-1.459A11.945 11.945 0 0 0 12 24c6.627 0 12-5.373 12-12S18.627 0 12 0zm0 21.9a9.874 9.874 0 0 1-5.027-1.373l-.361-.214-3.729.978.996-3.638-.235-.374A9.862 9.862 0 0 1 2.1 12C2.1 6.523 6.523 2.1 12 2.1S21.9 6.523 21.9 12 17.477 21.9 12 21.9z"/>
                        </svg>
                        {{ 'Orçamento via WhatsApp' }}
                    </button>
                    <Link :href="item.back_url"
                        class="flex items-center gap-2 px-8 py-4 rounded-full font-black text-sm uppercase tracking-widest border-2 transition-all hover:-translate-y-0.5"
                        style="font-family: Manrope, sans-serif; border-color: rgba(255,255,255,0.25); color: rgba(255,255,255,0.75);">
                        <span class="material-symbols-outlined text-base">arrow_back</span>
                        {{ 'Ver Mão de Obra' }}
                    </Link>
                </div>
            </div>
        </div>

    </div>
</template>
