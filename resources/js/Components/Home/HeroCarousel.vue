<script setup>
import { computed, onBeforeUnmount, onMounted, ref } from 'vue';
import HeroFullImage from '@/Components/Home/HeroFullImage.vue';
import HeroSplitScreen from '@/Components/Home/HeroSplitScreen.vue';
import HeroPhotoGrid from '@/Components/Home/HeroPhotoGrid.vue';

const props = defineProps({
    heroes: { type: Array, default: () => [] },
});

const layoutComponents = {
    full_image: HeroFullImage,
    split_screen: HeroSplitScreen,
    photo_grid: HeroPhotoGrid,
};

const activeIndex = ref(0);
const isPaused = ref(false);
let timer = null;

const isCarousel = computed(() => props.heroes.length > 1);
const activeHero = computed(() => props.heroes[activeIndex.value]);
const activeComponent = computed(() => layoutComponents[activeHero.value?.layout] ?? HeroFullImage);
const isLightLayout = computed(() => activeHero.value?.layout === 'split_screen');

function next() {
    if (!props.heroes.length) return;
    activeIndex.value = (activeIndex.value + 1) % props.heroes.length;
}

function prev() {
    if (!props.heroes.length) return;
    activeIndex.value = (activeIndex.value - 1 + props.heroes.length) % props.heroes.length;
}

function goTo(i) {
    activeIndex.value = i;
}

function startAutoplay() {
    if (!isCarousel.value) return;
    stopAutoplay();
    timer = setInterval(() => {
        if (!isPaused.value) next();
    }, 6000);
}

function stopAutoplay() {
    if (timer) {
        clearInterval(timer);
        timer = null;
    }
}

onMounted(startAutoplay);
onBeforeUnmount(stopAutoplay);
</script>

<template>
    <div v-if="activeHero" class="relative" @mouseenter="isPaused = true" @mouseleave="isPaused = false">
        <component :is="activeComponent" :hero="activeHero" :key="activeHero.id" />

        <template v-if="isCarousel">
            <!-- Setas -->
            <button type="button" @click="prev"
                class="absolute left-4 top-1/2 -translate-y-1/2 z-20 w-11 h-11 rounded-full flex items-center justify-center transition-colors shadow-lg"
                :style="isLightLayout
                    ? 'background-color: rgba(1,45,29,0.1); backdrop-filter: blur(4px); color: #012d1d;'
                    : 'background-color: rgba(255,255,255,0.15); backdrop-filter: blur(4px); color: #fff;'"
                aria-label="Anterior">
                <span class="material-symbols-outlined">chevron_left</span>
            </button>
            <button type="button" @click="next"
                class="absolute right-4 top-1/2 -translate-y-1/2 z-20 w-11 h-11 rounded-full flex items-center justify-center transition-colors shadow-lg"
                :style="isLightLayout
                    ? 'background-color: rgba(1,45,29,0.1); backdrop-filter: blur(4px); color: #012d1d;'
                    : 'background-color: rgba(255,255,255,0.15); backdrop-filter: blur(4px); color: #fff;'"
                aria-label="Próximo">
                <span class="material-symbols-outlined">chevron_right</span>
            </button>

            <!-- Dots -->
            <div class="absolute bottom-6 left-1/2 -translate-x-1/2 z-20 flex items-center gap-2">
                <button v-for="(hero, i) in heroes" :key="hero.id" type="button" @click="goTo(i)"
                    class="rounded-full transition-all"
                    :style="i === activeIndex
                        ? 'width: 28px; height: 8px; background-color: #fbbf24;'
                        : (isLightLayout
                            ? 'width: 8px; height: 8px; background-color: rgba(1,45,29,0.25);'
                            : 'width: 8px; height: 8px; background-color: rgba(255,255,255,0.5);')"
                    :aria-label="`Slide ${i + 1}`">
                </button>
            </div>
        </template>
    </div>
</template>
