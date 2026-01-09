<script setup>
import { ref } from 'vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import LoadingSpinner from '@/Components/LoadingSpinner.vue';
import { Head, Link, router } from '@inertiajs/vue3';

defineProps({
    employees: {
        type: Object,
        required: true,
    },
});

const deletingId = ref(null);

const deleteEmployee = (employeeId) => {
    if (confirm('¿Estás seguro de que deseas eliminar este empleado?')) {
        deletingId.value = employeeId;
        router.delete(route('employees.destroy', employeeId), {
            onFinish: () => {
                deletingId.value = null;
            },
        });
    }
};
</script>

<template>
    <Head title="Empleados" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl text-secondary leading-tight">Gestión de Empleados</h2>
                <Link :href="route('employees.create')">
                    <PrimaryButton>Crear Empleado</PrimaryButton>
                </Link>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div v-if="$page.props.flash?.success" class="mb-4 p-4 bg-green-100 text-green-700 rounded">
                            {{ $page.props.flash.success }}
                        </div>

                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-secondary">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">
                                            ID
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">
                                            Nombre
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">
                                            Email
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">
                                            Departamento
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">
                                            Posición
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">
                                            Código
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">
                                            Empleado Principal
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">
                                            Fecha de Registro
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">
                                            Acciones
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <tr v-for="employee in employees.data" :key="employee.id" class="hover:bg-gray-50">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-secondary">
                                            {{ employee.id }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-secondary">
                                            {{ employee.name }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-accent">
                                            {{ employee.email }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-secondary">
                                            {{ employee.department }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-secondary">
                                            {{ employee.position }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-secondary">
                                            {{ employee.code_employee || '-' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                                            <span
                                                v-if="employee.major_employee"
                                                class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800"
                                            >
                                                Sí
                                            </span>
                                            <span
                                                v-else
                                                class="px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800"
                                            >
                                                No
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-accent">
                                            {{ new Date(employee.created_at).toLocaleDateString('es-ES') }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            <div class="flex items-center gap-3">
                                                <Link
                                                    :href="route('employees.edit', employee.id)"
                                                    class="text-primary hover:text-primary-dark transition-colors"
                                                    title="Editar"
                                                >
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                    </svg>
                                                </Link>
                                                <button
                                                    @click="deleteEmployee(employee.id)"
                                                    :disabled="deletingId === employee.id"
                                                    class="text-red-600 hover:text-red-800 transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
                                                    title="Eliminar"
                                                >
                                                    <LoadingSpinner v-if="deletingId === employee.id" size="sm" color="gray" />
                                                    <svg v-else class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                    </svg>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <div v-if="employees.links && employees.links.length > 3" class="mt-4 flex justify-center">
                            <div class="flex space-x-2">
                                <Link
                                    v-for="link in employees.links"
                                    :key="link.label"
                                    :href="link.url"
                                    :class="[
                                        'px-3 py-2 rounded-md text-sm',
                                        link.active
                                            ? 'bg-primary text-white'
                                            : 'bg-gray-200 text-secondary hover:bg-gray-300',
                                        !link.url ? 'opacity-50 cursor-not-allowed' : ''
                                    ]"
                                    v-html="link.label"
                                />
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
