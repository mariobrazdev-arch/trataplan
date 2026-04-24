<script setup>
import { ref } from 'vue';
import AdminLayout from '@/Components/Layout/AdminLayout.vue';
import { Link, router } from '@inertiajs/vue3';
import { useConfirm } from '@/composables/useConfirm';

defineOptions({ layout: null });

const props = defineProps({
    versions: Array,
});

// ── Modal de visualização ────────────────────────────────────────────────────
const viewing   = ref(null);
const viewTab   = ref('hero');
const viewTabs  = [
    { key: 'hero',    label: 'Hero',          icon: 'image' },
    { key: 'history', label: 'Nossa História', icon: 'history_edu' },
    { key: 'quality', label: 'Qualidade',      icon: 'verified' },
    { key: 'cta',     label: 'Seção Final',    icon: 'campaign' },
];

function openView(v) {
    viewing.value = v;
    viewTab.value = 'hero';
}
function closeView() {
    viewing.value = null;
}

const { confirm } = useConfirm();

// ── Ações ────────────────────────────────────────────
async function activate(id) {
    const ok = await confirm({ title: 'Ativar versão', message: 'Ativar esta versão? A versão atual será desativada.', type: 'warning', confirmLabel: 'Ativar' });
    if (!ok) return;
    router.post(`/admin/sobre/${id}/ativar`, {}, { preserveScroll: true });
}
async function remove(id) {
    const ok = await confirm({ title: 'Excluir versão', message: 'Excluir esta versão do histórico? Esta ação não pode ser desfeita.' });
    if (!ok) return;
    router.delete(`/admin/sobre/${id}`, { preserveScroll: true });
}
</script>

<template>
    <AdminLayout>
        <template #title>Página Sobre</template>
        <template #subtitle>Gerencie as versões do conteúdo da página Sobre. Cada edição cria uma nova versão — você pode restaurar versões anteriores.</template>
        <template #actions>
            <Link href="/admin/sobre/criar"
                  class="flex items-center gap-2 px-6 py-2.5 rounded-lg font-headline font-bold text-sm text-white shadow-lg transition-all active:scale-95"
                  style="background-color: #012d1d;">
                <span class="material-symbols-outlined text-base">add</span>
                Nova Versão
            </Link>
        </template>

        <!-- Section header -->
        <div class="flex items-center justify-between mb-8">
            <h3 class="text-2xl font-headline font-bold text-primary">Histórico de Versões</h3>
            <span class="font-label text-[10px] uppercase tracking-[0.2em] text-outline">{{ versions?.length || 0 }} versões</span>
        </div>

        <!-- Empty -->
        <div v-if="!versions?.length" class="py-20 text-center rounded-xl" style="background-color: #f6f3ee;">
            <span class="material-symbols-outlined text-5xl mb-3 block" style="color: #c1c8c2;">info</span>
            <p class="font-body italic mb-4" style="color: #717973;">Nenhuma versão cadastrada ainda.</p>
            <Link href="/admin/sobre/criar"
                  class="inline-block font-label text-sm font-bold uppercase tracking-widest hover:underline"
                  style="color: #012d1d;">
                Criar primeira versão
            </Link>
        </div>

        <!-- Cards -->
        <div v-else class="space-y-4">
            <div
                v-for="v in versions"
                :key="v.id"
                class="group p-6 rounded-xl flex items-center gap-6 transition-all"
                :style="v.is_active
                    ? 'background-color: #f0fdf4; border: 1.5px solid #86efac;'
                    : 'background-color: #ffffff;'"
            >
                <!-- Ícone de status -->
                <div class="w-12 h-12 rounded-xl flex items-center justify-center flex-shrink-0"
                     :style="v.is_active ? 'background-color: #dcfce7;' : 'background-color: #f0ede8;'">
                    <span class="material-symbols-outlined text-xl"
                          :style="v.is_active ? 'color: #16a34a;' : 'color: #9ca3af;'">
                        {{ v.is_active ? 'check_circle' : 'history' }}
                    </span>
                </div>

                <!-- Informações -->
                <div class="flex-grow min-w-0">
                    <div class="flex items-center gap-3 mb-1">
                        <span
                            class="px-2 py-0.5 rounded font-label text-[9px] uppercase font-black tracking-widest"
                            :style="v.is_active
                                ? 'background-color: #bbf7d0; color: #166534;'
                                : 'background-color: #ebe8e3; color: #414844;'"
                        >
                            {{ v.is_active ? 'Ativa' : 'Inativa' }}
                        </span>
                        <span class="text-xs italic font-body" style="color: #717973;">ID #{{ v.id }}</span>
                    </div>
                    <h4 class="text-xl font-headline font-extrabold text-primary">
                        {{ v.version_label || `Versão #${v.id}` }}
                    </h4>
                    <p class="text-sm mt-0.5" style="color: #717973;">
                        Criada em {{ v.created_at }}
                    </p>
                    <p v-if="v.hero_title_pt" class="text-sm italic mt-1 truncate" style="color: #9ca3af;">
                        "{{ v.hero_title_pt }}"
                    </p>
                </div>

                <!-- Ações -->
                <div class="flex flex-col gap-2 flex-shrink-0">
                    <!-- Ver -->
                    <button
                        @click="openView(v)"
                        class="p-2 rounded-lg transition-colors"
                        title="Visualizar dados"
                        style="color: #6a5e33;"
                        @mouseenter="$event.currentTarget.style.backgroundColor='#fef3c7'"
                        @mouseleave="$event.currentTarget.style.backgroundColor='transparent'"
                    >
                        <span class="material-symbols-outlined">visibility</span>
                    </button>

                    <!-- Editar -->
                    <Link
                        :href="`/admin/sobre/${v.id}/editar`"
                        class="p-2 rounded-lg transition-colors block"
                        title="Editar esta versão"
                        style="color: #1d4ed8;"
                        @mouseenter="$event.currentTarget.style.backgroundColor='#eff6ff'"
                        @mouseleave="$event.currentTarget.style.backgroundColor='transparent'"
                    >
                        <span class="material-symbols-outlined">edit</span>
                    </Link>

                    <!-- Clonar -->
                    <Link
                        :href="`/admin/sobre/criar?from=${v.id}`"
                        class="p-2 rounded-lg transition-colors block"
                        title="Clonar esta versão"
                        style="color: #374151;"
                        @mouseenter="$event.currentTarget.style.backgroundColor='#f3f4f6'"
                        @mouseleave="$event.currentTarget.style.backgroundColor='transparent'"
                    >
                        <span class="material-symbols-outlined">content_copy</span>
                    </Link>

                    <!-- Ativar (só se inativa) -->
                    <button
                        v-if="!v.is_active"
                        @click="activate(v.id)"
                        class="p-2 rounded-lg transition-colors"
                        title="Ativar esta versão"
                        style="color: #012d1d;"
                        @mouseenter="$event.currentTarget.style.backgroundColor='#c1ecd4'"
                        @mouseleave="$event.currentTarget.style.backgroundColor='transparent'"
                    >
                        <span class="material-symbols-outlined">check_circle</span>
                    </button>

                    <!-- Excluir (só se inativa) -->
                    <button
                        v-if="!v.is_active"
                        @click="remove(v.id)"
                        class="p-2 rounded-lg transition-colors"
                        title="Excluir versão"
                        style="color: #ba1a1a;"
                        @mouseenter="$event.currentTarget.style.backgroundColor='#ffdad6'"
                        @mouseleave="$event.currentTarget.style.backgroundColor='transparent'"
                    >
                        <span class="material-symbols-outlined">delete</span>
                    </button>
                </div>
            </div>
        </div>

        <!-- Help -->
        <p class="mt-6 text-xs" style="color: #9ca3af;">
            <span class="material-symbols-outlined text-sm align-middle mr-1">info</span>
            Use <strong>Clonar</strong> para criar uma nova versão a partir de qualquer registro. Apenas a versão <strong>Ativa</strong> é exibida no site.
        </p>
    </AdminLayout>

    <!-- ═══════════════════════ MODAL DE VISUALIZAÇÃO ═══════════════════════ -->
    <teleport to="body">
        <transition name="modal">
            <div
                v-if="viewing"
                class="fixed inset-0 z-50 flex items-start justify-end"
                @click.self="closeView"
            >
                <!-- Backdrop -->
                <div class="absolute inset-0 bg-black/40 backdrop-blur-sm" @click="closeView"></div>

                <!-- Drawer -->
                <div class="relative w-full max-w-2xl h-screen overflow-y-auto shadow-2xl flex flex-col"
                     style="background-color: #fcf9f4;">

                    <!-- Drawer header -->
                    <div class="flex items-center justify-between px-6 py-5 border-b sticky top-0 z-10"
                         style="background-color: #fcf9f4; border-color: #e5e0d6;">
                        <div>
                            <p class="text-xs font-black uppercase tracking-widest mb-0.5" style="color: #6a5e33; font-family: Manrope, sans-serif;">Visualizando versão</p>
                            <h3 class="font-headline font-bold text-xl" style="color: #012d1d;">
                                {{ viewing.version_label || `Versão #${viewing.id}` }}
                            </h3>
                        </div>
                        <button @click="closeView"
                                class="w-9 h-9 rounded-xl flex items-center justify-center transition-colors hover:bg-black/5"
                                style="color: #374151;">
                            <span class="material-symbols-outlined">close</span>
                        </button>
                    </div>

                    <!-- Status + meta -->
                    <div class="px-6 py-3 flex items-center gap-3 border-b" style="border-color: #e5e0d6;">
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold"
                              :style="viewing.is_active
                                  ? 'background-color: #dcfce7; color: #16a34a;'
                                  : 'background-color: #f3f4f6; color: #6b7280;'">
                            <span class="w-1.5 h-1.5 rounded-full"
                                  :style="viewing.is_active ? 'background-color:#16a34a' : 'background-color:#9ca3af'"></span>
                            {{ viewing.is_active ? 'Ativa' : 'Inativa' }}
                        </span>
                        <span class="text-xs" style="color: #9ca3af;">Criada em {{ viewing.created_at }}</span>
                    </div>

                    <!-- Abas -->
                    <div class="px-6 pt-4">
                        <div class="flex gap-1 rounded-xl p-1" style="background-color: #f0ede8;">
                            <button
                                v-for="tab in viewTabs"
                                :key="tab.key"
                                @click="viewTab = tab.key"
                                class="flex-1 flex items-center justify-center gap-1.5 px-3 py-2.5 rounded-lg text-xs font-bold transition-all"
                                :style="viewTab === tab.key
                                    ? 'background-color: #012d1d; color: #fff;'
                                    : 'color: #717973;'"
                            >
                                <span class="material-symbols-outlined text-sm">{{ tab.icon }}</span>
                                <span class="hidden sm:inline">{{ tab.label }}</span>
                            </button>
                        </div>
                    </div>

                    <!-- Conteúdo das abas -->
                    <div class="px-6 py-5 flex-1 space-y-5">

                        <!-- ── Hero ── -->
                        <template v-if="viewTab === 'hero'">
                            <div class="view-pair">
                                <div class="view-field"><span class="view-label">Título PT</span><p class="view-value">{{ viewing.hero_title_pt || '—' }}</p></div>
                                <div class="view-field"><span class="view-label">Título EN</span><p class="view-value">{{ viewing.hero_title_en || '—' }}</p></div>
                            </div>
                            <div class="view-pair">
                                <div class="view-field"><span class="view-label">Subtítulo PT</span><p class="view-value">{{ viewing.hero_subtitle_pt || '—' }}</p></div>
                                <div class="view-field"><span class="view-label">Subtítulo EN</span><p class="view-value">{{ viewing.hero_subtitle_en || '—' }}</p></div>
                            </div>
                            <div v-if="viewing.hero_photo_url" class="view-field">
                                <span class="view-label">Foto Hero</span>
                                <img :src="viewing.hero_photo_url" class="mt-2 w-full h-40 object-cover rounded-xl" />
                            </div>
                            <div class="rounded-xl p-4" style="background-color: #fefce8; border: 1px solid #fef08a;">
                                <p class="view-label mb-2">Badge Amarela</p>
                                <div class="flex items-center gap-4">
                                    <div style="background-color: #fbbf24; border-radius: 12px; padding: 12px 20px; display: inline-flex; flex-direction: column;">
                                        <span class="font-black text-2xl leading-none" style="color: #012d1d; font-family: Manrope, sans-serif;">{{ viewing.hero_badge_value || '+15' }}</span>
                                        <span class="text-xs font-bold uppercase tracking-widest mt-1" style="color: #012d1d; font-family: Manrope, sans-serif;">{{ viewing.hero_badge_label_pt || '—' }}</span>
                                    </div>
                                    <div class="text-sm" style="color: #6b7280;"><strong>PT:</strong> {{ viewing.hero_badge_label_pt || '—' }}<br><strong>EN:</strong> {{ viewing.hero_badge_label_en || '—' }}</div>
                                </div>
                            </div>
                        </template>

                        <!-- ── História ── -->
                        <template v-if="viewTab === 'history'">
                            <div class="view-pair">
                                <div class="view-field"><span class="view-label">Título PT</span><p class="view-value">{{ viewing.history_title_pt || '—' }}</p></div>
                                <div class="view-field"><span class="view-label">Título EN</span><p class="view-value">{{ viewing.history_title_en || '—' }}</p></div>
                            </div>
                            <div class="view-pair">
                                <div class="view-field">
                                    <span class="view-label">Descrição PT</span>
                                    <p class="view-value whitespace-pre-line">{{ viewing.history_content_pt || '—' }}</p>
                                </div>
                                <div class="view-field">
                                    <span class="view-label">Descrição EN</span>
                                    <p class="view-value whitespace-pre-line">{{ viewing.history_content_en || '—' }}</p>
                                </div>
                            </div>
                            <div v-if="viewing.history_photo_url" class="view-field">
                                <span class="view-label">Foto Histórica</span>
                                <img :src="viewing.history_photo_url" class="mt-2 w-full h-40 object-cover rounded-xl" />
                            </div>
                            <div v-if="viewing.history_items?.length" class="view-field">
                                <span class="view-label">Itens / Estatísticas</span>
                                <div class="mt-2 flex flex-wrap gap-2">
                                    <div v-for="item in viewing.history_items" :key="item.value"
                                         class="rounded-xl px-4 py-3 text-center"
                                         style="background-color: #f0ede8; min-width: 80px;">
                                        <p class="font-black text-xl leading-tight" style="color: #012d1d; font-family: Manrope, sans-serif;">{{ item.value }}</p>
                                        <p class="text-xs mt-0.5" style="color: #717973;">{{ item.label_pt }}</p>
                                    </div>
                                </div>
                            </div>
                        </template>

                        <!-- ── Qualidade ── -->
                        <template v-if="viewTab === 'quality'">
                            <div class="view-pair">
                                <div class="view-field"><span class="view-label">Título PT</span><p class="view-value">{{ viewing.quality_title_pt || '—' }}</p></div>
                                <div class="view-field"><span class="view-label">Título EN</span><p class="view-value">{{ viewing.quality_title_en || '—' }}</p></div>
                            </div>
                            <div class="view-pair">
                                <div class="view-field"><span class="view-label">Subtítulo PT</span><p class="view-value">{{ viewing.quality_subtitle_pt || '—' }}</p></div>
                                <div class="view-field"><span class="view-label">Subtítulo EN</span><p class="view-value">{{ viewing.quality_subtitle_en || '—' }}</p></div>
                            </div>
                            <div v-if="viewing.quality_items?.length" class="view-field">
                                <span class="view-label">Cards ({{ viewing.quality_items.length }})</span>
                                <div class="mt-2 space-y-3">
                                    <div v-for="(item, i) in viewing.quality_items" :key="i"
                                         class="rounded-xl p-4 flex gap-4 items-start"
                                         style="background-color: #f8f6f2; border: 1px solid #e5e0d6;">
                                        <div class="w-10 h-10 rounded-lg flex items-center justify-center flex-shrink-0"
                                             style="background-color: #012d1d;">
                                            <span class="material-symbols-outlined text-lg"
                                                  style="color: #fbbf24; font-variation-settings: 'FILL' 1;">{{ item.icon || 'star' }}</span>
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <p class="font-bold text-sm" style="color: #012d1d; font-family: Manrope, sans-serif;">{{ item.title_pt }}</p>
                                            <p class="text-xs italic mb-1" style="color: #9ca3af;">EN: {{ item.title_en }}</p>
                                            <p class="text-xs leading-relaxed" style="color: #717973;">{{ item.desc_pt }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </template>

                        <!-- ── CTA ── -->
                        <template v-if="viewTab === 'cta'">
                            <div class="view-field">
                                <span class="view-label">Nome interno da seção</span>
                                <p class="view-value">{{ viewing.cta_section_name || '—' }}</p>
                            </div>
                            <div class="view-pair">
                                <div class="view-field"><span class="view-label">Título PT</span><p class="view-value">{{ viewing.cta_title_pt || '—' }}</p></div>
                                <div class="view-field"><span class="view-label">Título EN</span><p class="view-value">{{ viewing.cta_title_en || '—' }}</p></div>
                            </div>
                            <div class="view-pair">
                                <div class="view-field"><span class="view-label">Subtítulo PT</span><p class="view-value">{{ viewing.cta_subtitle_pt || '—' }}</p></div>
                                <div class="view-field"><span class="view-label">Subtítulo EN</span><p class="view-value">{{ viewing.cta_subtitle_en || '—' }}</p></div>
                            </div>
                        </template>
                    </div>

                    <!-- Drawer footer -->
                    <div class="px-6 py-4 border-t flex justify-between items-center sticky bottom-0"
                         style="background-color: #fcf9f4; border-color: #e5e0d6;">
                        <button @click="closeView"
                                class="px-4 py-2 rounded-lg text-sm font-bold border transition-colors hover:bg-black/5"
                                style="border-color: #e5e7eb; color: #374151;">
                            Fechar
                        </button>
                        <Link :href="`/admin/sobre/criar?from=${viewing.id}`"
                              class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl font-bold text-sm transition-all active:scale-95"
                              style="background-color: #012d1d; color: #fff; font-family: Manrope, sans-serif;">
                            <span class="material-symbols-outlined text-base">content_copy</span>
                            Clonar esta versão
                        </Link>
                    </div>
                </div>
            </div>
        </transition>
    </teleport>
</template>

<style scoped>
.view-pair  { display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; }
.view-field { display: flex; flex-direction: column; }
.view-label {
    font-size: 0.6875rem;
    font-weight: 800;
    text-transform: uppercase;
    letter-spacing: 0.1em;
    color: #717973;
    font-family: Manrope, sans-serif;
    margin-bottom: 0.25rem;
}
.view-value {
    font-size: 0.9375rem;
    color: #1f2937;
    line-height: 1.5;
    background-color: #f8f6f2;
    border-radius: 0.625rem;
    padding: 0.625rem 0.875rem;
    min-height: 2.5rem;
}

/* Transição do drawer */
.modal-enter-active,
.modal-leave-active { transition: opacity 0.2s ease; }
.modal-enter-active .relative,
.modal-leave-active .relative { transition: transform 0.25s cubic-bezier(0.4, 0, 0.2, 1); }
.modal-enter-from { opacity: 0; }
.modal-leave-to   { opacity: 0; }
.modal-enter-from .relative { transform: translateX(100%); }
.modal-leave-to   .relative { transform: translateX(100%); }
</style>
