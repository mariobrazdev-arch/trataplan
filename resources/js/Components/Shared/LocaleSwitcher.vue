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
        class="transition-colors font-headline text-sm font-bold tracking-wide uppercase border px-2.5 py-1 rounded-lg"
        style="color: #022c22cc; border-color: #022c2230; font-family: Manrope, sans-serif;"
        onmouseover="this.style.background='#f0fdf4'"
        onmouseout="this.style.background=''"
    >
        {{ locale === 'pt' ? 'EN' : 'PT' }}
    </a>
</template>
