<template>
    <div class="flex items-center gap-3">
        <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-gradient-to-br from-sky-500 via-cyan-500 to-emerald-400 shadow-lg shadow-sky-500/20">
            <svg class="h-6 w-6 text-white" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20" stroke-linecap="round" stroke-linejoin="round" />
                <path d="M6.5 17A2.5 2.5 0 0 0 4 14.5V5a2 2 0 0 1 2-2h14v14" stroke-linecap="round" stroke-linejoin="round" />
                <path d="M8 7h8M8 11h6" stroke-linecap="round" />
            </svg>
        </div>

        <div>
            <p class="text-sm font-semibold uppercase tracking-[0.2em] text-slate-500">
                {{ t('guest.eyebrow') }}
            </p>
            <p class="portal-display text-xl font-semibold text-slate-950">
                {{ t('common.appName') }}
            </p>
        </div>
    </div>
</template>

<script setup>
import { useI18n } from 'vue-i18n';

const { t } = useI18n();
</script>
