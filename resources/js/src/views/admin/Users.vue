<template>
    <div class="portal-page-grid">
        <PageHeader
            :eyebrow="t('spa.adminUsersEyebrow')"
            :title="t('spa.adminUsersTitle')"
            :description="t('spa.adminUsersDescription')"
        />

        <ErrorState v-if="error" :message="error" />
        <ErrorState v-if="submitError" :message="submitError" />

        <div v-if="message" class="portal-alert-success">
            {{ message }}
        </div>

        <LoadingState v-if="loading" :label="t('spa.loadingUsers')" />

        <template v-else-if="payload">
            <div class="grid gap-6 xl:grid-cols-[minmax(0,1fr)_28rem]">
                <div class="space-y-6">
                    <div class="portal-card rounded-[28px] p-5">
                        <label class="block space-y-2 text-sm font-medium text-slate-700">
                            <span>{{ t('spa.searchUsers') }}</span>
                            <input
                                v-model="search"
                                type="search"
                                :placeholder="t('spa.searchUsersPlaceholder')"
                                class="portal-input"
                            >
                        </label>
                    </div>

                    <div class="portal-card overflow-hidden rounded-[28px]">
                        <div class="border-b border-slate-200 px-6 py-5">
                            <h3 class="portal-display text-2xl font-semibold text-slate-950">{{ t('common.users') }}</h3>
                        </div>

                        <div v-if="filteredUsers.length" class="divide-y divide-slate-200">
                            <div
                                v-for="user in filteredUsers"
                                :key="user.id"
                                class="grid gap-4 px-6 py-5 lg:grid-cols-[minmax(0,1fr)_8rem_12rem]"
                            >
                                <div class="min-w-0">
                                    <p class="text-base font-semibold text-slate-950">{{ user.full_name }}</p>
                                    <p class="mt-1 text-sm portal-muted">{{ user.email }}</p>
                                    <p class="mt-3 text-xs font-semibold uppercase tracking-[0.16em] text-amber-700">
                                        {{ t(`options.roles.${user.primary_role}`) }}
                                    </p>
                                </div>

                                <div>
                                    <p class="text-xs font-semibold uppercase tracking-[0.16em] text-slate-500">{{ t('common.published') }}</p>
                                    <p class="mt-2 text-sm font-semibold text-slate-950">{{ formatPortalDate(user.joined_at, locale) || user.joined_at_label }}</p>
                                </div>

                                <div class="flex flex-wrap gap-2">
                                    <button
                                        type="button"
                                        class="portal-button-secondary"
                                        @click="selectUser(user)"
                                    >
                                        {{ t('common.edit') }}
                                    </button>
                                    <button
                                        type="button"
                                        class="portal-button-danger"
                                        @click="destroyUser(user)"
                                    >
                                        {{ t('common.delete') }}
                                    </button>
                                </div>
                            </div>
                        </div>

                        <EmptyState
                            v-else
                            :eyebrow="t('common.noData')"
                            :title="t('spa.noUsersMatchTitle')"
                            :description="t('spa.noUsersMatchDescription')"
                        />
                    </div>
                </div>

                <div class="space-y-6">
                    <section>
                        <h3 class="portal-display text-2xl font-semibold text-slate-950">{{ t('spa.createUser') }}</h3>
                        <p class="mt-2 text-sm portal-muted">{{ t('spa.adminUsersDescription') }}</p>
                        <UserForm
                            class="mt-5"
                            :submit-label="t('spa.createUser')"
                            :errors="serverErrors"
                            :busy="creating"
                            @submit="createUser"
                        />
                    </section>

                    <section v-if="editingUser">
                        <div class="flex items-center justify-between gap-3">
                            <div>
                                <h3 class="portal-display text-2xl font-semibold text-slate-950">{{ t('common.edit') }} {{ editingUser.full_name }}</h3>
                                <p class="mt-2 text-sm portal-muted">{{ t('spa.adminProfileDescription') }}</p>
                            </div>
                            <button type="button" class="text-sm font-semibold text-slate-700" @click="resetEditing">{{ t('common.cancel') }}</button>
                        </div>

                        <UserForm
                            class="mt-5"
                            :initial-values="editingValues"
                            :submit-label="t('common.save')"
                            :errors="serverErrors"
                            :busy="saving"
                            :require-password="false"
                            @submit="updateUser"
                        />
                    </section>
                </div>
            </div>
        </template>
    </div>
</template>

<script setup>
import { computed, onMounted, ref } from 'vue';
import { useI18n } from 'vue-i18n';
import EmptyState from '@/src/components/shared/EmptyState.vue';
import ErrorState from '@/src/components/shared/ErrorState.vue';
import LoadingState from '@/src/components/shared/LoadingState.vue';
import PageHeader from '@/src/components/shared/PageHeader.vue';
import UserForm from '@/src/components/users/UserForm.vue';
import { useAsyncState } from '@/src/composables/useAsyncState';
import { formatPortalDate } from '@/src/utils/formatters';
import { extractErrorMessage, portalApi } from '@/src/services/api';

const search = ref('');
const editingUser = ref(null);
const message = ref('');
const submitError = ref('');
const serverErrors = ref({});
const creating = ref(false);
const saving = ref(false);
const { locale, t } = useI18n();

const { data: payload, loading, error, execute } = useAsyncState(null);

const filteredUsers = computed(() => {
    const term = search.value.trim().toLowerCase();
    const users = payload.value?.users ?? [];

    if (!term) {
        return users;
    }

    return users.filter((user) =>
        [user.full_name, user.email, user.primary_role]
            .filter(Boolean)
            .some((value) => value.toLowerCase().includes(term)),
    );
});

const editingValues = computed(() => {
    if (!editingUser.value) {
        return {};
    }

    return {
        firstName: editingUser.value.first_name,
        lastName: editingUser.value.last_name,
        email: editingUser.value.email,
        role: editingUser.value.primary_role,
        track: editingUser.value.track,
        level: editingUser.value.level,
        domain: editingUser.value.domain,
    };
});

async function loadUsers() {
    await execute(() => portalApi.getUsers());
}

function resetFeedback() {
    message.value = '';
    submitError.value = '';
    serverErrors.value = {};
}

function selectUser(user) {
    editingUser.value = user;
    resetFeedback();
}

function resetEditing() {
    editingUser.value = null;
    resetFeedback();
}

async function createUser(values) {
    creating.value = true;
    resetFeedback();

    try {
        const response = await portalApi.createUser(values);
        message.value = response.message;
        await loadUsers();
    } catch (requestError) {
        serverErrors.value = requestError.response?.data?.errors ?? {};
        submitError.value = extractErrorMessage(requestError);
    } finally {
        creating.value = false;
    }
}

async function updateUser(values) {
    if (!editingUser.value) {
        return;
    }

    saving.value = true;
    resetFeedback();

    try {
        const response = await portalApi.updateUser(editingUser.value.id, values);
        message.value = response.message;
        editingUser.value = response.user;
        await loadUsers();
    } catch (requestError) {
        serverErrors.value = requestError.response?.data?.errors ?? {};
        submitError.value = extractErrorMessage(requestError);
    } finally {
        saving.value = false;
    }
}

async function destroyUser(user) {
    if (!window.confirm(`${t('spa.deleteUserConfirm')}: ${user.full_name} ?`)) {
        return;
    }

    resetFeedback();

    try {
        const response = await portalApi.deleteUser(user.id);
        message.value = response.message;
        if (editingUser.value?.id === user.id) {
            editingUser.value = null;
        }
        await loadUsers();
    } catch (requestError) {
        submitError.value = extractErrorMessage(requestError);
    }
}

onMounted(() => {
    loadUsers();
});
</script>
