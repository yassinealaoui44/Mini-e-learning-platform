<template>
    <div class="portal-card rounded-[26px] p-6" aria-live="polite" aria-busy="true">
        <div class="mb-4 flex items-center gap-3">
            <span class="h-3 w-3 animate-pulse rounded-full bg-slate-400" />
            <p class="text-sm font-medium text-slate-700">{{ label }}</p>
        </div>

        <div class="space-y-3">
            <div v-for="index in lines" :key="index" class="h-4 overflow-hidden rounded-lg bg-slate-100">
                <div class="portal-skeleton-shimmer h-full w-full rounded-lg" />
            </div>
        </div>
    </div>
</template>

<script setup>
defineProps({
    label: {
        type: String,
        default: '',
    },
    lines: {
        type: Number,
        default: 3,
    },
});
</script>
