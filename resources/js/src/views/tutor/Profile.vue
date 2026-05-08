<template>
    <div class="portal-page-grid">
        <PageHeader
            :eyebrow="t('spa.profileEyebrow')"
            :title="t('spa.profileTutorTitle')"
            :description="t('spa.profileTutorDescription')"
        />

        <ErrorState v-if="submitError" :message="submitError" />
        <ErrorState v-if="error" :message="error" />
        <LoadingState v-else-if="loading" :label="t('spa.loadingProfile')" />

        <form v-else class="portal-card rounded-[28px] p-6" @submit.prevent="submit">
            <div class="grid gap-5 lg:grid-cols-2">
                <label class="space-y-2 text-sm font-medium text-slate-700">
                    <span>{{ t('auth.fields.firstName') }}</span>
                    <input v-model="form.first_name" type="text" class="portal-input">
                    <span v-if="serverErrors.prenom" class="block text-xs text-rose-600">{{ serverErrors.prenom[0] }}</span>
                </label>

                <label class="space-y-2 text-sm font-medium text-slate-700">
                    <span>{{ t('auth.fields.lastName') }}</span>
                    <input v-model="form.last_name" type="text" class="portal-input">
                    <span v-if="serverErrors.nom" class="block text-xs text-rose-600">{{ serverErrors.nom[0] }}</span>
                </label>

                <label class="space-y-2 text-sm font-medium text-slate-700">
                    <span>{{ t('auth.fields.email') }}</span>
                    <input v-model="form.email" type="email" class="portal-input">
                    <span v-if="serverErrors.email" class="block text-xs text-rose-600">{{ serverErrors.email[0] }}</span>
                </label>

                <label class="space-y-2 text-sm font-medium text-slate-700">
                    <span>{{ t('auth.fields.domain') }}</span>
                    <input v-model="form.domain" type="text" class="portal-input">
                    <span v-if="serverErrors.domaine" class="block text-xs text-rose-600">{{ serverErrors.domaine[0] }}</span>
                </label>
            </div>

            <div v-if="message" class="portal-alert-success mt-5">
                {{ message }}
            </div>

            <div class="mt-6 flex justify-end">
                <button
                    type="submit"
                    class="portal-button-primary disabled:cursor-not-allowed disabled:opacity-60"
                    :disabled="saving"
                >
                    {{ saving ? t('common.saving') : t('spa.saveProfile') }}
                </button>
            </div>
        </form>
    </div>
</template>

<script setup>
import { onMounted, reactive, ref } from 'vue';
import { useI18n } from 'vue-i18n';
import ErrorState from '@/src/components/shared/ErrorState.vue';
import LoadingState from '@/src/components/shared/LoadingState.vue';
import PageHeader from '@/src/components/shared/PageHeader.vue';
import { useAsyncState } from '@/src/composables/useAsyncState';
import { initializeSession } from '@/src/stores/session';
import { extractErrorMessage, portalApi } from '@/src/services/api';

const { t } = useI18n();
const saving = ref(false);
const message = ref('');
const serverErrors = ref({});
const submitError = ref('');
const form = reactive({
    first_name: '',
    last_name: '',
    email: '',
    domain: '',
});

const { data: profile, loading, error, execute } = useAsyncState(null);

function applyProfile(payload) {
    form.first_name = payload.user.first_name ?? '';
    form.last_name = payload.user.last_name ?? '';
    form.email = payload.user.email ?? '';
    form.domain = payload.profile.domain ?? '';
}

async function loadProfile() {
    const payload = await execute(() => portalApi.getProfile());

    if (payload) {
        applyProfile(payload);
    }
}

async function submit() {
    saving.value = true;
    message.value = '';
    serverErrors.value = {};
    submitError.value = '';

    try {
        const payload = await portalApi.updateProfile({
            first_name: form.first_name,
            last_name: form.last_name,
            email: form.email,
            domain: form.domain,
        });

        applyProfile(payload);
        message.value = t('spa.profileUpdated');
        await initializeSession(true);
    } catch (requestError) {
        serverErrors.value = requestError.response?.data?.errors ?? {};
        submitError.value = extractErrorMessage(requestError);
    } finally {
        saving.value = false;
    }
}

onMounted(() => {
    loadProfile();
});
</script>
