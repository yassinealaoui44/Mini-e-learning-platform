import axios from 'axios';

const apiClient = axios.create({
    baseURL: '/api',
    withCredentials: true,
    headers: {
        Accept: 'application/json',
        'X-Requested-With': 'XMLHttpRequest',
        'X-Locale': typeof window !== 'undefined' ? window.localStorage.getItem('academyhub.locale') ?? document.documentElement.lang ?? 'en' : 'en',
    },
});

apiClient.interceptors.response.use(
    (response) => response,
    (error) => {
        if (error.response?.status === 401) {
            window.location.assign('/login');
        }

        return Promise.reject(error);
    },
);

function defaultFallbackMessage() {
    return typeof document !== 'undefined' && document.documentElement.lang === 'fr'
        ? 'Nous n’avons pas pu charger cette section pour le moment.'
        : 'We could not load this section right now.';
}

export function extractErrorMessage(error, fallback = defaultFallbackMessage()) {
    if (error.response?.data?.message) {
        return error.response.data.message;
    }

    if (typeof error.message === 'string' && error.message.length > 0) {
        return error.message;
    }

    return fallback;
}

export function setApiLocale(locale) {
    apiClient.defaults.headers.common['X-Locale'] = locale;
}

function appendFormValue(formData, key, value) {
    if (value === undefined || value === null || value === '') {
        return;
    }

    if (Array.isArray(value)) {
        value.forEach((item) => appendFormValue(formData, `${key}[]`, item));
        return;
    }

    formData.append(key, value);
}

export function toFormData(payload) {
    const formData = new FormData();

    Object.entries(payload).forEach(([key, value]) => appendFormValue(formData, key, value));

    return formData;
}

function sendMultipart(url, payload, method = 'post') {
    if (method === 'post') {
        return apiClient.post(url, toFormData(payload), {
            headers: {
                'Content-Type': 'multipart/form-data',
            },
        });
    }

    return apiClient.post(
        url,
        toFormData({
            ...payload,
            _method: method.toUpperCase(),
        }),
        {
            headers: {
                'Content-Type': 'multipart/form-data',
            },
        },
    );
}

export const portalApi = {
    getSession() {
        return apiClient.get('/me').then(({ data }) => data);
    },
    logout() {
        return axios.post('/logout');
    },
    getStudentDashboard() {
        return apiClient.get('/student/dashboard').then(({ data }) => data);
    },
    getCourses(params = {}) {
        return apiClient.get('/courses', { params }).then(({ data }) => data);
    },
    getCourse(courseId, params = {}) {
        return apiClient.get(`/courses/${courseId}`, { params }).then(({ data }) => data.course);
    },
    enrollInCourse(courseId) {
        return apiClient.post(`/student/courses/${courseId}/enroll`).then(({ data }) => data);
    },
    getLesson(lessonId) {
        return apiClient.get(`/lessons/${lessonId}`).then(({ data }) => data.lesson);
    },
    getStudentProgress() {
        return apiClient.get('/student/progress').then(({ data }) => data);
    },
    getProfile() {
        return apiClient.get('/profile').then(({ data }) => data);
    },
    updateProfile(payload) {
        return apiClient.patch('/profile', payload).then(({ data }) => data);
    },
    getTutorDashboard() {
        return apiClient.get('/tutor/dashboard').then(({ data }) => data);
    },
    getAdminDashboard() {
        return apiClient.get('/admin/dashboard').then(({ data }) => data);
    },
    createCourse(payload) {
        return sendMultipart('/courses', payload).then(({ data }) => data);
    },
    updateCourse(courseId, payload) {
        return sendMultipart(`/courses/${courseId}`, payload, 'put').then(({ data }) => data);
    },
    deleteCourse(courseId) {
        return apiClient.delete(`/courses/${courseId}`).then(({ data }) => data);
    },
    createLessonForCourse(courseId, payload) {
        return sendMultipart(`/courses/${courseId}/lessons`, payload).then(({ data }) => data);
    },
    getLessons(params = {}) {
        return apiClient.get('/lessons', { params }).then(({ data }) => data);
    },
    createLesson(payload) {
        return sendMultipart('/lessons', payload).then(({ data }) => data);
    },
    updateLesson(lessonId, payload) {
        return sendMultipart(`/lessons/${lessonId}`, payload, 'put').then(({ data }) => data);
    },
    deleteLesson(lessonId) {
        return apiClient.delete(`/lessons/${lessonId}`).then(({ data }) => data);
    },
    getUsers() {
        return apiClient.get('/users').then(({ data }) => data);
    },
    createUser(payload) {
        return apiClient.post('/users', payload).then(({ data }) => data);
    },
    updateUser(userId, payload) {
        return apiClient.put(`/users/${userId}`, payload).then(({ data }) => data);
    },
    deleteUser(userId) {
        return apiClient.delete(`/users/${userId}`).then(({ data }) => data);
    },
    getTutorStudents() {
        return apiClient.get('/tutor/students').then(({ data }) => data);
    },
    getTutorAnalytics() {
        return apiClient.get('/tutor/analytics').then(({ data }) => data);
    },
};
