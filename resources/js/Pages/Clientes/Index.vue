<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import LoadingSpinner from '@/Components/LoadingSpinner.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import InputLabel from '@/Components/InputLabel.vue';
import { Head, router, useForm, usePage } from '@inertiajs/vue3';
import { ref, watch, computed } from 'vue';

const page = usePage();

const userDisplay = computed(() => {
    const user = page.props.auth?.user;
    if (user && user.name && user.email) {
        return `${user.name} - ${user.email}`;
    }
    return '';
});

const props = defineProps({
    clientes: {
        type: Object,
        default: null,
    },
    total: {
        type: Number,
        default: 0,
    },
    filters: {
        type: Object,
        default: () => ({}),
    },
    error: {
        type: String,
        default: null,
    },
});

const form = useForm({
    cnomcliente: props.filters.cnomcliente || '',
    crnc: props.filters.crnc || '',
    ccedula: props.filters.ccedula || '',
    tipo_cliente: props.filters.tipo_cliente || '',
    estatus: props.filters.estatus || '',
    sucursal: props.filters.sucursal || '',
    es_prospecto: props.filters.es_prospecto || '',
    page: 1,
    page_size: 10,
});

const tipoClienteOptions = [
    { value: '', label: 'Todos' },
    { value: 'PERSONAL', label: 'PERSONAL' },
    { value: 'PERSONALES PREMIUM', label: 'PERSONALES PREMIUM' },
    { value: 'COMERCIAL', label: 'COMERCIAL' },
    { value: 'CORPORATIVOS', label: 'CORPORATIVOS' },
];

const estatusOptions = [
    { value: '', label: 'Todos' },
    { value: 'A', label: 'Activo' },
    { value: 'I', label: 'Inactivo' },
];

const sucursalOptions = [
    { value: '', label: 'Todas' },
    { value: 'Principal', label: 'Principal' },
    { value: 'Romana', label: 'Romana' },
    { value: 'Punta Cana', label: 'Punta Cana' },
];

const tipoOptions = [
    { value: '', label: 'Todos' },
    { value: 'C', label: 'Cliente' },
    { value: 'P', label: 'Prospecto' },
];

const aplicarFiltros = () => {
    form.page = 1; // Resetear a la primera página
    form.get(route('clientes.index'), {
        preserveState: true,
        preserveScroll: true,
    });
};

const limpiarFiltros = () => {
    form.reset();
    form.page = 1;
    form.get(route('clientes.index'), {
        preserveState: true,
        preserveScroll: true,
    });
};

const cambiarPagina = (page) => {
    form.page = page;
    form.get(route('clientes.index'), {
        preserveState: true,
        preserveScroll: true,
    });
};

const cambiarPageSize = (newSize) => {
    form.page_size = newSize;
    form.page = 1; // Resetear a la primera página
    form.get(route('clientes.index'), {
        preserveState: true,
        preserveScroll: true,
    });
};

const showModal = ref(false);
const selectedCliente = ref(null);
const kycForm = useForm({
    tipo_persona: '',
    name_client: '',
    lastname_client: '',
    email_client: '',
    tipo_tercero: '',
    sucursal: '',
    tipodeidentificacion: '',
    numero_identificacion: '',
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

const formatDateForInput = (dateString) => {
    if (!dateString) return '';
    try {
        const date = new Date(dateString);
        const day = String(date.getDate()).padStart(2, '0');
        const month = String(date.getMonth() + 1).padStart(2, '0');
        const year = date.getFullYear();
        return `${day}/${month}/${year}`;
    } catch {
        return '';
    }
};

// Convertir de dd/mm/aaaa a yyyy-mm-dd para input type="date"
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

// Convertir de yyyy-mm-dd a dd/mm/aaaa
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
    get: () => convertToDateInput(kycForm.fechadevencimiento),
    set: (value) => {
        kycForm.fechadevencimiento = convertFromDateInput(value);
    }
});

const fechanacimientoDate = computed({
    get: () => convertToDateInput(kycForm.fechanacimiento),
    set: (value) => {
        kycForm.fechanacimiento = convertFromDateInput(value);
    }
});

const fechaDate = computed({
    get: () => {
        if (!kycForm.fecha) return '';
        return convertToDateInput(kycForm.fecha);
    },
    set: (value) => {
        kycForm.fecha = convertFromDateInput(value);
    }
});

const getTipoIdentificacion = (cliente) => {
    if (cliente.cedula) return 'Cédula';
    if (cliente.rnc) return 'RNC';
    if (cliente.pasaporte) return 'Pasaporte';
    return '';
};

const getNumeroIdentificacion = (cliente) => {
    if (cliente.cedula) return cliente.cedula;
    if (cliente.rnc) return cliente.rnc;
    if (cliente.pasaporte) return cliente.pasaporte;
    return '';
};

const enviarKYC = (cliente) => {
    selectedCliente.value = cliente;
    
    // Determinar tipo de persona basado en tipo_cliente
    const tipoCliente = cliente.tipo_cliente || '';
    if (tipoCliente === 'PERSONAL' || tipoCliente === 'PERSONALES PREMIUM') {
        kycForm.tipo_persona = 'fisica';
    } else {
        kycForm.tipo_persona = 'juridica';
    }
    
    // Prellenar el formulario con los datos del cliente
    kycForm.name_client = cliente.nombre || '';
    kycForm.lastname_client = cliente.apellido || '';
    kycForm.email_client = cliente.email_1 || '';
    kycForm.sucursal = cliente.sucursal || 'Principal';
    kycForm.tipodeidentificacion = getTipoIdentificacion(cliente);
    kycForm.numero_identificacion = getNumeroIdentificacion(cliente);
    kycForm.sexo = cliente.sexo || '';
    kycForm.fechanacimiento = formatDateForInput(cliente.fecha_nacimiento);
    kycForm.profesion = cliente.profecion || '';
    kycForm.ocupacioncargo = cliente.cargo || '';
    kycForm.empresa = cliente.empresa || '';
    kycForm.direcciondondelabora = cliente.dirreccion_1 || '';
    kycForm.telefono = cliente.telefono_oficina || '';
    kycForm.celular = cliente.numero_celular || '';
    kycForm.direccionresidencia = cliente.dirreccion_casa_1 || '';
    kycForm.sector = cliente.dirreccion_casa_2 || '';
    kycForm.fecha = new Date().toLocaleDateString('es-ES', { day: '2-digit', month: '2-digit', year: 'numeric' });
    
    showModal.value = true;
};

const cerrarModal = () => {
    showModal.value = false;
    selectedCliente.value = null;
    kycForm.reset();
};

const submitKyc = () => {
    // Si seleccionó "Otro" en actividad económica, usar el campo otroactividadeconomica
    if (kycForm.informacionactividadeconomica === 'Otro' && kycForm.otroactividadeconomica) {
        kycForm.informacionactividadeconomica = kycForm.otroactividadeconomica;
    }
    
    kycForm.post(route('clientes.enviar-kyc'), {
        preserveScroll: true,
        onSuccess: () => {
            cerrarModal();
        },
    });
};

const getCedulaRnc = (cliente) => {
    if (cliente.cedula) {
        return cliente.cedula;
    }
    if (cliente.rnc) {
        return cliente.rnc;
    }
    return '-';
};

const getEstatusLabel = (estatus) => {
    return estatus === 'A' ? 'Activo' : 'Inactivo';
};

const getEstatusClass = (estatus) => {
    return estatus === 'A' 
        ? 'px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800' 
        : 'px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800';
};

const getNombreCompleto = (cliente) => {
    return `${cliente.nombre} ${cliente.apellido}`.trim();
};
</script>

<template>
    <Head title="Clientes" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-secondary leading-tight">Gestión de Clientes</h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <!-- Resumen -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <h3 class="text-lg font-semibold text-secondary">Total de Clientes</h3>
                                <p class="text-3xl font-bold text-primary mt-2">{{ total.toLocaleString() }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Filtros -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-secondary mb-4">Filtros de Búsqueda</h3>
                        
                        <div v-if="error" class="mb-4 p-4 bg-red-100 text-red-700 rounded">
                            {{ error }}
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                            <!-- Nombre -->
                            <div>
                                <InputLabel for="cnomcliente" value="Nombre (Parcial)" />
                                <TextInput
                                    id="cnomcliente"
                                    type="text"
                                    class="mt-1 block w-full bg-white"
                                    v-model="form.cnomcliente"
                                    placeholder="Buscar por nombre..."
                                />
                            </div>

                            <!-- RNC -->
                            <div>
                                <InputLabel for="crnc" value="RNC (Parcial)" />
                                <TextInput
                                    id="crnc"
                                    type="text"
                                    class="mt-1 block w-full bg-white"
                                    v-model="form.crnc"
                                    placeholder="Buscar por RNC..."
                                />
                            </div>

                            <!-- Cédula -->
                            <div>
                                <InputLabel for="ccedula" value="Cédula (Parcial)" />
                                <TextInput
                                    id="ccedula"
                                    type="text"
                                    class="mt-1 block w-full bg-white"
                                    v-model="form.ccedula"
                                    placeholder="Buscar por cédula..."
                                />
                            </div>

                            <!-- Tipo Cliente -->
                            <div>
                                <InputLabel for="tipo_cliente" value="Tipo Cliente" />
                                <select
                                    id="tipo_cliente"
                                    v-model="form.tipo_cliente"
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50"
                                >
                                    <option v-for="option in tipoClienteOptions" :key="option.value" :value="option.value">
                                        {{ option.label }}
                                    </option>
                                </select>
                            </div>

                            <!-- Estatus -->
                            <div>
                                <InputLabel for="estatus" value="Estatus" />
                                <select
                                    id="estatus"
                                    v-model="form.estatus"
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50"
                                >
                                    <option v-for="option in estatusOptions" :key="option.value" :value="option.value">
                                        {{ option.label }}
                                    </option>
                                </select>
                            </div>

                            <!-- Sucursal -->
                            <div>
                                <InputLabel for="sucursal" value="Sucursal" />
                                <select
                                    id="sucursal"
                                    v-model="form.sucursal"
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50"
                                >
                                    <option v-for="option in sucursalOptions" :key="option.value" :value="option.value">
                                        {{ option.label }}
                                    </option>
                                </select>
                            </div>

                            <!-- Tipo -->
                            <div>
                                <InputLabel for="es_prospecto" value="Tipo" />
                                <select
                                    id="es_prospecto"
                                    v-model="form.es_prospecto"
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50"
                                >
                                    <option v-for="option in tipoOptions" :key="option.value" :value="option.value">
                                        {{ option.label }}
                                    </option>
                                </select>
                            </div>
                        </div>

                        <div class="mt-4 flex gap-2">
                            <PrimaryButton @click="aplicarFiltros" :disabled="form.processing">
                                <span v-if="form.processing" class="flex items-center gap-2">
                                    <LoadingSpinner size="sm" color="white" />
                                    Buscando...
                                </span>
                                <span v-else>Buscar</span>
                            </PrimaryButton>
                            <button
                                @click="limpiarFiltros"
                                class="px-4 py-2 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-500 disabled:opacity-50 disabled:cursor-not-allowed flex items-center gap-2"
                                :disabled="form.processing"
                            >
                                <LoadingSpinner v-if="form.processing" size="sm" color="gray" />
                                Limpiar
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Tabla -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 relative">
                        <!-- Loading Overlay -->
                        <div
                            v-if="form.processing && (!clientes || !clientes.data || clientes.data.length === 0)"
                            class="absolute inset-0 bg-white bg-opacity-75 flex items-center justify-center z-10"
                        >
                            <div class="flex flex-col items-center gap-3">
                                <LoadingSpinner size="lg" color="primary" />
                                <p class="text-secondary">Cargando clientes...</p>
                            </div>
                        </div>

                        <div v-if="!form.processing && (!clientes || !clientes.data || clientes.data.length === 0)" class="text-center py-8">
                            <p class="text-gray-500">No se encontraron clientes con los filtros aplicados.</p>
                        </div>

                        <div v-else class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-secondary">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider" style="max-width: 150px; width: 150px;">
                                            Nombre
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">
                                            Cédula/RNC
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">
                                            Estatus
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider" style="max-width: 150px; width: 150px;">
                                            Correo
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">
                                            Tipo Cliente
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">
                                            Sucursal
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">
                                            Acciones
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <tr v-for="cliente in clientes.data" :key="cliente.id_client" class="hover:bg-gray-50">
                                        <td class="px-6 py-4 text-sm text-secondary" style="max-width: 250px; width: 250px;">
                                            <div 
                                                class="truncate" 
                                                :title="getNombreCompleto(cliente)"
                                            >
                                                {{ getNombreCompleto(cliente) }}
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-secondary">
                                            {{ getCedulaRnc(cliente) }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                                            <span :class="getEstatusClass(cliente.estatus)">
                                                {{ getEstatusLabel(cliente.estatus) }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 text-sm text-accent" style="max-width: 150px; width: 150px;">
                                            <div 
                                                class="truncate" 
                                                :title="cliente.email_1 || '-'"
                                            >
                                                {{ cliente.email_1 || '-' }}
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-secondary">
                                            {{ cliente.tipo_cliente }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-secondary">
                                            {{ cliente.sucursal }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            <button
                                                @click="enviarKYC(cliente)"
                                                class="px-3 py-1 bg-primary text-white rounded-md hover:bg-primary-dark focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-2 transition duration-150 ease-in-out"
                                            >
                                                Enviar KYC
                                            </button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <!-- Paginación -->
                        <div v-if="clientes && clientes.data && clientes.data.length > 0" class="mt-4 flex flex-col sm:flex-row justify-between items-center gap-4">
                            <div class="flex items-center gap-2">
                                <label for="page_size" class="text-sm text-secondary">Registros por página:</label>
                                <div class="relative">
                                    <select
                                        id="page_size"
                                        v-model="form.page_size"
                                        @change="cambiarPageSize(form.page_size)"
                                        :disabled="form.processing"
                                        class="px-6 py-2 border border-gray-300 rounded-md shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50 text-sm disabled:opacity-50 disabled:cursor-not-allowed"
                                    >
                                        <option value="10">10</option>
                                        <option value="30">30</option>
                                        <option value="50">50</option>
                                        <option value="100">100</option>
                                    </select>
                                    <LoadingSpinner
                                        v-if="form.processing"
                                        size="sm"
                                        color="gray"
                                        class="absolute right-2 top-1/2 transform -translate-y-1/2"
                                    />
                                </div>
                            </div>

                            <div class="flex items-center gap-2">
                                <button
                                    @click="cambiarPagina(clientes.current_page - 1)"
                                    :disabled="clientes.current_page <= 1 || form.processing"
                                    class="px-3 py-2 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300 disabled:opacity-50 disabled:cursor-not-allowed flex items-center gap-2"
                                >
                                    <LoadingSpinner v-if="form.processing && clientes.current_page > 1" size="sm" color="gray" />
                                    Anterior
                                </button>
                                
                                <span class="px-4 py-2 text-sm text-secondary">
                                    <span v-if="form.processing" class="flex items-center gap-2">
                                        <LoadingSpinner size="sm" color="gray" />
                                        Cargando...
                                    </span>
                                    <span v-else>
                                        Página {{ clientes.current_page }} de {{ clientes.last_page }}
                                    </span>
                                </span>
                                
                                <button
                                    @click="cambiarPagina(clientes.current_page + 1)"
                                    :disabled="clientes.current_page >= clientes.last_page || form.processing"
                                    class="px-3 py-2 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300 disabled:opacity-50 disabled:cursor-not-allowed flex items-center gap-2"
                                >
                                    Siguiente
                                    <LoadingSpinner v-if="form.processing && clientes.current_page < clientes.last_page" size="sm" color="gray" />
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal Enviar KYC -->
        <Transition
            enter-active-class="transition ease-out duration-300"
            enter-from-class="opacity-0"
            enter-to-class="opacity-100"
            leave-active-class="transition ease-in duration-200"
            leave-from-class="opacity-100"
            leave-to-class="opacity-0"
        >
            <div
                v-if="showModal"
                class="fixed inset-0 z-50 overflow-y-auto"
                @click.self="cerrarModal"
            >
                <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                    <div class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75" @click="cerrarModal"></div>

                    <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-4xl sm:w-full">
                        <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4 max-h-[90vh] overflow-y-auto">
                            <div class="flex items-center justify-between mb-4">
                                <h3 class="text-lg font-medium text-secondary">Enviar Formulario KYC</h3>
                                <button
                                    @click="cerrarModal"
                                    class="text-gray-400 hover:text-gray-500"
                                >
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </button>
                            </div>

                            <div v-if="$page.props.flash?.success" class="mb-4 p-4 bg-green-100 text-green-700 rounded">
                                {{ $page.props.flash.success }}
                            </div>

                            <div v-if="$page.props.errors?.kyc_error" class="mb-4 p-4 bg-red-100 text-red-700 rounded">
                                {{ $page.props.errors.kyc_error }}
                            </div>

                            <form @submit.prevent="submitKyc" class="space-y-4">
                                <!-- Empleado (ancho completo) -->
                                <!--<div>
                                    <InputLabel for="user_name" value="Usuario" />
                                    <TextInput
                                        id="user_name"
                                        type="text"
                                        class="mt-1 block w-full bg-gray-100 dark:bg-gray-700"
                                        :value="userDisplay"
                                        readonly
                                        disabled
                                    />
                                </div> -->

                                <!-- Campos Obligatorios -->
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <!-- Tipo de Persona * -->
                                    <div>
                                        <InputLabel for="tipo_persona" value="Tipo de Persona *" />
                                        <select
                                            id="tipo_persona"
                                            v-model="kycForm.tipo_persona"
                                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50"
                                            required
                                        >
                                            <option value="">Seleccione...</option>
                                            <option value="fisica">Persona Física</option>
                                            <option value="juridica">Persona Jurídica</option>
                                        </select>
                                        <span v-if="kycForm.errors.tipo_persona" class="text-red-600 text-sm">{{ kycForm.errors.tipo_persona }}</span>
                                    </div>

                                    <!-- Tipo de Tercero * -->
                                    <div>
                                        <InputLabel for="tipo_tercero" value="Tipo de Tercero *" />
                                        <select
                                            id="tipo_tercero"
                                            v-model="kycForm.tipo_tercero"
                                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50"
                                            required
                                        >
                                            <option value="">Seleccione...</option>
                                            <option value="Tomador">Tomador</option>
                                            <option value="Asegurado">Asegurado</option>
                                            <option value="Beneficiario">Beneficiario</option>
                                            <option value="Afianzado">Afianzado</option>
                                            <option value="Proveedor">Proveedor</option>
                                            <option value="Empleado">Empleado</option>
                                            <option value="Apoderado">Apoderado</option>
                                        </select>
                                        <span v-if="kycForm.errors.tipo_tercero" class="text-red-600 text-sm">{{ kycForm.errors.tipo_tercero }}</span>
                                    </div>

                                    <!-- Sucursal * -->
                                    <div>
                                        <InputLabel for="sucursal" value="Sucursal *" />
                                        <select
                                            id="sucursal"
                                            v-model="kycForm.sucursal"
                                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50"
                                            required
                                        >
                                            <option value="">Seleccione...</option>
                                            <option value="Principal">Principal</option>
                                            <option value="Romana">Romana</option>
                                            <option value="Punta Cana">Punta Cana</option>
                                        </select>
                                        <span v-if="kycForm.errors.sucursal" class="text-red-600 text-sm">{{ kycForm.errors.sucursal }}</span>
                                    </div>

                                    <!-- Tipo de Identificación * -->
                                    <div>
                                        <InputLabel for="tipodeidentificacion" value="Tipo de Identificación *" />
                                        <select
                                            id="tipodeidentificacion"
                                            v-model="kycForm.tipodeidentificacion"
                                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50"
                                            required
                                        >
                                            <option value="">Seleccione...</option>
                                            <option value="Cédula">Cédula</option>
                                            <option value="RNC">RNC</option>
                                            <option value="Pasaporte">Pasaporte</option>
                                        </select>
                                        <span v-if="kycForm.errors.tipodeidentificacion" class="text-red-600 text-sm">{{ kycForm.errors.tipodeidentificacion }}</span>
                                    </div>

                                    <!-- Número * -->
                                    <div>
                                        <InputLabel for="numero_identificacion" value="Número *" />
                                        <TextInput
                                            id="numero_identificacion"
                                            type="text"
                                            class="mt-1 block w-full"
                                            v-model="kycForm.numero_identificacion"
                                            required
                                        />
                                        <span v-if="kycForm.errors.numero_identificacion" class="text-red-600 text-sm">{{ kycForm.errors.numero_identificacion }}</span>
                                    </div>

                                    <!-- Fecha de Vencimiento * -->
                                    <div>
                                        <InputLabel for="fechadevencimiento" value="Fecha de Vencimiento *" />
                                        <input
                                            id="fechadevencimiento"
                                            type="date"
                                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50"
                                            v-model="fechadevencimientoDate"
                                            required
                                        />
                                        <span v-if="kycForm.errors.fechadevencimiento" class="text-red-600 text-sm">{{ kycForm.errors.fechadevencimiento }}</span>
                                    </div>

                                    <!-- Nombres * -->
                                    <div>
                                        <InputLabel for="name_client" value="Nombres *" />
                                        <TextInput
                                            id="name_client"
                                            type="text"
                                            class="mt-1 block w-full"
                                            v-model="kycForm.name_client"
                                            required
                                        />
                                        <span v-if="kycForm.errors.name_client" class="text-red-600 text-sm">{{ kycForm.errors.name_client }}</span>
                                    </div>

                                    <!-- Apellidos * -->
                                    <div>
                                        <InputLabel for="lastname_client" value="Apellidos *" />
                                        <TextInput
                                            id="lastname_client"
                                            type="text"
                                            class="mt-1 block w-full"
                                            v-model="kycForm.lastname_client"
                                            required
                                        />
                                        <span v-if="kycForm.errors.lastname_client" class="text-red-600 text-sm">{{ kycForm.errors.lastname_client }}</span>
                                    </div>

                                    <!-- Sexo * -->
                                    <div>
                                        <InputLabel for="sexo" value="Sexo *" />
                                        <select
                                            id="sexo"
                                            v-model="kycForm.sexo"
                                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50"
                                            required
                                        >
                                            <option value="">Seleccione...</option>
                                            <option value="M">Masculino</option>
                                            <option value="F">Femenino</option>
                                        </select>
                                        <span v-if="kycForm.errors.sexo" class="text-red-600 text-sm">{{ kycForm.errors.sexo }}</span>
                                    </div>

                                    <!-- Fecha de Nacimiento * -->
                                    <div>
                                        <InputLabel for="fechanacimiento" value="Fecha de Nacimiento" />
                                        <input
                                            id="fechanacimiento"
                                            type="date"
                                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50"
                                            v-model="fechanacimientoDate"                                            
                                        />
                                        <span v-if="kycForm.errors.fechanacimiento" class="text-red-600 text-sm">{{ kycForm.errors.fechanacimiento }}</span>
                                    </div>

                                    <!-- Ciudad de Nacimiento -->
                                    <div>
                                        <InputLabel for="ciudaddenacimiento" value="Ciudad de Nacimiento" />
                                        <TextInput
                                            id="ciudaddenacimiento"
                                            type="text"
                                            class="mt-1 block w-full"
                                            v-model="kycForm.ciudaddenacimiento"
                                        />
                                    </div>

                                    <!-- Provincia de Nacimiento -->
                                    <div>
                                        <InputLabel for="provinciadenacimiento" value="Provincia de Nacimiento" />
                                        <TextInput
                                            id="provinciadenacimiento"
                                            type="text"
                                            class="mt-1 block w-full"
                                            v-model="kycForm.provinciadenacimiento"
                                        />
                                    </div>

                                    <!-- Nacionalidad -->
                                    <div>
                                        <InputLabel for="nacionalidad" value="Nacionalidad" />
                                        <TextInput
                                            id="nacionalidad"
                                            type="text"
                                            class="mt-1 block w-full"
                                            v-model="kycForm.nacionalidad"
                                        />
                                    </div>

                                    <!-- Profesión * -->
                                    <div>
                                        <InputLabel for="profesion" value="Profesión" />
                                        <TextInput
                                            id="profesion"
                                            type="text"
                                            class="mt-1 block w-full"
                                            v-model="kycForm.profesion"
                                        />
                                        <span v-if="kycForm.errors.profesion" class="text-red-600 text-sm">{{ kycForm.errors.profesion }}</span>
                                    </div>

                                    <!-- Ocupación/Cargo * -->
                                    <div>
                                        <InputLabel for="ocupacioncargo" value="Ocupación/Cargo" />
                                        <TextInput
                                            id="ocupacioncargo"
                                            type="text"
                                            class="mt-1 block w-full"
                                            v-model="kycForm.ocupacioncargo"
                                        />
                                        <span v-if="kycForm.errors.ocupacioncargo" class="text-red-600 text-sm">{{ kycForm.errors.ocupacioncargo }}</span>
                                    </div>

                                    <!-- Empresa * -->
                                    <div>
                                        <InputLabel for="empresa" value="Empresa" />
                                        <TextInput
                                            id="empresa"
                                            type="text"
                                            class="mt-1 block w-full"
                                            v-model="kycForm.empresa"
                                        />
                                        <span v-if="kycForm.errors.empresa" class="text-red-600 text-sm">{{ kycForm.errors.empresa }}</span>
                                    </div>

                                    <!-- Dirección donde labora * -->
                                    <div>
                                        <InputLabel for="direcciondondelabora" value="Dirección donde labora" />
                                        <TextInput
                                            id="direcciondondelabora"
                                            type="text"
                                            class="mt-1 block w-full"
                                            v-model="kycForm.direcciondondelabora"
                                        />
                                        <span v-if="kycForm.errors.direcciondondelabora" class="text-red-600 text-sm">{{ kycForm.errors.direcciondondelabora }}</span>
                                    </div>

                                    <!-- Ciudad -->
                                    <div>
                                        <InputLabel for="ciudad" value="Ciudad" />
                                        <TextInput
                                            id="ciudad"
                                            type="text"
                                            class="mt-1 block w-full"
                                            v-model="kycForm.ciudad"
                                        />
                                    </div>

                                    <!-- Provincia -->
                                    <div>
                                        <InputLabel for="provincia" value="Provincia" />
                                        <TextInput
                                            id="provincia"
                                            type="text"
                                            class="mt-1 block w-full"
                                            v-model="kycForm.provincia"
                                        />
                                    </div>
                                    

                                    <!-- Teléfono * -->
                                    <div>
                                        <InputLabel for="telefono" value="Teléfono" />
                                        <TextInput
                                            id="telefono"
                                            type="text"
                                            class="mt-1 block w-full"
                                            v-model="kycForm.telefono"
                                        />
                                        <span v-if="kycForm.errors.telefono" class="text-red-600 text-sm">{{ kycForm.errors.telefono }}</span>
                                    </div>

                                    <!-- Ciudad Residencia -->
                                    <div>
                                        <InputLabel for="ciudadresidencia" value="Ciudad Residencia" />
                                        <TextInput
                                            id="ciudadresidencia"
                                            type="text"
                                            class="mt-1 block w-full"
                                            v-model="kycForm.ciudadresidencia"
                                        />
                                    </div>

                                    <!-- Provincia Residencia -->
                                    <div>
                                        <InputLabel for="provinviaresidencia" value="Provincia Residencia" />
                                        <TextInput
                                            id="provinviaresidencia"
                                            type="text"
                                            class="mt-1 block w-full"
                                            v-model="kycForm.provinviaresidencia"
                                        />
                                    </div>

                                    <!-- País Residencia -->
                                    <div>
                                        <InputLabel for="pais" value="País Residencia" />
                                        <TextInput
                                            id="pais"
                                            type="text"
                                            class="mt-1 block w-full"
                                            v-model="kycForm.pais"
                                        />
                                    </div>

                                    <!-- Teléfono Residencia -->
                                    <div>
                                        <InputLabel for="telefonoresidencia" value="Teléfono Residencia" />
                                        <TextInput
                                            id="telefonoresidencia"
                                            type="text"
                                            class="mt-1 block w-full"
                                            v-model="kycForm.telefonoresidencia"
                                        />
                                    </div>

                                    <!-- Celular -->
                                    <div>
                                        <InputLabel for="celular" value="Celular" />
                                        <TextInput
                                            id="celular"
                                            type="text"
                                            class="mt-1 block w-full"
                                            v-model="kycForm.celular"
                                        />
                                    </div>

                                    <!-- Dirección Residencia -->
                                    <div>
                                        <InputLabel for="direccionresidencia" value="Dirección Residencia" />
                                        <TextInput
                                            id="direccionresidencia"
                                            type="text"
                                            class="mt-1 block w-full"
                                            v-model="kycForm.direccionresidencia"
                                        />
                                    </div>

                                    <!-- Sector -->
                                    <div>
                                        <InputLabel for="sector" value="Sector" />
                                        <TextInput
                                            id="sector"
                                            type="text"
                                            class="mt-1 block w-full"
                                            v-model="kycForm.sector"
                                        />
                                    </div>

                                    <!-- Correo Electrónico -->
                                    <div>
                                        <InputLabel for="correoelectronico" value="Correo Electrónico" />
                                        <TextInput
                                            id="correoelectronico"
                                            type="email"
                                            class="mt-1 block w-full"
                                            v-model="kycForm.email_client"
                                        />
                                    </div>

                                    <!-- Enviar A -->
                                    <div>
                                        <InputLabel for="direccionparaenviarproductos" value="Enviar A" />
                                        <select
                                            id="direccionparaenviarproductos"
                                            v-model="kycForm.direccionparaenviarproductos"
                                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50"
                                        >
                                            <option value="">Seleccione...</option>
                                            <option value="Trabajo">Trabajo</option>
                                            <option value="Correo electrónico">Correo electrónico</option>
                                            <option value="Residencia">Residencia</option>
                                        </select>
                                    </div>

                                    <!-- Actividad Económica -->
                                    <div>
                                        <InputLabel for="informacionactividadeconomica" value="Actividad Económica" />
                                        <select
                                            id="informacionactividadeconomica"
                                            v-model="kycForm.informacionactividadeconomica"
                                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50"
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
                                    </div>

                                    <!-- Otro Actividad Económica (si selecciona Otro) -->
                                    <div v-if="kycForm.informacionactividadeconomica === 'Otro'">
                                        <InputLabel for="otroactividadeconomica" value="Especifique Actividad Económica" />
                                        <TextInput
                                            id="otroactividadeconomica"
                                            type="text"
                                            class="mt-1 block w-full"
                                            v-model="kycForm.otroactividadeconomica"
                                        />
                                    </div>

                                    <!-- Ingresos Actividad Principal -->
                                    <div>
                                        <InputLabel for="informacionfinanciera" value="Ingresos Actividad Principal" />
                                        <TextInput
                                            id="informacionfinanciera"
                                            type="text"
                                            class="mt-1 block w-full"
                                            v-model="kycForm.informacionfinanciera"
                                        />
                                    </div>

                                    <!-- Otros Ingresos Mensuales -->
                                    <div>
                                        <InputLabel for="otrosingresos" value="Otros Ingresos Mensuales" />
                                        <TextInput
                                            id="otrosingresos"
                                            type="text"
                                            class="mt-1 block w-full"
                                            v-model="kycForm.otrosingresos"
                                        />
                                    </div>

                                    <!-- Actividad Económica de Otros Ingresos -->
                                    <div>
                                        <InputLabel for="actividadeconomicadeotrosingresos" value="Actividad Económica de Otros Ingresos" />
                                        <TextInput
                                            id="actividadeconomicadeotrosingresos"
                                            type="text"
                                            class="mt-1 block w-full"
                                            v-model="kycForm.actividadeconomicadeotrosingresos"
                                        />
                                    </div>

                                    <!-- Maneja Recursos Públicos -->
                                    <div>
                                        <InputLabel for="recursospublicos" value="Maneja Recursos Públicos" />
                                        <select
                                            id="recursospublicos"
                                            v-model="kycForm.recursospublicos"
                                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50"
                                        >
                                            <option value="">Seleccione...</option>
                                            <option value="Si">Si</option>
                                            <option value="No">No</option>
                                        </select>
                                    </div>

                                    <!-- Respuesta Afirmativa Uno (si selecciona Si) -->
                                    <div v-if="kycForm.recursospublicos === 'Si'">
                                        <InputLabel for="respuestaafirmativauno" value="Especifique Recursos Públicos" />
                                        <TextInput
                                            id="respuestaafirmativauno"
                                            type="text"
                                            class="mt-1 block w-full"
                                            v-model="kycForm.respuestaafirmativauno"
                                        />
                                    </div>

                                    <!-- Posee Poder Público -->
                                    <div>
                                        <InputLabel for="poderpublico" value="Posee algún grado de Poder" />
                                        <select
                                            id="poderpublico"
                                            v-model="kycForm.poderpublico"
                                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50"
                                        >
                                            <option value="">Seleccione...</option>
                                            <option value="Si">Si</option>
                                            <option value="No">No</option>
                                        </select>
                                    </div>

                                    <!-- Persona Pública -->
                                    <div>
                                        <InputLabel for="personareconocida" value="Persona Pública" />
                                        <select
                                            id="personareconocida"
                                            v-model="kycForm.personareconocida"
                                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50"
                                        >
                                            <option value="">Seleccione...</option>
                                            <option value="Si">Si</option>
                                            <option value="No">No</option>
                                        </select>
                                    </div>

                                    <!-- Familia Pública -->
                                    <div>
                                        <InputLabel for="afirmativaderespuesta" value="Familia Pública" />
                                        <select
                                            id="afirmativaderespuesta"
                                            v-model="kycForm.afirmativaderespuesta"
                                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50"
                                        >
                                            <option value="">Seleccione...</option>
                                            <option value="Si">Si</option>
                                            <option value="No">No</option>
                                        </select>
                                    </div>

                                    <!-- Solicitud de Seguro -->
                                    <div>
                                        <InputLabel for="solicituddeseguro" value="Solicitud de Seguro" />
                                        <select
                                            id="solicituddeseguro"
                                            v-model="kycForm.solicituddeseguro"
                                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50"
                                        >
                                            <option value="">Seleccione...</option>
                                            <option value="Ramo">Ramo</option>
                                            <option value="Personas">Personas</option>
                                            <option value="Generales">Generales</option>
                                            <option value="Fianza">Fianza</option>
                                            <option value="Otro">Otro</option>
                                        </select>
                                    </div>

                                    <!-- Fecha -->
                                    <div>
                                        <InputLabel for="fecha" value="Fecha" />
                                        <input
                                            id="fecha"
                                            type="date"
                                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50 bg-gray-100"
                                            v-model="fechaDate"
                                            readonly
                                        />
                                    </div>
                                </div>

                                <!-- Botones -->
                                <div class="flex justify-end gap-3 mt-6">
                                    <button
                                        type="button"
                                        @click="cerrarModal"
                                        class="px-4 py-2 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300"
                                    >
                                        Cancelar
                                    </button>
                                    <PrimaryButton type="submit" :disabled="kycForm.processing">
                                        <span v-if="kycForm.processing" class="flex items-center gap-2">
                                            <LoadingSpinner size="sm" color="white" />
                                            Enviando...
                                        </span>
                                        <span v-else>Enviar KYC</span>
                                    </PrimaryButton>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </Transition>
    </AuthenticatedLayout>
</template>
