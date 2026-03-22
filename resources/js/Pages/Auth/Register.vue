<script setup>
import { useForm, Head, Link } from '@inertiajs/vue3';

// 1. Initialize the form with a default role
const form = useForm({
    nom: '',
    email: '',
    role: 'etudiant', // Virtual role: 'etudiant' or 'tuteur'
    password: '',
    password_confirmation: '',
});

const submit = () => {
    form.post(route('register.submit'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};
</script>

<template>
    <Head title="Inscription" />

    <div class="min-h-screen flex items-center justify-center bg-slate-50 px-4 py-12">
        <div class="max-w-md w-full bg-white rounded-2xl shadow-xl p-8 border border-slate-100">
            
            <div class="text-center mb-8">
                <h1 class="text-3xl font-extrabold text-blue-600">Rejoignez-nous</h1>
                <p class="text-slate-500 mt-2">Créez votre compte E-Learning EMSI</p>
            </div>

            <form @submit.prevent="submit" class="space-y-5">
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1">Nom Complet</label>
                    <input v-model="form.nom" type="text" required placeholder="Ex: Ahmed Alami"
                        class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 outline-none transition"
                        :class="{'border-red-500': form.errors.nom}">
                    <div v-if="form.errors.nom" class="text-red-500 text-xs mt-1">{{ form.errors.nom }}</div>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1">Adresse Email</label>
                    <input v-model="form.email" type="email" required placeholder="email@emsi.ma"
                        class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 outline-none transition"
                        :class="{'border-red-500': form.errors.email}">
                    <div v-if="form.errors.email" class="text-red-500 text-xs mt-1">{{ form.errors.email }}</div>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Je souhaite être :</label>
                    <div class="flex gap-4">
                        <label class="flex-1 border p-3 rounded-xl cursor-pointer transition text-center hover:bg-blue-50"
                            :class="form.role === 'etudiant' ? 'border-blue-600 bg-blue-50 text-blue-600 ring-2 ring-blue-100' : 'border-slate-200 text-slate-500'">
                            <input type="radio" v-model="form.role" value="etudiant" class="hidden">
                            <span class="block text-lg mb-1">🎓</span>
                            <span class="block text-xs font-bold uppercase tracking-wider">Étudiant</span>
                        </label>

                        <label class="flex-1 border p-3 rounded-xl cursor-pointer transition text-center hover:bg-blue-50"
                            :class="form.role === 'tuteur' ? 'border-blue-600 bg-blue-50 text-blue-600 ring-2 ring-blue-100' : 'border-slate-200 text-slate-500'">
                            <input type="radio" v-model="form.role" value="tuteur" class="hidden">
                            <span class="block text-lg mb-1">👨‍🏫</span>
                            <span class="block text-xs font-bold uppercase tracking-wider">Tuteur</span>
                        </label>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1">Mot de passe</label>
                        <input v-model="form.password" type="password" required
                            class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 outline-none transition">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1">Confirmation</label>
                        <input v-model="form.password_confirmation" type="password" required
                            class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 outline-none transition">
                    </div>
                </div>
                <div v-if="form.errors.password" class="text-red-500 text-xs mt-1">{{ form.errors.password }}</div>

                <button type="submit" :disabled="form.processing"
                    class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 rounded-lg shadow-lg transition active:transform active:scale-95 disabled:opacity-50">
                    <span v-if="form.processing">Création du compte...</span>
                    <span v-else>S'inscrire maintenant</span>
                </button>
            </form>

            <p class="text-center text-sm text-slate-500 mt-8">
                Déjà un compte ? 
                <Link :href="route('login')" class="text-blue-600 font-bold hover:underline">Se connecter</Link>
            </p>
        </div>
    </div>
</template>