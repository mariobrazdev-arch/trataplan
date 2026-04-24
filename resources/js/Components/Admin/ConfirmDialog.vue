<script setup>
import { useConfirm } from '@/composables/useConfirm';

const { dialogState: dialog, _accept, _dismiss } = useConfirm();

const icons = {
    danger:  { name: 'delete_forever', color: '#ba1a1a' },
    warning: { name: 'warning',        color: '#7a5900' },
    info:    { name: 'info',           color: '#012d1d' },
};

const confirmColors = {
    danger:  { bg: '#ba1a1a', text: '#ffffff' },
    warning: { bg: '#7a5900', text: '#ffffff' },
    info:    { bg: '#012d1d', text: '#ffffff' },
};
</script>

<template>
    <!-- Backdrop -->
    <Transition name="confirm-fade">
        <div
            v-if="dialog.show"
            class="fixed inset-0 z-[9999] flex items-center justify-center p-4"
            style="background-color: rgba(0, 0, 0, 0.45); backdrop-filter: blur(2px);"
            @click.self="_dismiss"
        >
            <!-- Card -->
            <Transition name="confirm-scale">
                <div
                    v-if="dialog.show"
                    class="w-full max-w-sm rounded-2xl shadow-2xl overflow-hidden"
                    style="background-color: #ffffff;"
                    role="alertdialog"
                    aria-modal="true"
                    :aria-labelledby="'confirm-title'"
                    :aria-describedby="'confirm-message'"
                >
                    <!-- Topo colorido -->
                    <div class="px-6 pt-6 pb-4 flex flex-col items-center text-center gap-3">
                        <!-- Ícone -->
                        <div
                            class="w-14 h-14 rounded-full flex items-center justify-center"
                            :style="{ backgroundColor: icons[dialog.type]?.color + '1a' }"
                        >
                            <span
                                class="material-symbols-outlined text-4xl"
                                style="font-variation-settings: 'FILL' 1;"
                                :style="{ color: icons[dialog.type]?.color }"
                            >{{ icons[dialog.type]?.name }}</span>
                        </div>

                        <!-- Título -->
                        <h2
                            id="confirm-title"
                            class="font-headline font-bold text-lg leading-tight"
                            style="color: #012d1d;"
                        >{{ dialog.title }}</h2>

                        <!-- Mensagem -->
                        <p
                            v-if="dialog.message"
                            id="confirm-message"
                            class="font-body text-sm leading-relaxed"
                            style="color: #717973;"
                        >{{ dialog.message }}</p>
                    </div>

                    <!-- Divisor -->
                    <div style="height: 1px; background-color: #f0ede8;"></div>

                    <!-- Botões -->
                    <div class="flex gap-3 px-6 py-4">
                        <!-- Cancelar -->
                        <button
                            type="button"
                            class="flex-1 py-2.5 rounded-xl font-headline font-bold text-sm transition-all active:scale-95"
                            style="background-color: #f0ede8; color: #012d1d;"
                            @click="_dismiss"
                        >{{ dialog.cancelLabel }}</button>

                        <!-- Confirmar -->
                        <button
                            type="button"
                            class="flex-1 py-2.5 rounded-xl font-headline font-bold text-sm transition-all active:scale-95"
                            :style="{ backgroundColor: confirmColors[dialog.type]?.bg, color: confirmColors[dialog.type]?.text }"
                            @click="_accept"
                        >{{ dialog.confirmLabel }}</button>
                    </div>

                    <!-- Rodapé de segurança -->
                    <div class="px-6 pb-4 text-center">
                        <p class="font-label text-[10px] uppercase tracking-widest" style="color: #c1c8c2;">
                            Esta ação requer confirmação
                        </p>
                    </div>
                </div>
            </Transition>
        </div>
    </Transition>
</template>

<style scoped>
.confirm-fade-enter-active,
.confirm-fade-leave-active {
    transition: opacity 0.2s ease;
}
.confirm-fade-enter-from,
.confirm-fade-leave-to {
    opacity: 0;
}

.confirm-scale-enter-active {
    transition: transform 0.2s cubic-bezier(0.34, 1.56, 0.64, 1), opacity 0.15s ease;
}
.confirm-scale-leave-active {
    transition: transform 0.15s ease, opacity 0.15s ease;
}
.confirm-scale-enter-from {
    transform: scale(0.85);
    opacity: 0;
}
.confirm-scale-leave-to {
    transform: scale(0.9);
    opacity: 0;
}
</style>
