<script setup>
import AdminLayout from '@/Components/Layout/AdminLayout.vue';
import { Link, router } from '@inertiajs/vue3';
import { ref } from 'vue';
import axios from 'axios';
import { useConfirm } from '@/composables/useConfirm';

defineOptions({ layout: null });

const props = defineProps({ services: Array });

const { confirm } = useConfirm();

async function destroy(service) {
    const ok = await confirm({ title: 'Remover serviço', message: `Remover "${service.name_pt}"? Esta ação não pode ser desfeita.` });
    if (!ok) return;
    router.delete(`/admin/portfolio/servicos/${service.id}`);
}

// ── Drag & Drop ──────────────────────────────────────────────────────────────
const draggingId  = ref(null);
const dragOverId  = ref(null);
const localServices = ref([...props.services]);
const saving      = ref(false);

function onDragStart(s) { draggingId.value = s.id; }

function onDragOver(e, s) {
    e.preventDefault();
    dragOverId.value = s.id;
}

function onDrop(target) {
    if (draggingId.value === target.id) return;

    const from = localServices.value.findIndex(s => s.id === draggingId.value);
    const to   = localServices.value.findIndex(s => s.id === target.id);

    const updated = [...localServices.value];
    const [moved] = updated.splice(from, 1);
    updated.splice(to, 0, moved);
    localServices.value = updated;

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
    const order = localServices.value.map((s, i) => ({ id: s.id, order: i + 1 }));
    axios.post('/admin/portfolio/servicos/reorder', { order })
        .finally(() => { saving.value = false; });
}
</script>

<template>
    <AdminLayout>
        <template #title>Mão de Obra</template>
        <template #subtitle>Arraste os cards para definir a ordem de exibição no catálogo.</template>
        <template #actions>
            <div class="flex items-center gap-3">
                <transition name="fade">
                    <span v-if="saving" class="text-xs font-label italic" style="color: #717973;">
                        Salvando ordem...
                    </span>
                </transition>
                <Link href="/admin/portfolio/servicos/criar"
                    class="flex items-center gap-2 px-6 py-2.5 rounded-lg font-headline font-bold text-sm text-white shadow-lg transition-all active:scale-95"
                    style="background-color: #012d1d;">
                    <span class="material-symbols-outlined text-base normal-case">add</span>
                    Novo Serviço
                </Link>
            </div>
        </template>

        <div class="flex items-center justify-between mb-8">
            <h3 class="text-2xl font-headline font-bold text-primary">Serviços Cadastrados</h3>
            <span class="font-label text-[10px] uppercase tracking-[0.2em] text-outline">{{ localServices?.length || 0 }} registros</span>
        </div>

        <div v-if="!localServices?.length" class="py-20 text-center rounded-xl" style="background-color: #f6f3ee;">
            <span class="material-symbols-outlined text-5xl mb-3 block normal-case" style="color: #c1c8c2;">construction</span>
            <p class="font-body italic" style="color: #717973;">Nenhum serviço cadastrado ainda.</p>
            <Link href="/admin/portfolio/servicos/criar" class="mt-4 inline-block font-label text-sm font-bold uppercase tracking-widest hover:underline" style="color: #012d1d;">Criar primeiro serviço</Link>
        </div>

        <div v-else class="space-y-3">
            <div v-for="service in localServices" :key="service.id"
                draggable="true"
                @dragstart="onDragStart(service)"
                @dragover="onDragOver($event, service)"
                @drop="onDrop(service)"
                @dragend="onDragEnd"
                class="group p-5 rounded-xl flex items-center gap-4 transition-all cursor-grab active:cursor-grabbing select-none"
                :style="{
                    backgroundColor: dragOverId === service.id ? '#e8f5ee' : '#ffffff',
                    opacity: draggingId === service.id ? '0.4' : '1',
                    border: dragOverId === service.id ? '2px dashed #012d1d' : '2px solid transparent',
                }">

                <!-- Handle -->
                <span class="material-symbols-outlined text-xl shrink-0 normal-case" style="color: #c1c8c2;">drag_indicator</span>

                <!-- Thumbnail -->
                <div class="w-16 h-16 flex-shrink-0 rounded-lg overflow-hidden" style="background-color: #f0ede8;">
                    <img v-if="service.cover_url" :src="service.cover_url" class="w-full h-full object-cover" />
                    <div v-else class="w-full h-full flex items-center justify-center">
                        <span class="material-symbols-outlined text-2xl normal-case" style="color: #c1c8c2;">construction</span>
                    </div>
                </div>

                <!-- Info -->
                <div class="flex-grow min-w-0">
                    <div class="flex items-center gap-2 mb-0.5">
                        <span class="px-2 py-0.5 rounded font-label text-[9px] uppercase font-black tracking-widest"
                            :style="service.is_active
                                ? 'background-color: #baaa79; color: #493f17;'
                                : 'background-color: #ebe8e3; color: #414844;'">
                            {{ service.is_active ? 'Ativo' : 'Inativo' }}
                        </span>
                    </div>
                    <h4 class="text-base font-headline font-extrabold text-primary">{{ service.name_pt }}</h4>
                    <p v-if="service.description_pt" class="text-sm font-body italic line-clamp-1 mt-0.5" style="color: #717973;">{{ service.description_pt }}</p>
                    <div class="flex items-center gap-1 mt-1" style="color: #717973;">
                        <span class="material-symbols-outlined text-sm normal-case">photo_library</span>
                        <span class="text-[10px] font-label font-bold uppercase tracking-tighter">{{ service.photos_count }} Fotos</span>
                    </div>
                </div>

                <!-- Actions -->
                <div class="flex items-center gap-1 opacity-0 group-hover:opacity-100 transition-opacity flex-shrink-0">
                    <Link :href="`/admin/portfolio/servicos/${service.id}/editar`"
                        class="p-2 rounded-lg transition-colors" title="Editar"
                        style="color: #012d1d;"
                        @mouseenter="$event.currentTarget.style.backgroundColor='#c1ecd4'"
                        @mouseleave="$event.currentTarget.style.backgroundColor='transparent'">
                        <span class="material-symbols-outlined normal-case">edit_square</span>
                    </Link>
                    <button @click.stop="destroy(service)"
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
