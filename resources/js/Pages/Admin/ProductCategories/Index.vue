<script setup>
import AdminLayout from '@/Components/Layout/AdminLayout.vue';
import { useForm, router } from '@inertiajs/vue3';
import axios from 'axios';
import { ref } from 'vue';
import { useConfirm } from '@/composables/useConfirm';

defineOptions({ layout: null });

const props = defineProps({ categories: Array });

const showModal   = ref(false);
const editingItem = ref(null);

const form = useForm({ name_pt: '', name_en: '', description_pt: '', description_en: '' });

function openCreate() {
    editingItem.value = null;
    form.reset();
    form.clearErrors();
    showModal.value = true;
}

function openEdit(cat) {
    editingItem.value   = cat;
    form.name_pt        = cat.name_pt ?? '';
    form.name_en        = cat.name_en ?? '';
    form.description_pt = cat.description_pt ?? '';
    form.description_en = cat.description_en ?? '';
    form.clearErrors();
    showModal.value = true;
}

function closeModal() { showModal.value = false; }

function submit() {
    if (editingItem.value) {
        form.transform(d => ({ ...d, _method: 'PATCH' }))
            .post(`/admin/produto-categorias/${editingItem.value.id}`, { onSuccess: () => closeModal() });
    } else {
        form.post('/admin/produto-categorias', { onSuccess: () => closeModal() });
    }
}

const { confirm } = useConfirm();

async function destroy(cat) {
    const ok = await confirm({ title: 'Remover categoria', message: `Remover a categoria "${cat.name_pt}"? Esta ação não pode ser desfeita.` });
    if (!ok) return;
    router.delete(`/admin/produto-categorias/${cat.id}`);
}

// ── Drag & Drop ──────────────────────────────────────────────────────────────
const draggingId  = ref(null);
const dragOverId  = ref(null);
const localCats   = ref([...props.categories]);
const saving      = ref(false);

function onDragStart(cat) {
    draggingId.value = cat.id;
}

function onDragOver(e, cat) {
    e.preventDefault();
    dragOverId.value = cat.id;
}

function onDrop(targetCat) {
    if (draggingId.value === targetCat.id) return;

    const from = localCats.value.findIndex(c => c.id === draggingId.value);
    const to   = localCats.value.findIndex(c => c.id === targetCat.id);

    const updated = [...localCats.value];
    const [moved] = updated.splice(from, 1);
    updated.splice(to, 0, moved);
    localCats.value = updated;

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
    const order = localCats.value.map((c, i) => ({ id: c.id, order: i + 1 }));
    axios.post('/admin/produto-categorias/reorder', { order })
        .finally(() => { saving.value = false; });
}
</script>

<template>
    <AdminLayout>
        <template #title>Categorias de Produto</template>
        <template #subtitle>Arraste os cards para definir a ordem de exibição no catálogo.</template>
        <template #actions>
            <div class="flex items-center gap-3">
                <transition name="fade">
                    <span v-if="saving" class="text-xs font-label italic" style="color: #717973;">
                        Salvando ordem...
                    </span>
                </transition>
                <button @click="openCreate"
                    class="flex items-center gap-2 px-6 py-2.5 rounded-lg font-headline font-bold text-sm text-white shadow-lg transition-all active:scale-95"
                    style="background-color: #012d1d;">
                    <span class="material-symbols-outlined text-base normal-case">add</span>
                    Nova Categoria
                </button>
            </div>
        </template>

        <div class="flex items-center justify-between mb-8">
            <h3 class="text-2xl font-headline font-bold text-primary">Categorias</h3>
            <span class="font-label text-[10px] uppercase tracking-[0.2em] text-outline">{{ localCats?.length || 0 }} registros</span>
        </div>

        <div v-if="!localCats?.length" class="py-20 text-center rounded-xl" style="background-color: #f6f3ee;">
            <span class="material-symbols-outlined text-5xl mb-3 block normal-case" style="color: #c1c8c2;">category</span>
            <p class="font-body italic" style="color: #717973;">Nenhuma categoria cadastrada ainda.</p>
            <button @click="openCreate" class="mt-4 inline-block font-label text-sm font-bold uppercase tracking-widest hover:underline" style="color: #012d1d;">
                Criar primeira categoria
            </button>
        </div>

        <div v-else class="space-y-3">
            <div v-for="cat in localCats" :key="cat.id"
                draggable="true"
                @dragstart="onDragStart(cat)"
                @dragover="onDragOver($event, cat)"
                @drop="onDrop(cat)"
                @dragend="onDragEnd"
                class="group p-5 rounded-xl flex items-center gap-4 transition-all cursor-grab active:cursor-grabbing select-none"
                :style="{
                    backgroundColor: dragOverId === cat.id ? '#e8f5ee' : '#ffffff',
                    opacity: draggingId === cat.id ? '0.4' : '1',
                    border: dragOverId === cat.id ? '2px dashed #012d1d' : '2px solid transparent',
                    transform: draggingId === cat.id ? '' : '',
                }">

                <!-- Handle -->
                <span class="material-symbols-outlined text-xl shrink-0 normal-case" style="color: #c1c8c2;">drag_indicator</span>

                <!-- Ícone -->
                <div class="w-10 h-10 rounded-lg flex items-center justify-center flex-shrink-0" style="background-color: #f0ede8;">
                    <span class="material-symbols-outlined text-lg normal-case" style="color: #012d1d;">category</span>
                </div>

                <!-- Info -->
                <div class="flex-grow min-w-0">
                    <h4 class="text-base font-headline font-extrabold text-primary">{{ cat.name_pt }}</h4>
                    <p v-if="cat.description_pt" class="text-sm font-body italic line-clamp-1 mt-0.5" style="color: #717973;">{{ cat.description_pt }}</p>
                </div>

                <!-- Actions -->
                <div class="flex items-center gap-1 opacity-0 group-hover:opacity-100 transition-opacity flex-shrink-0">
                    <button @click.stop="openEdit(cat)" class="p-2 rounded-lg transition-colors" title="Editar" style="color: #012d1d;"
                        @mouseenter="$event.currentTarget.style.backgroundColor='#c1ecd4'"
                        @mouseleave="$event.currentTarget.style.backgroundColor='transparent'">
                        <span class="material-symbols-outlined normal-case">edit_square</span>
                    </button>
                    <button @click.stop="destroy(cat)" class="p-2 rounded-lg transition-colors" title="Remover" style="color: #ba1a1a;"
                        @mouseenter="$event.currentTarget.style.backgroundColor='#ffdad6'"
                        @mouseleave="$event.currentTarget.style.backgroundColor='transparent'">
                        <span class="material-symbols-outlined normal-case">delete</span>
                    </button>
                </div>
            </div>
        </div>
    </AdminLayout>

    <!-- Modal -->
    <Teleport to="body">
        <Transition enter-active-class="transition duration-200 ease-out" enter-from-class="opacity-0" enter-to-class="opacity-100"
            leave-active-class="transition duration-150 ease-in" leave-from-class="opacity-100" leave-to-class="opacity-0">
            <div v-if="showModal" class="fixed inset-0 z-50 flex items-center justify-center p-4"
                style="background-color: rgba(0,0,0,0.45);" @click.self="closeModal">
                <div class="w-full max-w-lg rounded-2xl shadow-2xl overflow-hidden" style="background-color: #f6f3ee;">

                    <div class="flex items-center justify-between px-6 py-4 border-b" style="border-color: #e8e4de; background-color: #ffffff;">
                        <div>
                            <h2 class="text-lg font-headline font-bold text-primary">
                                {{ editingItem ? 'Editar Categoria' : 'Nova Categoria' }}
                            </h2>
                            <p class="text-xs font-body italic" style="color: #717973;">Preencha nos dois idiomas para exibição correta no site.</p>
                        </div>
                        <button @click="closeModal" class="p-1.5 rounded-lg transition-colors" style="color: #717973;"
                            @mouseenter="$event.currentTarget.style.backgroundColor='#ebe8e3'"
                            @mouseleave="$event.currentTarget.style.backgroundColor='transparent'">
                            <span class="material-symbols-outlined normal-case">close</span>
                        </button>
                    </div>

                    <form @submit.prevent="submit" class="px-6 py-5 space-y-4">
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block text-xs font-bold uppercase tracking-widest mb-1.5" style="color: #77574d;">Nome PT *</label>
                                <input v-model="form.name_pt" type="text" required maxlength="150"
                                    placeholder="Ex.: Eucalipto"
                                    class="w-full px-4 py-3 rounded-lg outline-none text-sm"
                                    style="background-color: #ffffff; border: 1.5px solid #e8e4de;"
                                    :style="form.errors.name_pt ? 'border-color:#ba1a1a;' : ''" />
                                <p v-if="form.errors.name_pt" class="text-xs mt-1" style="color:#ba1a1a;">{{ form.errors.name_pt }}</p>
                            </div>
                            <div>
                                <label class="block text-xs font-bold uppercase tracking-widest mb-1.5" style="color: #77574d;">Nome EN</label>
                                <input v-model="form.name_en" type="text" maxlength="150"
                                    placeholder="Ex.: Eucalyptus"
                                    class="w-full px-4 py-3 rounded-lg outline-none text-sm"
                                    style="background-color: #ffffff; border: 1.5px solid #e8e4de;" />
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block text-xs font-bold uppercase tracking-widest mb-1.5" style="color: #77574d;">Descrição PT</label>
                                <textarea v-model="form.description_pt" rows="3" maxlength="500"
                                    placeholder="Descrição em português..."
                                    class="w-full px-4 py-3 rounded-lg outline-none text-sm resize-none"
                                    style="background-color: #ffffff; border: 1.5px solid #e8e4de; font-family: Newsreader, serif;"></textarea>
                            </div>
                            <div>
                                <label class="block text-xs font-bold uppercase tracking-widest mb-1.5" style="color: #77574d;">Description EN</label>
                                <textarea v-model="form.description_en" rows="3" maxlength="500"
                                    placeholder="Description in English..."
                                    class="w-full px-4 py-3 rounded-lg outline-none text-sm resize-none"
                                    style="background-color: #ffffff; border: 1.5px solid #e8e4de; font-family: Newsreader, serif;"></textarea>
                            </div>
                        </div>

                        <div class="flex items-center justify-end gap-3 pt-2">
                            <button type="button" @click="closeModal"
                                class="px-5 py-2.5 rounded-lg text-sm font-bold font-label uppercase tracking-wider"
                                style="background-color: #ebe8e3; color: #414844;">Cancelar</button>
                            <button type="submit" :disabled="form.processing"
                                class="flex items-center gap-2 px-6 py-2.5 rounded-lg text-sm font-bold font-label uppercase tracking-wider text-white transition-all active:scale-95 disabled:opacity-60"
                                style="background-color: #012d1d;">
                                <span v-if="form.processing" class="material-symbols-outlined text-base animate-spin normal-case">progress_activity</span>
                                {{ editingItem ? 'Salvar alterações' : 'Criar categoria' }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </Transition>
    </Teleport>
</template>

<style scoped>
.fade-enter-active, .fade-leave-active { transition: opacity 0.3s; }
.fade-enter-from, .fade-leave-to { opacity: 0; }
</style>
