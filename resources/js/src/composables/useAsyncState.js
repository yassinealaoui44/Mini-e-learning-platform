import { ref } from 'vue';
import { extractErrorMessage } from '@/src/services/api';

export function useAsyncState(initialValue = null, fallbackMessage) {
    const data = ref(initialValue);
    const loading = ref(false);
    const error = ref('');

    async function execute(factory) {
        loading.value = true;
        error.value = '';

        try {
            const result = await factory();
            data.value = result;
            return result;
        } catch (requestError) {
            error.value = extractErrorMessage(requestError, fallbackMessage);
            return null;
        } finally {
            loading.value = false;
        }
    }

    return {
        data,
        loading,
        error,
        execute,
    };
}
