<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { computed } from 'vue';
import { useI18n } from 'vue-i18n';
import { LEVEL_OPTIONS, TRACK_OPTIONS } from '@/shared/academics';

const { t } = useI18n();

const specialites = computed(() =>
    TRACK_OPTIONS.map((track) => ({ key: track, label: track })),
);

const niveaux = computed(() =>
    LEVEL_OPTIONS.map((level) => ({ key: level, label: level })),
);

const form = useForm({
    prenom: '',
    nom: '',
    email: '',
    password: '',
    password_confirmation: '',
    role: 'etudiant',
    filiere: '',
    niveau: '',
    domaine: '',
});

const submit = () => {
    form.post(route('register'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};
</script>

<template>
    <GuestLayout>
        <Head :title="t('auth.register.head')" />

        <div class="mb-8">
            <h2 class="font-['Archivo'] text-3xl font-semibold text-slate-950">{{ t('auth.register.title') }}</h2>
            <p class="mt-2 text-sm text-slate-600">{{ t('auth.register.subtitle') }}</p>
        </div>

        <form @submit.prevent="submit">
            
            <div class="mb-6 border-b border-slate-200 pb-4">
                <InputLabel for="role" :value="t('auth.register.roleLabel')" class="text-lg font-bold" />
                <div class="mt-3 flex items-center space-x-6">
                    <label class="flex w-full cursor-pointer items-center rounded-xl border border-slate-200 p-3 transition hover:border-sky-300 hover:bg-slate-50">
                        <input type="radio" v-model="form.role" value="etudiant" class="text-indigo-600 focus:ring-indigo-500" />
                        <span class="ml-2 font-medium">{{ t('auth.register.student') }}</span>
                    </label>
                    <label class="flex w-full cursor-pointer items-center rounded-xl border border-slate-200 p-3 transition hover:border-emerald-300 hover:bg-slate-50">
                        <input type="radio" v-model="form.role" value="tuteur" class="text-indigo-600 focus:ring-indigo-500" />
                        <span class="ml-2 font-medium">{{ t('auth.register.tutor') }}</span>
                    </label>
                </div>
                <InputError class="mt-2" :message="form.errors.role" />
            </div>

            <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                <div>
                    <InputLabel for="prenom" :value="t('auth.fields.firstName')" />
                    <TextInput id="prenom" type="text" class="mt-1 block w-full" v-model="form.prenom" required autofocus />
                    <InputError class="mt-2" :message="form.errors.prenom" />
                </div>
                <div>
                    <InputLabel for="nom" :value="t('auth.fields.lastName')" />
                    <TextInput id="nom" type="text" class="mt-1 block w-full" v-model="form.nom" required />
                    <InputError class="mt-2" :message="form.errors.nom" />
                </div>
            </div>

            <div class="mt-4">
                <InputLabel for="email" :value="t('auth.register.institutionalEmail')" />
                <TextInput id="email" type="email" class="mt-1 block w-full" v-model="form.email" required />
                <InputError class="mt-2" :message="form.errors.email" />
            </div>

            <div v-if="form.role === 'etudiant'" class="mt-4 grid grid-cols-1 gap-4 rounded border border-blue-100 bg-blue-50 p-4 md:grid-cols-2">
                <div>
                    <InputLabel for="filiere" :value="t('auth.fields.track')" />
                    <select id="filiere" v-model="form.filiere" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="" disabled>{{ t('auth.register.specialtyPlaceholder') }}</option>
                        <option v-for="spec in specialites" :key="spec.key" :value="spec.label">{{ spec.label }}</option>
                    </select>
                    <InputError class="mt-2" :message="form.errors.filiere" />
                </div>
                <div>
                    <InputLabel for="niveau" :value="t('auth.fields.level')" />
                    <select id="niveau" v-model="form.niveau" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="" disabled>{{ t('auth.register.levelPlaceholder') }}</option>
                        <option v-for="niv in niveaux" :key="niv.key" :value="niv.label">{{ niv.label }}</option>
                    </select>
                    <InputError class="mt-2" :message="form.errors.niveau" />
                </div>
            </div>

            <div v-if="form.role === 'tuteur'" class="mt-4 rounded border border-green-100 bg-green-50 p-4">
                <InputLabel for="domaine" :value="t('auth.fields.domain')" />
                <select id="domaine" v-model="form.domaine" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                    <option value="" disabled>{{ t('auth.register.domainPlaceholder') }}</option>
                    <option v-for="spec in specialites" :key="spec.key" :value="spec.label">{{ spec.label }}</option>
                </select>
                <InputError class="mt-2" :message="form.errors.domaine" />
            </div>

            <div class="mt-4">
                <InputLabel for="password" :value="t('auth.fields.password')" />
                <TextInput id="password" type="password" class="mt-1 block w-full" v-model="form.password" required />
                <InputError class="mt-2" :message="form.errors.password" />
            </div>

            <div class="mt-4">
                <InputLabel for="password_confirmation" :value="t('auth.fields.confirmPassword')" />
                <TextInput id="password_confirmation" type="password" class="mt-1 block w-full" v-model="form.password_confirmation" required />
                <InputError class="mt-2" :message="form.errors.password_confirmation" />
            </div>

            <div class="mt-6 flex flex-col-reverse items-start gap-3 sm:flex-row sm:items-center sm:justify-end">
                <Link :href="route('login')" class="text-sm text-gray-600 underline hover:text-gray-900">{{ t('auth.register.alreadyRegistered') }}</Link>
                <PrimaryButton class="sm:ms-4" :loading="form.processing">{{ t('auth.register.button') }}</PrimaryButton>
            </div>
        </form>
    </GuestLayout>
</template>
