<script setup>
import AdminLayout from '@/Components/Layout/AdminLayout.vue';
import { useForm, router } from '@inertiajs/vue3';
import { ref } from 'vue';

defineOptions({ layout: null });
defineProps({ categories: Array });

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
    editingItem.value    = cat;
    form.name_pt         = cat.name_pt ?? '';
    form.name_en         = cat.name_en ?? '';
    form.description_pt  = cat.description_pt ?? '';
    form.description_en  = cat.description_en ?? '';
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

function destroy(cat) {
    if (confirm(`Remover a categoria "${cat.name_pt}"? Esta ação não pode ser desfeita.`)) {
        router.delete(`/admin/produto-categorias/${cat.id}`);
    }
}
</script>

<template>
    <AdminLayout>
        <template #title>Categorias de Produto</template>
        <template #subtitle>Grupos de classificação dos produtos cadastrados.</template>
        <template #actions>
            <button @click="openCreate"
                class="flex items-center gap-2 px-6 py-2.5 rounded-lg font-headline font-bold text-sm text-white shadow-lg transition-all active:scale-95"
                style="background-color: #012d1d;">
                <span class="material-symbols-outlined text-base">add</span>
                Nova Categoria
            </button>
        </template>

        <div class="flex items-center justify-between mb-8">
            <h3 class="text-2xl font-headline font-bold text-primary">Categorias</h3>
            <span class="font-label text-[10px] uppercase tracking-[0.2em] text-outline">{{ categories?.length || 0 }} registros</span>
        </div>

        <div v-if="!categories?.length" class="py-20 text-center rounded-xl" style="background-color: #f6f3ee;">
            <span class="material-symbols-outlined text-5xl mb-3 block" style="color: #c1c8c2;">category</span>
            <p class="font-body italic" style="color: #717973;">Nenhuma categoria cadastrada ainda.</p>
            <button @click="openCreate" class="mt-4 inline-block font-label text-sm font-bold uppercase tracking-widest hover:underline" style="color: #012d1d;">
                Criar primeira categoria
            </button>
        </div>

        <div v-else class="space-y-4">
            <div v-for="cat in categories" :key="cat.id"
                class="group p-5 rounded-xl flex items-center gap-6 transition-all hover:translate-x-2"
                style="background-color: #ffffff;">
                <div class="w-12 h-12 rounded-lg flex items-center justify-center flex-shrink-0" style="background-color: #f0ede8;">
                    <span class="material-symbols-outlined" style="color: #012d1d;">category</span>
                </div>
                <div class="flex-grow min-w-0">
                    <h4 class="text-base font-headline font-extrabold text-primary">{{ cat.name_pt }}</h4>
                    <p class="text-sm font-body italic mt-0.5" style="color: #6a5e33;">{{ cat.name_en || '—' }}</p>
                    <p v-if="cat.description_pt" class="text-sm font-body italic line-clamp-1 mt-1" style="color: #717973;">{{ cat.description_pt }}</p>
                </div>
                <div class="flex items-center gap-1 opacity-0 group-hover:opacity-100 transition-opacity flex-shrink-0">
                    <button @click="openEdit(cat)" class="p-2 rounded-lg transition-colors" title="Editar" style="color: #012d1d;"
                        @mouseenter="$event.currentTarget.style.backgroundColor='#c1ecd4'"
                        @mouseleave="$event.currentTarget.style.backgroundColor='transparent'">
                        <span class="material-symbols-outlined">edit_square</span>
                    </button>
                    <button @click="destroy(cat)" class="p-2 rounded-lg transition-colors" title="Remover" style="color: #ba1a1a;"
                        @mouseenter="$event.currentTarget.style.backgroundColor='#ffdad6'"
                        @mouseleave="$event.currentTarget.style.backgroundColor='transparent'">
                        <span class="material-symbols-outlined">delete</span>
                    </button>
                </div>
            </div>
        </div>
    </AdminLayout>

    <Teleport to="body">
        <Transition enter-active-class="transition duration-200 ease-out" enter-from-class="opacity-0" enter-to-class="opacity-100"
            leave-active-class="transition duration-150 ease-in" leave-from-class="opacity-100" leave-to-class="opacity-0">
            <div v-if="showModal" class="fixed inset-0 z-50 flex items-center justify-center p-4"
                style="background-color: rgba(0,0,0,0.45);" @click.self="closeModal">
                <Transition enter-active-class="transition duration-200 ease-out" enter-from-class="opacity-0 scale-95"
                    enter-to-class="opacity-100 scale-100" leave-active-class="transition duration-150 ease-in"
                    leave-from-class="opacity-100 scale-100" leave-to-class="opacity-0 scale-95">
                    <div v-if="showModal" class="w-full max-w-lg rounded-2xl shadow-2xl overflow-hidden" style="background-color: #f6f3ee;">

                        <!-- Header -->
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
                                <span class="material-symbols-outlined">close</span>
                            </button>
                        </div>

                        <!-- Body -->
                        <form @submit.prevent="submit" class="px-6 py-5 space-y-4">

                            <!-- Nomes -->
                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <label class="block text-xs font-bold uppercase tracking-widest mb-1.5" style="color: #77574d;">Nome PT *</label>
                                    <input v-model="form.name_pt" type="text" required maxlength="150"
                                        placeholder="Ex.: Jardinagem"
                                        class="w-full px-4 py-3 rounded-lg outline-none text-sm"
                                        style="background-color: #ffffff; border: 1.5px solid #e8e4de;"
                                        :style="form.errors.name_pt ? 'border-color:#ba1a1a;' : ''" />
                                    <p v-if="form.errors.name_pt" class="text-xs mt-1" style="color:#ba1a1a;">{{ form.errors.name_pt }}</p>
                                </div>
                                <div>
                                    <label class="block text-xs font-bold uppercase tracking-widest mb-1.5" style="color: #77574d;">Nome EN</label>
                                    <input v-model="form.name_en" type="text" maxlength="150"
                                        placeholder="Ex.: Landscaping"
                                        class="w-full px-4 py-3 rounded-lg outline-none text-sm"
                                        style="background-color: #ffffff; border: 1.5px solid #e8e4de;" />
                                </div>
                            </div>

                            <!-- Descrições -->
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
                                    <span v-if="form.processing" class="material-symbols-outlined text-base animate-spin">progress_activity</span>
                                    {{ editingItem ? 'Salvar alterações' : 'Criar categoria' }}
                                </button>
                            </div>
                        </form>
                    </div>
                </Transition>
            </div>
        </Transition>
    </Teleport>
</template>
