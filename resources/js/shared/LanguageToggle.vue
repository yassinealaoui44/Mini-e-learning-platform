<template>
    <div class="flex items-center gap-2 rounded-full border border-slate-200 bg-white/90 p-1">
        <span class="px-2 text-xs font-semibold uppercase tracking-[0.18em] text-slate-500">
            {{ t('common.language') }}
        </span>

        <button
            v-for="option in localeOptions"
            :key="option.value"
            type="button"
            :class="[
                'rounded-full px-3 py-1.5 text-xs font-semibold uppercase tracking-[0.16em] transition',
                locale === option.value ? activeClasses : 'text-slate-500 hover:text-slate-900',
            ]"
            @click="setLocale(option.value)"
        >
            {{ option.label }}
        </button>
    </div>
</template>

<script setup>
import { computed } from 'vue';
import { useI18n } from 'vue-i18n';
import { useLocale } from '@/i18n';

const props = defineProps({
    theme: {
        type: String,
        default: 'neutral',
    },
});

const { t } = useI18n();
const { locale, localeOptions, setLocale } = useLocale();

const activeClasses = computed(() =>
    props.theme === 'emerald'
        ? 'bg-emerald-500 text-white'
        : props.theme === 'sky'
          ? 'bg-sky-500 text-white'
          : 'bg-slate-950 text-white',
);
</script>
