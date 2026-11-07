<script setup>
import { router } from '@inertiajs/vue3';
import { useWhatsAppPicker } from '@/composables/useWhatsAppPicker';
import { computed } from 'vue';

const props = defineProps({
    hero: { type: Object, required: true },
});

const { open: openWhatsApp } = useWhatsAppPicker();

function handleCtaClick() {
    if (props.hero.cta_url) {
        router.visit(props.hero.cta_url);
    } else {
        openWhatsApp();
    }
}

const fallbackPhotos = [
    'https://images.unsplash.com/photo-1555041469-a586c61ea9bc?w=600&q=80',
    'https://images.unsplash.com/photo-1504307651254-35680f356dfd?w=600&q=80',
    'https://images.unsplash.com/photo-1597200381847-30ec200eeb9a?w=600&q=80',
    'https://images.unsplash.com/photo-1416879595882-3373a0480b5b?w=600&q=80',
    'https://images.unsplash.com/photo-1449158743715-0a90ebb6d2d8?w=600&q=80',
    'https://images.unsplash.com/photo-1505409859467-3a796fd5798e?w=600&q=80',
];
const gridPhotos = computed(() => {
    const photos = props.hero.photos?.length ? props.hero.photos : fallbackPhotos;
    return photos.slice(0, 6);
});
</script>

<template>
    <section class="relative min-h-[480px] md:h-[883px] flex items-center overflow-hidden" style="background-color: #012d1d;">
        <!-- Grid de fotos no fundo -->
        <div class="absolute inset-0 grid grid-cols-3 md:grid-cols-6 opacity-40">
            <div v-for="(photo, i) in gridPhotos" :key="i" class="overflow-hidden">
                <img :src="photo" :alt="`${hero.title} ${i + 1}`" class="w-full h-full object-cover" />
            </div>
        </div>
        <div class="absolute inset-0" style="background: linear-gradient(to bottom, rgba(1,45,29,0.55), rgba(1,45,29,0.85));"></div>

        <!-- Texto centralizado -->
        <div class="container mx-auto px-6 relative z-10 text-center">
            <div class="max-w-3xl mx-auto">
                <span class="text-primary-fixed text-xs font-black uppercase tracking-[0.3em]"
                      style="font-family: Manrope, sans-serif;">
                    Portfólio Trataplan
                </span>
                <h2 class="text-emerald-50 font-headline font-extrabold text-4xl md:text-5xl lg:text-6xl tracking-tight leading-[1.1] mt-3 mb-6">
                    {{ hero.title }}
                </h2>
                <p v-if="hero.subtitle" class="text-emerald-50/80 text-xl font-body mb-10 leading-relaxed italic max-w-2xl mx-auto">
                    {{ hero.subtitle }}
                </p>
                <button
                    type="button"
                    @click="handleCtaClick"
                    class="bg-tertiary hover:bg-tertiary/90 text-on-tertiary px-8 py-4 rounded-xl font-headline font-bold uppercase tracking-wider transition-transform active:scale-95 shadow-xl shadow-primary/20"
                >
                    {{ hero.cta_label || 'Falar no WhatsApp' }}
                </button>
            </div>
        </div>
    </section>
</template>
