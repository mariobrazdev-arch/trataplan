<script setup>
import AdminLayout from '@/Components/Layout/AdminLayout.vue';
import { Link, router } from '@inertiajs/vue3';
import { ref, computed } from 'vue';

defineOptions({ layout: null });
const props = defineProps({ messages: Object });
const filter = ref('todas');

const filteredMessages = computed(() => {
    if (!props.messages?.data) return [];
    if (filter.value === 'nao_lidas') return props.messages.data.filter(m => !m.is_read);
    if (filter.value === 'lidas') return props.messages.data.filter(m => m.is_read);
    return props.messages.data;
});

function destroy(id) {
    if (confirm('Excluir esta mensagem?')) {
        router.delete(`/admin/mensagens/${id}`);
    }
}

function initials(name) {
    return name?.split(' ').map(w => w[0]).slice(0, 2).join('').toUpperCase() || '?';
}
</script>

<template>
    <AdminLayout>
        <template #title>Mensagens</template>
        <template #subtitle>Solicitações recebidas pelo formulário de contato.</template>

        <!-- Filter tabs -->
        <div class="flex items-center justify-between mb-8">
            <h3 class="text-2xl font-headline font-bold text-primary">Caixa de Entrada</h3>
            <div class="flex p-1 rounded-lg" style="background-color: #ebe8e3;">
                <button v-for="tab in [{key:'todas',label:'Todas'},{key:'nao_lidas',label:'Não Lidas'},{key:'lidas',label:'Lidas'}]" :key="tab.key"
                    @click="filter = tab.key"
                    class="px-4 py-2 rounded-md font-label text-[10px] uppercase font-bold tracking-widest transition-all"
                    :style="filter === tab.key
                        ? 'background-color: #ffffff; color: #012d1d; box-shadow: 0 1px 3px rgba(0,0,0,.08);'
                        : 'color: #717973;'">
                    {{ tab.label }}
                </button>
            </div>
        </div>

        <!-- Empty -->
        <div v-if="!filteredMessages.length" class="py-20 text-center rounded-xl" style="background-color: #f6f3ee;">
            <span class="material-symbols-outlined text-5xl mb-3 block" style="color: #c1c8c2;">mail</span>
            <p class="font-body italic" style="color: #717973;">Nenhuma mensagem encontrada.</p>
        </div>

        <!-- Cards -->
        <div v-else class="space-y-6">
            <div v-for="msg in filteredMessages" :key="msg.id"
                class="group p-6 rounded-xl flex items-center gap-6 transition-all hover:translate-x-2"
                :style="!msg.is_read ? 'background-color: #fff;' : 'background-color: #fcf9f4;'">

                <!-- Avatar -->
                <div class="w-16 h-16 flex-shrink-0 rounded-lg flex items-center justify-center font-headline font-black text-xl"
                    :style="!msg.is_read ? 'background-color: #fed3c7; color: #77574d;' : 'background-color: #ebe8e3; color: #717973;'">
                    {{ initials(msg.name) }}
                </div>

                <!-- Info -->
                <div class="flex-grow min-w-0">
                    <div class="flex items-center gap-3 mb-1">
                        <span class="px-2 py-0.5 rounded font-label text-[9px] uppercase font-black tracking-widest"
                            :style="!msg.is_read
                                ? 'background-color: #fed3c7; color: #77574d;'
                                : 'background-color: #ebe8e3; color: #414844;'">
                            {{ msg.is_read ? 'Lida' : 'Não lida' }}
                        </span>
                        <span class="text-xs italic font-body" style="color: #717973;">{{ msg.created_at }}</span>
                    </div>
                    <h4 class="text-xl font-headline font-extrabold text-primary">{{ msg.name }}</h4>
                    <p class="text-sm font-body italic mt-0.5" style="color: #6a5e33;">{{ msg.email }}</p>
                    <p class="text-sm font-body line-clamp-1 mt-2 italic" style="color: #414844;">{{ msg.subject || '(sem assunto)' }}</p>
                </div>

                <!-- Actions (on hover) -->
                <div class="flex flex-col gap-2 opacity-0 group-hover:opacity-100 transition-opacity flex-shrink-0">
                    <Link :href="`/admin/mensagens/${msg.id}`"
                        class="p-2 rounded-lg transition-colors" title="Ver mensagem"
                        style="color: #012d1d;"
                        @mouseenter="$event.currentTarget.style.backgroundColor='#c1ecd4'"
                        @mouseleave="$event.currentTarget.style.backgroundColor='transparent'">
                        <span class="material-symbols-outlined">open_in_new</span>
                    </Link>
                    <button @click="destroy(msg.id)"
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
        <div v-if="messages?.links?.length > 3" class="mt-12 flex justify-center">
            <div class="flex gap-2 flex-wrap justify-center">
                <Link v-for="link in messages.links" :key="link.label"
                    :href="link.url || '#'"
                    v-html="link.label"
                    class="px-4 py-2 rounded-md font-label text-[10px] uppercase font-bold tracking-[0.15em] transition-all"
                    :class="link.url ? '' : 'opacity-30 pointer-events-none'"
                    :style="link.active ? 'background-color: #012d1d; color: white;' : 'background-color: #ebe8e3; color: #717973;'"
                />
            </div>
        </div>
    </AdminLayout>
</template>
