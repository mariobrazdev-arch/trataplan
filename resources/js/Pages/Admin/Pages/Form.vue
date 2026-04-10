<script setup>
import AdminLayout from '@/Components/Layout/AdminLayout.vue';
import { useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

defineOptions({ layout: null });

const props = defineProps({ page: Object });

const activeTab = ref('pt');

const form = useForm({
    title_pt: props.page?.title_pt || '',
    title_en: props.page?.title_en || '',
    content_pt: props.page?.content_pt || '',
    content_en: props.page?.content_en || '',
    meta_title_pt: props.page?.meta_title_pt || '',
    meta_title_en: props.page?.meta_title_en || '',
    meta_description_pt: props.page?.meta_description_pt || '',
    meta_description_en: props.page?.meta_description_en || '',
    is_published: props.page?.is_published ?? true,
});

function submit() {
    form.transform(data => ({ ...data, _method: 'PATCH' }))
        .post(`/admin/paginas/${props.page.id}`);
}
</script>

<template>
    <AdminLayout>
        <template #title>Editar: {{ page?.title_pt }}</template>

        <form @submit.prevent="submit" class="max-w-4xl space-y-6">
            <div class="flex gap-2 p-1 rounded-xl w-fit" style="background-color: #f0ede8;">
                <button type="button" v-for="tab in ['pt', 'en', 'seo']" :key="tab" @click="activeTab = tab"
                    class="px-5 py-2 rounded-lg text-sm font-bold transition-all"
                    :style="activeTab === tab ? 'background-color: #012d1d; color: white;' : 'color: #717973;'">
                    {{ tab === 'pt' ? 'Português' : tab === 'en' ? 'English' : 'SEO' }}
                </button>
            </div>

            <div v-show="activeTab === 'pt'" class="rounded-xl p-6 space-y-5" style="background-color: #ffffff;">
                <div>
                    <label class="block text-xs font-bold uppercase tracking-widest mb-2" style="color: #77574d;">Título (PT)</label>
                    <input v-model="form.title_pt" type="text" required class="w-full px-4 py-3 rounded-lg outline-none"
                        style="background-color: #f0ede8; border: none;" />
                </div>
                <div>
                    <label class="block text-xs font-bold uppercase tracking-widest mb-2" style="color: #77574d;">Conteúdo (PT)</label>
                    <textarea v-model="form.content_pt" rows="20" class="w-full px-4 py-3 rounded-lg outline-none resize-y"
                        style="background-color: #f0ede8; border: none; font-family: Newsreader, serif;"></textarea>
                    <p class="text-xs mt-1" style="color: #717973;">Aceita HTML</p>
                </div>
            </div>

            <div v-show="activeTab === 'en'" class="rounded-xl p-6 space-y-5" style="background-color: #ffffff;">
                <div>
                    <label class="block text-xs font-bold uppercase tracking-widest mb-2" style="color: #77574d;">Title (EN)</label>
                    <input v-model="form.title_en" type="text" class="w-full px-4 py-3 rounded-lg outline-none"
                        style="background-color: #f0ede8; border: none;" />
                </div>
                <div>
                    <label class="block text-xs font-bold uppercase tracking-widest mb-2" style="color: #77574d;">Content (EN)</label>
                    <textarea v-model="form.content_en" rows="20" class="w-full px-4 py-3 rounded-lg outline-none resize-y"
                        style="background-color: #f0ede8; border: none; font-family: Newsreader, serif;"></textarea>
                </div>
            </div>

            <div v-show="activeTab === 'seo'" class="rounded-xl p-6 space-y-5" style="background-color: #ffffff;">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-widest mb-2" style="color: #77574d;">Meta Título PT (60)</label>
                        <input v-model="form.meta_title_pt" maxlength="60" type="text" class="w-full px-4 py-3 rounded-lg outline-none"
                            style="background-color: #f0ede8; border: none;" />
                        <p class="text-xs mt-1" style="color: #717973;">{{ form.meta_title_pt?.length || 0 }}/60</p>
                    </div>
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-widest mb-2" style="color: #77574d;">Meta Title EN (60)</label>
                        <input v-model="form.meta_title_en" maxlength="60" type="text" class="w-full px-4 py-3 rounded-lg outline-none"
                            style="background-color: #f0ede8; border: none;" />
                        <p class="text-xs mt-1" style="color: #717973;">{{ form.meta_title_en?.length || 0 }}/60</p>
                    </div>
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-widest mb-2" style="color: #77574d;">Meta Descrição PT (160)</label>
                        <textarea v-model="form.meta_description_pt" maxlength="160" rows="3" class="w-full px-4 py-3 rounded-lg outline-none resize-none"
                            style="background-color: #f0ede8; border: none;"></textarea>
                        <p class="text-xs mt-1" style="color: #717973;">{{ form.meta_description_pt?.length || 0 }}/160</p>
                    </div>
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-widest mb-2" style="color: #77574d;">Meta Description EN (160)</label>
                        <textarea v-model="form.meta_description_en" maxlength="160" rows="3" class="w-full px-4 py-3 rounded-lg outline-none resize-none"
                            style="background-color: #f0ede8; border: none;"></textarea>
                        <p class="text-xs mt-1" style="color: #717973;">{{ form.meta_description_en?.length || 0 }}/160</p>
                    </div>
                </div>
            </div>

            <div class="rounded-xl p-5 flex items-center gap-3" style="background-color: #ffffff;">
                <label class="flex items-center gap-3 cursor-pointer">
                    <div class="relative">
                        <input type="checkbox" v-model="form.is_published" class="sr-only" />
                        <div class="w-10 h-5 rounded-full transition-colors" :style="form.is_published ? 'background-color: #012d1d;' : 'background-color: #c1c8c2;'"></div>
                        <div class="absolute top-0.5 left-0.5 w-4 h-4 bg-white rounded-full transition-transform" :style="form.is_published ? 'transform: translateX(20px);' : ''"></div>
                    </div>
                    <span class="text-sm font-bold" style="color: #1c1c19;">{{ form.is_published ? 'Página publicada' : 'Página oculta' }}</span>
                </label>
            </div>

            <div class="flex items-center gap-4">
                <button type="submit" :disabled="form.processing"
                    class="px-8 py-3 rounded-xl font-bold text-white hover:opacity-90 disabled:opacity-60"
                    style="background-color: #012d1d;">
                    Salvar Alterações
                </button>
                <a href="/admin/paginas" class="text-sm hover:underline" style="color: #717973;">Cancelar</a>
            </div>
        </form>
    </AdminLayout>
</template>
