import { computed, reactive, readonly } from 'vue';
import i18n from '@/i18n';
import { extractErrorMessage, portalApi } from '@/src/services/api';

export const sessionState = reactive({
    user: null,
    role: null,
    profile: null,
    loading: false,
    loaded: false,
    error: '',
});

let sessionPromise = null;

async function loadSession() {
    sessionState.loading = true;
    sessionState.error = '';

    try {
        const payload = await portalApi.getSession();

        sessionState.user = payload.user;
        sessionState.role = payload.role;
        sessionState.profile = payload.profile;
        sessionState.loaded = true;
    } catch (error) {
        sessionState.error = extractErrorMessage(error, i18n.global.t('spa.sessionError'));
        sessionState.user = null;
        sessionState.role = null;
        sessionState.profile = null;
        sessionState.loaded = true;
    } finally {
        sessionState.loading = false;
    }
}

export function initializeSession(force = false) {
    if (!sessionPromise || force) {
        sessionPromise = loadSession();
    }

    return sessionPromise;
}

export function useSession() {
    const fullName = computed(() => sessionState.user?.full_name ?? i18n.global.t('common.appName'));
    const initials = computed(() => {
        const source = sessionState.user?.full_name?.trim();

        if (!source) {
            return 'PU';
        }

        return source
            .split(' ')
            .slice(0, 2)
            .map((part) => part.charAt(0).toUpperCase())
            .join('');
    });

    return {
        state: readonly(sessionState),
        fullName,
        initials,
        isStudent: computed(() => sessionState.role === 'student'),
        isTutor: computed(() => sessionState.role === 'tutor'),
        isAdmin: computed(() => sessionState.role === 'admin'),
        refreshSession: () => initializeSession(true),
    };
}
