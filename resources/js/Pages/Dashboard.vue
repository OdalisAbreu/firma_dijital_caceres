<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, router, useForm } from '@inertiajs/vue3';
import { computed } from 'vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import LoadingSpinner from '@/Components/LoadingSpinner.vue';

const props = defineProps({
    kycSends: {
        type: Array,
        default: () => [],
    },
    totalCompletados: {
        type: Number,
        default: 0,
    },
    totalPendientes: {
        type: Number,
        default: 0,
    },
    totalRespondidosClientes: {
        type: Number,
        default: 0,
    },
    totalRespondidosColaborador: {
        type: Number,
        default: 0,
    },
});

const formatDate = (dateString) => {
    if (!dateString) return 'N/A';
    const date = new Date(dateString);
    return date.toLocaleDateString('es-ES', {
        year: 'numeric',
        month: '2-digit',
        day: '2-digit',
        hour: '2-digit',
        minute: '2-digit',
    });
};

const getStatusBadgeClass = (status) => {
    const statusLower = status?.toLowerCase() || '';
    if (statusLower === 'completed' || statusLower === 'signed' || statusLower === 'responsed') {
        return 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300';
    } else if (statusLower === 'received' || statusLower === 'pending') {
        return 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300';
    } else if (statusLower === 'waiting') {
        return 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300';
    } else {
        return 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300';
    }
};

const getStatusLabel = (status) => {
    const statusLower = status?.toLowerCase() || '';
    if (statusLower === 'completed') {
        return 'Completado';
    } else if (statusLower === 'responsed') {
        return 'Respondido';
    } else if (statusLower === 'received') {
        return 'Recibido';
    } else if (statusLower === 'pending') {
        return 'Pendiente';
    } else if (statusLower === 'waiting') {
        return 'Esperando';
    } else if (statusLower === 'signed') {
        return 'Firmado';
    }
    return status || 'Desconocido';
};

const form = useForm({});

const actualizarEstados = () => {
    if (confirm('¿Estás seguro de que deseas actualizar los estados de los KYC pendientes?')) {
        form.post(route('dashboard.actualizar-estados'), {
            preserveScroll: true,
            onSuccess: () => {
                // El mensaje de éxito se mostrará automáticamente desde el flash
            },
        });
    }
};
</script>

<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-secondary dark:text-white leading-tight">Dashboard</h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <!-- Estadísticas -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
                         <!-- Total Respondidos por Clientes -->
                         <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 bg-blue-100 dark:bg-blue-900 rounded-md p-3">
                                    <svg class="h-6 w-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                    </svg>
                                </div>
                                <div class="ml-4">
                                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Total KYC Respondidos por Clientes</p>
                                    <p class="text-2xl font-semibold text-secondary dark:text-white">{{ totalRespondidosClientes }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Total Respondidos por Colaborador -->
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 bg-purple-100 dark:bg-purple-900 rounded-md p-3">
                                    <svg class="h-6 w-6 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                                    </svg>
                                </div>
                                <div class="ml-4">
                                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Total KYC Respondidos por Colaborador</p>
                                    <p class="text-2xl font-semibold text-secondary dark:text-white">{{ totalRespondidosColaborador }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Total Completados -->
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 bg-green-100 dark:bg-green-900 rounded-md p-3">
                                    <svg class="h-6 w-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                                <div class="ml-4">
                                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Total KYC Completados</p>
                                    <p class="text-2xl font-semibold text-secondary dark:text-white">{{ totalCompletados }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Total Pendientes -->
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 bg-yellow-100 dark:bg-yellow-900 rounded-md p-3">
                                    <svg class="h-6 w-6 text-yellow-600 dark:text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                                <div class="ml-4">
                                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Total KYC Pendientes</p>
                                    <p class="text-2xl font-semibold text-secondary dark:text-white">{{ totalPendientes }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tabla de KYC -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-lg font-semibold text-secondary dark:text-white">Lista de Envíos KYC</h3>
                            <PrimaryButton 
                                @click="actualizarEstados" 
                                :disabled="form.processing"
                                class="flex items-center gap-2"
                            >
                                <span v-if="form.processing" class="flex items-center gap-2">
                                    <LoadingSpinner size="sm" color="white" />
                                    Actualizando...
                                </span>
                                <span v-else class="flex items-center gap-2">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                    </svg>
                                    Actualizar Estados
                                </span>
                            </PrimaryButton>
                        </div>

                        <div v-if="$page.props.flash?.success" class="mb-4 p-4 bg-green-100 dark:bg-green-900 text-green-700 dark:text-green-300 rounded">
                            {{ $page.props.flash.success }}
                        </div>
                        
                        <div v-if="kycSends.length === 0" class="text-center py-8">
                            <p class="text-gray-500 dark:text-gray-400">No hay envíos KYC registrados.</p>
                        </div>

                        <div v-else class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                <thead class="bg-secondary dark:bg-gray-700">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">
                                            Nombre Cliente
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">
                                            Correo Cliente
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">
                                            Estado
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">
                                            Estado Cliente
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">
                                            Estado Colaborador
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">
                                            Colaborador Firmante
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">
                                            Fecha de Envío
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                    <tr v-for="kyc in kycSends" :key="kyc.id" class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-secondary dark:text-gray-300">
                                            {{ kyc.name_client }} {{ kyc.lastname_client }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-secondary dark:text-gray-300">
                                            {{ kyc.email }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span
                                                :class="getStatusBadgeClass(kyc.kyc_status)"
                                                class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full"
                                            >
                                                {{ getStatusLabel(kyc.kyc_status) }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span
                                                :class="getStatusBadgeClass(kyc.status_firmante01)"
                                                class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full"
                                            >
                                                {{ getStatusLabel(kyc.status_firmante01) }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span
                                                :class="getStatusBadgeClass(kyc.status_firmante02)"
                                                class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full"
                                            >
                                                {{ getStatusLabel(kyc.status_firmante02) }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-secondary dark:text-gray-300">
                                            {{ kyc.user?.name || 'N/A' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-secondary dark:text-gray-300">
                                            {{ formatDate(kyc.created_at) }}
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
