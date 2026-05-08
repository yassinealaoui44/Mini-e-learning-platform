<template>
    <form class="portal-card rounded-[28px] p-6" @submit.prevent="submitForm">
        <div class="grid gap-5 lg:grid-cols-2">
            <label class="space-y-2 text-sm font-medium text-slate-700">
                <span>{{ t('common.lessonTitle') }}</span>
                <input
                    v-model="form.title"
                    type="text"
                    class="portal-input"
                    :placeholder="t('spa.lessonTitlePlaceholder')"
                >
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
            <input
                type="file"
                class="portal-file-input"
                @change="onFileChange"
            >
            <span class="block text-xs portal-muted">
                {{ t('spa.lessonFileHint') }}
            </span>
            <span v-if="errors.file" class="block text-xs text-rose-600">{{ errors.file[0] }}</span>
        </label>

        <div class="mt-6 flex justify-end">
            <button
                type="submit"
                class="portal-button-primary disabled:cursor-not-allowed disabled:opacity-60"
                :disabled="busy"
            >
                {{ busy ? t('spa.uploading') : t('spa.createLesson') }}
            </button>
        </div>
    </form>
</template>

<script setup>
import { reactive } from 'vue';
import { useI18n } from 'vue-i18n';

const props = defineProps({
    busy: {
        type: Boolean,
        default: false,
    },
    errors: {
        type: Object,
        default: () => ({}),
    },
});

const emit = defineEmits(['submit']);
const { t } = useI18n();

const form = reactive({
    title: '',
    type: 'video',
    file: null,
});

function onFileChange(event) {
    form.file = event.target.files?.[0] ?? null;
}

function submitForm() {
    emit('submit', {
        title: form.title,
        type: form.type,
        file: form.file,
        reset: () => {
            form.title = '';
            form.type = 'video';
            form.file = null;
        },
    });
}
</script>
