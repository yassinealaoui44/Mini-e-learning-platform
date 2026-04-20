<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, router, usePage } from '@inertiajs/vue3';
import { computed, ref } from 'vue';

const props = defineProps({
    stats: {
        type: Object,
        required: true,
    },
    roleBreakdown: {
        type: Array,
        default: () => [],
    },
    users: {
        type: Array,
        default: () => [],
    },
    courses: {
        type: Array,
        default: () => [],
    },
    recentLessons: {
        type: Array,
        default: () => [],
    },
    recentEnrollments: {
        type: Array,
        default: () => [],
    },
});

const currentUser = usePage().props.auth.user;
const userSearch = ref('');
const roleFilter = ref('all');
const courseSearch = ref('');
const courseSort = ref('students');

const filteredUsers = computed(() => {
    const search = userSearch.value.trim().toLowerCase();

    return props.users.filter((user) => {
        const matchesRole = roleFilter.value === 'all' || user.roles.includes(roleFilter.value);
        const haystack = `${user.full_name} ${user.email} ${user.domaine || ''} ${user.filiere || ''}`.toLowerCase();
        const matchesSearch = search === '' || haystack.includes(search);

        return matchesRole && matchesSearch;
    });
});

const filteredCourses = computed(() => {
    const search = courseSearch.value.trim().toLowerCase();
    const sortedCourses = [...props.courses].sort((left, right) => {
        if (courseSort.value === 'lessons') {
            return right.lessons_count - left.lessons_count;
        }

        if (courseSort.value === 'recent') {
            return (right.created_at || '').localeCompare(left.created_at || '');
        }

        return right.students_count - left.students_count;
    });

    return sortedCourses.filter((course) => {
        if (search === '') {
            return true;
        }

        const haystack = `${course.nom} ${course.filiere} ${course.tuteur_nom || ''}`.toLowerCase();

        return haystack.includes(search);
    });
});

const spotlightCourse = computed(() => filteredCourses.value[0] ?? null);

const toggleSuperUser = (user) => {
    router.post(route('admin.users.toggle-super-user', user.id_utilisateur), {}, {
        preserveScroll: true,
    });
};

const deleteCourse = (course) => {
    if (! window.confirm(`Supprimer le cours "${course.nom}" ? Cette action est irreversible.`)) {
        return;
    }

    router.delete(route('admin.cours.destroy', course.id), {
        preserveScroll: true,
    });
};
</script>

<template>
    <Head title="Dashboard Admin" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h2 class="text-2xl font-semibold text-slate-900">
                        Admin Panel
                    </h2>
                    <p class="text-sm text-slate-500">
                        Vue super utilisateur pour piloter la plateforme, les acces et le catalogue.
                    </p>
                </div>

                <span class="inline-flex rounded-full bg-sky-100 px-3 py-1 text-xs font-semibold uppercase tracking-[0.18em] text-sky-700">
                    Super user
                </span>
            </div>
        </template>

        <div class="py-10">
            <div class="mx-auto max-w-7xl space-y-8 px-4 sm:px-6 lg:px-8">
                <section class="overflow-hidden rounded-3xl bg-gradient-to-r from-slate-950 via-sky-900 to-cyan-600 text-white shadow-xl">
                    <div class="grid gap-8 px-6 py-8 lg:grid-cols-[1.45fr,1fr] lg:px-10">
                        <div class="space-y-4">
                            <span class="inline-flex rounded-full border border-white/20 bg-white/10 px-3 py-1 text-xs font-semibold uppercase tracking-[0.2em] text-sky-100">
                                Mission control
                            </span>

                            <div class="space-y-3">
                                <h1 class="text-3xl font-semibold leading-tight sm:text-4xl">
                                    {{ currentUser.prenom }} {{ currentUser.nom }}, vous avez la main sur toute la plateforme.
                                </h1>
                                <p class="max-w-2xl text-sm text-sky-50/90 sm:text-base">
                                    Supervisez les utilisateurs, suivez les contenus publies et ajustez les droits administrateur
                                    depuis un seul espace.
                                </p>
                            </div>

                            <div class="flex flex-wrap gap-3">
                                <a
                                    href="#utilisateurs"
                                    class="inline-flex items-center rounded-full bg-white px-5 py-2.5 text-sm font-semibold text-slate-900 transition hover:bg-slate-100"
                                >
                                    Gerer les acces
                                </a>
                                <a
                                    href="#cours"
                                    class="inline-flex items-center rounded-full border border-white/25 px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-white/10"
                                >
                                    Superviser les cours
                                </a>
                            </div>
                        </div>

                        <div class="rounded-3xl border border-white/15 bg-white/10 p-5 backdrop-blur">
                            <p class="text-sm font-medium text-sky-100">Etat global</p>
                            <div class="mt-4 grid gap-4 sm:grid-cols-2">
                                <div class="rounded-2xl bg-white/10 p-4">
                                    <p class="text-xs uppercase tracking-[0.18em] text-sky-100/80">Utilisateurs</p>
                                    <p class="mt-2 text-3xl font-semibold">{{ stats.total_users }}</p>
                                </div>
                                <div class="rounded-2xl bg-white/10 p-4">
                                    <p class="text-xs uppercase tracking-[0.18em] text-sky-100/80">Cours</p>
                                    <p class="mt-2 text-3xl font-semibold">{{ stats.courses }}</p>
                                </div>
                                <div class="rounded-2xl bg-white/10 p-4">
                                    <p class="text-xs uppercase tracking-[0.18em] text-sky-100/80">Lecons</p>
                                    <p class="mt-2 text-3xl font-semibold">{{ stats.lessons }}</p>
                                </div>
                                <div class="rounded-2xl bg-white/10 p-4">
                                    <p class="text-xs uppercase tracking-[0.18em] text-sky-100/80">Inscriptions</p>
                                    <p class="mt-2 text-3xl font-semibold">{{ stats.enrollments }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                <section class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
                    <div class="rounded-2xl border border-sky-200 bg-gradient-to-br from-white to-sky-50 p-5 shadow-sm">
                        <p class="text-sm font-medium text-sky-700">Administrateurs</p>
                        <p class="mt-3 text-3xl font-semibold text-slate-950">{{ stats.admins }}</p>
                        <p class="mt-2 text-sm text-sky-800/70">Comptes qui peuvent agir comme super utilisateurs.</p>
                    </div>

                    <div class="rounded-2xl border border-teal-200 bg-gradient-to-br from-white to-teal-50 p-5 shadow-sm">
                        <p class="text-sm font-medium text-teal-700">Tuteurs</p>
                        <p class="mt-3 text-3xl font-semibold text-slate-950">{{ stats.tuteurs }}</p>
                        <p class="mt-2 text-sm text-teal-800/70">Createurs de contenus actifs sur la plateforme.</p>
                    </div>

                    <div class="rounded-2xl border border-amber-200 bg-gradient-to-br from-white to-amber-50 p-5 shadow-sm">
                        <p class="text-sm font-medium text-amber-700">Etudiants</p>
                        <p class="mt-3 text-3xl font-semibold text-slate-950">{{ stats.etudiants }}</p>
                        <p class="mt-2 text-sm text-amber-800/70">Apprenants inscrits dans l'ecosysteme.</p>
                    </div>

                    <div class="rounded-2xl border border-cyan-200 bg-gradient-to-br from-white to-cyan-50 p-5 shadow-sm">
                        <p class="text-sm font-medium text-cyan-700">Catalogue</p>
                        <p class="mt-3 text-3xl font-semibold text-slate-950">{{ stats.courses }}</p>
                        <p class="mt-2 text-sm text-cyan-800/70">Cours actuellement visibles dans l'application.</p>
                    </div>
                </section>

                <section class="grid gap-6 xl:grid-cols-[1.3fr,0.9fr]">
                    <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
                        <div class="flex items-end justify-between gap-4">
                            <div>
                                <h3 class="text-2xl font-semibold text-slate-900">Distribution des roles</h3>
                                <p class="text-sm text-slate-500">
                                    Repartition instantanee des profils qui utilisent la plateforme.
                                </p>
                            </div>
                        </div>

                        <div class="mt-6 grid gap-4 md:grid-cols-3">
                            <article
                                v-for="item in roleBreakdown"
                                :key="item.label"
                                class="rounded-2xl border border-slate-200 bg-slate-50 p-4"
                            >
                                <p class="text-sm font-medium text-slate-600">{{ item.label }}</p>
                                <p class="mt-2 text-3xl font-semibold text-slate-950">{{ item.value }}</p>
                                <div class="mt-4 h-2 rounded-full bg-slate-200">
                                    <div
                                        class="h-2 rounded-full"
                                        :class="{
                                            'bg-sky-500': item.tone === 'sky',
                                            'bg-teal-500': item.tone === 'teal',
                                            'bg-amber-500': item.tone === 'amber',
                                        }"
                                        :style="{ width: `${stats.total_users ? Math.max((item.value / stats.total_users) * 100, 8) : 0}%` }"
                                    />
                                </div>
                            </article>
                        </div>
                    </div>

                    <section
                        v-if="spotlightCourse"
                        class="rounded-3xl border border-cyan-200 bg-gradient-to-br from-cyan-50 via-white to-sky-50 p-6 shadow-sm"
                    >
                        <span class="inline-flex rounded-full bg-cyan-100 px-3 py-1 text-xs font-semibold text-cyan-700">
                            Spotlight
                        </span>
                        <h3 class="mt-4 text-2xl font-semibold text-slate-900">{{ spotlightCourse.nom }}</h3>
                        <p class="mt-2 text-sm text-slate-500">
                            Le cours le plus visible selon votre tri actuel.
                        </p>

                        <div class="mt-5 grid gap-3 sm:grid-cols-2">
                            <div class="rounded-2xl bg-white p-4 shadow-sm">
                                <p class="text-xs uppercase tracking-[0.18em] text-cyan-500">Tuteur</p>
                                <p class="mt-2 text-lg font-semibold text-slate-900">{{ spotlightCourse.tuteur_nom || 'Non renseigne' }}</p>
                            </div>
                            <div class="rounded-2xl bg-white p-4 shadow-sm">
                                <p class="text-xs uppercase tracking-[0.18em] text-cyan-500">Filiere</p>
                                <p class="mt-2 text-lg font-semibold text-slate-900">{{ spotlightCourse.filiere }}</p>
                            </div>
                            <div class="rounded-2xl bg-white p-4 shadow-sm">
                                <p class="text-xs uppercase tracking-[0.18em] text-cyan-500">Lecons</p>
                                <p class="mt-2 text-lg font-semibold text-slate-900">{{ spotlightCourse.lessons_count }}</p>
                            </div>
                            <div class="rounded-2xl bg-white p-4 shadow-sm">
                                <p class="text-xs uppercase tracking-[0.18em] text-cyan-500">Etudiants</p>
                                <p class="mt-2 text-lg font-semibold text-slate-900">{{ spotlightCourse.students_count }}</p>
                            </div>
                        </div>
                    </section>
                </section>

                <section id="utilisateurs" class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
                    <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
                        <div>
                            <h3 class="text-2xl font-semibold text-slate-900">Gestion des utilisateurs</h3>
                            <p class="text-sm text-slate-500">
                                Recherchez un compte, filtrez par role et attribuez l'acces super utilisateur.
                            </p>
                        </div>

                        <div class="grid gap-3 sm:grid-cols-[minmax(240px,1fr),auto]">
                            <input
                                v-model="userSearch"
                                type="text"
                                placeholder="Rechercher un utilisateur..."
                                class="rounded-full border border-slate-300 px-4 py-2.5 text-sm text-slate-700 shadow-sm focus:border-sky-500 focus:outline-none focus:ring-2 focus:ring-sky-500/20"
                            >

                            <select
                                v-model="roleFilter"
                                class="rounded-full border border-slate-300 px-4 py-2.5 text-sm text-slate-700 shadow-sm focus:border-sky-500 focus:outline-none focus:ring-2 focus:ring-sky-500/20"
                            >
                                <option value="all">Tous les roles</option>
                                <option value="admin">Admins</option>
                                <option value="tuteur">Tuteurs</option>
                                <option value="etudiant">Etudiants</option>
                            </select>
                        </div>
                    </div>

                    <div class="mt-6 grid gap-4 xl:grid-cols-2">
                        <article
                            v-for="user in filteredUsers"
                            :key="user.id_utilisateur"
                            class="rounded-3xl border border-slate-200 bg-slate-50 p-5"
                        >
                            <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
                                <div class="space-y-3">
                                    <div>
                                        <h4 class="text-lg font-semibold text-slate-900">{{ user.full_name }}</h4>
                                        <p class="text-sm text-slate-500">{{ user.email }}</p>
                                    </div>

                                    <div class="flex flex-wrap gap-2">
                                        <span
                                            v-for="role in user.roles"
                                            :key="role"
                                            class="rounded-full px-3 py-1 text-xs font-semibold uppercase"
                                            :class="{
                                                'bg-sky-100 text-sky-700': role === 'admin',
                                                'bg-teal-100 text-teal-700': role === 'tuteur',
                                                'bg-amber-100 text-amber-700': role === 'etudiant',
                                            }"
                                        >
                                            {{ role }}
                                        </span>
                                    </div>

                                    <div class="text-sm text-slate-500">
                                        <p v-if="user.domaine">Domaine: {{ user.domaine }}</p>
                                        <p v-if="user.filiere">Filiere: {{ user.filiere }}</p>
                                        <p v-if="user.niveau">Niveau: {{ user.niveau }}</p>
                                        <p>Inscrit le {{ user.joined_at }}</p>
                                    </div>
                                </div>

                                <button
                                    type="button"
                                    class="inline-flex items-center justify-center rounded-full px-4 py-2 text-sm font-semibold transition disabled:cursor-not-allowed disabled:opacity-50"
                                    :class="user.is_admin ? 'border border-rose-300 text-rose-700 hover:bg-rose-50' : 'bg-sky-900 text-white hover:bg-sky-800'"
                                    :disabled="user.id_utilisateur === currentUser.id_utilisateur"
                                    @click="toggleSuperUser(user)"
                                >
                                    {{
                                        user.id_utilisateur === currentUser.id_utilisateur
                                            ? 'Compte courant'
                                            : user.is_admin
                                                ? 'Retirer acces admin'
                                                : 'Promouvoir admin'
                                    }}
                                </button>
                            </div>
                        </article>
                    </div>

                    <div
                        v-if="filteredUsers.length === 0"
                        class="mt-6 rounded-3xl border border-dashed border-slate-300 px-6 py-10 text-center"
                    >
                        <p class="text-lg font-semibold text-slate-900">Aucun utilisateur ne correspond au filtre</p>
                        <p class="mt-2 text-sm text-slate-500">
                            Essayez une recherche plus large ou un autre role.
                        </p>
                    </div>
                </section>

                <section id="cours" class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
                    <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
                        <div>
                            <h3 class="text-2xl font-semibold text-slate-900">Supervision du catalogue</h3>
                            <p class="text-sm text-slate-500">
                                Cherchez un cours, changez le tri et retirez un contenu si necessaire.
                            </p>
                        </div>

                        <div class="grid gap-3 sm:grid-cols-[minmax(240px,1fr),auto]">
                            <input
                                v-model="courseSearch"
                                type="text"
                                placeholder="Rechercher un cours..."
                                class="rounded-full border border-slate-300 px-4 py-2.5 text-sm text-slate-700 shadow-sm focus:border-cyan-500 focus:outline-none focus:ring-2 focus:ring-cyan-500/20"
                            >

                            <select
                                v-model="courseSort"
                                class="rounded-full border border-slate-300 px-4 py-2.5 text-sm text-slate-700 shadow-sm focus:border-cyan-500 focus:outline-none focus:ring-2 focus:ring-cyan-500/20"
                            >
                                <option value="students">Trier par etudiants</option>
                                <option value="lessons">Trier par lecons</option>
                                <option value="recent">Trier par recence</option>
                            </select>
                        </div>
                    </div>

                    <div class="mt-6 grid gap-5 lg:grid-cols-2">
                        <article
                            v-for="course in filteredCourses"
                            :key="course.id"
                            class="overflow-hidden rounded-3xl border border-slate-200 bg-slate-50"
                        >
                            <div class="grid gap-0 md:grid-cols-[160px,1fr]">
                                <div class="flex min-h-[180px] items-center justify-center bg-gradient-to-br from-sky-100 via-cyan-100 to-white">
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

                                <div class="space-y-4 p-5">
                                    <div>
                                        <div class="flex flex-wrap gap-2 text-xs font-medium">
                                            <span class="rounded-full bg-cyan-100 px-3 py-1 text-cyan-700">
                                                {{ course.filiere }}
                                            </span>
                                            <span class="rounded-full bg-slate-200 px-3 py-1 text-slate-600">
                                                Cree le {{ course.created_at }}
                                            </span>
                                        </div>

                                        <h4 class="mt-3 text-xl font-semibold text-slate-900">{{ course.nom }}</h4>
                                        <p class="mt-1 text-sm text-slate-500">
                                            Tuteur: {{ course.tuteur_nom || 'Non renseigne' }}
                                        </p>
                                    </div>

                                    <div class="grid grid-cols-2 gap-3">
                                        <div class="rounded-2xl bg-white px-4 py-3 shadow-sm">
                                            <p class="text-xs uppercase tracking-[0.18em] text-cyan-500">Lecons</p>
                                            <p class="mt-1 text-2xl font-semibold text-slate-950">{{ course.lessons_count }}</p>
                                        </div>
                                        <div class="rounded-2xl bg-white px-4 py-3 shadow-sm">
                                            <p class="text-xs uppercase tracking-[0.18em] text-cyan-500">Etudiants</p>
                                            <p class="mt-1 text-2xl font-semibold text-slate-950">{{ course.students_count }}</p>
                                        </div>
                                    </div>

                                    <button
                                        type="button"
                                        class="inline-flex items-center justify-center rounded-full border border-rose-300 px-4 py-2.5 text-sm font-semibold text-rose-700 transition hover:bg-rose-50"
                                        @click="deleteCourse(course)"
                                    >
                                        Supprimer ce cours
                                    </button>
                                </div>
                            </div>
                        </article>
                    </div>

                    <div
                        v-if="filteredCourses.length === 0"
                        class="mt-6 rounded-3xl border border-dashed border-slate-300 px-6 py-10 text-center"
                    >
                        <p class="text-lg font-semibold text-slate-900">Aucun cours trouve</p>
                        <p class="mt-2 text-sm text-slate-500">
                            Modifiez votre recherche ou votre tri pour explorer le catalogue.
                        </p>
                    </div>
                </section>

                <section class="grid gap-6 xl:grid-cols-2">
                    <div
                        v-if="recentLessons.length"
                        class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm"
                    >
                        <h3 class="text-2xl font-semibold text-slate-900">Dernieres lecons publiees</h3>
                        <p class="mt-1 text-sm text-slate-500">
                            Activite recente cote contenus.
                        </p>

                        <div class="mt-5 space-y-3">
                            <article
                                v-for="lesson in recentLessons"
                                :key="lesson.id"
                                class="flex items-center justify-between gap-4 rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3"
                            >
                                <div>
                                    <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">
                                        {{ lesson.cours_nom || 'Cours' }}
                                    </p>
                                    <h4 class="mt-1 text-base font-semibold text-slate-900">{{ lesson.titre }}</h4>
                                    <p class="mt-1 text-sm text-slate-500">{{ lesson.created_at }}</p>
                                </div>
                                <span
                                    class="rounded-full px-3 py-1 text-xs font-semibold uppercase"
                                    :class="lesson.type === 'video' ? 'bg-cyan-100 text-cyan-700' : 'bg-amber-100 text-amber-700'"
                                >
                                    {{ lesson.type }}
                                </span>
                            </article>
                        </div>
                    </div>

                    <div
                        v-if="recentEnrollments.length"
                        class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm"
                    >
                        <h3 class="text-2xl font-semibold text-slate-900">Dernieres inscriptions</h3>
                        <p class="mt-1 text-sm text-slate-500">
                            Suivi des nouvelles adhesions aux cours.
                        </p>

                        <div class="mt-5 space-y-3">
                            <article
                                v-for="enrollment in recentEnrollments"
                                :key="enrollment.id"
                                class="rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3"
                            >
                                <p class="text-sm font-semibold text-slate-900">
                                    {{ enrollment.etudiant_nom || 'Etudiant' }}
                                </p>
                                <p class="mt-1 text-sm text-slate-500">
                                    a rejoint {{ enrollment.cours_nom || 'un cours' }}
                                </p>
                                <p class="mt-2 text-xs uppercase tracking-[0.18em] text-slate-400">
                                    {{ enrollment.date_inscription }}
                                </p>
                            </article>
                        </div>
                    </div>
                </section>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
