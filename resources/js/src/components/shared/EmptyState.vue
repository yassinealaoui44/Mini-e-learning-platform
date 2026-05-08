<template>
    <div class="portal-card rounded-[26px] p-8 text-center">
        <p class="text-sm font-semibold uppercase tracking-[0.16em] text-slate-500">
            {{ eyebrow }}
        </p>
        <h3 class="portal-display mt-3 text-2xl font-semibold text-slate-950">
            {{ title }}
        </h3>
        <p class="mx-auto mt-3 max-w-lg text-sm portal-muted">
            {{ description }}
        </p>
        <div v-if="$slots.actions" class="mt-5 flex justify-center">
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
        required: true,
    },
});
</script>
