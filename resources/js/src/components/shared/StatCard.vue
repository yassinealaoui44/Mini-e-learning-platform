<template>
    <article class="portal-card rounded-[26px] p-5">
        <p class="text-sm font-semibold uppercase tracking-[0.16em] text-slate-500">
            {{ label }}
        </p>
        <p class="portal-display mt-4 text-4xl font-semibold text-slate-950">
            {{ value }}
        </p>
        <p v-if="note" class="mt-2 text-sm portal-muted">
            {{ note }}
        </p>
    </article>
</template>

<script setup>
defineProps({
    label: {
        type: String,
        required: true,
    },
    value: {
        type: [String, Number],
        required: true,
    },
    note: {
        type: String,
        default: '',
    },
});
</script>
