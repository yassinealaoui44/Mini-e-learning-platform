<template>
    <div class="rounded-[26px] border border-rose-200 bg-rose-50 px-5 py-4 text-sm text-rose-700">
        {{ message }}
    </div>
</template>

<script setup>
defineProps({
    message: {
        type: String,
        required: true,
    },
});
</script>
