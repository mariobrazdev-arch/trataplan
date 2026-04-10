<script setup>
import AdminLayout from '@/Components/Layout/AdminLayout.vue';
import { Link } from '@inertiajs/vue3';

defineOptions({ layout: null });

const props = defineProps({
    stats: Object,
    recent_messages: Array,
});
</script>

<template>
    <AdminLayout>
        <template #title>Dashboard</template>
        <template #subtitle>Visão geral do sistema e atividades recentes.</template>

        <!-- Stats cards -->
        <div class="grid grid-cols-2 lg:grid-cols-5 gap-4 mb-8">
            <div v-for="stat in [
                { label: 'Postagens', value: stats.posts_count, icon: 'article', sub: `${stats.published_posts} publicadas` },
                { label: 'Páginas', value: stats.pages_count, icon: 'web', sub: '' },
                { label: 'Parceiros', value: stats.partners_count, icon: 'handshake', sub: 'ativos' },
                { label: 'Mensagens', value: stats.unread_messages, icon: 'mail', sub: 'não lidas', alert: stats.unread_messages > 0 },
            ]" :key="stat.label"
                class="rounded-xl p-5 shadow-sm"
                :style="stat.alert && stat.value > 0 ? 'background-color: #c1ecd4;' : 'background-color: #ffffff;'"
            >
                <div class="flex items-center justify-between mb-2">
                    <span class="text-xs font-bold uppercase tracking-widest" style="color: #717973;">{{ stat.label }}</span>
                    <span class="material-symbols-outlined text-xl" style="color: #6a5e33;">{{ stat.icon }}</span>
                </div>
                <div class="text-3xl font-black" style="color: #012d1d;">{{ stat.value }}</div>
                <p v-if="stat.sub" class="text-xs mt-1" style="color: #717973;">{{ stat.sub }}</p>
            </div>

            <!-- Quick links card -->
            <div class="rounded-xl p-5 shadow-sm" style="background-color: #012d1d;">
                <p class="text-xs font-bold uppercase tracking-widest mb-3 text-emerald-300">Ações Rápidas</p>
                <div class="space-y-2">
                    <Link href="/admin/posts/criar" class="flex items-center gap-2 text-sm text-white/80 hover:text-white transition-colors">
                        <span class="material-symbols-outlined text-base">add</span>
                        Nova postagem
                    </Link>
                    <Link href="/admin/parceiros/criar" class="flex items-center gap-2 text-sm text-white/80 hover:text-white transition-colors">
                        <span class="material-symbols-outlined text-base">add</span>
                        Novo parceiro
                    </Link>
                    <Link href="/admin/mensagens" class="flex items-center gap-2 text-sm text-white/80 hover:text-white transition-colors">
                        <span class="material-symbols-outlined text-base">mail</span>
                        Ver mensagens
                    </Link>
                </div>
            </div>
        </div>

        <!-- Recent messages -->
        <div class="rounded-xl shadow-sm overflow-hidden" style="background-color: #ffffff;">
            <div class="flex items-center justify-between px-6 py-4 border-b" style="border-color: #f0ede8;">
                <h2 class="font-bold" style="color: #012d1d;">Mensagens Recentes</h2>
                <Link href="/admin/mensagens" class="text-sm hover:underline" style="color: #6a5e33;">Ver todas</Link>
            </div>
            <div v-if="!recent_messages?.length" class="px-6 py-10 text-center" style="color: #717973;">
                Nenhuma mensagem ainda.
            </div>
            <table v-else class="w-full">
                <tbody>
                    <tr v-for="msg in recent_messages" :key="msg.id"
                        class="border-b hover:bg-stone-50 transition-colors"
                        style="border-color: #f6f3ee;">
                        <td class="px-6 py-3">
                            <div class="flex items-center gap-2">
                                <span v-if="!msg.is_read" class="w-2 h-2 rounded-full" style="background-color: #012d1d;"></span>
                                <span v-else class="w-2 h-2 rounded-full" style="background-color: #c1c8c2;"></span>
                                <span class="font-semibold text-sm" style="color: #1c1c19;">{{ msg.name }}</span>
                            </div>
                            <p class="text-xs ml-4" style="color: #717973;">{{ msg.email }}</p>
                        </td>
                        <td class="px-4 py-3 hidden md:table-cell text-sm" style="color: #414844;">{{ msg.subject || '–' }}</td>
                        <td class="px-4 py-3 text-xs hidden sm:table-cell" style="color: #717973;">{{ msg.created_at }}</td>
                        <td class="px-6 py-3 text-right">
                            <Link :href="`/admin/mensagens/${msg.id}`" class="text-xs font-bold hover:underline" style="color: #012d1d;">Ver</Link>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </AdminLayout>
</template>
