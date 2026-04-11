<script setup>
import SeoHead from '@/Components/Shared/SeoHead.vue';
import { Link } from '@inertiajs/vue3';
import { useLocale } from '@/composables/useLocale';
import { useSettings } from '@/composables/useSettings';
import { ref, computed } from 'vue';

const props = defineProps({
    post: Object,
    prev: Object,
    next: Object,
    seo: Object,
});

const { locale, t } = useLocale();
const { setting } = useSettings();

const whatsappNumber = computed(() =>
    (setting('whatsapp_number', '554999600329')).replace(/\D/g, '')
);
const whatsappUrl = computed(() =>
    `https://wa.me/${whatsappNumber.value}?text=${encodeURIComponent(t('Olá! Tenho dúvidas sobre o artigo: ', 'Hello! I have questions about the post: ') + props.post.title)}`
);

// Galeria: thumb selecionada
const activePhoto = ref(props.post.photos?.[0]?.url || null);

const fallback = 'https://images.unsplash.com/photo-1558618666-fcd25c85cd64?w=1200&q=80';

function blogUrl() {
    return locale.value === 'en' ? '/en/blog' : '/blog';
}
function postUrl(slug) {
    return `${locale.value === 'en' ? '/en' : ''}/blog/${slug}`;
}
</script>

<template>
    <SeoHead v-bind="seo" />

    <div style="background-color: #fcf9f4;">

        <!-- ── HERO ──────────────────────────────────────────────── -->
        <div class="relative w-full overflow-hidden" style="height: clamp(340px, 55vw, 620px);">
            <img
                :src="post.featured_image_url || fallback"
                :alt="post.title"
                class="absolute inset-0 w-full h-full object-cover"
                @error="$event.target.src = fallback" />
            <!-- overlay gradiente -->
            <div class="absolute inset-0"
                style="background: linear-gradient(to top, rgba(1,45,29,0.92) 0%, rgba(1,45,29,0.45) 45%, rgba(0,0,0,0.15) 100%);"></div>

            <!-- Conteúdo sobreposto no rodapé do hero -->
            <div class="absolute bottom-0 left-0 right-0 pb-8 pt-12 px-6 md:px-12 max-w-5xl">
                <!-- Badge + data -->
                <div class="flex items-center gap-3 mb-4">
                    <span v-if="post.category_label"
                        class="px-3 py-1 rounded-full text-[10px] font-bold uppercase tracking-widest"
                        style="font-family:Manrope,sans-serif; background-color:rgba(255,255,255,0.18); color:#c1ecd4; backdrop-filter:blur(4px);">
                        {{ post.category_label }}
                    </span>
                    <span class="text-xs font-bold uppercase tracking-widest"
                        style="font-family:Manrope,sans-serif; color:rgba(255,255,255,0.65);">
                        {{ post.published_at }}
                    </span>
                </div>

                <!-- Título -->
                <h1 class="text-3xl md:text-5xl font-black text-white leading-tight"
                    style="font-family:Manrope,sans-serif; max-width:780px;">
                    {{ post.title }}
                </h1>

                <!-- Autor + voltar -->
                <div class="flex items-center gap-4 mt-4">
                    <span v-if="post.author"
                        class="text-sm text-white/70"
                        style="font-family:Newsreader,serif;">
                        {{ t('por', 'by') }} {{ post.author }}
                    </span>
                </div>
            </div>

            <!-- Botão WhatsApp flutuante -->
            <a :href="whatsappUrl" target="_blank" rel="noopener"
                class="absolute bottom-8 right-6 flex items-center gap-2 px-4 py-2.5 rounded-full shadow-xl transition-all hover:scale-105"
                style="background-color:#25d366; color:#fff; font-family:Manrope,sans-serif; font-size:12px; font-weight:700;">
                <span class="material-symbols-outlined text-base" style="font-variation-settings:'FILL' 1;">chat</span>
                <span class="hidden sm:inline">{{ t('Fale conosco', 'Contact us') }}</span>
            </a>
        </div>

        <!-- ── CONTEÚDO PRINCIPAL ───────────────────────────────── -->
        <div class="max-w-5xl mx-auto px-6 md:px-8 py-14">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-12">

                <!-- SIDEBAR esquerda -->
                <aside class="lg:col-span-3 order-2 lg:order-1">

                    <!-- Voltar ao blog -->
                    <Link :href="blogUrl()"
                        class="inline-flex items-center gap-2 text-xs font-bold uppercase tracking-widest mb-8 group"
                        style="font-family:Manrope,sans-serif; color:#012d1d;">
                        <span class="material-symbols-outlined text-base transition-transform group-hover:-translate-x-1">arrow_back</span>
                        {{ t('Voltar ao Blog', 'Back to Blog') }}
                    </Link>

                    <!-- Ficha da postagem -->
                    <div class="rounded-xl p-5 space-y-5" style="background-color:#fff;">
                        <div class="flex items-center gap-2 pb-3" style="border-bottom:1px solid #ebe8e3;">
                            <span class="material-symbols-outlined text-base" style="color:#6a5e33; font-variation-settings:'FILL' 1;">info</span>
                            <p class="text-xs font-bold uppercase tracking-widest"
                                style="font-family:Manrope,sans-serif; color:#012d1d;">
                                {{ t('Sobre este artigo', 'About this post') }}
                            </p>
                        </div>

                        <div v-if="post.category_label" class="space-y-1">
                            <p class="text-[9px] font-bold uppercase tracking-[0.2em]"
                                style="font-family:Manrope,sans-serif; color:#9e9e9e;">
                                {{ t('Categoria', 'Category') }}
                            </p>
                            <p class="text-sm font-bold" style="font-family:Manrope,sans-serif; color:#012d1d;">
                                {{ post.category_label }}
                            </p>
                        </div>

                        <div class="space-y-1">
                            <p class="text-[9px] font-bold uppercase tracking-[0.2em]"
                                style="font-family:Manrope,sans-serif; color:#9e9e9e;">
                                {{ t('Publicado em', 'Published on') }}
                            </p>
                            <p class="text-sm font-bold" style="font-family:Manrope,sans-serif; color:#012d1d;">
                                {{ post.published_at }}
                            </p>
                        </div>

                        <div v-if="post.author" class="space-y-1">
                            <p class="text-[9px] font-bold uppercase tracking-[0.2em]"
                                style="font-family:Manrope,sans-serif; color:#9e9e9e;">
                                {{ t('Autor', 'Author') }}
                            </p>
                            <p class="text-sm font-bold" style="font-family:Manrope,sans-serif; color:#012d1d;">
                                {{ post.author }}
                            </p>
                        </div>
                    </div>

                    <!-- Foto extra (primeira da galeria como destaque lateral) -->
                    <div v-if="post.photos?.length"
                        class="mt-6 rounded-xl overflow-hidden relative cursor-pointer group"
                        @click="activePhoto = post.photos[0].url">
                        <img :src="post.photos[0].url" alt="Foto"
                            class="w-full object-cover transition-transform duration-500 group-hover:scale-105"
                            style="height:160px;" />
                        <div class="absolute bottom-0 left-0 right-0 p-3"
                            style="background:linear-gradient(to top, rgba(1,45,29,0.85) 0%, transparent 100%);">
                            <span class="text-[9px] font-bold uppercase tracking-widest text-white"
                                style="font-family:Manrope,sans-serif;">
                                {{ t('VER GALERIA', 'VIEW GALLERY') }}
                            </span>
                        </div>
                    </div>
                </aside>

                <!-- CORPO DO ARTIGO -->
                <main class="lg:col-span-9 order-1 lg:order-2">

                    <!-- Excerpt / pull quote -->
                    <blockquote v-if="post.excerpt"
                        class="text-xl md:text-2xl leading-relaxed mb-10 pl-6"
                        style="font-family:Newsreader,serif; font-style:italic; color:#5d4c40; border-left:3px solid #6a5e33;">
                        "{{ post.excerpt }}"
                    </blockquote>

                    <!-- Conteúdo -->
                    <div
                        v-if="post.content"
                        class="leading-relaxed space-y-5 text-base md:text-lg"
                        style="font-family:Newsreader,serif; color:#1c1c19;"
                        v-html="post.content">
                    </div>
                    <p v-else class="text-base italic" style="font-family:Newsreader,serif; color:#9e9e9e;">
                        {{ t('Conteúdo não disponível.', 'Content not available.') }}
                    </p>

                    <!-- Galeria de fotos -->
                    <div v-if="post.photos?.length" class="mt-12">
                        <h2 class="text-xl font-black mb-6"
                            style="font-family:Manrope,sans-serif; color:#012d1d;">
                            {{ t('Galeria de Imagens', 'Photo Gallery') }}
                        </h2>

                        <!-- Visualizador principal -->
                        <div class="grid grid-cols-3 gap-3">
                            <!-- Imagem grande -->
                            <div class="col-span-2 rounded-xl overflow-hidden" style="max-height:380px;">
                                <img
                                    :src="activePhoto || post.photos[0].url"
                                    alt="Foto em destaque"
                                    class="w-full h-full object-cover" />
                            </div>
                            <!-- Miniaturas -->
                            <div class="col-span-1 flex flex-col gap-3">
                                <div
                                    v-for="photo in post.photos.slice(0, 3)"
                                    :key="photo.id"
                                    class="rounded-xl overflow-hidden cursor-pointer transition-all"
                                    :style="activePhoto === photo.url ? 'ring: 2px solid #012d1d; opacity:1;' : 'opacity:0.75;'"
                                    style="height:118px;"
                                    @click="activePhoto = photo.url">
                                    <img :src="photo.url" alt="Miniatura"
                                        class="w-full h-full object-cover hover:opacity-100 transition-opacity" />
                                </div>
                            </div>
                        </div>

                        <!-- Linha de fotos adicionais -->
                        <div v-if="post.photos.length > 3" class="flex gap-3 mt-3 overflow-x-auto pb-2">
                            <div
                                v-for="photo in post.photos.slice(3)"
                                :key="photo.id"
                                class="flex-shrink-0 rounded-xl overflow-hidden cursor-pointer"
                                style="width:120px; height:90px;"
                                @click="activePhoto = photo.url">
                                <img :src="photo.url" alt="Miniatura"
                                    class="w-full h-full object-cover hover:opacity-80 transition-opacity" />
                            </div>
                        </div>
                    </div>

                    <!-- CTA WhatsApp -->
                    <div class="mt-14 rounded-2xl p-8 flex flex-col md:flex-row items-start md:items-center gap-6"
                        style="background-color:#012d1d;">
                        <div class="flex-1">
                            <h3 class="text-xl font-black text-white mb-2"
                                style="font-family:Manrope,sans-serif;">
                                {{ t('Interessado em nossos produtos?', 'Interested in our products?') }}
                            </h3>
                            <p class="text-sm italic text-white/70"
                                style="font-family:Newsreader,serif;">
                                {{ t('Nossa equipe está pronta para transformar seu projeto em realidade.', 'Our team is ready to bring your project to life.') }}
                            </p>
                        </div>
                        <a :href="whatsappUrl" target="_blank" rel="noopener"
                            class="flex-shrink-0 flex items-center gap-3 px-6 py-3 rounded-xl font-bold text-sm transition-all hover:scale-105 shadow-lg"
                            style="font-family:Manrope,sans-serif; background-color:#25d366; color:#fff;">
                            <span class="material-symbols-outlined" style="font-variation-settings:'FILL' 1;">chat</span>
                            {{ t('Solicitar Orçamento via WhatsApp', 'Request Quote on WhatsApp') }}
                        </a>
                    </div>

                    <!-- Navegação prev/next -->
                    <div class="mt-12 pt-8 grid grid-cols-1 sm:grid-cols-2 gap-4"
                        style="border-top:1px solid #e5e2dd;">
                        <Link v-if="prev" :href="postUrl(prev.slug)"
                            class="flex items-start gap-3 p-4 rounded-xl group transition-all hover:translate-x-1"
                            style="background-color:#fff;">
                            <span class="material-symbols-outlined text-xl mt-0.5 group-hover:-translate-x-1 transition-transform flex-shrink-0"
                                style="color:#6a5e33;">arrow_back</span>
                            <div>
                                <p class="text-[9px] font-bold uppercase tracking-[0.2em] mb-1"
                                    style="font-family:Manrope,sans-serif; color:#9e9e9e;">
                                    {{ t('Anterior', 'Previous') }}
                                </p>
                                <p class="text-sm font-bold leading-snug"
                                    style="font-family:Manrope,sans-serif; color:#012d1d;">
                                    {{ prev.title }}
                                </p>
                            </div>
                        </Link>
                        <div v-else></div>

                        <Link v-if="next" :href="postUrl(next.slug)"
                            class="flex items-start justify-end gap-3 p-4 rounded-xl group text-right transition-all hover:-translate-x-1"
                            style="background-color:#fff;">
                            <div>
                                <p class="text-[9px] font-bold uppercase tracking-[0.2em] mb-1"
                                    style="font-family:Manrope,sans-serif; color:#9e9e9e;">
                                    {{ t('Próximo', 'Next') }}
                                </p>
                                <p class="text-sm font-bold leading-snug"
                                    style="font-family:Manrope,sans-serif; color:#012d1d;">
                                    {{ next.title }}
                                </p>
                            </div>
                            <span class="material-symbols-outlined text-xl mt-0.5 group-hover:translate-x-1 transition-transform flex-shrink-0"
                                style="color:#6a5e33;">arrow_forward</span>
                        </Link>
                    </div>
                </main>
            </div>
        </div>
    </div>
</template>
