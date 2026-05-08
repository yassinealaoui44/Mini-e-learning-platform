<template>
    <header class="portal-surface sticky top-4 z-30 rounded-[28px] px-5 py-4">
        <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
            <div class="flex items-start gap-3">
                <button
                    type="button"
                    class="rounded-full border border-slate-200 p-2 text-slate-500 transition hover:border-slate-300 hover:text-slate-900 lg:hidden"
                    @click="$emit('toggle-sidebar')"
                >
                    <span class="sr-only">{{ t('layouts.openSidebar') }}</span>
                    <svg class="h-5 w-5" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.8">
                        <path d="M3 5h14M3 10h14M3 15h14" stroke-linecap="round" />
                    </svg>
                </button>

                <div>
                    <p :class="accentClasses.eyebrow" class="text-xs font-semibold uppercase tracking-[0.2em]">
                        {{ t(roleLabel) }}
                    </p>
                    <h2 class="portal-display mt-2 text-2xl font-semibold text-slate-950">
                        {{ t(title) }}
                    </h2>
                    <p class="mt-1 text-sm portal-muted">
                        {{ t(subtitle) }}
                    </p>
                </div>
            </div>

            <div class="flex flex-wrap items-center gap-3">
                <LanguageToggle :theme="theme === 'tutor' ? 'emerald' : theme === 'student' ? 'sky' : 'neutral'" />

                <div class="rounded-2xl border border-slate-200 bg-white/80 px-4 py-3">
                    <p class="text-sm font-semibold text-slate-950">
                        {{ fullName }}
                    </p>
                    <p class="text-xs portal-muted">
                        {{ emailLabel }}
                    </p>
                </div>

                <div :class="accentClasses.avatar" class="flex h-12 w-12 items-center justify-center rounded-2xl text-sm font-semibold text-white">
                    {{ initials }}
                </div>

                <button
                    type="button"
                    class="portal-button-secondary"
                    :disabled="isLoggingOut"
                    @click="handleLogout"
                >
                    <svg
                        v-if="isLoggingOut"
                        class="mr-2 h-4 w-4 animate-spin"
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="2"
                    >
                        <circle class="opacity-25" cx="12" cy="12" r="10" />
                        <path class="opacity-90" d="M22 12a10 10 0 0 1-10 10" />
                    </svg>
                    {{ isLoggingOut ? 'Signing out...' : t('common.logOut') }}
                </button>
            </div>
        </div>
    </header>
</template>

<script setup>
import { computed, ref } from 'vue';
import { useI18n } from 'vue-i18n';
import LanguageToggle from '@/shared/LanguageToggle.vue';
import { portalApi } from '@/src/services/api';
import { sessionState, useSession } from '@/src/stores/session';

const props = defineProps({
    title: {
        type: String,
        required: true,
    },
    subtitle: {
        type: String,
        default: '',
    },
    roleLabel: {
        type: String,
        required: true,
    },
    theme: {
        type: String,
        default: 'student',
    },
});

defineEmits(['toggle-sidebar']);

const { t } = useI18n();
const { fullName, initials } = useSession();
const isLoggingOut = ref(false);

const emailLabel = computed(() => sessionState.user?.email ?? t('layouts.signedIn'));

const accentClasses = computed(() =>
    props.theme === 'tutor'
        ? {
              eyebrow: 'text-emerald-700',
              avatar: 'bg-gradient-to-br from-emerald-500 to-teal-500',
          }
        : props.theme === 'admin'
          ? {
                eyebrow: 'text-amber-700',
                avatar: 'bg-gradient-to-br from-amber-500 to-orange-500',
            }
        : {
              eyebrow: 'text-sky-700',
              avatar: 'bg-gradient-to-br from-sky-500 to-cyan-500',
          },
);

async function handleLogout() {
    if (isLoggingOut.value) {
        return;
    }

    isLoggingOut.value = true;
    try {
        await portalApi.logout();
        window.location.assign('/login');
    } finally {
        isLoggingOut.value = false;
    }
}
</script>
