<template>
    <form class="portal-card rounded-[28px] p-6" @submit.prevent="submitForm">
        <div class="grid gap-5 lg:grid-cols-2">
            <label class="space-y-2 text-sm font-medium text-slate-700">
                <span>{{ t('common.lessonTitle') }}</span>
                <input v-model="form.title" type="text" class="portal-input">
                <span v-if="errors.titre" class="block text-xs text-rose-600">{{ errors.titre[0] }}</span>
            </label>

            <label class="space-y-2 text-sm font-medium text-slate-700">
                <span>{{ t('common.lessonType') }}</span>
                <select v-model="form.type" class="portal-select">
                    <option value="video">{{ t('common.video') }}</option>
                    <option value="pdf">PDF</option>
                </select>
                <span v-if="errors.type" class="block text-xs text-rose-600">{{ errors.type[0] }}</span>
            </label>
        </div>

        <label class="mt-5 block space-y-2 text-sm font-medium text-slate-700">
            <span>{{ t('common.lessonFile') }}</span>
            <input type="file" class="portal-file-input" @change="onFileChange">
            <span class="block text-xs portal-muted">{{ t('spa.lessonReplaceHint') }}</span>
            <span v-if="errors.file" class="block text-xs text-rose-600">{{ errors.file[0] }}</span>
        </label>

        <div class="mt-6 flex justify-end">
            <button
                type="submit"
                class="portal-button-primary disabled:cursor-not-allowed disabled:opacity-60"
                :disabled="busy"
            >
                {{ busy ? t('common.saving') : submitLabel }}
            </button>
        </div>
    </form>
</template>

<script setup>
import { reactive, watch } from 'vue';
import { useI18n } from 'vue-i18n';

const props = defineProps({
    initialValues: {
        type: Object,
        default: () => ({
            title: '',
            type: 'video',
        }),
    },
    submitLabel: {
        type: String,
        default: '',
    },
    errors: {
        type: Object,
        default: () => ({}),
    },
    busy: {
        type: Boolean,
        default: false,
    },
});

const emit = defineEmits(['submit']);
const { t } = useI18n();

const form = reactive({
    title: '',
    type: 'video',
    file: null,
});

watch(
    () => props.initialValues,
    (values) => {
        form.title = values.title ?? '';
        form.type = values.type ?? 'video';
        form.file = null;
    },
    { immediate: true, deep: true },
);

function onFileChange(event) {
    form.file = event.target.files?.[0] ?? null;
}

function submitForm() {
    emit('submit', {
        titre: form.title,
        type: form.type,
        file: form.file,
    });
}
</script>
