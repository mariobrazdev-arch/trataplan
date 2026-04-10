<script setup>
import AdminLayout from '@/Components/Layout/AdminLayout.vue';
import { Link, router } from '@inertiajs/vue3';

defineOptions({ layout: null });

const props = defineProps({ message: Object });

function destroy() {
    if (confirm('Excluir esta mensagem?')) {
        router.delete(`/admin/mensagens/${props.message.id}`, {
            onSuccess: () => router.visit('/admin/mensagens'),
        });
    }
}
</script>

<template>
    <AdminLayout>
        <template #title>Mensagem #{{ message.id }}</template>

        <div class="max-w-2xl space-y-6">
            <div class="rounded-xl p-6 space-y-5" style="background-color: #ffffff;">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <p class="text-xs font-bold uppercase tracking-widest mb-1" style="color: #77574d;">Nome</p>
                        <p style="color: #1c1c19;">{{ message.name }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-bold uppercase tracking-widest mb-1" style="color: #77574d;">Email</p>
                        <a :href="`mailto:${message.email}`" class="hover:underline" style="color: #012d1d;">{{ message.email }}</a>
                    </div>
                    <div v-if="message.phone">
                        <p class="text-xs font-bold uppercase tracking-widest mb-1" style="color: #77574d;">Telefone</p>
                        <a :href="`tel:${message.phone}`" style="color: #012d1d;">{{ message.phone }}</a>
                    </div>
                    <div>
                        <p class="text-xs font-bold uppercase tracking-widest mb-1" style="color: #77574d;">Idioma</p>
                        <span class="px-2 py-0.5 rounded text-xs font-bold" style="background-color: #f0ede8; color: #012d1d;">{{ message.locale.toUpperCase() }}</span>
                    </div>
                    <div v-if="message.subject" class="col-span-2">
                        <p class="text-xs font-bold uppercase tracking-widest mb-1" style="color: #77574d;">Assunto</p>
                        <p style="color: #1c1c19;">{{ message.subject }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-bold uppercase tracking-widest mb-1" style="color: #77574d;">Recebida em</p>
                        <p style="color: #717973;">{{ message.created_at }}</p>
                    </div>
                </div>

                <div>
                    <p class="text-xs font-bold uppercase tracking-widest mb-3" style="color: #77574d;">Mensagem</p>
                    <div class="p-5 rounded-lg whitespace-pre-wrap leading-relaxed" style="background-color: #f6f3ee; color: #1c1c19; font-family: Newsreader, serif;">{{ message.message }}</div>
                </div>
            </div>

            <div class="flex items-center gap-4">
                <a :href="`mailto:${message.email}?subject=Re: ${message.subject || 'Contato Trataplan'}`"
                    class="flex items-center gap-2 px-6 py-3 rounded-xl font-bold text-white"
                    style="background-color: #012d1d;">
                    <span class="material-symbols-outlined text-base">reply</span>
                    Responder por Email
                </a>
                <Link href="/admin/mensagens" class="text-sm hover:underline" style="color: #717973;">Voltar</Link>
                <button @click="destroy" class="text-sm hover:underline ml-auto" style="color: #ba1a1a;">Excluir</button>
            </div>
        </div>
    </AdminLayout>
</template>
