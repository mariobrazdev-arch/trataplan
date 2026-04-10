<script setup>
import AdminLayout from '@/Components/Layout/AdminLayout.vue';
import { Link, router } from '@inertiajs/vue3';
import { ref, computed } from 'vue';

defineOptions({ layout: null });

const props = defineProps({ posts: Object });
const filter = ref('todos');

const filteredPosts = computed(() => {
    if (!props.posts?.data) return [];
    if (filter.value === 'publicados') return props.posts.data.filter(p => p.is_published);
    if (filter.value === 'rascunhos') return props.posts.data.filter(p => !p.is_published);
    return props.posts.data;
});

function destroy(post) {
    if (confirm(`Excluir "${post.title_pt}"?`)) {
        router.delete(`/admin/posts/${post.id}`);
    }
}
</script>

<template>
    <AdminLayout>
        <template #title>Últimos Trabalhos</template>
        <template #subtitle>Gerencie as execuções e publicações do portfólio.</template>
        <template #actions>
            <Link href="/admin/posts/criar"
                class="flex items-center gap-2 px-6 py-2.5 rounded-lg font-headline font-bold text-sm text-white shadow-lg transition-all active:scale-95"
                style="background-color: #012d1d;">
                <span class="material-symbols-outlined text-base">add</span>
                Nova Postagem
            </Link>
        </template>

        <!-- Filter tabs -->
        <div class="flex items-center justify-between mb-8">
            <h3 class="text-2xl font-headline font-bold text-primary">Portfólio de Trabalhos</h3>
            <div class="flex p-1 rounded-lg" style="background-color: #ebe8e3;">
                <button v-for="tab in [{key:'todos',label:'Todos'},{key:'publicados',label:'Publicados'},{key:'rascunhos',label:'Rascunhos'}]" :key="tab.key"
                    @click="filter = tab.key"
                    class="px-4 py-2 rounded-md font-label text-[10px] uppercase font-bold tracking-widest transition-all"
                    :style="filter === tab.key
                        ? 'background-color: #ffffff; color: #012d1d; box-shadow: 0 1px 3px rgba(0,0,0,.08);'
                        : 'color: #717973;'">
                    {{ tab.label }}
                </button>
            </div>
        </div>

        <!-- Empty state -->
        <div v-if="!filteredPosts.length" class="py-20 text-center rounded-xl" style="background-color: #f6f3ee;">
            <span class="material-symbols-outlined text-5xl mb-3 block" style="color: #c1c8c2;">article</span>
            <p class="font-body italic" style="color: #717973;">Nenhuma postagem encontrada.</p>
            <Link href="/admin/posts/criar" class="mt-4 inline-block font-label text-sm font-bold uppercase tracking-widest hover:underline" style="color: #012d1d;">Criar a primeira</Link>
        </div>

        <!-- Cards -->
        <div v-else class="space-y-6">
            <div v-for="post in filteredPosts" :key="post.id"
                class="group p-6 rounded-xl flex items-center gap-6 transition-all hover:translate-x-2"
                style="background-color: #ffffff;">

                <!-- Thumbnail -->
                <div class="w-32 h-32 flex-shrink-0 rounded-lg overflow-hidden" style="background-color: #f0ede8;">
                    <img v-if="post.featured_image_url"
                        :src="post.featured_image_url"
                        class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500" />
                    <div v-else class="w-full h-full flex items-center justify-center">
                        <span class="material-symbols-outlined text-4xl" style="color: #c1c8c2;">article</span>
                    </div>
                </div>

                <!-- Info -->
                <div class="flex-grow min-w-0">
                    <div class="flex items-center gap-3 mb-1">
                        <span class="px-2 py-0.5 rounded font-label text-[9px] uppercase font-black tracking-widest"
                            :style="post.is_published
                                ? 'background-color: #baaa79; color: #493f17;'
                                : 'background-color: #ebe8e3; color: #414844;'">
                            {{ post.is_published ? 'Publicado' : 'Rascunho' }}
                        </span>
                        <span class="text-xs italic font-body" style="color: #717973;">
                            {{ post.published_at || `Criado em ${post.created_at}` }}
                        </span>
                    </div>
                    <h4 class="text-xl font-headline font-extrabold text-primary truncate">{{ post.title_pt }}</h4>
                    <p class="text-sm font-body line-clamp-2 mt-1 italic" style="color: #414844;">{{ post.title_en }}</p>
                    <div class="flex items-center gap-6 mt-4">
                        <div class="flex items-center gap-1" style="color: #717973;">
                            <span class="material-symbols-outlined text-sm">photo_library</span>
                            <span class="text-[10px] font-label font-bold uppercase tracking-tighter">{{ post.photos_count || 0 }} Fotos</span>
                        </div>
                        <div class="flex items-center gap-1" style="color: #717973;">
                            <span class="material-symbols-outlined text-sm">person</span>
                            <span class="text-[10px] font-label font-bold uppercase tracking-tighter">{{ post.author }}</span>
                        </div>
                    </div>
                </div>

                <!-- Actions (on hover) -->
                <div class="flex flex-col gap-2 opacity-0 group-hover:opacity-100 transition-opacity flex-shrink-0">
                    <Link :href="`/admin/posts/${post.id}/editar`"
                        class="p-2 rounded-lg transition-colors" title="Editar"
                        style="color: #012d1d;"
                        @mouseenter="$event.currentTarget.style.backgroundColor='#c1ecd4'"
                        @mouseleave="$event.currentTarget.style.backgroundColor='transparent'">
                        <span class="material-symbols-outlined">edit_square</span>
                    </Link>
                    <button @click="destroy(post)"
                        class="p-2 rounded-lg transition-colors" title="Excluir"
                        style="color: #ba1a1a;"
                        @mouseenter="$event.currentTarget.style.backgroundColor='#ffdad6'"
                        @mouseleave="$event.currentTarget.style.backgroundColor='transparent'">
                        <span class="material-symbols-outlined">delete</span>
                    </button>
                </div>
            </div>
        </div>

        <!-- Pagination -->
        <div v-if="posts?.links?.length > 3" class="mt-12 flex justify-center">
            <div class="flex gap-2 flex-wrap justify-center">
                <Link v-for="link in posts.links" :key="link.label"
                    :href="link.url || '#'"
                    v-html="link.label"
                    class="group px-4 py-2 rounded-md font-label text-[10px] uppercase font-bold tracking-[0.15em] transition-all"
                    :class="link.url ? '' : 'opacity-30 pointer-events-none'"
                    :style="link.active ? 'background-color: #012d1d; color: white;' : 'background-color: #ebe8e3; color: #717973;'"
                />
            </div>
        </div>
    </AdminLayout>
</template>
