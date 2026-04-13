<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

const specialites = [
    'Computer science',
    'Cybersecurity',
    'Data science and AI',
    'Financial engineering',
    'Software engineering',
    'Civil engineering'
];

// ✅ Added the list of levels
const niveaux = [
    '1er année',
    '2ème année',
    '3ème année',
    '4ème année',
    '5ème année'
];

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
        <Head title="Register" />

        <form @submit.prevent="submit">
            
            <div class="mb-6 pb-4 border-b border-gray-200">
                <InputLabel for="role" value="Je m'inscris en tant que :" class="text-lg font-bold" />
                <div class="flex items-center space-x-6 mt-3">
                    <label class="flex items-center cursor-pointer p-2 border rounded hover:bg-gray-50 w-full">
                        <input type="radio" v-model="form.role" value="etudiant" class="text-indigo-600 focus:ring-indigo-500" />
                        <span class="ml-2 font-medium">Étudiant</span>
                    </label>
                    <label class="flex items-center cursor-pointer p-2 border rounded hover:bg-gray-50 w-full">
                        <input type="radio" v-model="form.role" value="tuteur" class="text-indigo-600 focus:ring-indigo-500" />
                        <span class="ml-2 font-medium">Tuteur / Professeur</span>
                    </label>
                </div>
                <InputError class="mt-2" :message="form.errors.role" />
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <InputLabel for="prenom" value="Prénom" />
                    <TextInput id="prenom" type="text" class="mt-1 block w-full" v-model="form.prenom" required autofocus />
                    <InputError class="mt-2" :message="form.errors.prenom" />
                </div>
                <div>
                    <InputLabel for="nom" value="Nom" />
                    <TextInput id="nom" type="text" class="mt-1 block w-full" v-model="form.nom" required />
                    <InputError class="mt-2" :message="form.errors.nom" />
                </div>
            </div>

            <div class="mt-4">
                <InputLabel for="email" value="Email Institutionnel" />
                <TextInput id="email" type="email" class="mt-1 block w-full" v-model="form.email" required />
                <InputError class="mt-2" :message="form.errors.email" />
            </div>

            <div v-if="form.role === 'etudiant'" class="mt-4 grid grid-cols-2 gap-4 p-4 bg-blue-50 rounded border border-blue-100">
                <div>
                    <InputLabel for="filiere" value="Filière" />
                    <select id="filiere" v-model="form.filiere" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                        <option value="" disabled>Choisir filière...</option>
                        <option v-for="spec in specialites" :key="spec" :value="spec">{{ spec }}</option>
                    </select>
                    <InputError class="mt-2" :message="form.errors.filiere" />
                </div>
                <div>
                    <InputLabel for="niveau" value="Niveau" />
                    <select id="niveau" v-model="form.niveau" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                        <option value="" disabled>Choisir année...</option>
                        <option v-for="niv in niveaux" :key="niv" :value="niv">{{ niv }}</option>
                    </select>
                    <InputError class="mt-2" :message="form.errors.niveau" />
                </div>
            </div>

            <div v-if="form.role === 'tuteur'" class="mt-4 p-4 bg-green-50 rounded border border-green-100">
                <InputLabel for="domaine" value="Domaine d'expertise" />
                <select id="domaine" v-model="form.domaine" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                    <option value="" disabled>Sélectionnez un domaine...</option>
                    <option v-for="spec in specialites" :key="spec" :value="spec">{{ spec }}</option>
                </select>
                <InputError class="mt-2" :message="form.errors.domaine" />
            </div>

            <div class="mt-4">
                <InputLabel for="password" value="Mot de passe" />
                <TextInput id="password" type="password" class="mt-1 block w-full" v-model="form.password" required />
                <InputError class="mt-2" :message="form.errors.password" />
            </div>

            <div class="mt-4">
                <InputLabel for="password_confirmation" value="Confirmer le mot de passe" />
                <TextInput id="password_confirmation" type="password" class="mt-1 block w-full" v-model="form.password_confirmation" required />
                <InputError class="mt-2" :message="form.errors.password_confirmation" />
            </div>

            <div class="mt-6 flex items-center justify-end">
                <Link :href="route('login')" class="text-sm text-gray-600 underline hover:text-gray-900">Déjà inscrit ?</Link>
                <PrimaryButton class="ms-4" :class="{ 'opacity-25': form.processing }" :disabled="form.processing">S'inscrire</PrimaryButton>
            </div>
        </form>
    </GuestLayout>
</template>