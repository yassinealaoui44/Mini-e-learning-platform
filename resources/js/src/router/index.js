import { createRouter, createWebHistory } from 'vue-router';
import i18n from '@/i18n';
import { initializeSession, sessionState } from '@/src/stores/session';

const routes = [
    {
        path: '/dashboard',
        beforeEnter: async () => {
            await initializeSession();

            if (!sessionState.role) {
                return '/login';
            }

            return sessionState.role === 'tutor'
                ? { name: 'tutor-dashboard' }
                : sessionState.role === 'admin'
                  ? { name: 'admin-dashboard' }
                  : { name: 'student-dashboard' };
        },
    },
    {
        path: '/student',
        component: () => import('@/src/layouts/StudentLayout.vue'),
        meta: {
            portal: 'student',
        },
        children: [
            { path: '', redirect: { name: 'student-dashboard' } },
            {
                path: 'dashboard',
                name: 'student-dashboard',
                component: () => import('@/src/views/student/Dashboard.vue'),
                meta: {
                    titleKey: 'routes.student.dashboardTitle',
                    subtitleKey: 'routes.student.dashboardSubtitle',
                },
            },
            {
                path: 'courses',
                name: 'student-courses',
                component: () => import('@/src/views/student/Courses.vue'),
                meta: {
                    titleKey: 'routes.student.coursesTitle',
                    subtitleKey: 'routes.student.coursesSubtitle',
                },
            },
            {
                path: 'courses/:id',
                name: 'student-course-details',
                component: () => import('@/src/views/student/CourseDetails.vue'),
                meta: {
                    titleKey: 'routes.student.courseDetailsTitle',
                    subtitleKey: 'routes.student.courseDetailsSubtitle',
                },
            },
            {
                path: 'lessons/:id',
                name: 'student-lesson',
                component: () => import('@/src/views/student/LessonView.vue'),
                meta: {
                    titleKey: 'routes.student.lessonTitle',
                    subtitleKey: 'routes.student.lessonSubtitle',
                },
            },
            {
                path: 'discover',
                name: 'student-discover',
                component: () => import('@/src/views/student/Discover.vue'),
                meta: {
                    titleKey: 'routes.student.discoverTitle',
                    subtitleKey: 'routes.student.discoverSubtitle',
                },
            },
            {
                path: 'progress',
                name: 'student-progress',
                component: () => import('@/src/views/student/Progress.vue'),
                meta: {
                    titleKey: 'routes.student.progressTitle',
                    subtitleKey: 'routes.student.progressSubtitle',
                },
            },
            {
                path: 'profile',
                name: 'student-profile',
                component: () => import('@/src/views/student/Profile.vue'),
                meta: {
                    titleKey: 'routes.student.profileTitle',
                    subtitleKey: 'routes.student.profileSubtitle',
                },
            },
        ],
    },
    {
        path: '/tutor',
        component: () => import('@/src/layouts/TutorLayout.vue'),
        meta: {
            portal: 'tutor',
        },
        children: [
            { path: '', redirect: { name: 'tutor-dashboard' } },
            {
                path: 'dashboard',
                name: 'tutor-dashboard',
                component: () => import('@/src/views/tutor/Dashboard.vue'),
                meta: {
                    titleKey: 'routes.tutor.dashboardTitle',
                    subtitleKey: 'routes.tutor.dashboardSubtitle',
                },
            },
            {
                path: 'courses',
                name: 'tutor-courses',
                component: () => import('@/src/views/tutor/Courses.vue'),
                meta: {
                    titleKey: 'routes.tutor.coursesTitle',
                    subtitleKey: 'routes.tutor.coursesSubtitle',
                },
            },
            {
                path: 'courses/create',
                name: 'tutor-course-create',
                component: () => import('@/src/views/tutor/CourseCreate.vue'),
                meta: {
                    titleKey: 'routes.tutor.createCourseTitle',
                    subtitleKey: 'routes.tutor.createCourseSubtitle',
                },
            },
            {
                path: 'courses/:id/edit',
                name: 'tutor-course-edit',
                component: () => import('@/src/views/tutor/CourseEdit.vue'),
                meta: {
                    titleKey: 'routes.tutor.editCourseTitle',
                    subtitleKey: 'routes.tutor.editCourseSubtitle',
                },
            },
            {
                path: 'courses/:id/lessons',
                name: 'tutor-course-lessons',
                component: () => import('@/src/views/tutor/CourseLessons.vue'),
                meta: {
                    titleKey: 'routes.tutor.lessonsTitle',
                    subtitleKey: 'routes.tutor.lessonsSubtitle',
                },
            },
            {
                path: 'lessons/:id',
                name: 'tutor-lesson-viewer',
                component: () => import('@/src/views/tutor/LessonView.vue'),
                meta: {
                    titleKey: 'routes.tutor.lessonsTitle',
                    subtitleKey: 'routes.tutor.lessonsSubtitle',
                },
            },
            {
                path: 'students',
                name: 'tutor-students',
                component: () => import('@/src/views/tutor/Students.vue'),
                meta: {
                    titleKey: 'routes.tutor.studentsTitle',
                    subtitleKey: 'routes.tutor.studentsSubtitle',
                },
            },
            {
                path: 'analytics',
                name: 'tutor-analytics',
                component: () => import('@/src/views/tutor/Analytics.vue'),
                meta: {
                    titleKey: 'routes.tutor.analyticsTitle',
                    subtitleKey: 'routes.tutor.analyticsSubtitle',
                },
            },
            {
                path: 'profile',
                name: 'tutor-profile',
                component: () => import('@/src/views/tutor/Profile.vue'),
                meta: {
                    titleKey: 'routes.tutor.profileTitle',
                    subtitleKey: 'routes.tutor.profileSubtitle',
                },
            },
        ],
    },
    {
        path: '/admin',
        component: () => import('@/src/layouts/AdminLayout.vue'),
        meta: {
            portal: 'admin',
        },
        children: [
            { path: '', redirect: { name: 'admin-dashboard' } },
            {
                path: 'dashboard',
                name: 'admin-dashboard',
                component: () => import('@/src/views/admin/Dashboard.vue'),
                meta: {
                    titleKey: 'routes.admin.dashboardTitle',
                    subtitleKey: 'routes.admin.dashboardSubtitle',
                },
            },
            {
                path: 'users',
                name: 'admin-users',
                component: () => import('@/src/views/admin/Users.vue'),
                meta: {
                    titleKey: 'routes.admin.usersTitle',
                    subtitleKey: 'routes.admin.usersSubtitle',
                },
            },
            {
                path: 'courses',
                name: 'admin-courses',
                component: () => import('@/src/views/admin/Courses.vue'),
                meta: {
                    titleKey: 'routes.admin.coursesTitle',
                    subtitleKey: 'routes.admin.coursesSubtitle',
                },
            },
            {
                path: 'courses/create',
                name: 'admin-course-create',
                component: () => import('@/src/views/admin/CourseCreate.vue'),
                meta: {
                    titleKey: 'routes.admin.createCourseTitle',
                    subtitleKey: 'routes.admin.createCourseSubtitle',
                },
            },
            {
                path: 'courses/:id/edit',
                name: 'admin-course-edit',
                component: () => import('@/src/views/admin/CourseEdit.vue'),
                meta: {
                    titleKey: 'routes.admin.editCourseTitle',
                    subtitleKey: 'routes.admin.editCourseSubtitle',
                },
            },
            {
                path: 'courses/:id/lessons',
                name: 'admin-course-lessons',
                component: () => import('@/src/views/admin/CourseLessons.vue'),
                meta: {
                    titleKey: 'routes.admin.courseLessonsTitle',
                    subtitleKey: 'routes.admin.courseLessonsSubtitle',
                },
            },
            {
                path: 'lessons/:id/view',
                name: 'admin-lesson-viewer',
                component: () => import('@/src/views/admin/LessonView.vue'),
                meta: {
                    titleKey: 'routes.admin.lessonsTitle',
                    subtitleKey: 'routes.admin.lessonsSubtitle',
                },
            },
            {
                path: 'lessons',
                name: 'admin-lessons',
                component: () => import('@/src/views/admin/Lessons.vue'),
                meta: {
                    titleKey: 'routes.admin.lessonsTitle',
                    subtitleKey: 'routes.admin.lessonsSubtitle',
                },
            },
            {
                path: 'profile',
                name: 'admin-profile',
                component: () => import('@/src/views/admin/Profile.vue'),
                meta: {
                    titleKey: 'routes.admin.profileTitle',
                    subtitleKey: 'routes.admin.profileSubtitle',
                },
            },
        ],
    },
    {
        path: '/:pathMatch(.*)*',
        name: 'not-found',
        component: () => import('@/src/views/NotFound.vue'),
        meta: {
            titleKey: 'routes.notFound.title',
            subtitleKey: 'routes.notFound.subtitle',
        },
    },
];

const router = createRouter({
    history: createWebHistory(),
    routes,
    scrollBehavior() {
        return { top: 0 };
    },
});

router.beforeEach(async (to) => {
    if (!to.meta.portal) {
        return true;
    }

    await initializeSession();

    if (!sessionState.role) {
        return '/login';
    }

    if (to.meta.portal !== sessionState.role) {
        return sessionState.role === 'tutor'
            ? { name: 'tutor-dashboard' }
            : sessionState.role === 'admin'
              ? { name: 'admin-dashboard' }
              : { name: 'student-dashboard' };
    }

    return true;
});

router.afterEach((to) => {
    const routeTitle = typeof to.meta.titleKey === 'string'
        ? i18n.global.t(to.meta.titleKey)
        : i18n.global.t('common.appName');

    document.title = `${routeTitle} | ${i18n.global.t('common.appName')}`;
});

export default router;
