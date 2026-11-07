<script setup>
import { router } from '@inertiajs/vue3';
import { useWhatsAppPicker } from '@/composables/useWhatsAppPicker';
import { computed } from 'vue';

const props = defineProps({
    hero: { type: Object, required: true },
});

const { open: openWhatsApp } = useWhatsAppPicker();

const fallbackImg = 'https://images.unsplash.com/photo-1504307651254-35680f356dfd?w=900&q=80';
const mainPhoto = computed(() => props.hero.main_image_url || props.hero.photos?.[0] || fallbackImg);
const sidePhotos = computed(() => (props.hero.photos || []).slice(0, 3));

function handleCtaClick() {
    if (props.hero.cta_url) {
        router.visit(props.hero.cta_url);
    } else {
        openWhatsApp();
    }
}
</script>

<template>
    <section class="relative min-h-[480px] md:h-[883px] flex items-center overflow-hidden bg-surface-container-low">
        <div class="container mx-auto px-6 grid md:grid-cols-2 gap-10 items-center">
            <!-- Texto -->
            <div class="max-w-xl">
                <span class="text-secondary text-xs font-black uppercase tracking-[0.3em]"
                      style="font-family: Manrope, sans-serif;">
                    Trataplan
                </span>
                <h2 class="text-primary font-headline font-extrabold text-4xl md:text-5xl lg:text-6xl tracking-tight leading-[1.1] mt-3 mb-6">
                    {{ hero.title }}
                </h2>
                <p v-if="hero.subtitle" class="text-on-surface-variant text-lg font-body mb-8 leading-relaxed italic">
                    {{ hero.subtitle }}
                </p>
                <button
                    type="button"
                    @click="handleCtaClick"
                    class="bg-primary hover:bg-primary/90 text-white px-8 py-4 rounded-xl font-headline font-bold uppercase tracking-wider transition-transform active:scale-95 shadow-xl shadow-primary/20"
                >
                    {{ hero.cta_label || 'Falar no WhatsApp' }}
                </button>
            </div>

            <!-- Imagem principal + mini-grid -->
            <div v-if="sidePhotos.length" class="grid grid-cols-2 gap-3 h-[360px] md:h-[525px]">
                <div class="rounded-2xl overflow-hidden shadow-xl min-h-0 min-w-0">
                    <img :src="mainPhoto" :alt="hero.title" class="w-full h-full object-cover" />
                </div>
                <div class="grid gap-3 min-h-0" :class="sidePhotos.length > 1 ? 'grid-rows-2' : 'grid-rows-1'">
                    <div v-for="(photo, i) in sidePhotos" :key="i" class="rounded-2xl overflow-hidden shadow-xl min-h-0 min-w-0">
                        <img :src="photo" :alt="`${hero.title} ${i + 1}`" class="w-full h-full object-cover" />
                    </div>
                </div>
            </div>
            <div v-else class="rounded-2xl overflow-hidden shadow-xl h-[360px] md:h-[525px] min-h-0 min-w-0">
                <img :src="mainPhoto" :alt="hero.title" class="w-full h-full object-cover" />
            </div>
        </div>
    </section>
</template>
