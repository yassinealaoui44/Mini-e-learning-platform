<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, router, usePage } from '@inertiajs/vue3';
import { ref } from 'vue';

defineProps({
    stats: {
        type: Object,
        required: true,
    },
    enrolledCourses: {
        type: Array,
        default: () => [],
    },
    recommendedCourses: {
        type: Array,
        default: () => [],
    },
    otherCourses: {
        type: Array,
        default: () => [],
    },
    recentLessons: {
        type: Array,
        default: () => [],
    },
});

const user = usePage().props.auth.user;
const enrollingCourseId = ref(null);

const enroll = (courseId) => {
    enrollingCourseId.value = courseId;

    router.post(route('etudiant.cours.enroll', courseId), {}, {
        preserveScroll: true,
        preserveState: false,
        onFinish: () => {
            enrollingCourseId.value = null;
        },
    });
};
</script>

<template>
    <Head title="Dashboard Etudiant" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h2 class="text-2xl font-semibold text-slate-900">
                        Espace Etudiant
                    </h2>
                    <p class="text-sm text-slate-500">
                        Suivez vos cours, trouvez de nouveaux contenus et avancez a votre rythme.
                    </p>
                </div>

                <div class="flex flex-wrap gap-2 text-xs font-medium">
                    <span class="rounded-full bg-orange-100 px-3 py-1 text-orange-700">
                        Filiere: {{ user.filiere || 'A renseigner' }}
                    </span>
                    <span class="rounded-full bg-amber-100 px-3 py-1 text-amber-700">
                        Niveau: {{ user.niveau || 'A renseigner' }}
                    </span>
                </div>
            </div>
        </template>

        <div class="py-10">
            <div class="mx-auto max-w-7xl space-y-8 px-4 sm:px-6 lg:px-8">
                <section class="overflow-hidden rounded-3xl bg-gradient-to-r from-rose-950 via-orange-800 to-amber-500 text-white shadow-xl">
                    <div class="grid gap-8 px-6 py-8 lg:grid-cols-[1.5fr,1fr] lg:px-10">
                        <div class="space-y-4">
                            <span class="inline-flex rounded-full border border-white/20 bg-white/10 px-3 py-1 text-xs font-semibold uppercase tracking-[0.2em] text-orange-100">
                                Tableau de progression
                            </span>

                            <div class="space-y-3">
                                <h1 class="text-3xl font-semibold leading-tight sm:text-4xl">
                                    Bienvenue {{ user.prenom }} {{ user.nom }}, votre parcours continue ici.
                                </h1>
                                <p class="max-w-2xl text-sm text-orange-50/90 sm:text-base">
                                    Retrouvez vos cours en cours, les dernieres lecons disponibles et une selection de contenus
                                    adaptes a votre filiere.
                                </p>
                            </div>

                            <div class="flex flex-wrap gap-3">
                                <a
                                    href="#mes-cours"
                                    class="inline-flex items-center rounded-full bg-white px-5 py-2.5 text-sm font-semibold text-slate-900 transition hover:bg-slate-100"
                                >
                                    Voir mes cours
                                </a>
                                <a
                                    href="#decouverte"
                                    class="inline-flex items-center rounded-full border border-white/25 px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-white/10"
                                >
                                    Explorer de nouveaux cours
                                </a>
                            </div>
                        </div>

                        <div class="rounded-3xl border border-white/15 bg-white/10 p-5 backdrop-blur">
                            <p class="text-sm font-medium text-orange-100">Focus du moment</p>
                            <div class="mt-4 space-y-4">
                                <div class="rounded-2xl bg-white/10 p-4">
                                    <p class="text-xs uppercase tracking-[0.18em] text-orange-100/80">Cours suivis</p>
                                    <p class="mt-2 text-3xl font-semibold">{{ stats.enrolled_courses }}</p>
                                </div>
                                <div class="rounded-2xl bg-white/10 p-4">
                                    <p class="text-xs uppercase tracking-[0.18em] text-orange-100/80">Lecons disponibles</p>
                                    <p class="mt-2 text-3xl font-semibold">{{ stats.total_lessons }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                <section class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
                    <div class="rounded-2xl border border-orange-200 bg-gradient-to-br from-white to-orange-50 p-5 shadow-sm">
                        <p class="text-sm font-medium text-orange-700">Cours suivis</p>
                        <p class="mt-3 text-3xl font-semibold text-rose-950">{{ stats.enrolled_courses }}</p>
                        <p class="mt-2 text-sm text-orange-800/70">Vos inscriptions actives depuis le tableau de bord.</p>
                    </div>

                    <div class="rounded-2xl border border-amber-200 bg-gradient-to-br from-white to-amber-50 p-5 shadow-sm">
                        <p class="text-sm font-medium text-amber-700">Cours de votre filiere</p>
                        <p class="mt-3 text-3xl font-semibold text-rose-950">{{ stats.matching_courses }}</p>
                        <p class="mt-2 text-sm text-amber-800/70">Selection disponible pour {{ user.filiere || 'votre profil' }}.</p>
                    </div>

                    <div class="rounded-2xl border border-rose-200 bg-gradient-to-br from-white to-rose-50 p-5 shadow-sm">
                        <p class="text-sm font-medium text-rose-700">Lecons accessibles</p>
                        <p class="mt-3 text-3xl font-semibold text-rose-950">{{ stats.total_lessons }}</p>
                        <p class="mt-2 text-sm text-rose-800/70">Total des lecons attachees a vos cours actuels.</p>
                    </div>

                    <div class="rounded-2xl border border-orange-200 bg-gradient-to-br from-white to-orange-50 p-5 shadow-sm">
                        <p class="text-sm font-medium text-orange-700">Tuteurs actifs</p>
                        <p class="mt-3 text-3xl font-semibold text-rose-950">{{ stats.active_tutors }}</p>
                        <p class="mt-2 text-sm text-orange-800/70">Intervenants qui proposent des cours dans votre espace.</p>
                    </div>
                </section>

                <section id="mes-cours" class="space-y-4">
                    <div class="flex items-end justify-between gap-4">
                        <div>
                            <h3 class="text-2xl font-semibold text-slate-900">Mes cours</h3>
                            <p class="text-sm text-slate-500">
                                Tous les cours auxquels vous etes deja inscrit.
                            </p>
                        </div>
                    </div>

                    <div
                        v-if="enrolledCourses.length === 0"
                        class="rounded-3xl border border-dashed border-slate-300 bg-white px-6 py-10 text-center shadow-sm"
                    >
                        <p class="text-lg font-semibold text-slate-900">Aucune inscription pour le moment</p>
                        <p class="mt-2 text-sm text-slate-500">
                            Commencez par rejoindre un cours dans la section decouverte.
                        </p>
                        <a
                            href="#decouverte"
                            class="mt-5 inline-flex items-center rounded-full bg-rose-900 px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-rose-800"
                        >
                            Voir les cours disponibles
                        </a>
                    </div>

                    <div v-else class="grid gap-5 lg:grid-cols-2">
                        <article
                            v-for="course in enrolledCourses"
                            :key="course.id"
                            class="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm"
                        >
                            <div class="grid gap-0 md:grid-cols-[180px,1fr]">
                                <div
                                    class="flex min-h-[180px] items-center justify-center bg-gradient-to-br from-orange-100 via-amber-100 to-rose-100"
                                >
                                    <img
                                        v-if="course.thumbnail_url"
                                        :src="course.thumbnail_url"
                                        :alt="course.nom"
                                        class="h-full w-full object-cover"
                                    >
                                    <span v-else class="px-6 text-center text-sm font-medium text-slate-500">
                                        Apercu du cours
                                    </span>
                                </div>

                                <div class="space-y-4 p-6">
                                    <div class="flex flex-wrap items-start justify-between gap-3">
                                        <div>
                                            <div class="flex flex-wrap gap-2 text-xs font-medium">
                                                <span class="rounded-full bg-orange-100 px-3 py-1 text-orange-700">
                                                    {{ course.filiere }}
                                                </span>
                                                <span class="rounded-full bg-slate-100 px-3 py-1 text-slate-600">
                                                    {{ course.students_count }} etudiant(s)
                                                </span>
                                            </div>
                                            <h4 class="mt-3 text-xl font-semibold text-slate-900">{{ course.nom }}</h4>
                                            <p class="mt-1 text-sm text-slate-500">
                                                Tuteur: {{ course.tuteur_nom || 'Non renseigne' }}
                                            </p>
                                            <p class="mt-1 text-sm text-slate-500">
                                                Inscrit le {{ course.enrolled_at || 'recemment' }}
                                            </p>
                                        </div>

                                        <div class="rounded-2xl bg-rose-50 px-4 py-3 text-center">
                                            <p class="text-xs uppercase tracking-[0.18em] text-rose-400">Lecons</p>
                                            <p class="mt-1 text-2xl font-semibold text-rose-950">{{ course.lessons_count }}</p>
                                        </div>
                                    </div>

                                    <div>
                                        <p class="text-sm font-semibold text-slate-800">Dernieres lecons</p>
                                        <div v-if="course.latest_lessons.length" class="mt-3 space-y-3">
                                            <div
                                                v-for="lesson in course.latest_lessons"
                                                :key="lesson.id"
                                                class="flex items-center justify-between gap-4 rounded-2xl border border-slate-200 px-4 py-3"
                                            >
                                                <div>
                                                    <p class="font-medium text-slate-900">{{ lesson.titre }}</p>
                                                    <p class="text-sm text-slate-500">Ajoutee le {{ lesson.created_at }}</p>
                                                </div>
                                                <span
                                                    class="rounded-full px-3 py-1 text-xs font-semibold uppercase"
                                                    :class="lesson.type === 'video' ? 'bg-rose-100 text-rose-700' : 'bg-orange-100 text-orange-700'"
                                                >
                                                    {{ lesson.type }}
                                                </span>
                                            </div>
                                        </div>
                                        <p v-else class="mt-3 text-sm text-slate-500">
                                            Ce cours n'a pas encore de lecon.
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </article>
                    </div>
                </section>

                <section
                    v-if="recentLessons.length"
                    class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm"
                >
                    <div class="flex items-end justify-between gap-4">
                        <div>
                            <h3 class="text-2xl font-semibold text-slate-900">Nouvelles lecons</h3>
                            <p class="text-sm text-slate-500">
                                Les ajouts les plus recents dans vos cours.
                            </p>
                        </div>
                    </div>

                    <div class="mt-5 grid gap-4 lg:grid-cols-3">
                        <article
                            v-for="lesson in recentLessons"
                            :key="lesson.id"
                            class="rounded-2xl border border-slate-200 bg-slate-50 p-4"
                        >
                            <div class="flex items-start justify-between gap-3">
                                <div>
                                    <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">
                                        {{ lesson.cours_nom || 'Cours' }}
                                    </p>
                                    <h4 class="mt-2 text-lg font-semibold text-slate-900">{{ lesson.titre }}</h4>
                                </div>
                                <span
                                class="rounded-full px-3 py-1 text-xs font-semibold uppercase"
                                :class="lesson.type === 'video' ? 'bg-rose-100 text-rose-700' : 'bg-orange-100 text-orange-700'"
                                >
                                    {{ lesson.type }}
                                </span>
                            </div>

                            <p class="mt-3 text-sm text-slate-500">Mis en ligne le {{ lesson.created_at }}</p>

                            <a
                                v-if="lesson.file_url"
                                :href="lesson.file_url"
                                target="_blank"
                                class="mt-4 inline-flex items-center text-sm font-semibold text-rose-700 transition hover:text-rose-600"
                            >
                                Ouvrir la ressource
                            </a>
                        </article>
                    </div>
                </section>

                <section id="decouverte" class="space-y-4">
                    <div>
                        <h3 class="text-2xl font-semibold text-slate-900">Cours recommandes</h3>
                        <p class="text-sm text-slate-500">
                            Une selection prioritaire pour votre filiere et votre niveau d'avancement.
                        </p>
                    </div>

                    <div
                        v-if="recommendedCourses.length === 0"
                        class="rounded-3xl border border-dashed border-slate-300 bg-white px-6 py-10 text-center shadow-sm"
                    >
                        <p class="text-lg font-semibold text-slate-900">Aucun cours recommande pour l'instant</p>
                        <p class="mt-2 text-sm text-slate-500">
                            Les cours apparaitront ici des qu'un tuteur publiera du contenu dans votre filiere.
                        </p>
                    </div>

                    <div v-else class="grid gap-5 lg:grid-cols-3">
                        <article
                            v-for="course in recommendedCourses"
                            :key="course.id"
                            class="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm"
                        >
                            <div class="flex min-h-[180px] items-center justify-center bg-gradient-to-br from-amber-100 via-orange-100 to-rose-100">
                                <img
                                    v-if="course.thumbnail_url"
                                    :src="course.thumbnail_url"
                                    :alt="course.nom"
                                    class="h-full w-full object-cover"
                                >
                                <span v-else class="px-6 text-center text-sm font-medium text-slate-500">
                                    Nouveau cours a decouvrir
                                </span>
                            </div>

                            <div class="space-y-4 p-6">
                                <div>
                                    <div class="flex flex-wrap gap-2 text-xs font-medium">
                                        <span class="rounded-full bg-amber-100 px-3 py-1 text-amber-700">
                                            {{ course.filiere }}
                                        </span>
                                        <span class="rounded-full bg-slate-100 px-3 py-1 text-slate-600">
                                            {{ course.lessons_count }} lecon(s)
                                        </span>
                                    </div>
                                    <h4 class="mt-3 text-xl font-semibold text-slate-900">{{ course.nom }}</h4>
                                    <p class="mt-1 text-sm text-slate-500">
                                        Tuteur: {{ course.tuteur_nom || 'Non renseigne' }}
                                    </p>
                                    <p class="mt-1 text-sm text-slate-500">
                                        {{ course.students_count }} inscription(s) au total
                                    </p>
                                </div>

                                <button
                                    type="button"
                                    class="inline-flex w-full items-center justify-center rounded-full bg-rose-900 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-rose-800 disabled:cursor-not-allowed disabled:opacity-60"
                                    :disabled="enrollingCourseId === course.id"
                                    @click="enroll(course.id)"
                                >
                                    {{ enrollingCourseId === course.id ? 'Inscription...' : 'Rejoindre ce cours' }}
                                </button>
                            </div>
                        </article>
                    </div>
                </section>

                <section v-if="otherCourses.length" class="space-y-4">
                    <div>
                        <h3 class="text-2xl font-semibold text-slate-900">Explorer d'autres filieres</h3>
                        <p class="text-sm text-slate-500">
                            Pour aller plus loin ou elargir votre parcours.
                        </p>
                    </div>

                    <div class="grid gap-5 lg:grid-cols-2">
                        <article
                            v-for="course in otherCourses"
                            :key="course.id"
                            class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm"
                        >
                            <div class="flex flex-wrap items-start justify-between gap-4">
                                <div>
                                    <span class="rounded-full bg-orange-100 px-3 py-1 text-xs font-semibold text-orange-700">
                                        {{ course.filiere }}
                                    </span>
                                    <h4 class="mt-3 text-xl font-semibold text-slate-900">{{ course.nom }}</h4>
                                    <p class="mt-1 text-sm text-slate-500">
                                        Tuteur: {{ course.tuteur_nom || 'Non renseigne' }}
                                    </p>
                                </div>

                                <button
                                    type="button"
                                    class="inline-flex items-center justify-center rounded-full border border-rose-300 px-4 py-2 text-sm font-semibold text-rose-700 transition hover:border-rose-400 hover:bg-rose-50 disabled:cursor-not-allowed disabled:opacity-60"
                                    :disabled="enrollingCourseId === course.id"
                                    @click="enroll(course.id)"
                                >
                                    {{ enrollingCourseId === course.id ? 'Inscription...' : 'S inscrire' }}
                                </button>
                            </div>
                        </article>
                    </div>
                </section>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
