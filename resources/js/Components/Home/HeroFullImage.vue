<script setup>
import { router } from '@inertiajs/vue3';
import { useWhatsAppPicker } from '@/composables/useWhatsAppPicker';

const props = defineProps({
    hero: { type: Object, required: true },
});

const { open: openWhatsApp } = useWhatsAppPicker();

const fallbackImg = 'https://images.unsplash.com/photo-1558618666-fcd25c85cd64?w=1920&q=80';

function handleCtaClick() {
    if (props.hero.cta_url) {
        router.visit(props.hero.cta_url);
    } else {
        openWhatsApp();
    }
}
</script>

<template>
    <section class="relative min-h-[480px] md:h-[883px] flex items-center overflow-hidden bg-white">
        <div class="absolute inset-0 z-0">
            <img
                class="w-full h-full object-contain"
                :src="hero.main_image_url || fallbackImg"
                :alt="hero.title"
            />
            <div class="absolute inset-0 bg-gradient-to-r from-primary/90 via-primary/40 to-transparent"></div>
        </div>
        <div class="container mx-auto px-6 relative z-10 grid md:grid-cols-2">
            <div class="max-w-2xl">
                <h2 class="text-emerald-50 font-headline font-extrabold text-4xl md:text-5xl lg:text-7xl tracking-tight leading-[1.1] mb-6">
                    {{ hero.title }}
                </h2>
                <p v-if="hero.subtitle" class="text-emerald-50/80 text-xl font-body mb-10 max-w-lg leading-relaxed italic">
                    {{ hero.subtitle }}
                </p>
                <div class="flex flex-wrap gap-4 items-center">
                    <button
                        type="button"
                        @click="handleCtaClick"
                        class="bg-tertiary hover:bg-tertiary/90 text-on-tertiary px-8 py-4 rounded-xl font-headline font-bold uppercase tracking-wider transition-transform active:scale-95 shadow-xl shadow-primary/20"
                    >
                        {{ hero.cta_label || 'Falar no WhatsApp' }}
                    </button>
                    <div class="bg-secondary-container/20 backdrop-blur-md border border-white/10 px-6 py-4 rounded-xl flex items-center gap-3">
                        <span class="material-symbols-outlined text-secondary-container" style="font-variation-settings: 'FILL' 1;">verified</span>
                        <span class="text-emerald-50 font-headline font-bold text-sm uppercase tracking-widest">
                            15 Anos de Garantia
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </section>
</template>
