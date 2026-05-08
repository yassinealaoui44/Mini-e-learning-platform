<template>
    <label class="space-y-2 text-sm font-medium text-slate-700">
        <span>{{ label }}</span>
        <select
            :id="id"
            :value="modelValue"
            :required="required"
            class="portal-select"
            @change="$emit('update:modelValue', $event.target.value)"
        >
            <option value="" :disabled="required">{{ placeholder }}</option>
            <option v-for="option in options" :key="option.value" :value="option.value">
                {{ option.label }}
            </option>
        </select>
        <span v-if="error" class="block text-xs text-rose-600">{{ error }}</span>
    </label>
</template>

<script setup>
defineProps({
    id: {
        type: String,
        default: '',
    },
    label: {
        type: String,
        required: true,
    },
    modelValue: {
        type: String,
        default: '',
    },
    placeholder: {
        type: String,
        required: true,
    },
    options: {
        type: Array,
        default: () => [],
    },
    error: {
        type: String,
        default: '',
    },
    required: {
        type: Boolean,
        default: false,
    },
});

defineEmits(['update:modelValue']);
</script>
