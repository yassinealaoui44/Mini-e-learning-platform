<template>
    <button
        class="inline-flex items-center justify-center gap-2 rounded-md border border-transparent bg-gray-800 px-4 py-2 text-xs font-semibold uppercase tracking-widest text-white transition duration-150 ease-in-out hover:bg-gray-700 focus:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 active:bg-gray-900 disabled:cursor-not-allowed disabled:opacity-70"
        :disabled="disabled || loading"
        :aria-busy="loading"
    >
        <svg
            v-if="loading"
            class="h-4 w-4 animate-spin text-white"
            viewBox="0 0 24 24"
            fill="none"
            stroke="currentColor"
            stroke-width="2"
        >
            <circle class="opacity-25" cx="12" cy="12" r="10" />
            <path class="opacity-90" d="M22 12a10 10 0 0 1-10 10" />
        </svg>
        <slot>{{ loadingText }}</slot>
    </button>
</template>

<script setup>
defineProps({
    loading: {
        type: Boolean,
        default: false,
    },
    disabled: {
        type: Boolean,
        default: false,
    },
    loadingText: {
        type: String,
        default: 'Loading...',
    },
});
</script>
