<script setup>
import Navbar from '@/Components/Layout/Navbar.vue';
import Footer from '@/Components/Layout/Footer.vue';
import FloatingButtons from '@/Components/Layout/FloatingButtons.vue';
import WhatsAppPicker from '@/Components/WhatsAppPicker.vue';
import { usePage, router } from '@inertiajs/vue3';
import { computed, onMounted, onBeforeUnmount } from 'vue';

const page = usePage();
const flash = computed(() => page.props.flash);

// ── Analytics tracking ───────────────────────────────────────────────────────
let trackId   = null;
let trackStart = null;
const offListeners = [];

async function trackView() {
    trackStart = Date.now();
    trackId = null;
    try {
        const res = await fetch('/api/track', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                url:      window.location.pathname + window.location.search,
                title:    document.title,
                referrer: document.referrer,
            }),
        });
        const data = await res.json();
        trackId = data.id ?? null;
    } catch {}
}

function sendDuration() {
    if (!trackId || !trackStart) return;
    const secs = Math.round((Date.now() - trackStart) / 1000);
    if (secs < 1) return;
    navigator.sendBeacon?.('/api/track/duration',
        new Blob([JSON.stringify({ id: trackId, duration: secs })], { type: 'application/json' })
    );
    trackId = null;
    trackStart = null;
}

function onVisibilityChange() {
    if (document.visibilityState === 'hidden') sendDuration();
}

onMounted(() => {
    trackView();
    offListeners.push(router.on('before',   () => sendDuration()));
    offListeners.push(router.on('navigate', () => setTimeout(trackView, 80)));
    window.addEventListener('beforeunload',     sendDuration);
    document.addEventListener('visibilitychange', onVisibilityChange);
});

onBeforeUnmount(() => {
    sendDuration();
    offListeners.forEach(fn => fn());
    window.removeEventListener('beforeunload',      sendDuration);
    document.removeEventListener('visibilitychange', onVisibilityChange);
});
</script>

<template>
    <div class="min-h-screen flex flex-col bg-surface" style="font-family: Newsreader, Georgia, serif; color: #1c1c19;">
        <Navbar />

        <!-- Flash messages -->
        <Transition
            enter-active-class="transition-all duration-400 ease-out"
            enter-from-class="opacity-0 translate-y-[-100%]"
            enter-to-class="opacity-100 translate-y-0"
        >
            <div v-if="flash?.success"
                class="fixed top-20 left-1/2 -translate-x-1/2 z-50 bg-primary text-white px-8 py-3 rounded-xl shadow-xl font-headline text-sm">
                ✓ {{ typeof flash.success === 'string' ? flash.success : 'Mensagem enviada com sucesso!' }}
            </div>
        </Transition>

        <main class="pt-16 flex-grow">
            <slot />
        </main>

        <Footer />
        <FloatingButtons />
        <WhatsAppPicker />
    </div>
</template>
