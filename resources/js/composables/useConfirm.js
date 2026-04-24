import { reactive } from 'vue';

// Singleton — estado compartilhado entre todos os componentes
const state = reactive({
    show: false,
    title: '',
    message: '',
    type: 'danger',       // 'danger' | 'warning' | 'info'
    confirmLabel: 'Confirmar',
    cancelLabel: 'Cancelar',
    resolve: null,
});

export function useConfirm() {
    /**
     * Abre o modal de confirmação.
     * @param {object} options
     * @param {string} options.title        Título do diálogo
     * @param {string} options.message      Mensagem / pergunta
     * @param {'danger'|'warning'|'info'} [options.type='danger']
     * @param {string} [options.confirmLabel='Confirmar']
     * @param {string} [options.cancelLabel='Cancelar']
     * @returns {Promise<boolean>}
     */
    function confirm({
        title = 'Confirmar ação',
        message = '',
        type = 'danger',
        confirmLabel = 'Confirmar',
        cancelLabel = 'Cancelar',
    } = {}) {
        return new Promise((resolve) => {
            Object.assign(state, { show: true, title, message, type, confirmLabel, cancelLabel, resolve });
        });
    }

    function _accept() {
        state.show = false;
        state.resolve?.(true);
        state.resolve = null;
    }

    function _dismiss() {
        state.show = false;
        state.resolve?.(false);
        state.resolve = null;
    }

    return { dialogState: state, confirm, _accept, _dismiss };
}
