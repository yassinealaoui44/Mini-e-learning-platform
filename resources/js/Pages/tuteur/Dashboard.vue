<script setup>
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm, usePage } from '@inertiajs/vue3';

const props = defineProps({
    stats: {
        type: Object,
        required: true,
    },
    courses: {
        type: Array,
        default: () => [],
    },
    recentLessons: {
        type: Array,
        default: () => [],
    },
});

const filiereOptions = [
    'Computer science',
    'Cybersecurity',
    'Data science and AI',
    'Financial engineering',
    'Software engineering',
    'Civil engineering',
];

const user = usePage().props.auth.user;

const courseForm = useForm({
    nom: '',
    filiere: '',
    thumbnail: null,
});

const lessonForms = Object.fromEntries(
    props.courses.map((course) => [
        course.id,
        useForm({
            titre: '',
            type: 'pdf',
            file: null,
        }),
    ]),
);

const lessonForm = (courseId) => {
    if (!lessonForms[courseId]) {
        lessonForms[courseId] = useForm({
            titre: '',
            type: 'pdf',
            file: null,
        });
    }

    return lessonForms[courseId];
};

const submitCourse = () => {
    courseForm.post(route('tuteur.cours.store'), {
        forceFormData: true,
        preserveScroll: true,
        preserveState: false,
        onSuccess: () => {
            courseForm.reset();
        },
    });
};

const submitLesson = (courseId) => {
    const form = lessonForm(courseId);

    form.post(route('tuteur.lecons.store', courseId), {
        forceFormData: true,
        preserveScroll: true,
        preserveState: false,
        onSuccess: () => {
            form.reset();
            form.type = 'pdf';
        },
    });
};
</script>

<template>
    <Head title="Dashboard Tuteur" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h2 class="text-2xl font-semibold text-slate-900">
                        Espace Tuteur
                    </h2>
                    <p class="text-sm text-slate-500">
                        Creez vos cours, ajoutez des lecons et suivez l'activite de vos etudiants.
                    </p>
                </div>

                <span class="inline-flex rounded-full bg-teal-100 px-3 py-1 text-xs font-semibold text-teal-700">
                    Domaine: {{ user.domaine || 'A renseigner' }}
                </span>
            </div>
        </template>

        <div class="py-10">
            <div class="mx-auto max-w-7xl space-y-8 px-4 sm:px-6 lg:px-8">
                <section class="overflow-hidden rounded-3xl bg-gradient-to-r from-slate-950 via-teal-900 to-amber-600 text-white shadow-xl">
                    <div class="grid gap-8 px-6 py-8 lg:grid-cols-[1.4fr,1fr] lg:px-10">
                        <div class="space-y-4">
                            <span class="inline-flex rounded-full border border-white/15 bg-white/10 px-3 py-1 text-xs font-semibold uppercase tracking-[0.2em] text-teal-100">
                                Console pedagogique
                            </span>

                            <div class="space-y-3">
                                <h1 class="text-3xl font-semibold leading-tight sm:text-4xl">
                                    Bonjour {{ user.prenom }} {{ user.nom }}, votre espace de creation est pret.
                                </h1>
                                <p class="max-w-2xl text-sm text-teal-50/90 sm:text-base">
                                    Depuis ce tableau de bord, vous pouvez publier un cours, enrichir son contenu avec des
                                    lecons et suivre la dynamique des inscriptions.
                                </p>
                            </div>

                            <div class="flex flex-wrap gap-3">
                                <a
                                    href="#creation-cours"
                                    class="inline-flex items-center rounded-full bg-white px-5 py-2.5 text-sm font-semibold text-slate-900 transition hover:bg-slate-100"
                                >
                                    Creer un cours
                                </a>
                                <a
                                    href="#mes-cours"
                                    class="inline-flex items-center rounded-full border border-white/20 px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-white/10"
                                >
                                    Gerer mes contenus
                                </a>
                            </div>
                        </div>

                        <div class="rounded-3xl border border-white/15 bg-white/10 p-5 backdrop-blur">
                            <p class="text-sm font-medium text-teal-100">Vue d'ensemble</p>
                            <div class="mt-4 grid gap-4 sm:grid-cols-2">
                                <div class="rounded-2xl bg-white/10 p-4">
                                    <p class="text-xs uppercase tracking-[0.18em] text-teal-100/80">Cours</p>
                                    <p class="mt-2 text-3xl font-semibold">{{ stats.courses }}</p>
                                </div>
                                <div class="rounded-2xl bg-white/10 p-4">
                                    <p class="text-xs uppercase tracking-[0.18em] text-teal-100/80">Lecons</p>
                                    <p class="mt-2 text-3xl font-semibold">{{ stats.lessons }}</p>
                                </div>
                                <div class="rounded-2xl bg-white/10 p-4">
                                    <p class="text-xs uppercase tracking-[0.18em] text-teal-100/80">Etudiants</p>
                                    <p class="mt-2 text-3xl font-semibold">{{ stats.students }}</p>
                                </div>
                                <div class="rounded-2xl bg-white/10 p-4">
                                    <p class="text-xs uppercase tracking-[0.18em] text-teal-100/80">Filieres</p>
                                    <p class="mt-2 text-3xl font-semibold">{{ stats.filieres }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                <section class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
                    <div class="rounded-2xl border border-teal-200 bg-gradient-to-br from-white to-teal-50 p-5 shadow-sm">
                        <p class="text-sm font-medium text-teal-700">Cours publies</p>
                        <p class="mt-3 text-3xl font-semibold text-slate-950">{{ stats.courses }}</p>
                        <p class="mt-2 text-sm text-teal-800/70">Nombre total de cours geres depuis votre compte.</p>
                    </div>

                    <div class="rounded-2xl border border-cyan-200 bg-gradient-to-br from-white to-cyan-50 p-5 shadow-sm">
                        <p class="text-sm font-medium text-cyan-700">Lecons en ligne</p>
                        <p class="mt-3 text-3xl font-semibold text-slate-950">{{ stats.lessons }}</p>
                        <p class="mt-2 text-sm text-cyan-800/70">Toutes les ressources pedagogiques deja ajoutees.</p>
                    </div>

                    <div class="rounded-2xl border border-amber-200 bg-gradient-to-br from-white to-amber-50 p-5 shadow-sm">
                        <p class="text-sm font-medium text-amber-700">Inscriptions etudiantes</p>
                        <p class="mt-3 text-3xl font-semibold text-slate-950">{{ stats.students }}</p>
                        <p class="mt-2 text-sm text-amber-800/70">Somme des etudiants inscrits sur vos cours.</p>
                    </div>

                    <div class="rounded-2xl border border-teal-200 bg-gradient-to-br from-white to-teal-50 p-5 shadow-sm">
                        <p class="text-sm font-medium text-teal-700">Filieres couvertes</p>
                        <p class="mt-3 text-3xl font-semibold text-slate-950">{{ stats.filieres }}</p>
                        <p class="mt-2 text-sm text-teal-800/70">Diversite des parcours adresses par vos contenus.</p>
                    </div>
                </section>

                <section id="creation-cours" class="rounded-3xl border border-teal-200 bg-gradient-to-br from-white to-teal-50 p-6 shadow-sm">
                    <div class="grid gap-8 lg:grid-cols-[1fr,1.2fr]">
                        <div class="space-y-3">
                            <span class="inline-flex rounded-full bg-teal-100 px-3 py-1 text-xs font-semibold text-teal-700">
                                Nouveau contenu
                            </span>
                            <h3 class="text-2xl font-semibold text-slate-900">Creer un nouveau cours</h3>
                            <p class="text-sm text-slate-500">
                                Donnez un nom a votre cours, choisissez sa filiere et ajoutez une miniature pour rendre
                                votre catalogue plus clair.
                            </p>
                        </div>

                        <form class="grid gap-4 sm:grid-cols-2" @submit.prevent="submitCourse">
                            <div class="sm:col-span-2">
                                <InputLabel for="course_nom" value="Nom du cours" />
                                <TextInput
                                    id="course_nom"
                                    v-model="courseForm.nom"
                                    type="text"
                                    class="mt-1 block w-full"
                                    placeholder="Ex: Introduction a Laravel"
                                />
                                <InputError class="mt-2" :message="courseForm.errors.nom" />
                            </div>

                            <div>
                                <InputLabel for="course_filiere" value="Filiere" />
                                <select
                                    id="course_filiere"
                                    v-model="courseForm.filiere"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-teal-500 focus:ring-teal-500"
                                >
                                    <option value="" disabled>Choisir une filiere</option>
                                    <option v-for="filiere in filiereOptions" :key="filiere" :value="filiere">
                                        {{ filiere }}
                                    </option>
                                </select>
                                <InputError class="mt-2" :message="courseForm.errors.filiere" />
                            </div>

                            <div>
                                <InputLabel for="course_thumbnail" value="Miniature" />
                                <input
                                    id="course_thumbnail"
                                    type="file"
                                    accept=".jpg,.jpeg,.png,.webp"
                                    class="mt-1 block w-full rounded-md border border-slate-300 px-3 py-2 text-sm text-slate-600 shadow-sm focus:border-teal-500 focus:outline-none focus:ring-2 focus:ring-teal-500/30"
                                    @input="courseForm.thumbnail = $event.target.files[0]"
                                >
                                <InputError class="mt-2" :message="courseForm.errors.thumbnail" />
                            </div>

                            <div class="sm:col-span-2">
                                <button
                                    type="submit"
                                    class="inline-flex items-center rounded-full bg-teal-900 px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-teal-800 disabled:cursor-not-allowed disabled:opacity-60"
                                    :disabled="courseForm.processing"
                                >
                                    {{ courseForm.processing ? 'Creation...' : 'Publier le cours' }}
                                </button>
                            </div>
                        </form>
                    </div>
                </section>

                <section id="mes-cours" class="space-y-4">
                    <div>
                        <h3 class="text-2xl font-semibold text-slate-900">Mes cours</h3>
                        <p class="text-sm text-slate-500">
                            Ajoutez des lecons et gardez une vue claire sur chaque cours.
                        </p>
                    </div>

                    <div
                        v-if="courses.length === 0"
                        class="rounded-3xl border border-dashed border-slate-300 bg-white px-6 py-10 text-center shadow-sm"
                    >
                        <p class="text-lg font-semibold text-slate-900">Aucun cours publie pour le moment</p>
                        <p class="mt-2 text-sm text-slate-500">
                            Creez votre premier cours ci-dessus pour lancer votre espace de formation.
                        </p>
                    </div>

                    <div v-else class="grid gap-6 xl:grid-cols-2">
                        <article
                            v-for="course in courses"
                            :key="course.id"
                            class="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm"
                        >
                            <div class="grid gap-0 md:grid-cols-[190px,1fr]">
                                <div class="flex min-h-[220px] items-center justify-center bg-gradient-to-br from-teal-100 via-cyan-100 to-amber-100">
                                    <img
                                        v-if="course.thumbnail_url"
                                        :src="course.thumbnail_url"
                                        :alt="course.nom"
                                        class="h-full w-full object-cover"
                                    >
                                    <span v-else class="px-6 text-center text-sm font-medium text-slate-500">
                                        Miniature du cours
                                    </span>
                                </div>

                                <div class="space-y-5 p-6">
                                    <div class="flex flex-wrap items-start justify-between gap-4">
                                        <div>
                                            <div class="flex flex-wrap gap-2 text-xs font-medium">
                                                <span class="rounded-full bg-teal-100 px-3 py-1 text-teal-700">
                                                    {{ course.filiere }}
                                                </span>
                                                <span class="rounded-full bg-slate-100 px-3 py-1 text-slate-600">
                                                    Cree le {{ course.created_at }}
                                                </span>
                                            </div>
                                            <h4 class="mt-3 text-xl font-semibold text-slate-900">{{ course.nom }}</h4>
                                        </div>

                                        <div class="grid grid-cols-2 gap-3 text-center">
                                            <div class="rounded-2xl bg-teal-50 px-4 py-3">
                                                <p class="text-xs uppercase tracking-[0.18em] text-teal-400">Lecons</p>
                                                <p class="mt-1 text-2xl font-semibold text-slate-950">{{ course.lessons_count }}</p>
                                            </div>
                                            <div class="rounded-2xl bg-amber-50 px-4 py-3">
                                                <p class="text-xs uppercase tracking-[0.18em] text-amber-400">Etudiants</p>
                                                <p class="mt-1 text-2xl font-semibold text-slate-950">{{ course.students_count }}</p>
                                            </div>
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
                                                    :class="lesson.type === 'video' ? 'bg-cyan-100 text-cyan-700' : 'bg-amber-100 text-amber-700'"
                                                >
                                                    {{ lesson.type }}
                                                </span>
                                            </div>
                                        </div>
                                        <p v-else class="mt-3 text-sm text-slate-500">
                                            Aucune lecon ajoutee pour l'instant.
                                        </p>
                                    </div>

                                    <form class="space-y-4 rounded-3xl border border-teal-200 bg-white/80 p-4" @submit.prevent="submitLesson(course.id)">
                                        <div>
                                            <p class="text-sm font-semibold text-slate-900">Ajouter une lecon</p>
                                            <p class="mt-1 text-sm text-slate-500">
                                                Importez un PDF ou une video pour enrichir ce cours.
                                            </p>
                                        </div>

                                        <div>
                                            <InputLabel :for="`lesson_title_${course.id}`" value="Titre" />
                                            <TextInput
                                                :id="`lesson_title_${course.id}`"
                                                v-model="lessonForm(course.id).titre"
                                                type="text"
                                                class="mt-1 block w-full"
                                                placeholder="Ex: Chapitre 1 - Introduction"
                                            />
                                            <InputError class="mt-2" :message="lessonForm(course.id).errors.titre" />
                                        </div>

                                        <div class="grid gap-4 sm:grid-cols-2">
                                            <div>
                                                <InputLabel :for="`lesson_type_${course.id}`" value="Type de contenu" />
                                                <select
                                                    :id="`lesson_type_${course.id}`"
                                                    v-model="lessonForm(course.id).type"
                                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-teal-500 focus:ring-teal-500"
                                                >
                                                    <option value="pdf">PDF</option>
                                                    <option value="video">Video</option>
                                                </select>
                                                <InputError class="mt-2" :message="lessonForm(course.id).errors.type" />
                                            </div>

                                            <div>
                                                <InputLabel :for="`lesson_file_${course.id}`" value="Fichier" />
                                                <input
                                                    :id="`lesson_file_${course.id}`"
                                                    type="file"
                                                    class="mt-1 block w-full rounded-md border border-slate-300 px-3 py-2 text-sm text-slate-600 shadow-sm focus:border-teal-500 focus:outline-none focus:ring-2 focus:ring-teal-500/30"
                                                    :accept="lessonForm(course.id).type === 'video' ? '.mp4,.mov,.avi,.webm' : '.pdf'"
                                                    @input="lessonForm(course.id).file = $event.target.files[0]"
                                                >
                                                <InputError class="mt-2" :message="lessonForm(course.id).errors.file" />
                                            </div>
                                        </div>

                                        <button
                                            type="submit"
                                            class="inline-flex items-center rounded-full border border-teal-900 px-5 py-2.5 text-sm font-semibold text-teal-900 transition hover:bg-teal-900 hover:text-white disabled:cursor-not-allowed disabled:opacity-60"
                                            :disabled="lessonForm(course.id).processing"
                                        >
                                            {{ lessonForm(course.id).processing ? 'Import...' : 'Ajouter la lecon' }}
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </article>
                    </div>
                </section>

                <section
                    v-if="recentLessons.length"
                    class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm"
                >
                    <div>
                        <h3 class="text-2xl font-semibold text-slate-900">Activite recente</h3>
                        <p class="text-sm text-slate-500">
                            Les derniers contenus que vous avez mis en ligne.
                        </p>
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
                                    :class="lesson.type === 'video' ? 'bg-cyan-100 text-cyan-700' : 'bg-amber-100 text-amber-700'"
                                >
                                    {{ lesson.type }}
                                </span>
                            </div>

                            <p class="mt-3 text-sm text-slate-500">Publiee le {{ lesson.created_at }}</p>
                        </article>
                    </div>
                </section>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
