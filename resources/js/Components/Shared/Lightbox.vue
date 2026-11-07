<script setup>
import { computed, onBeforeUnmount, onMounted, watch } from 'vue';

const props = defineProps({
    open: { type: Boolean, default: false },
    photos: { type: Array, default: () => [] },
    startIndex: { type: Number, default: 0 },
});

const emit = defineEmits(['update:open', 'update:startIndex']);

const activeIndex = computed({
    get: () => props.startIndex,
    set: (value) => emit('update:startIndex', value),
});

function close() {
    emit('update:open', false);
}

function next() {
    if (!props.photos.length) return;
    activeIndex.value = (activeIndex.value + 1) % props.photos.length;
}

function prev() {
    if (!props.photos.length) return;
    activeIndex.value = (activeIndex.value - 1 + props.photos.length) % props.photos.length;
}

function onKeydown(e) {
    if (!props.open) return;
    if (e.key === 'Escape') close();
    if (e.key === 'ArrowRight') next();
    if (e.key === 'ArrowLeft') prev();
}

watch(() => props.open, (isOpen) => {
    document.body.style.overflow = isOpen ? 'hidden' : '';
});

onMounted(() => window.addEventListener('keydown', onKeydown));
onBeforeUnmount(() => {
    window.removeEventListener('keydown', onKeydown);
    document.body.style.overflow = '';
});
</script>

<template>
    <Teleport to="body">
        <div v-if="open"
            class="fixed inset-0 z-[100] flex items-center justify-center"
            style="background-color: rgba(10, 10, 10, 0.95);"
            @click.self="close">

            <button type="button" @click="close"
                class="absolute top-5 right-5 flex items-center justify-center w-11 h-11 rounded-full transition-colors hover:bg-white/10"
                aria-label="Fechar">
                <span class="material-symbols-outlined text-3xl text-white">close</span>
            </button>

            <div v-if="photos.length"
                class="absolute top-5 left-5 px-3 py-1.5 rounded-full text-xs font-bold uppercase tracking-widest"
                style="background-color: rgba(255,255,255,0.1); color: #fff;">
                {{ activeIndex + 1 }} / {{ photos.length }}
            </div>

            <button v-if="photos.length > 1" type="button" @click.stop="prev"
                class="absolute left-2 md:left-6 flex items-center justify-center w-12 h-12 rounded-full transition-colors hover:bg-white/10"
                aria-label="Anterior">
                <span class="material-symbols-outlined text-4xl text-white">chevron_left</span>
            </button>

            <img v-if="photos[activeIndex]"
                :src="photos[activeIndex]"
                :alt="`Foto ${activeIndex + 1}`"
                class="max-w-[90vw] max-h-[85vh] object-contain select-none"
                @click.stop
            />

            <button v-if="photos.length > 1" type="button" @click.stop="next"
                class="absolute right-2 md:right-6 flex items-center justify-center w-12 h-12 rounded-full transition-colors hover:bg-white/10"
                aria-label="Próxima">
                <span class="material-symbols-outlined text-4xl text-white">chevron_right</span>
            </button>
        </div>
    </Teleport>
</template>
