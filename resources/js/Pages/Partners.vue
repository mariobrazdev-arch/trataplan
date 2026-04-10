<script setup>
import SeoHead from '@/Components/Shared/SeoHead.vue';
import { useLocale } from '@/composables/useLocale';

const props = defineProps({
    partners: Array,
    seo: Object,
});

const { t } = useLocale();
</script>

<template>
    <SeoHead v-bind="seo" />

    <div class="pt-28 pb-20" style="background-color: #fcf9f4;">
        <div class="container mx-auto px-6">
            <!-- Header -->
            <div class="text-center mb-16">
                <span class="font-bold tracking-widest text-sm uppercase mb-3 block" style="font-family: Manrope, sans-serif; color: #6a5e33;">
                    {{ t('PARCEIROS', 'PARTNERS') }}
                </span>
                <h1 class="text-4xl md:text-5xl font-bold" style="font-family: Manrope, sans-serif; color: #012d1d;">
                    {{ t('Nossos Parceiros', 'Our Partners') }}
                </h1>
                <div class="w-24 h-1 mx-auto mt-6" style="background-color: #6a5e33;"></div>
            </div>

            <!-- Empty state -->
            <div v-if="!partners?.length" class="text-center py-24">
                <span class="material-symbols-outlined text-6xl mb-4 block" style="color: #c1c8c2;">handshake</span>
                <p style="font-family: Newsreader, serif; color: #717973;">
                    {{ t('Parceiros em breve.', 'Partners coming soon.') }}
                </p>
            </div>

            <!-- Partners grid -->
            <div v-else class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-8">
                <a
                    v-for="partner in partners"
                    :key="partner.id"
                    :href="partner.website || '#'"
                    :target="partner.website ? '_blank' : '_self'"
                    rel="noopener"
                    class="group flex flex-col items-center gap-4 p-8 rounded-xl transition-all hover:-translate-y-1"
                    style="background-color: #f0ede8;"
                >
                    <div class="w-24 h-24 flex items-center justify-center">
                        <img
                            v-if="partner.logo_url"
                            :src="partner.logo_url"
                            :alt="partner.name"
                            class="max-w-full max-h-full object-contain filter grayscale group-hover:grayscale-0 transition-all duration-300"
                        />
                        <span v-else class="material-symbols-outlined text-5xl" style="color: #c1c8c2;">business</span>
                    </div>
                    <span class="font-bold text-center" style="font-family: Manrope, sans-serif; color: #012d1d;">{{ partner.name }}</span>
                    <p v-if="partner.description" class="text-sm text-center" style="font-family: Newsreader, serif; color: #414844;">{{ partner.description }}</p>
                </a>
            </div>
        </div>
    </div>
</template>
