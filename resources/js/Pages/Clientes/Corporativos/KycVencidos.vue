<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import LoadingSpinner from '@/Components/LoadingSpinner.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import InputLabel from '@/Components/InputLabel.vue';
import { Head, router, useForm, usePage } from '@inertiajs/vue3';
import { ref, computed } from 'vue';

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
    totalesPorEstado: {
        type: Object,
        default: () => ({}),
    },
});

const form = useForm({
    cnomcliente: props.filters.cnomcliente || '',
    estado_formulario: props.filters.estado_formulario || 'VENCIDO (NO REMITIDO)',
    page: 1,
    page_size: 10,
});

const estadoFormularioOptions = [
    { value: 'VENCIDO (NO REMITIDO)', label: 'Vencido (No Remitido)' },
    { value: 'VENCIDO', label: 'Vencido' },
    { value: 'VIGENTE', label: 'Vigente' },
    { value: 'PENDIENTE DE REMISION', label: 'Pendiente de Remisión' },
    { value: 'SIN CLASIFICAR', label: 'Sin Clasificar' },
];

const aplicarFiltros = () => {
    form.page = 1; // Resetear a la primera página
    form.get(route('clientes.corporativos.kyc-vencidos'), {
        preserveState: true,
        preserveScroll: true,
    });
};

const limpiarFiltros = () => {
    form.cnomcliente = '';
    form.estado_formulario = 'VENCIDO (NO REMITIDO)';
    form.page = 1;
    form.page_size = 10;
    form.get(route('clientes.corporativos.kyc-vencidos'), {
        preserveState: false,
        preserveScroll: false,
    });
};

const cambiarPagina = (page) => {
    form.page = page;
    form.get(route('clientes.corporativos.kyc-vencidos'), {
        preserveState: true,
        preserveScroll: true,
    });
};

const cambiarPageSize = (newSize) => {
    form.page_size = newSize;
    form.page = 1; // Resetear a la primera página
    form.get(route('clientes.corporativos.kyc-vencidos'), {
        preserveState: true,
        preserveScroll: true,
    });
};

const getCedulaRncPasaporte = (cliente) => {
    if (cliente.cedula) {
        return cliente.cedula;
    }
    if (cliente.rnc) {
        return cliente.rnc;
    }
    if (cliente.pasaporte) {
        return cliente.pasaporte;
    }
    return '-';
};

const getNombreCompleto = (cliente) => {
    return `${cliente.nombre || ''} ${cliente.apellido || ''}`.trim();
};

const formatDate = (dateString) => {
    if (!dateString) return 'N/A';
    try {
        const date = new Date(dateString);
        return date.toLocaleDateString('es-ES', {
            year: 'numeric',
            month: '2-digit',
            day: '2-digit',
        });
    } catch {
        return dateString;
    }
};

const getEstadoBadgeClass = (estado) => {
    const estadoLower = estado?.toLowerCase() || '';
    if (estadoLower.includes('vencido')) {
        return 'px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300';
    } else if (estadoLower.includes('vigente')) {
        return 'px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300';
    } else if (estadoLower.includes('pendiente')) {
        return 'px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300';
    }
    return 'px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300';
};

const page = usePage();

const userDisplay = computed(() => {
    const user = page.props.auth?.user;
    if (user && user.name && user.email) {
        return `${user.name} - ${user.email}`;
    }
    return '';
});

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
    provinciaresidencia: '',
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
    poderpublicodescripcion: '',
    personareconocida: '',
    influenciapublicadescripcion: '',
    afirmativaderespuesta: '',
    afirmativaderespuestadescripcion: '',
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

const getTipoTercero = (cliente) => {
    // Determinar el tipo de tercero activo basándose en los campos tipo_tercero_*
    if (cliente.tipo_tercero_tomador === 1) return 'Tomador';
    if (cliente.tipo_tercero_asegurado === 1) return 'Asegurado';
    if (cliente.tipo_tercero_beneficiario === 1) return 'Beneficiario';
    if (cliente.tipo_tercero_afianzado === 1) return 'Afianzado';
    if (cliente.tipo_tercero_proveedor === 1) return 'Proveedor';
    if (cliente.tipo_tercero_empleado === 1) return 'Empleado';
    if (cliente.tipo_tercero_apoderado === 1) return 'Apoderado';
    return '';
};

const getEnviarA = (cliente) => {
    // Determinar dónde enviar basándose en los campos autorizo_envio_*
    // Prioridad: Correo > Trabajo > Oficina principal > Domicilio > Reticencia
    if (cliente.autorizo_envio_correo === 1) return 'Correo Electrónico';
    if (cliente.autorizo_envio_trabajo === 1) return 'Trabajo';
    if (cliente.autorizo_envio_oficina_principal === 1) return 'Oficina principal';
    if (cliente.autorizo_envio_domicilio === 1) return 'Domicilio';
    if (cliente.autorizo_envio_recidencia === 1) return 'Reticencia';
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
    kycForm.email_client = cliente.correo_electronico || '';
    kycForm.sucursal = cliente.sucursal || 'Principal';
    kycForm.tipo_tercero = getTipoTercero(cliente);
    kycForm.tipodeidentificacion = getTipoIdentificacion(cliente);
    kycForm.numero_identificacion = getNumeroIdentificacion(cliente);
    kycForm.sexo = cliente.sexo || '';
    kycForm.fechanacimiento = formatDateForInput(cliente.fecha_nacimiento);
    kycForm.profesion = cliente.profesion || '';
    kycForm.ocupacioncargo = cliente.ocupacion || '';
    kycForm.empresa = cliente.empresa || '';
    kycForm.direcciondondelabora = cliente.dirreccion_lavoral || '';
    kycForm.ciudad = cliente.ciudad_oficina || '';
    kycForm.provincia = cliente.provincia_empresa || '';
    kycForm.telefono = cliente.telefono_empresa || '';
    kycForm.celular = cliente.numero_telefono || '';
    kycForm.ciudadresidencia = cliente.ciudad_recidencia || '';
    kycForm.provinciaresidencia = cliente.provincia_recidiencia || '';
    kycForm.pais = cliente.pais_recidencia || '';
    kycForm.direccionresidencia = cliente.dirreccion_recidencia || '';
    kycForm.sector = cliente.sector || '';
    kycForm.ciudaddenacimiento = cliente.ciudad_nacimiento || '';
    kycForm.provinciadenacimiento = cliente.porvincia_nacimiento || '';
    kycForm.nacionalidad = cliente.nacionalidad || '';
    // Usar fecha_venc_form si existe (fecha de vencimiento del formulario), sino usar fecha_vencimiento (fecha de vencimiento de identificación)
    kycForm.fechadevencimiento = formatDateForInput(cliente.fecha_venc_form || cliente.fecha_vencimiento);
    // Campos adicionales del endpoint de KYC
    kycForm.informacionactividadeconomica = cliente.actividad_economica || '';
    kycForm.informacionfinanciera = cliente.ingesos_mensuales ? String(cliente.ingesos_mensuales) : '';
    kycForm.direccionparaenviarproductos = getEnviarA(cliente);
    kycForm.otrosingresos = cliente.otros_ingresos ? String(cliente.otros_ingresos) : '';
    kycForm.actividadeconomicadeotrosingresos = cliente.otros_ingresos_actividad || '';
    // Mapear recursos_publicos
    if (cliente.recursos_publicos && cliente.recursos_publicos !== '' && cliente.recursos_publicos !== 'No') {
        kycForm.recursospublicos = 'Si';
        kycForm.respuestaafirmativauno = cliente.recursos_publicos_descripcion || '';
    } else if (cliente.recursos_publicos === 'No') {
        kycForm.recursospublicos = 'No';
    } else {
        kycForm.recursospublicos = '';
    }
    // Mapear poder_publico
    if (cliente.poder_publico && cliente.poder_publico !== '' && cliente.poder_publico !== 'No') {
        kycForm.poderpublico = 'Si';
        kycForm.poderpublicodescripcion = cliente.poder_publico_descripcion || '';
    } else if (cliente.poder_publico === 'No') {
        kycForm.poderpublico = 'No';
    } else {
        kycForm.poderpublico = '';
    }
    // Mapear influencia_publica
    if (cliente.influencia_publica && cliente.influencia_publica !== '' && cliente.influencia_publica !== 'No') {
        kycForm.personareconocida = 'Si';
        kycForm.influenciapublicadescripcion = cliente.influencia_publica_descripcion || '';
    } else if (cliente.influencia_publica === 'No') {
        kycForm.personareconocida = 'No';
    } else {
        kycForm.personareconocida = '';
    }
    // Mapear afirmativo_familia
    if (cliente.afirmativo_familia && cliente.afirmativo_familia !== '' && cliente.afirmativo_familia !== 'No') {
        kycForm.afirmativaderespuesta = 'Si';
        kycForm.afirmativaderespuestadescripcion = cliente.afirmativo_familia_descripcion || '';
    } else if (cliente.afirmativo_familia === 'No') {
        kycForm.afirmativaderespuesta = 'No';
    } else {
        kycForm.afirmativaderespuesta = '';
    }
    // Mapear solicitud de seguro: construir string basado en los campos booleanos
    const solicitudesSeguro = [];
    if (cliente.solicitud_seguro_persona === 1) solicitudesSeguro.push('Personas');
    if (cliente.solicitud_seguro_generales === 1) solicitudesSeguro.push('Generales');
    if (cliente.solicitud_seguro_fianza === 1) solicitudesSeguro.push('Fianza');
    if (cliente.solicitud_seguro_otros && cliente.solicitud_seguro_otros !== '' && cliente.solicitud_seguro_otros !== null) {
        solicitudesSeguro.push(cliente.solicitud_seguro_otros);
    }
    kycForm.solicituddeseguro = solicitudesSeguro.length > 0 ? solicitudesSeguro.join(', ') : '';
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
</script>

<template>
    <Head title="Clientes con KYC Vencidos" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-secondary leading-tight">Clientes con KYC Vencidos</h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <!-- Resumen -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <h3 class="text-lg font-semibold text-secondary dark:text-white">Total de Clientes Corporativos con KYC Vencidos</h3>
                                <p class="text-3xl font-bold text-primary mt-2">{{ total.toLocaleString() }}</p>
                            </div>
                            <div v-if="totalesPorEstado && Object.keys(totalesPorEstado).length > 0" class="text-right">
                                <h4 class="text-sm font-semibold text-secondary dark:text-white mb-2">Totales por Estado:</h4>
                                <div class="space-y-1 text-sm">
                                    <div v-if="totalesPorEstado.vigente !== undefined">
                                        <span class="text-green-600 dark:text-green-400">Vigente:</span> {{ totalesPorEstado.vigente }}
                                    </div>
                                    <div v-if="totalesPorEstado.vencido !== undefined">
                                        <span class="text-red-600 dark:text-red-400">Vencido:</span> {{ totalesPorEstado.vencido }}
                                    </div>
                                    <div v-if="totalesPorEstado.vencido_no_remitido !== undefined">
                                        <span class="text-red-600 dark:text-red-400">Vencido (No Remitido):</span> {{ totalesPorEstado.vencido_no_remitido }}
                                    </div>
                                    <div v-if="totalesPorEstado.pendiente_de_remision !== undefined">
                                        <span class="text-yellow-600 dark:text-yellow-400">Pendiente de Remisión:</span> {{ totalesPorEstado.pendiente_de_remision }}
                                    </div>
                                    <div v-if="totalesPorEstado.sin_clasificar !== undefined">
                                        <span class="text-gray-600 dark:text-gray-400">Sin Clasificar:</span> {{ totalesPorEstado.sin_clasificar }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Filtros -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-secondary dark:text-white mb-4">Filtros de Búsqueda</h3>
                        
                        <div v-if="error" class="mb-4 p-4 bg-red-100 dark:bg-red-900 text-red-700 dark:text-red-300 rounded">
                            {{ error }}
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <!-- Nombre -->
                            <div>
                                <InputLabel for="cnomcliente" value="Nombre (Parcial)" />
                                <TextInput
                                    id="cnomcliente"
                                    type="text"
                                    class="mt-1 block w-full bg-white dark:bg-gray-900"
                                    v-model="form.cnomcliente"
                                    placeholder="Buscar por nombre..."
                                />
                            </div>

                            <!-- Estado Formulario -->
                            <div>
                                <InputLabel for="estado_formulario" value="Estado del Formulario" />
                                <select
                                    id="estado_formulario"
                                    v-model="form.estado_formulario"
                                    class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 rounded-md shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50 bg-white"
                                >
                                    <option v-for="option in estadoFormularioOptions" :key="option.value" :value="option.value">
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
                                class="px-4 py-2 bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-md hover:bg-gray-300 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-gray-500 disabled:opacity-50 disabled:cursor-not-allowed flex items-center gap-2"
                                :disabled="form.processing"
                            >
                                <LoadingSpinner v-if="form.processing" size="sm" color="gray" />
                                Limpiar
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Tabla -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 relative">
                        <!-- Loading Overlay -->
                        <div
                            v-if="form.processing && (!clientes || !clientes.data || clientes.data.length === 0)"
                            class="absolute inset-0 bg-white dark:bg-gray-800 bg-opacity-75 flex items-center justify-center z-10"
                        >
                            <div class="flex flex-col items-center gap-3">
                                <LoadingSpinner size="lg" color="primary" />
                                <p class="text-secondary dark:text-white">Cargando clientes...</p>
                            </div>
                        </div>

                        <div v-if="!form.processing && (!clientes || !clientes.data || clientes.data.length === 0)" class="text-center py-8">
                            <p class="text-gray-500 dark:text-gray-400">No se encontraron clientes con los filtros aplicados.</p>
                        </div>

                        <div v-else class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                <thead class="bg-secondary">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider" style="max-width: 150px; width: 150px;">
                                            Nombre
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">
                                            Identificación
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider" style="max-width: 150px; width: 150px;">
                                            Email
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">
                                            Estado Formulario
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">
                                            Fecha Vencimiento
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">
                                            Días hasta Vencimiento
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">
                                            Acciones
                                        </th>
                                      <!--  <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">
                                            Form. Remitido
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">
                                            Form. Recibido
                                        </th>-->
                                    </tr>
                                </thead>
                                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                    <tr v-for="cliente in clientes.data" :key="cliente.id_client" class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                        <td class="px-6 py-4 text-sm text-secondary dark:text-gray-300" style="max-width: 200px; width: 200px;">
                                            <div 
                                                class="truncate" 
                                                :title="getNombreCompleto(cliente)"
                                            >
                                                {{ getNombreCompleto(cliente) }}
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-secondary dark:text-gray-300">
                                            {{ getCedulaRncPasaporte(cliente) }}
                                        </td>
                                        <td class="px-6 py-4 text-sm text-accent dark:text-blue-400" style="max-width: 200px; width: 200px;">
                                            <div 
                                                class="truncate" 
                                                :title="cliente.correo_electronico || '-'"
                                            >
                                                {{ cliente.correo_electronico || '-' }}
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                                            <span :class="getEstadoBadgeClass(cliente.estado_formulario)">
                                                {{ cliente.estado_formulario || 'N/A' }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-secondary dark:text-gray-300">
                                            {{ formatDate(cliente.fecha_venc_form) }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                                            <span 
                                                :class="{
                                                    'text-red-600 dark:text-red-400 font-semibold': cliente.dias_hasta_vencimiento < 0,
                                                    'text-yellow-600 dark:text-yellow-400 font-semibold': cliente.dias_hasta_vencimiento >= 0 && cliente.dias_hasta_vencimiento <= 30,
                                                    'text-green-600 dark:text-green-400': cliente.dias_hasta_vencimiento > 30
                                                }"
                                            >
                                                {{ cliente.dias_hasta_vencimiento !== undefined ? cliente.dias_hasta_vencimiento : 'N/A' }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            <button
                                                @click="enviarKYC(cliente)"
                                                class="px-3 py-1 bg-primary text-white rounded-md hover:bg-primary-dark focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-2 transition duration-150 ease-in-out"
                                            >
                                                Enviar KYC
                                            </button>
                                        </td>
                                       <!-- <td class="px-6 py-4 whitespace-nowrap text-sm text-secondary dark:text-gray-300">
                                            {{ cliente.formremitido === 1 ? 'Sí' : 'No' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-secondary dark:text-gray-300">
                                            {{ cliente.formrecibido === 1 ? 'Sí' : 'No' }}
                                        </td>-->
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <!-- Paginación -->
                        <div v-if="clientes && clientes.data && clientes.data.length > 0" class="mt-4 flex flex-col sm:flex-row justify-between items-center gap-4">
                            <div class="flex items-center gap-2">
                                <label for="page_size" class="text-sm text-secondary dark:text-white">Registros por página:</label>
                                <div class="relative">
                                    <select
                                        id="page_size"
                                        v-model="form.page_size"
                                        @change="cambiarPageSize(form.page_size)"
                                        :disabled="form.processing"
                                        class="px-6 py-2 border border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 rounded-md shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50 text-sm disabled:opacity-50 disabled:cursor-not-allowed"
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
                                    class="px-3 py-2 bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-md hover:bg-gray-300 dark:hover:bg-gray-600 disabled:opacity-50 disabled:cursor-not-allowed flex items-center gap-2"
                                >
                                    <LoadingSpinner v-if="form.processing && clientes.current_page > 1" size="sm" color="gray" />
                                    Anterior
                                </button>
                                
                                <span class="px-4 py-2 text-sm text-secondary dark:text-white">
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
                                    class="px-3 py-2 bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-md hover:bg-gray-300 dark:hover:bg-gray-600 disabled:opacity-50 disabled:cursor-not-allowed flex items-center gap-2"
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

                    <div class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-4xl sm:w-full">
                        <div class="bg-white dark:bg-gray-800 px-4 pt-5 pb-4 sm:p-6 sm:pb-4 max-h-[90vh] overflow-y-auto">
                            <div class="flex items-center justify-between mb-4">
                                <h3 class="text-lg font-medium text-secondary dark:text-white">Enviar Formulario KYC</h3>
                                <button
                                    @click="cerrarModal"
                                    class="text-gray-400 hover:text-gray-500 dark:hover:text-gray-300"
                                >
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </button>
                            </div>

                            <div v-if="$page.props.flash?.success" class="mb-4 p-4 bg-green-100 dark:bg-green-900 text-green-700 dark:text-green-300 rounded">
                                {{ $page.props.flash.success }}
                            </div>

                            <div v-if="$page.props.errors?.kyc_error" class="mb-4 p-4 bg-red-100 dark:bg-red-900 text-red-700 dark:text-red-300 rounded">
                                {{ $page.props.errors.kyc_error }}
                            </div>

                            <form @submit.prevent="submitKyc" class="space-y-4">
                                <!-- Campos Obligatorios -->
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <!-- Tipo de Persona * -->
                                    <div>
                                        <InputLabel for="tipo_persona" value="Tipo de Persona *" />
                                        <select
                                            id="tipo_persona"
                                            v-model="kycForm.tipo_persona"
                                            class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 rounded-md shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50 bg-white"
                                            required
                                        >
                                            <option value="">Seleccione...</option>
                                            <option value="fisica">Persona Física</option>
                                            <option value="juridica">Persona Jurídica</option>
                                        </select>
                                        <span v-if="kycForm.errors.tipo_persona" class="text-red-600 dark:text-red-400 text-sm">{{ kycForm.errors.tipo_persona }}</span>
                                    </div>

                                    <!-- Tipo de Tercero * -->
                                    <div>
                                        <InputLabel for="tipo_tercero" value="Tipo de Tercero *" />
                                        <select
                                            id="tipo_tercero"
                                            v-model="kycForm.tipo_tercero"
                                            class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 rounded-md shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50 bg-white"
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
                                        <span v-if="kycForm.errors.tipo_tercero" class="text-red-600 dark:text-red-400 text-sm">{{ kycForm.errors.tipo_tercero }}</span>
                                    </div>

                                    <!-- Sucursal * -->
                                    <div>
                                        <InputLabel for="sucursal" value="Sucursal *" />
                                        <select
                                            id="sucursal"
                                            v-model="kycForm.sucursal"
                                            class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 rounded-md shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50 bg-white"
                                            required
                                        >
                                            <option value="">Seleccione...</option>
                                            <option value="Principal">Principal</option>
                                            <option value="Romana">Romana</option>
                                            <option value="Punta Cana">Punta Cana</option>
                                        </select>
                                        <span v-if="kycForm.errors.sucursal" class="text-red-600 dark:text-red-400 text-sm">{{ kycForm.errors.sucursal }}</span>
                                    </div>

                                    <!-- Tipo de Identificación * -->
                                    <div>
                                        <InputLabel for="tipodeidentificacion" value="Tipo de Identificación *" />
                                        <select
                                            id="tipodeidentificacion"
                                            v-model="kycForm.tipodeidentificacion"
                                            class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 rounded-md shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50 bg-white"
                                            required
                                        >
                                            <option value="">Seleccione...</option>
                                            <option value="Cédula">Cédula</option>
                                            <option value="RNC">RNC</option>
                                            <option value="Pasaporte">Pasaporte</option>
                                        </select>
                                        <span v-if="kycForm.errors.tipodeidentificacion" class="text-red-600 dark:text-red-400 text-sm">{{ kycForm.errors.tipodeidentificacion }}</span>
                                    </div>

                                    <!-- Número * -->
                                    <div>
                                        <InputLabel for="numero_identificacion" value="Número *" />
                                        <TextInput
                                            id="numero_identificacion"
                                            type="text"
                                            class="mt-1 block w-full bg-white dark:bg-gray-900"
                                            v-model="kycForm.numero_identificacion"
                                            required
                                        />
                                        <span v-if="kycForm.errors.numero_identificacion" class="text-red-600 dark:text-red-400 text-sm">{{ kycForm.errors.numero_identificacion }}</span>
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
                                        <span v-if="kycForm.errors.fechadevencimiento" class="text-red-600 dark:text-red-400 text-sm">{{ kycForm.errors.fechadevencimiento }}</span>
                                    </div>

                                    <!-- Nombres * -->
                                    <div>
                                        <InputLabel for="name_client" value="Nombres *" />
                                        <TextInput
                                            id="name_client"
                                            type="text"
                                            class="mt-1 block w-full bg-white dark:bg-gray-900"
                                            v-model="kycForm.name_client"
                                            required
                                        />
                                        <span v-if="kycForm.errors.name_client" class="text-red-600 dark:text-red-400 text-sm">{{ kycForm.errors.name_client }}</span>
                                    </div>

                                    <!-- Apellidos * -->
                                    <div>
                                        <InputLabel for="lastname_client" value="Apellidos *" />
                                        <TextInput
                                            id="lastname_client"
                                            type="text"
                                            class="mt-1 block w-full bg-white dark:bg-gray-900"
                                            v-model="kycForm.lastname_client"
                                            required
                                        />
                                        <span v-if="kycForm.errors.lastname_client" class="text-red-600 dark:text-red-400 text-sm">{{ kycForm.errors.lastname_client }}</span>
                                    </div>

                                    <!-- Sexo * -->
                                    <div>
                                        <InputLabel for="sexo" value="Sexo *" />
                                        <select
                                            id="sexo"
                                            v-model="kycForm.sexo"
                                            class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 rounded-md shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50 bg-white"
                                            required
                                        >
                                            <option value="">Seleccione...</option>
                                            <option value="M">Masculino</option>
                                            <option value="F">Femenino</option>
                                        </select>
                                        <span v-if="kycForm.errors.sexo" class="text-red-600 dark:text-red-400 text-sm">{{ kycForm.errors.sexo }}</span>
                                    </div>

                                    <!-- Fecha de Nacimiento -->
                                    <div>
                                        <InputLabel for="fechanacimiento" value="Fecha de Nacimiento" />
                                        <input
                                            id="fechanacimiento"
                                            type="date"
                                            class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 rounded-md shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50 bg-white"
                                            v-model="fechanacimientoDate"
                                        />
                                        <span v-if="kycForm.errors.fechanacimiento" class="text-red-600 dark:text-red-400 text-sm">{{ kycForm.errors.fechanacimiento }}</span>
                                    </div>

                                    <!-- Ciudad de Nacimiento -->
                                    <div>
                                        <InputLabel for="ciudaddenacimiento" value="Ciudad de Nacimiento" />
                                        <TextInput
                                            id="ciudaddenacimiento"
                                            type="text"
                                            class="mt-1 block w-full bg-white dark:bg-gray-900"
                                            v-model="kycForm.ciudaddenacimiento"
                                        />
                                    </div>

                                    <!-- Provincia de Nacimiento -->
                                    <div>
                                        <InputLabel for="provinciadenacimiento" value="Provincia de Nacimiento" />
                                        <TextInput
                                            id="provinciadenacimiento"
                                            type="text"
                                            class="mt-1 block w-full bg-white dark:bg-gray-900"
                                            v-model="kycForm.provinciadenacimiento"
                                        />
                                    </div>

                                    <!-- Nacionalidad -->
                                    <div>
                                        <InputLabel for="nacionalidad" value="Nacionalidad" />
                                        <TextInput
                                            id="nacionalidad"
                                            type="text"
                                            class="mt-1 block w-full bg-white dark:bg-gray-900"
                                            v-model="kycForm.nacionalidad"
                                        />
                                    </div>

                                    <!-- Profesión -->
                                    <div>
                                        <InputLabel for="profesion" value="Profesión" />
                                        <TextInput
                                            id="profesion"
                                            type="text"
                                            class="mt-1 block w-full bg-white dark:bg-gray-900"
                                            v-model="kycForm.profesion"
                                        />
                                        <span v-if="kycForm.errors.profesion" class="text-red-600 dark:text-red-400 text-sm">{{ kycForm.errors.profesion }}</span>
                                    </div>

                                    <!-- Ocupación/Cargo -->
                                    <div>
                                        <InputLabel for="ocupacioncargo" value="Ocupación/Cargo" />
                                        <TextInput
                                            id="ocupacioncargo"
                                            type="text"
                                            class="mt-1 block w-full bg-white dark:bg-gray-900"
                                            v-model="kycForm.ocupacioncargo"
                                        />
                                        <span v-if="kycForm.errors.ocupacioncargo" class="text-red-600 dark:text-red-400 text-sm">{{ kycForm.errors.ocupacioncargo }}</span>
                                    </div>

                                    <!-- Empresa -->
                                    <div>
                                        <InputLabel for="empresa" value="Empresa" />
                                        <TextInput
                                            id="empresa"
                                            type="text"
                                            class="mt-1 block w-full bg-white dark:bg-gray-900"
                                            v-model="kycForm.empresa"
                                        />
                                        <span v-if="kycForm.errors.empresa" class="text-red-600 dark:text-red-400 text-sm">{{ kycForm.errors.empresa }}</span>
                                    </div>

                                    <!-- Dirección donde labora -->
                                    <div>
                                        <InputLabel for="direcciondondelabora" value="Dirección donde labora" />
                                        <TextInput
                                            id="direcciondondelabora"
                                            type="text"
                                            class="mt-1 block w-full bg-white dark:bg-gray-900"
                                            v-model="kycForm.direcciondondelabora"
                                        />
                                        <span v-if="kycForm.errors.direcciondondelabora" class="text-red-600 dark:text-red-400 text-sm">{{ kycForm.errors.direcciondondelabora }}</span>
                                    </div>

                                    <!-- Ciudad -->
                                    <div>
                                        <InputLabel for="ciudad" value="Ciudad" />
                                        <TextInput
                                            id="ciudad"
                                            type="text"
                                            class="mt-1 block w-full bg-white dark:bg-gray-900"
                                            v-model="kycForm.ciudad"
                                        />
                                    </div>

                                    <!-- Provincia -->
                                    <div>
                                        <InputLabel for="provincia" value="Provincia" />
                                        <TextInput
                                            id="provincia"
                                            type="text"
                                            class="mt-1 block w-full bg-white dark:bg-gray-900"
                                            v-model="kycForm.provincia"
                                        />
                                    </div>

                                    <!-- Teléfono -->
                                    <div>
                                        <InputLabel for="telefono" value="Teléfono" />
                                        <TextInput
                                            id="telefono"
                                            type="text"
                                            class="mt-1 block w-full bg-white dark:bg-gray-900"
                                            v-model="kycForm.telefono"
                                        />
                                        <span v-if="kycForm.errors.telefono" class="text-red-600 dark:text-red-400 text-sm">{{ kycForm.errors.telefono }}</span>
                                    </div>

                                    <!-- Ciudad Residencia -->
                                    <div>
                                        <InputLabel for="ciudadresidencia" value="Ciudad Residencia" />
                                        <TextInput
                                            id="ciudadresidencia"
                                            type="text"
                                            class="mt-1 block w-full bg-white dark:bg-gray-900"
                                            v-model="kycForm.ciudadresidencia"
                                        />
                                    </div>

                                    <!-- Provincia Residencia -->
                                    <div>
                                        <InputLabel for="provinciaresidencia" value="Provincia Residencia" />
                                        <TextInput
                                            id="provinciaresidencia"
                                            type="text"
                                            class="mt-1 block w-full bg-white dark:bg-gray-900"
                                            v-model="kycForm.provinciaresidencia"
                                        />
                                    </div>

                                    <!-- País Residencia -->
                                    <div>
                                        <InputLabel for="pais" value="País Residencia" />
                                        <TextInput
                                            id="pais"
                                            type="text"
                                            class="mt-1 block w-full bg-white dark:bg-gray-900"
                                            v-model="kycForm.pais"
                                        />
                                    </div>

                                    <!-- Teléfono Residencia -->
                                    <div>
                                        <InputLabel for="telefonoresidencia" value="Teléfono Residencia" />
                                        <TextInput
                                            id="telefonoresidencia"
                                            type="text"
                                            class="mt-1 block w-full bg-white dark:bg-gray-900"
                                            v-model="kycForm.telefonoresidencia"
                                        />
                                    </div>

                                    <!-- Celular -->
                                    <div>
                                        <InputLabel for="celular" value="Celular" />
                                        <TextInput
                                            id="celular"
                                            type="text"
                                            class="mt-1 block w-full bg-white dark:bg-gray-900"
                                            v-model="kycForm.celular"
                                        />
                                    </div>

                                    <!-- Dirección Residencia -->
                                    <div>
                                        <InputLabel for="direccionresidencia" value="Dirección Residencia" />
                                        <TextInput
                                            id="direccionresidencia"
                                            type="text"
                                            class="mt-1 block w-full bg-white dark:bg-gray-900"
                                            v-model="kycForm.direccionresidencia"
                                        />
                                    </div>

                                    <!-- Sector -->
                                    <div>
                                        <InputLabel for="sector" value="Sector" />
                                        <TextInput
                                            id="sector"
                                            type="text"
                                            class="mt-1 block w-full bg-white dark:bg-gray-900"
                                            v-model="kycForm.sector"
                                        />
                                    </div>

                                    <!-- Correo Electrónico -->
                                    <div>
                                        <InputLabel for="correoelectronico" value="Correo Electrónico" />
                                        <TextInput
                                            id="correoelectronico"
                                            type="email"
                                            class="mt-1 block w-full bg-white dark:bg-gray-900"
                                            v-model="kycForm.email_client"
                                        />
                                    </div>

                                    <!-- Enviar A -->
                                    <div>
                                        <InputLabel for="direccionparaenviarproductos" value="Enviar A" />
                                        <select
                                            id="direccionparaenviarproductos"
                                            v-model="kycForm.direccionparaenviarproductos"
                                            class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 rounded-md shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50 bg-white"
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
                                    </div>

                                    <!-- Otro Actividad Económica (si selecciona Otro) -->
                                    <div v-if="kycForm.informacionactividadeconomica === 'Otro'">
                                        <InputLabel for="otroactividadeconomica" value="Especifique Actividad Económica" />
                                        <TextInput
                                            id="otroactividadeconomica"
                                            type="text"
                                            class="mt-1 block w-full bg-white dark:bg-gray-900"
                                            v-model="kycForm.otroactividadeconomica"
                                        />
                                    </div>

                                    <!-- Ingresos Actividad Principal -->
                                    <div>
                                        <InputLabel for="informacionfinanciera" value="Ingresos Actividad Principal" />
                                        <TextInput
                                            id="informacionfinanciera"
                                            type="text"
                                            class="mt-1 block w-full bg-white dark:bg-gray-900"
                                            v-model="kycForm.informacionfinanciera"
                                        />
                                    </div>

                                    <!-- Otros Ingresos Mensuales -->
                                    <div>
                                        <InputLabel for="otrosingresos" value="Otros Ingresos Mensuales" />
                                        <TextInput
                                            id="otrosingresos"
                                            type="text"
                                            class="mt-1 block w-full bg-white dark:bg-gray-900"
                                            v-model="kycForm.otrosingresos"
                                        />
                                    </div>

                                    <!-- Actividad Económica de Otros Ingresos -->
                                    <div>
                                        <InputLabel for="actividadeconomicadeotrosingresos" value="Actividad Económica de Otros Ingresos" />
                                        <TextInput
                                            id="actividadeconomicadeotrosingresos"
                                            type="text"
                                            class="mt-1 block w-full bg-white dark:bg-gray-900"
                                            v-model="kycForm.actividadeconomicadeotrosingresos"
                                        />
                                    </div>

                                    <!-- Maneja Recursos Públicos -->
                                    <div>
                                        <InputLabel for="recursospublicos" value="Maneja Recursos Públicos" />
                                        <select
                                            id="recursospublicos"
                                            v-model="kycForm.recursospublicos"
                                            class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 rounded-md shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50 bg-white"
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
                                            class="mt-1 block w-full bg-white dark:bg-gray-900"
                                            v-model="kycForm.respuestaafirmativauno"
                                        />
                                    </div>

                                    <!-- Posee Poder Público -->
                                    <div>
                                        <InputLabel for="poderpublico" value="Posee algún grado de Poder" />
                                        <select
                                            id="poderpublico"
                                            v-model="kycForm.poderpublico"
                                            class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 rounded-md shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50 bg-white"
                                        >
                                            <option value="">Seleccione...</option>
                                            <option value="Si">Si</option>
                                            <option value="No">No</option>
                                        </select>
                                    </div>

                                    <!-- Descripción Poder Público (si selecciona Si) -->
                                    <div v-if="kycForm.poderpublico === 'Si'">
                                        <InputLabel for="poderpublicodescripcion" value="Especifique Poder Público" />
                                        <TextInput
                                            id="poderpublicodescripcion"
                                            type="text"
                                            class="mt-1 block w-full bg-white dark:bg-gray-900"
                                            v-model="kycForm.poderpublicodescripcion"
                                        />
                                    </div>

                                    <!-- Persona Pública -->
                                    <div>
                                        <InputLabel for="personareconocida" value="Persona Pública" />
                                        <select
                                            id="personareconocida"
                                            v-model="kycForm.personareconocida"
                                            class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 rounded-md shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50 bg-white"
                                        >
                                            <option value="">Seleccione...</option>
                                            <option value="Si">Si</option>
                                            <option value="No">No</option>
                                        </select>
                                    </div>

                                    <!-- Descripción Influencia Pública (si selecciona Si) -->
                                    <div v-if="kycForm.personareconocida === 'Si'">
                                        <InputLabel for="influenciapublicadescripcion" value="Especifique Influencia Pública" />
                                        <TextInput
                                            id="influenciapublicadescripcion"
                                            type="text"
                                            class="mt-1 block w-full bg-white dark:bg-gray-900"
                                            v-model="kycForm.influenciapublicadescripcion"
                                        />
                                    </div>

                                    <!-- Familia Pública -->
                                    <div>
                                        <InputLabel for="afirmativaderespuesta" value="Familia Pública" />
                                        <select
                                            id="afirmativaderespuesta"
                                            v-model="kycForm.afirmativaderespuesta"
                                            class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 rounded-md shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50 bg-white"
                                        >
                                            <option value="">Seleccione...</option>
                                            <option value="Si">Si</option>
                                            <option value="No">No</option>
                                        </select>
                                    </div>

                                    <!-- Descripción Afirmativo Familia (si selecciona Si) -->
                                    <div v-if="kycForm.afirmativaderespuesta === 'Si'">
                                        <InputLabel for="afirmativaderespuestadescripcion" value="Especifique Afirmativo Familia" />
                                        <TextInput
                                            id="afirmativaderespuestadescripcion"
                                            type="text"
                                            class="mt-1 block w-full bg-white dark:bg-gray-900"
                                            v-model="kycForm.afirmativaderespuestadescripcion"
                                        />
                                    </div>

                                    <!-- Solicitud de Seguro -->
                                    <div>
                                        <InputLabel for="solicituddeseguro" value="Solicitud de Seguro" />
                                        <TextInput
                                            id="solicituddeseguro"
                                            type="text"
                                            class="mt-1 block w-full bg-white dark:bg-gray-900"
                                            v-model="kycForm.solicituddeseguro"
                                            placeholder="Ej: Personas, Generales, Fianza"
                                        />
                                    </div>

                                    <!-- Fecha -->
                                    <div>
                                        <InputLabel for="fecha" value="Fecha" />
                                        <input
                                            id="fecha"
                                            type="date"
                                            class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 rounded-md shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50 bg-gray-100 dark:bg-gray-700"
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
                                        class="px-4 py-2 bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-md hover:bg-gray-300 dark:hover:bg-gray-600"
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
