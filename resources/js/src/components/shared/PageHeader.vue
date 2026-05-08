<template>
    <div class="flex flex-col gap-4 rounded-[28px] border border-slate-200 bg-white/85 px-6 py-6 shadow-sm lg:flex-row lg:items-end lg:justify-between">
        <div>
            <p v-if="eyebrow" class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">
                {{ eyebrow }}
            </p>
            <h3 class="portal-display mt-2 text-2xl font-semibold text-slate-950">
                {{ title }}
            </h3>
            <p v-if="description" class="mt-2 max-w-2xl text-sm portal-muted">
                {{ description }}
            </p>
        </div>

        <div v-if="$slots.actions" class="flex flex-wrap items-center gap-3">
            <slot name="actions" />
        </div>
    </div>
</template>

<script setup>
defineProps({
    eyebrow: {
        type: String,
        default: '',
    },
    title: {
        type: String,
        required: true,
    },
    description: {
        type: String,
        default: '',
    },
});
</script>
