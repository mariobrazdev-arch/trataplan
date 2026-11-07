<script setup>
import AdminLayout from '@/Components/Layout/AdminLayout.vue';
import { Link, router } from '@inertiajs/vue3';
import { ref, watch } from 'vue';
import axios from 'axios';
import { useConfirm } from '@/composables/useConfirm';

defineOptions({ layout: null });

const props = defineProps({ heroes: Array, canCreate: Boolean });

const { confirm } = useConfirm();

const layoutLabels = {
    full_image: 'Imagem Cheia',
    split_screen: 'Split-Screen',
    photo_grid: 'Grid de Fotos',
};
const layoutIcons = {
    full_image: 'panorama',
    split_screen: 'splitscreen',
    photo_grid: 'grid_view',
};

async function destroy(hero) {
    const ok = await confirm({ title: 'Remover hero', message: `Remover "${hero.title_pt}"? Esta ação não pode ser desfeita.` });
    if (!ok) return;
    router.delete(`/admin/heroes/${hero.id}`);
}

function toggleActive(hero) {
    router.post(`/admin/heroes/${hero.id}/toggle`, {}, { preserveScroll: true, preserveState: false });
}

// ── Drag & Drop ──────────────────────────────────────────────────────────────
const draggingId  = ref(null);
const dragOverId  = ref(null);
const localHeroes = ref([...props.heroes]);
const saving      = ref(false);

// Resincroniza a lista local sempre que os props mudarem (ex: após toggleActive)
watch(() => props.heroes, (newHeroes) => {
    localHeroes.value = [...newHeroes];
});

function onDragStart(h) { draggingId.value = h.id; }

function onDragOver(e, h) {
    e.preventDefault();
    dragOverId.value = h.id;
}

function onDrop(target) {
    if (draggingId.value === target.id) return;

    const from = localHeroes.value.findIndex(h => h.id === draggingId.value);
    const to   = localHeroes.value.findIndex(h => h.id === target.id);

    const updated = [...localHeroes.value];
    const [moved] = updated.splice(from, 1);
    updated.splice(to, 0, moved);
    localHeroes.value = updated;

    draggingId.value = null;
    dragOverId.value = null;

    saveOrder();
}

function onDragEnd() {
    draggingId.value = null;
    dragOverId.value = null;
}

function saveOrder() {
    saving.value = true;
    const order = localHeroes.value.map((h, i) => ({ id: h.id, order: i + 1 }));
    axios.post('/admin/heroes/reorder', { order })
        .finally(() => { saving.value = false; });
}
</script>

<template>
    <AdminLayout>
        <template #title>Heroes</template>
        <template #subtitle>Até 3 banners da Home. Com 2 ou mais ativos, formam um carrossel automático.</template>
        <template #actions>
            <div class="flex items-center gap-3">
                <transition name="fade">
                    <span v-if="saving" class="text-xs font-label italic" style="color: #717973;">
                        Salvando ordem...
                    </span>
                </transition>
                <Link v-if="canCreate" href="/admin/heroes/criar"
                    class="flex items-center gap-2 px-6 py-2.5 rounded-lg font-headline font-bold text-sm text-white shadow-lg transition-all active:scale-95"
                    style="background-color: #012d1d;">
                    <span class="material-symbols-outlined text-base normal-case">add</span>
                    Novo Hero
                </Link>
            </div>
        </template>

        <div class="flex items-center justify-between mb-8">
            <h3 class="text-2xl font-headline font-bold text-primary">Heroes Cadastrados</h3>
            <span class="font-label text-[10px] uppercase tracking-[0.2em] text-outline">{{ localHeroes?.length || 0 }} / 3</span>
        </div>

        <div v-if="!localHeroes?.length" class="py-20 text-center rounded-xl" style="background-color: #f6f3ee;">
            <span class="material-symbols-outlined text-5xl mb-3 block normal-case" style="color: #c1c8c2;">view_carousel</span>
            <p class="font-body italic" style="color: #717973;">Nenhum hero cadastrado ainda.</p>
            <Link href="/admin/heroes/criar" class="mt-4 inline-block font-label text-sm font-bold uppercase tracking-widest hover:underline" style="color: #012d1d;">Criar primeiro hero</Link>
        </div>

        <div v-else class="space-y-3">
            <div v-for="hero in localHeroes" :key="hero.id"
                draggable="true"
                @dragstart="onDragStart(hero)"
                @dragover="onDragOver($event, hero)"
                @drop="onDrop(hero)"
                @dragend="onDragEnd"
                class="group p-5 rounded-xl flex items-center gap-4 transition-all cursor-grab active:cursor-grabbing select-none"
                :style="{
                    backgroundColor: dragOverId === hero.id ? '#e8f5ee' : '#ffffff',
                    opacity: draggingId === hero.id ? '0.4' : '1',
                    border: dragOverId === hero.id ? '2px dashed #012d1d' : '2px solid transparent',
                }">

                <span class="material-symbols-outlined text-xl shrink-0 normal-case" style="color: #c1c8c2;">drag_indicator</span>

                <div class="w-16 h-16 flex-shrink-0 rounded-lg overflow-hidden" style="background-color: #f0ede8;">
                    <img v-if="hero.cover_preview" :src="hero.cover_preview" class="w-full h-full object-cover" />
                    <div v-else class="w-full h-full flex items-center justify-center">
                        <span class="material-symbols-outlined text-2xl normal-case" style="color: #c1c8c2;">{{ layoutIcons[hero.layout] }}</span>
                    </div>
                </div>

                <div class="flex-grow min-w-0">
                    <div class="flex items-center gap-2 mb-0.5">
                        <button type="button" @click.stop="toggleActive(hero)"
                            class="px-2 py-0.5 rounded font-label text-[9px] uppercase font-black tracking-widest transition-colors"
                            :style="hero.is_active
                                ? 'background-color: #baaa79; color: #493f17;'
                                : 'background-color: #ebe8e3; color: #414844;'">
                            {{ hero.is_active ? 'Ativo' : 'Inativo' }}
                        </button>
                        <span class="px-2 py-0.5 rounded font-label text-[9px] uppercase font-black tracking-widest flex items-center gap-1"
                            style="background-color: #e8e4de; color: #6a5e33;">
                            <span class="material-symbols-outlined text-xs normal-case" style="font-size: 12px;">{{ layoutIcons[hero.layout] }}</span>
                            {{ layoutLabels[hero.layout] }}
                        </span>
                    </div>
                    <h4 class="text-base font-headline font-extrabold text-primary">{{ hero.title_pt }}</h4>
                    <div class="flex items-center gap-1 mt-1" style="color: #717973;">
                        <span class="material-symbols-outlined text-sm normal-case">photo_library</span>
                        <span class="text-[10px] font-label font-bold uppercase tracking-tighter">{{ hero.photos_count }} Fotos</span>
                    </div>
                </div>

                <div class="flex items-center gap-1 opacity-0 group-hover:opacity-100 transition-opacity flex-shrink-0">
                    <Link :href="`/admin/heroes/${hero.id}/editar`"
                        class="p-2 rounded-lg transition-colors" title="Editar"
                        style="color: #012d1d;"
                        @mouseenter="$event.currentTarget.style.backgroundColor='#c1ecd4'"
                        @mouseleave="$event.currentTarget.style.backgroundColor='transparent'">
                        <span class="material-symbols-outlined normal-case">edit_square</span>
                    </Link>
                    <button @click.stop="destroy(hero)"
                        class="p-2 rounded-lg transition-colors" title="Remover"
                        style="color: #ba1a1a;"
                        @mouseenter="$event.currentTarget.style.backgroundColor='#ffdad6'"
                        @mouseleave="$event.currentTarget.style.backgroundColor='transparent'">
                        <span class="material-symbols-outlined normal-case">delete</span>
                    </button>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>

<style scoped>
.fade-enter-active, .fade-leave-active { transition: opacity 0.3s; }
.fade-enter-from, .fade-leave-to { opacity: 0; }
</style>
