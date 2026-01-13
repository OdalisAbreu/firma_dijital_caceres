<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import LoadingSpinner from '@/Components/LoadingSpinner.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

const form = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
    role: 'colaborador',
});

const submit = () => {
    form.post(route('admin.store'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};
</script>

<template>
    <Head title="Crear Usuario" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center">
                <Link :href="route('admin.index')" class="text-secondary hover:text-primary mr-4">
                    ← Volver
                </Link>
                <h2 class="font-semibold text-xl text-secondary leading-tight">Crear Nuevo Usuario</h2>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <form @submit.prevent="submit">
                            <div>
                                <InputLabel for="name" value="Nombre" />

                                <TextInput
                                    id="name"
                                    type="text"
                                    class="mt-1 block w-full"
                                    v-model="form.name"
                                    required
                                    autofocus
                                />

                                <InputError class="mt-2" :message="form.errors.name" />
                            </div>

                            <div class="mt-4">
                                <InputLabel for="email" value="Correo Electrónico" />

                                <TextInput
                                    id="email"
                                    type="email"
                                    class="mt-1 block w-full"
                                    v-model="form.email"
                                    required
                                />

                                <InputError class="mt-2" :message="form.errors.email" />
                            </div>

                            <div class="mt-4">
                                <InputLabel for="password" value="Contraseña" />

                                <TextInput
                                    id="password"
                                    type="password"
                                    class="mt-1 block w-full"
                                    v-model="form.password"
                                    required
                                />

                                <InputError class="mt-2" :message="form.errors.password" />
                            </div>

                            <div class="mt-4">
                                <InputLabel for="password_confirmation" value="Confirmar Contraseña" />

                                <TextInput
                                    id="password_confirmation"
                                    type="password"
                                    class="mt-1 block w-full"
                                    v-model="form.password_confirmation"
                                    required
                                />

                                <InputError class="mt-2" :message="form.errors.password_confirmation" />
                            </div>

                            <div class="mt-4">
                                <InputLabel for="role" value="Rol" />
                                <select
                                    id="role"
                                    v-model="form.role"
                                    class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 rounded-md shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50"
                                    required
                                >
                                    <option value="administrador">Administrador</option>
                                    <option value="colaborador">Colaborador</option>
                                </select>
                                <InputError class="mt-2" :message="form.errors.role" />
                            </div>

                            <div class="flex items-center justify-end mt-4">
                                <Link
                                    :href="route('admin.index')"
                                    class="underline text-sm text-secondary hover:text-primary rounded-md mr-4"
                                >
                                    Cancelar
                                </Link>

                                <PrimaryButton :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                                    <span v-if="form.processing" class="flex items-center gap-2">
                                        <LoadingSpinner size="sm" color="white" />
                                        Creando...
                                    </span>
                                    <span v-else>Crear Usuario</span>
                                </PrimaryButton>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

