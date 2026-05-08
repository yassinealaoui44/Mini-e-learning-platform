<template>
    <form class="portal-card rounded-[28px] p-6" @submit.prevent="submitForm">
        <div class="grid gap-5 lg:grid-cols-2">
            <label class="space-y-2 text-sm font-medium text-slate-700">
                <span>{{ t('common.courseTitle') }}</span>
                <input
                    v-model="form.title"
                    type="text"
                    class="portal-input"
                    :placeholder="t('spa.courseTitlePlaceholder')"
                >
                <span v-if="errors.nom" class="block text-xs text-rose-600">{{ errors.nom[0] }}</span>
            </label>

            <AcademicSelect
                id="course-track"
                v-model="form.track"
                :label="t('common.track')"
                :placeholder="t('auth.register.specialtyPlaceholder')"
                :options="trackOptions"
                :error="errors.filiere?.[0]"
                :required="true"
            />
        </div>

        <label v-if="showOwner" class="mt-5 block space-y-2 text-sm font-medium text-slate-700">
            <span>{{ t('options.roles.tutor') }}</span>
            <select v-model="form.ownerId" class="portal-select">
                <option value="">{{ t('common.select') }}</option>
                <option v-for="owner in ownerOptions" :key="owner.id" :value="owner.id">{{ owner.label }}</option>
            </select>
            <span v-if="errors.id_tuteur" class="block text-xs text-rose-600">{{ errors.id_tuteur[0] }}</span>
        </label>

        <label class="mt-5 block space-y-2 text-sm font-medium text-slate-700">
            <span>{{ t('common.coverImage') }}</span>
            <img
                v-if="imagePreview"
                :src="imagePreview"
                :alt="t('common.coverImage')"
                class="h-44 w-full rounded-2xl border border-slate-200 object-cover md:max-w-md"
            >
            <input
                type="file"
                accept="image/png,image/jpeg,image/webp"
                class="portal-file-input"
                @change="onFileChange"
            >
            <span class="block text-xs portal-muted">
                {{ t('spa.courseImageOptional') }}
            </span>
            <span v-if="errors.cover_image" class="block text-xs text-rose-600">{{ errors.cover_image[0] }}</span>
            <span v-else-if="errors.thumbnail" class="block text-xs text-rose-600">{{ errors.thumbnail[0] }}</span>
        </label>

        <div class="mt-6 flex flex-wrap items-center justify-between gap-3">
            <p class="text-sm portal-muted">
                {{ t('spa.courseFormHint') }}
            </p>

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
import { onBeforeUnmount, reactive, ref, watch } from 'vue';
import { useI18n } from 'vue-i18n';
import AcademicSelect from '@/src/components/shared/AcademicSelect.vue';
import { TRACK_OPTIONS } from '@/shared/academics';

const props = defineProps({
    initialValues: {
        type: Object,
        default: () => ({
            title: '',
            track: '',
            thumbnail: null,
            ownerId: '',
        }),
    },
    submitLabel: {
        type: String,
        default: '',
    },
    busy: {
        type: Boolean,
        default: false,
    },
    errors: {
        type: Object,
        default: () => ({}),
    },
    showOwner: {
        type: Boolean,
        default: false,
    },
    ownerOptions: {
        type: Array,
        default: () => [],
    },
    currentCoverImageUrl: {
        type: String,
        default: '',
    },
});

const emit = defineEmits(['submit']);
const { t } = useI18n();
const trackOptions = TRACK_OPTIONS.map((track) => ({ value: track, label: track }));

const imagePreview = ref(props.currentCoverImageUrl ?? '');
let objectUrl = null;

const form = reactive({
    title: '',
    track: '',
    thumbnail: null,
    ownerId: '',
});

watch(
    () => props.initialValues,
    (values) => {
        form.title = values.title ?? '';
        form.track = values.track ?? '';
        form.thumbnail = null;
        form.ownerId = values.ownerId ?? '';

        imagePreview.value = props.currentCoverImageUrl ?? '';
        if (objectUrl) {
            URL.revokeObjectURL(objectUrl);
            objectUrl = null;
        }
    },
    { immediate: true, deep: true },
);

watch(
    () => props.currentCoverImageUrl,
    (url) => {
        if (!objectUrl) {
            imagePreview.value = url ?? '';
        }
    },
);

function onFileChange(event) {
    form.thumbnail = event.target.files?.[0] ?? null;

    if (objectUrl) {
        URL.revokeObjectURL(objectUrl);
        objectUrl = null;
    }

    if (form.thumbnail) {
        objectUrl = URL.createObjectURL(form.thumbnail);
        imagePreview.value = objectUrl;
        return;
    }

    imagePreview.value = props.currentCoverImageUrl ?? '';
}

onBeforeUnmount(() => {
    if (objectUrl) {
        URL.revokeObjectURL(objectUrl);
        objectUrl = null;
    }
});

function submitForm() {
    emit('submit', {
        title: form.title,
        track: form.track,
        cover_image: form.thumbnail,
        ownerId: form.ownerId,
    });
}
</script>
