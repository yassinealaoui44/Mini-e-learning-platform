<template>
    <div class="portal-page-grid">
        <PageHeader
            :eyebrow="t('spa.adminLessonsEyebrow')"
            :title="t('spa.adminLessonsTitle')"
            :description="t('spa.adminLessonsDescription')"
        />

        <ErrorState v-if="error" :message="error" />
        <ErrorState v-if="submitError" :message="submitError" />

        <div v-if="message" class="portal-alert-success">
            {{ message }}
        </div>

        <div class="portal-card rounded-[28px] p-5">
            <label class="block space-y-2 text-sm font-medium text-slate-700">
                <span>{{ t('spa.searchLessons') }}</span>
                <input
                    v-model="search"
                    type="search"
                    :placeholder="t('spa.searchLessonsPlaceholder')"
                    class="portal-input"
                >
            </label>
        </div>

        <LoadingState v-if="loading" :label="t('spa.loadingLessons')" />

        <template v-else-if="payload">
            <div class="grid gap-6 xl:grid-cols-[minmax(0,1fr)_28rem]">
                <div class="portal-card overflow-hidden rounded-[28px]">
                    <div class="border-b border-slate-200 px-6 py-5">
                        <h3 class="portal-display text-2xl font-semibold text-slate-950">{{ t('common.lessons') }}</h3>
                    </div>

                    <div v-if="filteredLessons.length" class="divide-y divide-slate-200">
                        <div
                            v-for="lesson in filteredLessons"
                            :key="lesson.id"
                            class="grid gap-4 px-6 py-5 lg:grid-cols-[minmax(0,1fr)_12rem]"
                        >
                            <div class="min-w-0">
                                <p class="text-xs font-semibold uppercase tracking-[0.16em] text-slate-500">{{ lesson.type === 'video' ? t('common.video') : 'PDF' }}</p>
                                <p class="mt-2 text-base font-semibold text-slate-950">{{ lesson.title }}</p>
                                <p class="mt-1 text-sm portal-muted">{{ lesson.course?.title }}</p>
                            </div>

                            <div class="flex flex-wrap gap-2">
                                <button
                                    type="button"
                                    class="portal-button-secondary"
                                    @click="selectedLesson = lesson"
                                >
                                    {{ t('common.edit') }}
                                </button>
                                <button
                                    type="button"
                                    class="portal-button-danger"
                                    @click="destroyLesson(lesson)"
                                >
                                    {{ t('common.delete') }}
                                </button>
                            </div>
                        </div>
                    </div>

                    <EmptyState
                        v-else
                        :eyebrow="t('common.noData')"
                        :title="t('spa.noAdminLessonsTitle')"
                        :description="t('spa.noAdminLessonsDescription')"
                    />
                </div>

                <div v-if="selectedLesson" class="space-y-4">
                    <div>
                        <h3 class="portal-display text-2xl font-semibold text-slate-950">{{ t('common.edit') }} {{ selectedLesson.title }}</h3>
                        <p class="mt-2 text-sm portal-muted">{{ t('spa.adminLessonsDescription') }}</p>
                    </div>

                    <LessonEditorForm
                        :initial-values="{ title: selectedLesson.title, type: selectedLesson.type }"
                        :submit-label="t('common.save')"
                        :errors="serverErrors"
                        :busy="saving"
                        @submit="updateLesson"
                    />
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
import LessonEditorForm from '@/src/components/lessons/LessonEditorForm.vue';
import { useAsyncState } from '@/src/composables/useAsyncState';
import { extractErrorMessage, portalApi } from '@/src/services/api';

const search = ref('');
const selectedLesson = ref(null);
const message = ref('');
const submitError = ref('');
const serverErrors = ref({});
const saving = ref(false);
const { t } = useI18n();
const { data: payload, loading, error, execute } = useAsyncState({ lessons: [] });

const filteredLessons = computed(() => {
    const term = search.value.trim().toLowerCase();
    const lessons = payload.value.lessons ?? [];

    if (!term) {
        return lessons;
    }

    return lessons.filter((lesson) =>
        [lesson.title, lesson.course?.title]
            .filter(Boolean)
            .some((value) => value.toLowerCase().includes(term)),
    );
});

async function loadLessons() {
    await execute(() => portalApi.getLessons());
}

async function updateLesson(values) {
    if (!selectedLesson.value) {
        return;
    }

    saving.value = true;
    message.value = '';
    submitError.value = '';
    serverErrors.value = {};

    try {
        const response = await portalApi.updateLesson(selectedLesson.value.id, values);
        message.value = response.message;
        selectedLesson.value = response.lesson;
        await loadLessons();
    } catch (requestError) {
        serverErrors.value = requestError.response?.data?.errors ?? {};
        submitError.value = extractErrorMessage(requestError);
    } finally {
        saving.value = false;
    }
}

async function destroyLesson(lesson) {
    if (!window.confirm(`${t('spa.deleteLessonConfirm')}: ${lesson.title} ?`)) {
        return;
    }

    message.value = '';
    submitError.value = '';

    try {
        const response = await portalApi.deleteLesson(lesson.id);
        message.value = response.message;
        if (selectedLesson.value?.id === lesson.id) {
            selectedLesson.value = null;
        }
        await loadLessons();
    } catch (requestError) {
        submitError.value = extractErrorMessage(requestError);
    }
}

onMounted(() => {
    loadLessons();
});
</script>
