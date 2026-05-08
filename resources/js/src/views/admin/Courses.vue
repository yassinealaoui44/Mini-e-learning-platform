<template>
    <div class="portal-page-grid">
        <PageHeader
            :eyebrow="t('spa.adminCoursesEyebrow')"
            :title="t('spa.adminCoursesTitle')"
            :description="t('spa.adminCoursesDescription')"
        >
            <template #actions>
                <RouterLink
                    :to="{ name: 'admin-course-create' }"
                    class="portal-button-primary"
                >
                    {{ t('spa.newCourseButton') }}
                </RouterLink>
            </template>
        </PageHeader>

        <ErrorState v-if="error" :message="error" />
        <ErrorState v-if="submitError" :message="submitError" />

        <div v-if="message" class="portal-alert-success">
            {{ message }}
        </div>

        <div class="portal-card rounded-[28px] p-5">
            <label class="block space-y-2 text-sm font-medium text-slate-700">
                <span>{{ t('common.search') }} {{ t('common.courses').toLowerCase() }}</span>
                <input
                    v-model="search"
                    type="search"
                    :placeholder="t('spa.searchCoursesPlaceholderStudent')"
                    class="portal-input"
                >
            </label>
        </div>

        <LoadingState v-if="loading" :label="t('spa.loadingCourses')" />

        <div v-else-if="filteredCourses.length" class="grid gap-5 xl:grid-cols-2">
            <CourseCard
                v-for="course in filteredCourses"
                :key="course.id"
                :course="course"
                accent="admin"
                :primary-to="{ name: 'admin-course-edit', params: { id: course.id } }"
                :primary-label="t('common.edit')"
            >
                <template #actions>
                    <RouterLink
                        :to="{ name: 'admin-course-edit', params: { id: course.id } }"
                        class="portal-button-primary"
                    >
                        {{ t('common.edit') }}
                    </RouterLink>
                    <RouterLink
                        :to="{ name: 'admin-course-lessons', params: { id: course.id } }"
                        class="portal-button-secondary"
                    >
                        {{ t('common.lessons') }}
                    </RouterLink>
                    <button
                        type="button"
                        class="portal-button-danger"
                        @click="destroyCourse(course)"
                    >
                        {{ t('common.delete') }}
                    </button>
                </template>
            </CourseCard>
        </div>

        <EmptyState
            v-else
            :eyebrow="t('common.noData')"
            :title="t('spa.noTutorCoursesTitle')"
            :description="t('spa.addCourseForPlatform')"
        />
    </div>
</template>

<script setup>
import { computed, onMounted, ref } from 'vue';
import { RouterLink } from 'vue-router';
import { useI18n } from 'vue-i18n';
import CourseCard from '@/src/components/courses/CourseCard.vue';
import EmptyState from '@/src/components/shared/EmptyState.vue';
import ErrorState from '@/src/components/shared/ErrorState.vue';
import LoadingState from '@/src/components/shared/LoadingState.vue';
import PageHeader from '@/src/components/shared/PageHeader.vue';
import { useAsyncState } from '@/src/composables/useAsyncState';
import { extractErrorMessage, portalApi } from '@/src/services/api';

const search = ref('');
const message = ref('');
const submitError = ref('');
const { t } = useI18n();
const { data: payload, loading, error, execute } = useAsyncState({ courses: [] });

const filteredCourses = computed(() => {
    const term = search.value.trim().toLowerCase();
    const courses = payload.value.courses ?? [];

    if (!term) {
        return courses;
    }

    return courses.filter((course) =>
        [course.title, course.track, course.tutor_name]
            .filter(Boolean)
            .some((value) => value.toLowerCase().includes(term)),
    );
});

async function loadCourses() {
    await execute(() => portalApi.getCourses({ scope: 'all' }));
}

async function destroyCourse(course) {
    if (!window.confirm(`${t('spa.deleteCourseConfirm')}: ${course.title} ?`)) {
        return;
    }

    message.value = '';
    submitError.value = '';

    try {
        const response = await portalApi.deleteCourse(course.id);
        message.value = response.message;
        await loadCourses();
    } catch (requestError) {
        submitError.value = extractErrorMessage(requestError);
    }
}

onMounted(() => {
    loadCourses();
});
</script>
