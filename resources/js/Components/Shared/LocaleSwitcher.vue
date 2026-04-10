<script setup>
import { useLocale } from '@/composables/useLocale';

const { locale, switchLocaleUrl } = useLocale();

function getUrl() {
    return switchLocaleUrl(window.location.pathname);
}
</script>

<template>
    <a
        :href="getUrl()"
        class="text-emerald-100/70 hover:text-white transition-colors font-headline text-sm tracking-wide uppercase border border-emerald-100/20 px-2 py-1 rounded"
    >
        {{ locale === 'pt' ? 'EN' : 'PT' }}
    </a>
</template>
