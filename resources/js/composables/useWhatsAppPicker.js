import { reactive } from 'vue';

// Estado singleton — compartilhado por todos os componentes
const state = reactive({ isOpen: false, message: '' });

export function useWhatsAppPicker() {
    function open(message = '') {
        state.message = message;
        state.isOpen  = true;
    }

    function close() {
        state.isOpen  = false;
        state.message = '';
    }

    return { state, open, close };
}
