<template>
    <form class="portal-card rounded-[28px] p-6" @submit.prevent="submitForm">
        <div class="grid gap-5 lg:grid-cols-2">
            <label class="space-y-2 text-sm font-medium text-slate-700">
                <span>{{ t('auth.fields.firstName') }}</span>
                <input v-model="form.firstName" type="text" class="portal-input">
                <span v-if="errors.first_name" class="block text-xs text-rose-600">{{ errors.first_name[0] }}</span>
            </label>

            <label class="space-y-2 text-sm font-medium text-slate-700">
                <span>{{ t('auth.fields.lastName') }}</span>
                <input v-model="form.lastName" type="text" class="portal-input">
                <span v-if="errors.last_name" class="block text-xs text-rose-600">{{ errors.last_name[0] }}</span>
            </label>

            <label class="space-y-2 text-sm font-medium text-slate-700">
                <span>{{ t('auth.fields.email') }}</span>
                <input v-model="form.email" type="email" class="portal-input">
                <span v-if="errors.email" class="block text-xs text-rose-600">{{ errors.email[0] }}</span>
            </label>

            <label class="space-y-2 text-sm font-medium text-slate-700">
                <span>{{ t('common.role') }}</span>
                <select v-model="form.role" class="portal-select">
                    <option value="student">{{ t('options.roles.student') }}</option>
                    <option value="tutor">{{ t('options.roles.tutor') }}</option>
                    <option value="admin">{{ t('options.roles.admin') }}</option>
                </select>
                <span v-if="errors.role" class="block text-xs text-rose-600">{{ errors.role[0] }}</span>
            </label>

            <label class="space-y-2 text-sm font-medium text-slate-700 lg:col-span-2">
                <span>{{ t('auth.fields.password') }}</span>
                <input
                    v-model="form.password"
                    type="password"
                    class="portal-input"
                    :placeholder="requirePassword ? t('spa.minimumPassword') : t('spa.leavePasswordBlank')"
                >
                <span v-if="errors.password" class="block text-xs text-rose-600">{{ errors.password[0] }}</span>
            </label>
        </div>

        <div v-if="form.role === 'student'" class="mt-5 grid gap-5 lg:grid-cols-2">
            <AcademicSelect
                id="student-track"
                v-model="form.track"
                :label="t('auth.fields.track')"
                :placeholder="t('auth.register.specialtyPlaceholder')"
                :options="trackOptions"
                :error="errors.track?.[0]"
                :required="true"
            />

            <AcademicSelect
                id="student-level"
                v-model="form.level"
                :label="t('auth.fields.level')"
                :placeholder="t('auth.register.levelPlaceholder')"
                :options="levelOptions"
                :error="errors.level?.[0]"
                :required="true"
            />
        </div>

        <label v-if="form.role === 'tutor'" class="mt-5 block space-y-2 text-sm font-medium text-slate-700">
            <span>{{ t('auth.fields.domain') }}</span>
            <input v-model="form.domain" type="text" class="portal-input">
            <span v-if="errors.domain" class="block text-xs text-rose-600">{{ errors.domain[0] }}</span>
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
import AcademicSelect from '@/src/components/shared/AcademicSelect.vue';
import { LEVEL_OPTIONS, TRACK_OPTIONS } from '@/shared/academics';

const props = defineProps({
    initialValues: {
        type: Object,
        default: () => ({
            firstName: '',
            lastName: '',
            email: '',
            role: 'student',
            password: '',
            track: '',
            level: '',
            domain: '',
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
    requirePassword: {
        type: Boolean,
        default: true,
    },
});

const emit = defineEmits(['submit']);
const { t } = useI18n();
const trackOptions = TRACK_OPTIONS.map((track) => ({ value: track, label: track }));
const levelOptions = LEVEL_OPTIONS.map((level) => ({ value: level, label: level }));

const form = reactive({
    firstName: '',
    lastName: '',
    email: '',
    role: 'student',
    password: '',
    track: '',
    level: '',
    domain: '',
});

watch(
    () => props.initialValues,
    (values) => {
        form.firstName = values.firstName ?? '';
        form.lastName = values.lastName ?? '';
        form.email = values.email ?? '';
        form.role = values.role ?? 'student';
        form.password = '';
        form.track = values.track ?? '';
        form.level = values.level ?? '';
        form.domain = values.domain ?? '';
    },
    { immediate: true, deep: true },
);

function submitForm() {
    emit('submit', {
        first_name: form.firstName,
        last_name: form.lastName,
        email: form.email,
        password: form.password,
        role: form.role,
        track: form.track,
        level: form.level,
        domain: form.domain,
    });
}
</script>
