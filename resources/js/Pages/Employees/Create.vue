<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import Checkbox from '@/Components/Checkbox.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

const form = useForm({
    name: '',
    email: '',
    department: '',
    position: '',
    code_employee: '',
    major_employee: false,
});

const submit = () => {
    form.post(route('employees.store'));
};
</script>

<template>
    <Head title="Crear Empleado" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center">
                <Link :href="route('employees.index')" class="text-secondary hover:text-primary mr-4">
                    ← Volver
                </Link>
                <h2 class="font-semibold text-xl text-secondary leading-tight">Crear Nuevo Empleado</h2>
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
                                    class="mt-1 block w-full bg-white"
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
                                    class="mt-1 block w-full bg-white"
                                    v-model="form.email"
                                    required
                                />

                                <InputError class="mt-2" :message="form.errors.email" />
                            </div>

                            <div class="mt-4">
                                <InputLabel for="department" value="Departamento" />

                                <TextInput
                                    id="department"
                                    type="text"
                                    class="mt-1 block w-full bg-white"
                                    v-model="form.department"
                                    required
                                />

                                <InputError class="mt-2" :message="form.errors.department" />
                            </div>

                            <div class="mt-4">
                                <InputLabel for="position" value="Posición" />

                                <TextInput
                                    id="position"
                                    type="text"
                                    class="mt-1 block w-full bg-white"
                                    v-model="form.position"
                                    required
                                />

                                <InputError class="mt-2" :message="form.errors.position" />
                            </div>

                            <div class="mt-4">
                                <InputLabel for="code_employee" value="Código de Empleado" />

                                <TextInput
                                    id="code_employee"
                                    type="text"
                                    class="mt-1 block w-full bg-white"
                                    v-model="form.code_employee"
                                />

                                <InputError class="mt-2" :message="form.errors.code_employee" />
                            </div>

                            <div class="mt-4">
                                <label class="flex items-center">
                                    <Checkbox name="major_employee" v-model:checked="form.major_employee" />
                                    <span class="ms-2 text-sm text-secondary">Empleado Principal</span>
                                </label>
                                <p class="mt-1 text-xs text-gray-500">
                                    Si se marca, este empleado será el principal y se desactivará cualquier otro empleado principal existente.
                                </p>
                                <InputError class="mt-2" :message="form.errors.major_employee" />
                            </div>

                            <div class="flex items-center justify-end mt-4">
                                <Link
                                    :href="route('employees.index')"
                                    class="underline text-sm text-secondary hover:text-primary rounded-md mr-4"
                                >
                                    Cancelar
                                </Link>

                                <PrimaryButton :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                                    Crear Empleado
                                </PrimaryButton>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
