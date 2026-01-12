<script setup>
import { ref, computed } from 'vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import LoadingSpinner from '@/Components/LoadingSpinner.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

const props = defineProps({
    employees: {
        type: Array,
        required: true,
    },
});

// Buscar el empleado principal para preseleccionarlo
const getMajorEmployeeId = () => {
    const majorEmployee = props.employees.find(emp => emp.major_employee === true);
    return majorEmployee ? majorEmployee.id.toString() : (props.employees[0] ? props.employees[0].id.toString() : '');
};

const form = useForm({
    employee_id: getMajorEmployeeId(),
    title: '',
    description: '',
    name_client: '',
    lastname_client: '',
    email_client: '',
    tipo_identificacion: '',
    numero_identificacion: '',
    tipo_tercero: '',
    sucursal: '',
});

const tipoIdentificacionOptions = [
    { value: '', label: 'Seleccione...' },
    { value: 'Cédula', label: 'Cédula' },
    { value: 'Pasaporte', label: 'Pasaporte' },
    { value: 'ID residencia', label: 'ID Residencia' },
];

const tipoTerceroOptions = [
    { value: '', label: 'Seleccione...' },
    { value: 'PERSONAL', label: 'PERSONAL' },
    { value: 'PERSONALES PREMIUM', label: 'PERSONALES PREMIUM' },
    { value: 'COMERCIAL', label: 'COMERCIAL' },
    { value: 'CORPORATIVOS', label: 'CORPORATIVOS' },
];

const sucursalOptions = [
    { value: '', label: 'Seleccione...' },
    { value: 'Principal', label: 'Principal' },
    { value: 'Romana', label: 'Romana' },
    { value: 'Punta Cana', label: 'Punta Cana' },
];

const mostrarCampoNumero = computed(() => {
    return form.tipo_identificacion !== '';
});

const getPlaceholderIdentificacion = () => {
    const tipo = form.tipo_identificacion;
    if (tipo === 'Cédula') return 'Ingrese el número de cédula';
    if (tipo === 'Pasaporte') return 'Ingrese el número de pasaporte';
    if (tipo === 'ID residencia') return 'Ingrese el número de ID residencia';
    return 'Ingrese el número de identificación';
};

const submit = () => {
    form.post(route('kyc-send.store'));
};
</script>

<template>
    <Head title="Nuevo Cliente - Envío KYC" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center">
                <Link :href="route('clientes.index')" class="text-secondary hover:text-primary mr-4">
                    ← Volver
                </Link>
                <h2 class="font-semibold text-xl text-secondary leading-tight">Nuevo Cliente - Envío KYC</h2>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div v-if="$page.props.flash?.success" class="mb-4 p-4 bg-green-100 dark:bg-green-900 text-green-700 dark:text-green-300 rounded">
                            {{ $page.props.flash.success }}
                        </div>

                        <div v-if="$page.props.errors?.kyc_error" class="mb-4 p-4 bg-red-100 dark:bg-red-900 text-red-700 dark:text-red-300 rounded">
                            {{ $page.props.errors.kyc_error }}
                        </div>

                        <!-- Mostrar errores de validación generales -->
                        <div v-if="Object.keys(form.errors).length > 0 && !form.processing" class="mb-4 p-4 bg-red-100 dark:bg-red-900 text-red-700 dark:text-red-300 rounded">
                            <p class="font-semibold mb-2">Por favor, corrige los siguientes errores:</p>
                            <ul class="list-disc list-inside">
                                <li v-for="(error, field) in form.errors" :key="field">
                                    {{ error }}
                                </li>
                            </ul>
                        </div>

                        <form @submit.prevent="submit">
                            <!-- Empleado -->
                            <div class="mt-4">
                                <InputLabel for="employee_id" value="Empleado *" />
                                <select
                                    id="employee_id"
                                    v-model="form.employee_id"
                                    class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 rounded-md shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50 bg-white"
                                    required
                                >
                                    <option value="">Seleccione un empleado...</option>
                                    <option
                                        v-for="employee in employees"
                                        :key="employee.id"
                                        :value="employee.id"
                                    >
                                        {{ employee.name }} {{ employee.major_employee ? '(Principal)' : '' }} - {{ employee.email }}
                                    </option>
                                </select>
                                <InputError class="mt-2" :message="form.errors.employee_id" />
                            </div>

                            <!-- Título -->
                            <!--<div class="mt-4">
                                <InputLabel for="title" value="Título del Documento *" />
                                <TextInput
                                    id="title"
                                    type="text"
                                    class="mt-1 block w-full bg-white dark:bg-gray-900"
                                    v-model="form.title"
                                    required
                                    autofocus
                                />
                                <InputError class="mt-2" :message="form.errors.title" />
                            </div>-->

                            <!-- Descripción -->
                            <!--<div class="mt-4">
                                <InputLabel for="description" value="Descripción" />
                                <textarea
                                    id="description"
                                    v-model="form.description"
                                    rows="3"
                                    class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 rounded-md shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50 bg-white"
                                ></textarea>
                                <InputError class="mt-2" :message="form.errors.description" />
                            </div>

                            <!-- Nombre Cliente -->
                            <div class="mt-4">
                                <InputLabel for="name_client" value="Nombre del Cliente *" />
                                <TextInput
                                    id="name_client"
                                    type="text"
                                    class="mt-1 block w-full bg-white dark:bg-gray-900"
                                    v-model="form.name_client"
                                    required
                                />
                                <InputError class="mt-2" :message="form.errors.name_client" />
                            </div>

                            <!-- Apellido Cliente -->
                            <div class="mt-4">
                                <InputLabel for="lastname_client" value="Apellido del Cliente *" />
                                <TextInput
                                    id="lastname_client"
                                    type="text"
                                    class="mt-1 block w-full bg-white dark:bg-gray-900"
                                    v-model="form.lastname_client"
                                    required
                                />
                                <InputError class="mt-2" :message="form.errors.lastname_client" />
                            </div>

                            <!-- Email Cliente -->
                            <div class="mt-4">
                                <InputLabel for="email_client" value="Correo Electrónico del Cliente *" />
                                <TextInput
                                    id="email_client"
                                    type="email"
                                    class="mt-1 block w-full bg-white dark:bg-gray-900"
                                    v-model="form.email_client"
                                    required
                                />
                                <InputError class="mt-2" :message="form.errors.email_client" />
                            </div>

                            <!-- Tipo de Identificación -->
                            <div class="mt-4">
                                <InputLabel for="tipo_identificacion" value="Tipo de Identificación *" />
                                <select
                                    id="tipo_identificacion"
                                    v-model="form.tipo_identificacion"
                                    class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 rounded-md shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50 bg-white"
                                    required
                                >
                                    <option
                                        v-for="option in tipoIdentificacionOptions"
                                        :key="option.value"
                                        :value="option.value"
                                    >
                                        {{ option.label }}
                                    </option>
                                </select>
                                <InputError class="mt-2" :message="form.errors.tipo_identificacion" />
                            </div>

                            <!-- Número de Identificación -->
                            <div v-if="mostrarCampoNumero" class="mt-4">
                                <InputLabel for="numero_identificacion" value="Número de Identificación *" />
                                <TextInput
                                    id="numero_identificacion"
                                    type="text"
                                    class="mt-1 block w-full bg-white dark:bg-gray-900"
                                    v-model="form.numero_identificacion"
                                    :required="mostrarCampoNumero"
                                    :placeholder="getPlaceholderIdentificacion()"
                                />
                                <InputError class="mt-2" :message="form.errors.numero_identificacion" />
                            </div>

                            <!-- Tipo de Tercero -->
                            <div class="mt-4">
                                <InputLabel for="tipo_tercero" value="Tipo de Tercero *" />
                                <select
                                    id="tipo_tercero"
                                    v-model="form.tipo_tercero"
                                    class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 rounded-md shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50 bg-white"
                                    required
                                >
                                    <option
                                        v-for="option in tipoTerceroOptions"
                                        :key="option.value"
                                        :value="option.value"
                                    >
                                        {{ option.label }}
                                    </option>
                                </select>
                                <InputError class="mt-2" :message="form.errors.tipo_tercero" />
                            </div>

                            <!-- Sucursal -->
                            <div class="mt-4">
                                <InputLabel for="sucursal" value="Sucursal *" />
                                <select
                                    id="sucursal"
                                    v-model="form.sucursal"
                                    class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 rounded-md shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50 bg-white"
                                    required
                                >
                                    <option
                                        v-for="option in sucursalOptions"
                                        :key="option.value"
                                        :value="option.value"
                                    >
                                        {{ option.label }}
                                    </option>
                                </select>
                                <InputError class="mt-2" :message="form.errors.sucursal" />
                            </div>

                            <div class="flex items-center justify-end mt-6">
                                <Link
                                    :href="route('clientes.index')"
                                    class="underline text-sm text-secondary hover:text-primary rounded-md mr-4"
                                >
                                    Cancelar
                                </Link>

                                <PrimaryButton :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                                    <span v-if="form.processing" class="flex items-center gap-2">
                                        <LoadingSpinner size="sm" color="white" />
                                        Enviando...
                                    </span>
                                    <span v-else>Enviar Formulario KYC</span>
                                </PrimaryButton>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
