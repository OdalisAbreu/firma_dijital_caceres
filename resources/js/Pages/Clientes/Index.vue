<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import LoadingSpinner from '@/Components/LoadingSpinner.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import InputLabel from '@/Components/InputLabel.vue';
import { Head, router, useForm } from '@inertiajs/vue3';
import { ref, watch } from 'vue';

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

const enviarKYC = (cliente) => {
    // TODO: Implementar lógica para enviar KYC
    alert(`Enviar KYC para: ${cliente.nombre} ${cliente.apellido}`);
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
    </AuthenticatedLayout>
</template>
