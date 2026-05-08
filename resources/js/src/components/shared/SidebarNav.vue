<template>
    <aside
        :class="[
            'portal-surface fixed inset-y-4 left-4 z-40 flex w-[calc(100vw-2rem)] max-w-72 flex-col rounded-[28px] p-5 transition-transform duration-300 lg:w-72 lg:translate-x-0',
            open ? 'translate-x-0' : '-translate-x-[120%]',
        ]"
    >
        <div class="mb-8 flex items-start justify-between gap-4">
            <div>
                <p :class="accentClasses.chip" class="inline-flex rounded-full px-3 py-1 text-xs font-semibold uppercase tracking-[0.2em]">
                    {{ t(label) }}
                </p>
                <h1 class="portal-display mt-4 text-2xl font-semibold text-slate-950">
                    {{ t(title) }}
                </h1>
                <p class="mt-2 text-sm portal-muted">
                    {{ t(subtitle) }}
                </p>
            </div>

            <button
                type="button"
                class="rounded-full border border-slate-200 p-2 text-slate-500 transition hover:border-slate-300 hover:text-slate-900 lg:hidden"
                @click="$emit('close')"
            >
                <span class="sr-only">{{ t('layouts.closeSidebar') }}</span>
                <svg class="h-5 w-5" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.8">
                    <path d="M5 5l10 10M15 5L5 15" stroke-linecap="round" />
                </svg>
            </button>
        </div>

        <nav class="space-y-2">
            <RouterLink
                v-for="item in items"
                :key="item.name"
                :to="{ name: item.name }"
                :class="[
                    'group flex items-start gap-3 rounded-2xl px-4 py-3 transition',
                    $route.name === item.name ? accentClasses.linkActive : 'text-slate-600 hover:bg-slate-50 hover:text-slate-950',
                ]"
                @click="$emit('close')"
            >
                <span
                    :class="[
                        'mt-1 h-2.5 w-2.5 rounded-full transition',
                        $route.name === item.name ? accentClasses.dotActive : 'bg-slate-300 group-hover:bg-slate-500',
                    ]"
                />

                <span class="min-w-0">
                    <span class="block text-sm font-semibold">{{ t(item.labelKey) }}</span>
                    <span class="mt-1 block text-xs portal-muted">{{ t(item.hintKey) }}</span>
                </span>
            </RouterLink>
        </nav>

        <div class="mt-auto rounded-3xl bg-slate-950 px-5 py-4 text-sm text-slate-100">
            <p class="font-semibold">{{ t('layouts.builtForFocusTitle') }}</p>
            <p class="mt-2 text-slate-300">
                {{ t('layouts.builtForFocusDescription') }}
            </p>
        </div>
    </aside>
</template>

<script setup>
import { computed } from 'vue';
import { RouterLink } from 'vue-router';
import { useI18n } from 'vue-i18n';

const props = defineProps({
    open: {
        type: Boolean,
        default: false,
    },
    title: {
        type: String,
        required: true,
    },
    subtitle: {
        type: String,
        required: true,
    },
    label: {
        type: String,
        required: true,
    },
    theme: {
        type: String,
        default: 'student',
    },
    items: {
        type: Array,
        default: () => [],
    },
});

defineEmits(['close']);

const { t } = useI18n();

const accentClasses = computed(() =>
    props.theme === 'tutor'
        ? {
              chip: 'bg-emerald-50 text-emerald-700 ring-1 ring-emerald-200',
              linkActive: 'bg-gradient-to-r from-emerald-500/12 to-teal-500/12 text-slate-950',
              dotActive: 'bg-emerald-500',
          }
        : props.theme === 'admin'
          ? {
                chip: 'bg-amber-50 text-amber-700 ring-1 ring-amber-200',
                linkActive: 'bg-gradient-to-r from-amber-500/12 to-orange-500/12 text-slate-950',
                dotActive: 'bg-amber-500',
            }
        : {
              chip: 'bg-sky-50 text-sky-700 ring-1 ring-sky-200',
              linkActive: 'bg-gradient-to-r from-sky-500/12 to-cyan-500/12 text-slate-950',
              dotActive: 'bg-sky-500',
          },
);
</script>
