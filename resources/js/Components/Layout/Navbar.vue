<script setup>
import { ref, computed } from 'vue';
import { Link } from '@inertiajs/vue3';
import LocaleSwitcher from '@/Components/Shared/LocaleSwitcher.vue';
import { useLocale } from '@/composables/useLocale';
import { useSettings } from '@/composables/useSettings';

const { locale, t } = useLocale();
const { whatsappUrl, logoTransparentUrl, logoWhiteBgUrl } = useSettings();
const mobileOpen = ref(false);

// Para fundo branco usa logo_white_bg; fallback para transparent
const logoUrl = computed(() => logoWhiteBgUrl.value || logoTransparentUrl.value);

const navLinks = computed(() => {
    const prefix = locale.value === 'en' ? '/en' : '';
    return [
        { label: t('Início', 'Home'),        href: prefix + '/' },
        { label: t('Sobre', 'About'),        href: prefix + (locale.value === 'en' ? '/about'    : '/sobre') },
        { label: t('Produtos', 'Products'),  href: prefix + (locale.value === 'en' ? '/products' : '/produtos') },
        { label: 'Blog',                     href: prefix + '/blog' },
        { label: t('Contato', 'Contact'),    href: prefix + (locale.value === 'en' ? '/contact'  : '/contato') },
    ];
});

function isActive(href) {
    const path = window.location.pathname;
    if (href === '/' || href === '/en/') return path === href || path === '/en';
    return path.startsWith(href);
}
</script>

<template>
    <header class="fixed top-0 w-full z-50">
        <!-- Amber accent ribbon -->
        <div class="h-0.5 w-full bg-gradient-to-r from-amber-600 via-amber-400 to-amber-600"></div>

        <!-- Main nav bar — fundo branco -->
        <nav class="bg-white/95 backdrop-blur-md shadow-sm border-b border-emerald-950/8">
            <div class="flex justify-between items-center px-6 py-3 max-w-7xl mx-auto">

                <!-- Logo -->
                <Link :href="locale === 'en' ? '/en' : '/'" class="flex items-center gap-2.5 shrink-0">
                    <img
                        v-if="logoUrl"
                        :src="logoUrl"
                        alt="Trataplan"
                        class="h-20 w-auto object-contain"
                    />
                    <template v-else>
                        <span class="material-symbols-outlined text-emerald-800 text-2xl leading-none"
                              style="font-variation-settings: 'FILL' 1;">forest</span>
                        <span class="text-xl font-black tracking-tight leading-none"
                              style="font-family: Manrope, sans-serif; letter-spacing: -0.02em; color: #022c22;">TRATAPLAN</span>
                    </template>
                </Link>

                <!-- Desktop nav links -->
                <div class="hidden md:flex items-center gap-1">
                    <Link
                        v-for="link in navLinks"
                        :key="link.href"
                        :href="link.href"
                        class="relative px-3 py-1.5 rounded-lg text-lg font-medium transition-all duration-200"
                        :class="isActive(link.href)
                            ? 'bg-emerald-50'
                            : 'hover:bg-emerald-50/70'"
                        :style="isActive(link.href)
                            ? 'color: #022c22; font-family: Manrope, sans-serif; font-weight: 700;'
                            : 'color: #022c22cc; font-family: Manrope, sans-serif;'"
                    >
                        {{ link.label }}
                        <!-- Active dot -->
                        <span
                            v-if="isActive(link.href)"
                            class="absolute bottom-0.5 left-1/2 -translate-x-1/2 w-1 h-1 rounded-full bg-amber-500"
                        ></span>
                    </Link>
                </div>

                <!-- Right: locale + CTA -->
                <div class="hidden md:flex items-center gap-3">
                    <LocaleSwitcher />
                    <a
                        :href="whatsappUrl"
                        target="_blank"
                        rel="noopener noreferrer"
                        class="flex items-center gap-2 px-4 py-2 rounded-lg font-bold text-sm transition-all duration-200 active:scale-95 text-white hover:opacity-90"
                        style="background-color: #022c22; font-family: Manrope, sans-serif;"
                    >
                        <svg class="w-4 h-4 shrink-0" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
                        </svg>
                        WhatsApp
                    </a>
                </div>

                <!-- Mobile hamburger -->
                <button
                    class="md:hidden p-2 rounded-lg transition-colors hover:bg-emerald-50"
                    style="color: #022c22;"
                    @click="mobileOpen = !mobileOpen"
                    :aria-expanded="mobileOpen"
                    aria-label="Menu"
                >
                    <span class="material-symbols-outlined text-2xl leading-none">
                        {{ mobileOpen ? 'close' : 'menu' }}
                    </span>
                </button>
            </div>

            <!-- Mobile menu -->
            <Transition
                enter-active-class="transition-all duration-200 ease-out"
                enter-from-class="opacity-0 -translate-y-1"
                enter-to-class="opacity-100 translate-y-0"
                leave-active-class="transition-all duration-150 ease-in"
                leave-from-class="opacity-100 translate-y-0"
                leave-to-class="opacity-0 -translate-y-1"
            >
                <div v-if="mobileOpen" class="md:hidden border-t px-4 py-4 space-y-1 bg-white"
                     style="border-color: #022c2215;">
                    <Link
                        v-for="link in navLinks"
                        :key="link.href"
                        :href="link.href"
                        class="flex items-center justify-between px-3 py-2.5 rounded-lg text-sm font-medium transition-colors hover:bg-emerald-50"
                        :style="isActive(link.href)
                            ? 'color: #022c22; font-weight: 700; background: #f0fdf4;'
                            : 'color: #022c22cc;'"
                        style="font-family: Manrope, sans-serif;"
                        @click="mobileOpen = false"
                    >
                        {{ link.label }}
                        <span v-if="isActive(link.href)" class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                    </Link>

                    <div class="flex items-center justify-between pt-3 mt-2 border-t" style="border-color: #022c2215;">
                        <LocaleSwitcher />
                        <a
                            :href="whatsappUrl"
                            target="_blank"
                            rel="noopener noreferrer"
                            class="flex items-center gap-2 px-4 py-2 rounded-lg font-bold text-sm text-white transition-colors hover:opacity-90"
                            style="background-color: #022c22; font-family: Manrope, sans-serif;"
                        >
                            WhatsApp
                        </a>
                    </div>
                </div>
            </Transition>
        </nav>
    </header>
</template>
