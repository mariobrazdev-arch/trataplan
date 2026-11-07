<script setup>
import AdminLayout from '@/Components/Layout/AdminLayout.vue';
import { useForm } from '@inertiajs/vue3';
import { ref, computed } from 'vue';

defineOptions({ layout: null });

const props = defineProps({ partner: Object });
const isEditing = computed(() => !!props.partner);
const previewLogo = ref(props.partner?.logo_url || null);

const form = useForm({
    name: props.partner?.name || '',
    website: props.partner?.website || '',
    description_pt: props.partner?.description_pt || '',
    order: props.partner?.order ?? 0,
    is_active: props.partner?.is_active ?? true,
    logo: null,
});

function handleLogo(e) {
    const file = e.target.files[0];
    if (!file) return;
    form.logo = file;
    previewLogo.value = URL.createObjectURL(file);
}

function submit() {
    if (isEditing.value) {
        form.transform(d => ({ ...d, _method: 'PATCH' })).post(`/admin/parceiros/${props.partner.id}`);
    } else {
        form.post('/admin/parceiros');
    }
}
</script>

<template>
    <AdminLayout>
        <template #title>{{ isEditing ? 'Editar Parceiro' : 'Novo Parceiro' }}</template>

        <form @submit.prevent="submit" class="max-w-2xl space-y-6">
            <div class="rounded-xl p-6 space-y-5" style="background-color: #ffffff;">
                <div>
                    <label class="block text-xs font-bold uppercase tracking-widest mb-2" style="color: #77574d;">Nome *</label>
                    <input v-model="form.name" required type="text" class="w-full px-4 py-3 rounded-lg outline-none"
                        style="background-color: #f0ede8; border: none;" />
                </div>
                <div>
                    <label class="block text-xs font-bold uppercase tracking-widest mb-2" style="color: #77574d;">Website</label>
                    <input v-model="form.website" type="url" class="w-full px-4 py-3 rounded-lg outline-none"
                        style="background-color: #f0ede8; border: none;" placeholder="https://" />
                </div>
                <div>
                    <label class="block text-xs font-bold uppercase tracking-widest mb-2" style="color: #77574d;">Descrição</label>
                    <textarea v-model="form.description_pt" rows="3" class="w-full px-4 py-3 rounded-lg outline-none resize-none"
                        style="background-color: #f0ede8; border: none; font-family: Newsreader, serif;"></textarea>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-widest mb-2" style="color: #77574d;">Ordem</label>
                        <input v-model="form.order" type="number" min="0" class="w-full px-4 py-3 rounded-lg outline-none"
                            style="background-color: #f0ede8; border: none;" />
                    </div>
                    <div class="flex items-end pb-3">
                        <label class="flex items-center gap-3 cursor-pointer">
                            <div class="relative">
                                <input type="checkbox" v-model="form.is_active" class="sr-only" />
                                <div class="w-10 h-5 rounded-full transition-colors" :style="form.is_active ? 'background-color: #012d1d;' : 'background-color: #c1c8c2;'"></div>
                                <div class="absolute top-0.5 left-0.5 w-4 h-4 bg-white rounded-full transition-transform" :style="form.is_active ? 'transform: translateX(20px);' : ''"></div>
                            </div>
                            <span class="text-sm font-bold" style="color: #1c1c19;">{{ form.is_active ? 'Ativo' : 'Inativo' }}</span>
                        </label>
                    </div>
                </div>

                <!-- Logo upload -->
                <div>
                    <label class="block text-xs font-bold uppercase tracking-widest mb-2" style="color: #77574d;">Logo</label>
                    <div class="flex items-center gap-4">
                        <div v-if="previewLogo" class="w-20 h-20 rounded-xl overflow-hidden flex items-center justify-center" style="background-color: #f0ede8;">
                            <img :src="previewLogo" class="max-w-full max-h-full object-contain" />
                        </div>
                        <label class="cursor-pointer flex items-center gap-2 px-4 py-2.5 rounded-lg text-sm font-bold"
                            style="background-color: #f0ede8; color: #012d1d;">
                            <span class="material-symbols-outlined text-base">upload</span>
                            {{ previewLogo ? 'Trocar logo' : 'Enviar logo' }}
                            <input type="file" accept="image/*" class="hidden" @change="handleLogo" />
                        </label>
                    </div>
                </div>
            </div>

            <div class="flex items-center gap-4">
                <button type="submit" :disabled="form.processing"
                    class="px-8 py-3 rounded-xl font-bold text-white hover:opacity-90 disabled:opacity-60"
                    style="background-color: #012d1d;">
                    {{ isEditing ? 'Salvar' : 'Criar Parceiro' }}
                </button>
                <a href="/admin/parceiros" class="text-sm hover:underline" style="color: #717973;">Cancelar</a>
            </div>
        </form>
    </AdminLayout>
</template>
