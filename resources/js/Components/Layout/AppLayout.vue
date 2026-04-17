<script setup>
import Navbar from '@/Components/Layout/Navbar.vue';
import Footer from '@/Components/Layout/Footer.vue';
import FloatingButtons from '@/Components/Layout/FloatingButtons.vue';
import WhatsAppPicker from '@/Components/WhatsAppPicker.vue';
import { usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

const page = usePage();
const flash = computed(() => page.props.flash);
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
