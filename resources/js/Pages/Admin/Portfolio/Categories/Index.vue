<script setup>
import AdminLayout from '@/Components/Layout/AdminLayout.vue';
import { Link, router } from '@inertiajs/vue3';
import { useConfirm } from '@/composables/useConfirm';

defineOptions({ layout: null });
defineProps({ categories: Array });

const { confirm } = useConfirm();

async function destroy(cat) {
    const ok = await confirm({ title: 'Remover categoria', message: `Remover "${cat.name_pt}"? Esta ação não pode ser desfeita.` });
    if (!ok) return;
    router.delete(`/admin/portfolio/categorias/${cat.id}`);
}
</script>

<template>
    <AdminLayout>
        <template #title>Produtos</template>
        <template #subtitle>Catálogo de produtos e segmentos de atuação.</template>
        <template #actions>
            <Link href="/admin/portfolio/categorias/criar"
                class="flex items-center gap-2 px-6 py-2.5 rounded-lg font-headline font-bold text-sm text-white shadow-lg transition-all active:scale-95"
                style="background-color: #012d1d;">
                <span class="material-symbols-outlined text-base">add</span>
                Novo Produto
            </Link>
        </template>

        <!-- Section header -->
        <div class="flex items-center justify-between mb-8">
            <h3 class="text-2xl font-headline font-bold text-primary">Catálogo de Produtos</h3>
            <span class="font-label text-[10px] uppercase tracking-[0.2em] text-outline">{{ categories?.length || 0 }} registros</span>
        </div>

        <!-- Empty -->
        <div v-if="!categories?.length" class="py-20 text-center rounded-xl" style="background-color: #f6f3ee;">
            <span class="material-symbols-outlined text-5xl mb-3 block" style="color: #c1c8c2;">inventory_2</span>
            <p class="font-body italic" style="color: #717973;">Nenhum produto cadastrado ainda.</p>
            <Link href="/admin/portfolio/categorias/criar" class="mt-4 inline-block font-label text-sm font-bold uppercase tracking-widest hover:underline" style="color: #012d1d;">Criar primeiro produto</Link>
        </div>

        <!-- Cards -->
        <div v-else class="space-y-6">
            <div v-for="cat in categories" :key="cat.id"
                class="group p-6 rounded-xl flex items-center gap-6 transition-all hover:translate-x-2"
                style="background-color: #ffffff;">

                <!-- Thumbnail -->
                <div class="w-32 h-32 flex-shrink-0 rounded-lg overflow-hidden" style="background-color: #f0ede8;">
                    <img v-if="cat.cover_url"
                        :src="cat.cover_url"
                        class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500" />
                    <div v-else class="w-full h-full flex items-center justify-center">
                        <span class="material-symbols-outlined text-4xl" style="color: #c1c8c2;">inventory_2</span>
                    </div>
                </div>

                <!-- Info -->
                <div class="flex-grow min-w-0">
                    <div class="flex items-center gap-3 mb-1">
                        <span class="px-2 py-0.5 rounded font-label text-[9px] uppercase font-black tracking-widest"
                            :style="cat.is_active
                                ? 'background-color: #baaa79; color: #493f17;'
                                : 'background-color: #ebe8e3; color: #414844;'">
                            {{ cat.is_active ? 'Ativo' : 'Inativo' }}
                        </span>
                        <span v-if="cat.product_category_name"
                            class="px-2 py-0.5 rounded font-label text-[9px] uppercase font-black tracking-widest"
                            style="background-color: #e0f2e9; color: #012d1d;">
                            {{ cat.product_category_name }}
                        </span>
                    </div>
                    <h4 class="text-xl font-headline font-extrabold text-primary">{{ cat.name_pt }}</h4>
                    
                    <p v-if="cat.description_pt" class="text-sm font-body line-clamp-2 mt-1 italic" style="color: #717973;">{{ cat.description_pt }}</p>
                    <div class="flex items-center gap-6 mt-4">
                        <div class="flex items-center gap-1" style="color: #717973;">
                            <span class="material-symbols-outlined text-sm">photo_library</span>
                            <span class="text-[10px] font-label font-bold uppercase tracking-tighter">{{ cat.photos_count }} Fotos</span>
                        </div>
                    </div>
                </div>

                <!-- Actions (on hover) -->
                <div class="flex flex-col gap-2 opacity-0 group-hover:opacity-100 transition-opacity flex-shrink-0">
                    <Link :href="`/admin/portfolio/categorias/${cat.id}/editar`"
                        class="p-2 rounded-lg transition-colors" title="Editar"
                        style="color: #012d1d;"
                        @mouseenter="$event.currentTarget.style.backgroundColor='#c1ecd4'"
                        @mouseleave="$event.currentTarget.style.backgroundColor='transparent'">
                        <span class="material-symbols-outlined">edit_square</span>
                    </Link>
                    <button @click="destroy(cat)"
                        class="p-2 rounded-lg transition-colors" title="Remover"
                        style="color: #ba1a1a;"
                        @mouseenter="$event.currentTarget.style.backgroundColor='#ffdad6'"
                        @mouseleave="$event.currentTarget.style.backgroundColor='transparent'">
                        <span class="material-symbols-outlined">delete</span>
                    </button>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
