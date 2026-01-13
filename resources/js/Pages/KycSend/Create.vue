<script setup>
import { computed } from 'vue';

// Funciones para conversión de fechas
const convertToDateInput = (dateString) => {
    if (!dateString) return '';
    try {
        const parts = dateString.split('/');
        if (parts.length === 3) {
            const day = parts[0].padStart(2, '0');
            const month = parts[1].padStart(2, '0');
            const year = parts[2];
            return `${year}-${month}-${day}`;
        }
        return '';
    } catch {
        return '';
    }
};

const convertFromDateInput = (dateString) => {
    if (!dateString) return '';
    try {
        const parts = dateString.split('-');
        if (parts.length === 3) {
            const year = parts[0];
            const month = parts[1];
            const day = parts[2];
            return `${day}/${month}/${year}`;
        }
        return '';
    } catch {
        return '';
    }
};

// Computed properties para los campos de fecha
const fechadevencimientoDate = computed({
    get: () => convertToDateInput(form.fechadevencimiento),
    set: (value) => {
        form.fechadevencimiento = convertFromDateInput(value);
    }
});

const fechanacimientoDate = computed({
    get: () => convertToDateInput(form.fechanacimiento),
    set: (value) => {
        form.fechanacimiento = convertFromDateInput(value);
    }
});

const fechaDate = computed({
    get: () => {
        if (!form.fecha) return '';
        return convertToDateInput(form.fecha);
    },
    set: (value) => {
        form.fecha = convertFromDateInput(value);
    }
});
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import LoadingSpinner from '@/Components/LoadingSpinner.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm, usePage } from '@inertiajs/vue3';

const page = usePage();

const userDisplay = computed(() => {
    const user = page.props.auth?.user;
    if (user && user.name && user.email) {
        return `${user.name} - ${user.email}`;
    }
    return '';
});

const form = useForm({
    title: '',
    description: '',
    name_client: '',
    lastname_client: '',
    email_client: '',
    tipo_identificacion: '',
    numero_identificacion: '',
    tipo_tercero: '',
    sucursal: '',
    fechadevencimiento: '',
    sexo: '',
    fechanacimiento: '',
    ciudaddenacimiento: '',
    provinciadenacimiento: '',
    nacionalidad: '',
    profesion: '',
    ocupacioncargo: '',
    empresa: '',
    direcciondondelabora: '',
    ciudad: '',
    provincia: '',
    telefono: '',
    ciudadresidencia: '',
    provinviaresidencia: '',
    pais: '',
    telefonoresidencia: '',
    celular: '',
    direccionresidencia: '',
    sector: '',
    direccionparaenviarproductos: '',
    informacionactividadeconomica: '',
    otroactividadeconomica: '',
    informacionfinanciera: '',
    otrosingresos: '',
    actividadeconomicadeotrosingresos: '',
    recursospublicos: '',
    respuestaafirmativauno: '',
    poderpublico: '',
    personareconocida: '',
    afirmativaderespuesta: '',
    solicituddeseguro: '',
    fecha: new Date().toLocaleDateString('es-ES', { day: '2-digit', month: '2-digit', year: 'numeric' }),
});

const tipoIdentificacionOptions = [
    { value: '', label: 'Seleccione...' },
    { value: 'Cédula', label: 'Cédula' },
    { value: 'RNC', label: 'RNC' },
    { value: 'Pasaporte', label: 'Pasaporte' },
];

const tipoTerceroOptions = [
    { value: '', label: 'Seleccione...' },
    { value: 'Tomador', label: 'Tomador' },
    { value: 'Asegurado', label: 'Asegurado' },
    { value: 'Beneficiario', label: 'Beneficiario' },
    { value: 'Afianzado', label: 'Afianzado' },
    { value: 'Proveedor', label: 'Proveedor' },
    { value: 'Empleado', label: 'Empleado' },
    { value: 'Apoderado', label: 'Apoderado' },
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
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
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
                            <!-- Usuario (ancho completo, bloqueado) -->
                            <div class="mt-4">
                                <InputLabel for="user_name" value="Usuario" />
                                <TextInput
                                    id="user_name"
                                    type="text"
                                    class="mt-1 block w-full bg-gray-100 dark:bg-gray-700"
                                    :value="userDisplay"
                                    readonly
                                    disabled
                                />
                            </div>

                            <!-- Campos en grid de 3 columnas -->
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 mt-4">
                                <!-- Nombre Cliente -->
                                <div>
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
                                <div>
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
                                <div>
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
                                <div>
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
                                <div v-if="mostrarCampoNumero">
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
                                <div>
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
                                <div>
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

                                <!-- Fecha de Vencimiento * -->
                                <div>
                                <InputLabel for="fechadevencimiento" value="Fecha de Vencimiento *" />
                                <input
                                    id="fechadevencimiento"
                                    type="date"
                                    class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 rounded-md shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50 bg-white"
                                    v-model="fechadevencimientoDate"
                                    required
                                />
                                    <InputError class="mt-2" :message="form.errors.fechadevencimiento" />
                                </div>

                                <!-- Sexo * -->
                                <div>
                                <InputLabel for="sexo" value="Sexo *" />
                                <select
                                    id="sexo"
                                    v-model="form.sexo"
                                    class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 rounded-md shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50 bg-white"
                                    required
                                >
                                    <option value="">Seleccione...</option>
                                    <option value="M">Masculino</option>
                                    <option value="F">Femenino</option>
                                </select>
                                    <InputError class="mt-2" :message="form.errors.sexo" />
                                </div>

                                <!-- Fecha de Nacimiento * -->
                                <div>
                                <InputLabel for="fechanacimiento" value="Fecha de Nacimiento *" />
                                <input
                                    id="fechanacimiento"
                                    type="date"
                                    class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 rounded-md shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50 bg-white"
                                    v-model="fechanacimientoDate"
                                    required
                                />
                                    <InputError class="mt-2" :message="form.errors.fechanacimiento" />
                                </div>

                                <!-- Ciudad de Nacimiento -->
                                <div>
                                <InputLabel for="ciudaddenacimiento" value="Ciudad de Nacimiento" />
                                <TextInput
                                    id="ciudaddenacimiento"
                                    type="text"
                                    class="mt-1 block w-full bg-white dark:bg-gray-900"
                                    v-model="form.ciudaddenacimiento"
                                />
                                    <InputError class="mt-2" :message="form.errors.ciudaddenacimiento" />
                                </div>

                                <!-- Provincia de Nacimiento -->
                                <div>
                                <InputLabel for="provinciadenacimiento" value="Provincia de Nacimiento" />
                                <TextInput
                                    id="provinciadenacimiento"
                                    type="text"
                                    class="mt-1 block w-full bg-white dark:bg-gray-900"
                                    v-model="form.provinciadenacimiento"
                                />
                                    <InputError class="mt-2" :message="form.errors.provinciadenacimiento" />
                                </div>

                                <!-- Nacionalidad -->
                                <div>
                                <InputLabel for="nacionalidad" value="Nacionalidad" />
                                <TextInput
                                    id="nacionalidad"
                                    type="text"
                                    class="mt-1 block w-full bg-white dark:bg-gray-900"
                                    v-model="form.nacionalidad"
                                />
                                    <InputError class="mt-2" :message="form.errors.nacionalidad" />
                                </div>

                                <!-- Profesión * -->
                                <div>
                                <InputLabel for="profesion" value="Profesión *" />
                                <TextInput
                                    id="profesion"
                                    type="text"
                                    class="mt-1 block w-full bg-white dark:bg-gray-900"
                                    v-model="form.profesion"
                                    required
                                />
                                    <InputError class="mt-2" :message="form.errors.profesion" />
                                </div>

                                <!-- Ocupación/Cargo * -->
                                <div>
                                <InputLabel for="ocupacioncargo" value="Ocupación/Cargo *" />
                                <TextInput
                                    id="ocupacioncargo"
                                    type="text"
                                    class="mt-1 block w-full bg-white dark:bg-gray-900"
                                    v-model="form.ocupacioncargo"
                                    required
                                />
                                    <InputError class="mt-2" :message="form.errors.ocupacioncargo" />
                                </div>

                                <!-- Empresa * -->
                                <div>
                                <InputLabel for="empresa" value="Empresa *" />
                                <TextInput
                                    id="empresa"
                                    type="text"
                                    class="mt-1 block w-full bg-white dark:bg-gray-900"
                                    v-model="form.empresa"
                                    required
                                />
                                    <InputError class="mt-2" :message="form.errors.empresa" />
                                </div>

                                <!-- Dirección donde labora * -->
                                <div>
                                <InputLabel for="direcciondondelabora" value="Dirección donde labora *" />
                                <TextInput
                                    id="direcciondondelabora"
                                    type="text"
                                    class="mt-1 block w-full bg-white dark:bg-gray-900"
                                    v-model="form.direcciondondelabora"
                                    required
                                />
                                    <InputError class="mt-2" :message="form.errors.direcciondondelabora" />
                                </div>

                                <!-- Ciudad -->
                                <div>
                                <InputLabel for="ciudad" value="Ciudad" />
                                <TextInput
                                    id="ciudad"
                                    type="text"
                                    class="mt-1 block w-full bg-white dark:bg-gray-900"
                                    v-model="form.ciudad"
                                />
                                    <InputError class="mt-2" :message="form.errors.ciudad" />
                                </div>

                                <!-- Provincia -->
                                <div>
                                <InputLabel for="provincia" value="Provincia" />
                                <TextInput
                                    id="provincia"
                                    type="text"
                                    class="mt-1 block w-full bg-white dark:bg-gray-900"
                                    v-model="form.provincia"
                                />
                                    <InputError class="mt-2" :message="form.errors.provincia" />
                                </div>

                                <!-- Teléfono * -->
                                <div>
                                <InputLabel for="telefono" value="Teléfono *" />
                                <TextInput
                                    id="telefono"
                                    type="text"
                                    class="mt-1 block w-full bg-white dark:bg-gray-900"
                                    v-model="form.telefono"
                                    required
                                />
                                    <InputError class="mt-2" :message="form.errors.telefono" />
                                </div>

                                <!-- Ciudad Residencia -->
                                <div>
                                <InputLabel for="ciudadresidencia" value="Ciudad Residencia" />
                                <TextInput
                                    id="ciudadresidencia"
                                    type="text"
                                    class="mt-1 block w-full bg-white dark:bg-gray-900"
                                    v-model="form.ciudadresidencia"
                                />
                                    <InputError class="mt-2" :message="form.errors.ciudadresidencia" />
                                </div>

                                <!-- Provincia Residencia -->
                                <div>
                                <InputLabel for="provinviaresidencia" value="Provincia Residencia" />
                                <TextInput
                                    id="provinviaresidencia"
                                    type="text"
                                    class="mt-1 block w-full bg-white dark:bg-gray-900"
                                    v-model="form.provinviaresidencia"
                                />
                                    <InputError class="mt-2" :message="form.errors.provinviaresidencia" />
                                </div>

                                <!-- País Residencia -->
                                <div>
                                <InputLabel for="pais" value="País Residencia" />
                                <TextInput
                                    id="pais"
                                    type="text"
                                    class="mt-1 block w-full bg-white dark:bg-gray-900"
                                    v-model="form.pais"
                                />
                                    <InputError class="mt-2" :message="form.errors.pais" />
                                </div>

                                <!-- Teléfono Residencia -->
                                <div>
                                <InputLabel for="telefonoresidencia" value="Teléfono Residencia" />
                                <TextInput
                                    id="telefonoresidencia"
                                    type="text"
                                    class="mt-1 block w-full bg-white dark:bg-gray-900"
                                    v-model="form.telefonoresidencia"
                                />
                                    <InputError class="mt-2" :message="form.errors.telefonoresidencia" />
                                </div>

                                <!-- Celular -->
                                <div>
                                <InputLabel for="celular" value="Celular" />
                                <TextInput
                                    id="celular"
                                    type="text"
                                    class="mt-1 block w-full bg-white dark:bg-gray-900"
                                    v-model="form.celular"
                                />
                                    <InputError class="mt-2" :message="form.errors.celular" />
                                </div>

                                <!-- Dirección Residencia -->
                                <div>
                                <InputLabel for="direccionresidencia" value="Dirección Residencia" />
                                <TextInput
                                    id="direccionresidencia"
                                    type="text"
                                    class="mt-1 block w-full bg-white dark:bg-gray-900"
                                    v-model="form.direccionresidencia"
                                />
                                    <InputError class="mt-2" :message="form.errors.direccionresidencia" />
                                </div>

                                <!-- Sector -->
                                <div>
                                <InputLabel for="sector" value="Sector" />
                                <TextInput
                                    id="sector"
                                    type="text"
                                    class="mt-1 block w-full bg-white dark:bg-gray-900"
                                    v-model="form.sector"
                                />
                                    <InputError class="mt-2" :message="form.errors.sector" />
                                </div>

                                <!-- Enviar A -->
                                <div>
                                <InputLabel for="direccionparaenviarproductos" value="Enviar A" />
                                <select
                                    id="direccionparaenviarproductos"
                                    v-model="form.direccionparaenviarproductos"
                                    class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 rounded-md shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50 bg-white"
                                >
                                    <option value="">Seleccione...</option>
                                    <option value="Trabajo">Trabajo</option>
                                    <option value="Correo electrónico">Correo electrónico</option>
                                    <option value="Residencia">Residencia</option>
                                </select>
                                    <InputError class="mt-2" :message="form.errors.direccionparaenviarproductos" />
                                </div>

                                <!-- Actividad Económica -->
                                <div>
                                <InputLabel for="informacionactividadeconomica" value="Actividad Económica" />
                                <select
                                    id="informacionactividadeconomica"
                                    v-model="form.informacionactividadeconomica"
                                    class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 rounded-md shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50 bg-white"
                                >
                                    <option value="">Seleccione...</option>
                                    <option value="Empleado/Asalariado">Empleado/Asalariado</option>
                                    <option value="Propio/Socio">Propio/Socio</option>
                                    <option value="Jubilado/Pensionado">Jubilado/Pensionado</option>
                                    <option value="Inversionista/Prestamista">Inversionista/Prestamista</option>
                                    <option value="Independiente">Independiente</option>
                                    <option value="Estudiante">Estudiante</option>
                                    <option value="Ama de casa">Ama de casa</option>
                                    <option value="Otro">Otro</option>
                                </select>
                                    <InputError class="mt-2" :message="form.errors.informacionactividadeconomica" />
                                </div>

                                <!-- Otro Actividad Económica (si selecciona Otro) -->
                                <div v-if="form.informacionactividadeconomica === 'Otro'">
                                <InputLabel for="otroactividadeconomica" value="Especifique Actividad Económica" />
                                <TextInput
                                    id="otroactividadeconomica"
                                    type="text"
                                    class="mt-1 block w-full bg-white dark:bg-gray-900"
                                    v-model="form.otroactividadeconomica"
                                />
                                    <InputError class="mt-2" :message="form.errors.otroactividadeconomica" />
                                </div>

                                <!-- Ingresos Actividad Principal -->
                                <div>
                                <InputLabel for="informacionfinanciera" value="Ingresos Actividad Principal" />
                                <TextInput
                                    id="informacionfinanciera"
                                    type="text"
                                    class="mt-1 block w-full bg-white dark:bg-gray-900"
                                    v-model="form.informacionfinanciera"
                                />
                                    <InputError class="mt-2" :message="form.errors.informacionfinanciera" />
                                </div>

                                <!-- Otros Ingresos Mensuales -->
                                <div>
                                <InputLabel for="otrosingresos" value="Otros Ingresos Mensuales" />
                                <TextInput
                                    id="otrosingresos"
                                    type="text"
                                    class="mt-1 block w-full bg-white dark:bg-gray-900"
                                    v-model="form.otrosingresos"
                                />
                                    <InputError class="mt-2" :message="form.errors.otrosingresos" />
                                </div>

                                <!-- Actividad Económica de Otros Ingresos -->
                                <div>
                                <InputLabel for="actividadeconomicadeotrosingresos" value="Actividad Económica de Otros Ingresos" />
                                <TextInput
                                    id="actividadeconomicadeotrosingresos"
                                    type="text"
                                    class="mt-1 block w-full bg-white dark:bg-gray-900"
                                    v-model="form.actividadeconomicadeotrosingresos"
                                />
                                    <InputError class="mt-2" :message="form.errors.actividadeconomicadeotrosingresos" />
                                </div>

                                <!-- Maneja Recursos Públicos -->
                                <div>
                                <InputLabel for="recursospublicos" value="Maneja Recursos Públicos" />
                                <select
                                    id="recursospublicos"
                                    v-model="form.recursospublicos"
                                    class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 rounded-md shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50 bg-white"
                                >
                                    <option value="">Seleccione...</option>
                                    <option value="Si">Si</option>
                                    <option value="No">No</option>
                                </select>
                                    <InputError class="mt-2" :message="form.errors.recursospublicos" />
                                </div>

                                <!-- Respuesta Afirmativa Uno (si selecciona Si) -->
                                <div v-if="form.recursospublicos === 'Si'">
                                <InputLabel for="respuestaafirmativauno" value="Especifique Recursos Públicos" />
                                <TextInput
                                    id="respuestaafirmativauno"
                                    type="text"
                                    class="mt-1 block w-full bg-white dark:bg-gray-900"
                                    v-model="form.respuestaafirmativauno"
                                />
                                    <InputError class="mt-2" :message="form.errors.respuestaafirmativauno" />
                                </div>

                                <!-- Posee Poder Público -->
                                <div>
                                <InputLabel for="poderpublico" value="Posee algún grado de Poder" />
                                <select
                                    id="poderpublico"
                                    v-model="form.poderpublico"
                                    class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 rounded-md shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50 bg-white"
                                >
                                    <option value="">Seleccione...</option>
                                    <option value="Si">Si</option>
                                    <option value="No">No</option>
                                </select>
                                    <InputError class="mt-2" :message="form.errors.poderpublico" />
                                </div>

                                <!-- Persona Pública -->
                                <div>
                                <InputLabel for="personareconocida" value="Persona Pública" />
                                <select
                                    id="personareconocida"
                                    v-model="form.personareconocida"
                                    class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 rounded-md shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50 bg-white"
                                >
                                    <option value="">Seleccione...</option>
                                    <option value="Si">Si</option>
                                    <option value="No">No</option>
                                </select>
                                    <InputError class="mt-2" :message="form.errors.personareconocida" />
                                </div>

                                <!-- Familia Pública -->
                                <div>
                                <InputLabel for="afirmativaderespuesta" value="Familia Pública" />
                                <select
                                    id="afirmativaderespuesta"
                                    v-model="form.afirmativaderespuesta"
                                    class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 rounded-md shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50 bg-white"
                                >
                                    <option value="">Seleccione...</option>
                                    <option value="Si">Si</option>
                                    <option value="No">No</option>
                                </select>
                                    <InputError class="mt-2" :message="form.errors.afirmativaderespuesta" />
                                </div>

                                <!-- Solicitud de Seguro -->
                                <div>
                                <InputLabel for="solicituddeseguro" value="Solicitud de Seguro" />
                                <select
                                    id="solicituddeseguro"
                                    v-model="form.solicituddeseguro"
                                    class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 rounded-md shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50 bg-white"
                                >
                                    <option value="">Seleccione...</option>
                                    <option value="Ramo">Ramo</option>
                                    <option value="Personas">Personas</option>
                                    <option value="Generales">Generales</option>
                                    <option value="Fianza">Fianza</option>
                                    <option value="Otro">Otro</option>
                                </select>
                                    <InputError class="mt-2" :message="form.errors.solicituddeseguro" />
                                </div>

                                <!-- Fecha -->
                                <div>
                                <InputLabel for="fecha" value="Fecha" />
                                <input
                                    id="fecha"
                                    type="date"
                                    class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 rounded-md shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50 bg-gray-100"
                                    v-model="fechaDate"
                                    readonly
                                />
                                    <InputError class="mt-2" :message="form.errors.fecha" />
                                </div>
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
