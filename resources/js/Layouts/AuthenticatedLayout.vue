<script setup>
import { ref, onMounted } from 'vue';
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import Dropdown from '@/Components/Dropdown.vue';
import DropdownLink from '@/Components/DropdownLink.vue';
import LoadingSpinner from '@/Components/LoadingSpinner.vue';
import NavLink from '@/Components/NavLink.vue';
import ResponsiveNavLink from '@/Components/ResponsiveNavLink.vue';
import { Link, router } from '@inertiajs/vue3';
import { useDarkMode } from '@/composables/useDarkMode';

const showingNavigationDropdown = ref(false);
const { isDark, toggleDarkMode } = useDarkMode();
const isLoading = ref(false);

onMounted(() => {
    router.on('start', () => {
        isLoading.value = true;
    });

    router.on('finish', () => {
        isLoading.value = false;
    });
});
</script>

<template>
    <div>
        <!-- Loading Overlay Global -->
        <Transition
            enter-active-class="transition-opacity duration-200"
            enter-from-class="opacity-0"
            enter-to-class="opacity-100"
            leave-active-class="transition-opacity duration-200"
            leave-from-class="opacity-100"
            leave-to-class="opacity-0"
        >
            <div
                v-if="isLoading"
                class="fixed inset-0 bg-white dark:bg-gray-900 bg-opacity-75 dark:bg-opacity-75 z-50 flex items-center justify-center"
            >
                <div class="flex flex-col items-center gap-4">
                    <LoadingSpinner size="lg" color="primary" />
                    <p class="text-secondary dark:text-white text-lg font-medium">Cargando...</p>
                </div>
            </div>
        </Transition>

        <div class="min-h-screen bg-gray-50 dark:bg-gray-900">
            <nav class="bg-secondary border-b border-secondary-dark">
                <!-- Primary Navigation Menu -->
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="flex justify-between h-16">
                        <div class="flex">
                            <!-- Logo -->
                            <div class="shrink-0 flex items-center">
                                <Link :href="route('dashboard')">
                                    <ApplicationLogo
                                        class="block h-9 w-auto fill-current text-white"
                                    />
                                </Link>
                            </div>

                            <!-- Navigation Links -->
                            <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex items-center">
                                <NavLink :href="route('dashboard')" :active="route().current('dashboard')">
                                    Dashboard
                                </NavLink>
                                
                                <!-- Envío Individual Dropdown -->
                                <div class="relative -my-px">
                                    <Dropdown align="left" width="48">
                                        <template #trigger>
                                            <button
                                                type="button"
                                                :class="[
                                                    'inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium leading-5 transition duration-150 ease-in-out focus:outline-none',
                                                    route().current('clientes.*')
                                                        ? 'border-primary text-white focus:border-primary-light'
                                                        : 'border-transparent text-accent hover:text-white hover:border-accent-light focus:text-white focus:border-accent-light'
                                                ]"
                                            >
                                                Envío Individual
                                                <svg
                                                    class="ms-1 -me-0.5 h-4 w-4"
                                                    xmlns="http://www.w3.org/2000/svg"
                                                    viewBox="0 0 20 20"
                                                    fill="currentColor"
                                                >
                                                    <path
                                                        fill-rule="evenodd"
                                                        d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                                        clip-rule="evenodd"
                                                    />
                                                </svg>
                                            </button>
                                        </template>

                                        <template #content>
                                            <DropdownLink :href="route('clientes.index')" :active="route().current('clientes.*')">
                                                Clientes
                                            </DropdownLink>
                                            <DropdownLink :href="route('kyc-send.create')" :active="route().current('kyc-send.*')">
                                                Nuevo Cliente
                                            </DropdownLink>
                                        </template>
                                    </Dropdown>
                                </div>

                                <!-- Configuración Dropdown - Solo para administradores -->
                                <div v-if="$page.props.auth.user?.role === 'administrador'" class="relative -my-px">
                                    <Dropdown align="left" width="48">
                                        <template #trigger>
                                            <button
                                                type="button"
                                                :class="[
                                                    'inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium leading-5 transition duration-150 ease-in-out focus:outline-none',
                                                    route().current('admin.*') || route().current('employees.*')
                                                        ? 'border-primary text-white focus:border-primary-light'
                                                        : 'border-transparent text-accent hover:text-white hover:border-accent-light focus:text-white focus:border-accent-light'
                                                ]"
                                            >
                                                Configuración
                                                <svg
                                                    class="ms-1 -me-0.5 h-4 w-4"
                                                    xmlns="http://www.w3.org/2000/svg"
                                                    viewBox="0 0 20 20"
                                                    fill="currentColor"
                                                >
                                                    <path
                                                        fill-rule="evenodd"
                                                        d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                                        clip-rule="evenodd"
                                                    />
                                                </svg>
                                            </button>
                                        </template>

                                        <template #content>
                                            <DropdownLink :href="route('admin.index')" :active="route().current('admin.*')">
                                                Administración
                                            </DropdownLink>
                                            <DropdownLink :href="route('employees.index')" :active="route().current('employees.*')">
                                                Empleados
                                            </DropdownLink>
                                        </template>
                                    </Dropdown>
                                </div>
                            </div>
                        </div>

                        <div class="hidden sm:flex sm:items-center sm:ms-6">
                            <!-- Dark Mode Toggle -->
                            <button
                                @click="toggleDarkMode"
                                type="button"
                                class="inline-flex items-center justify-center p-2 rounded-md text-white hover:text-accent-light hover:bg-secondary-light focus:outline-none focus:ring-2 focus:ring-inset focus:ring-primary transition ease-in-out duration-150 me-3"
                                :title="isDark ? 'Cambiar a modo claro' : 'Cambiar a modo oscuro'"
                            >
                                <!-- Icono Sol (modo claro) -->
                                <svg
                                    v-if="isDark"
                                    class="h-5 w-5"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24"
                                    xmlns="http://www.w3.org/2000/svg"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"
                                    />
                                </svg>
                                <!-- Icono Luna (modo oscuro) -->
                                <svg
                                    v-else
                                    class="h-5 w-5"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24"
                                    xmlns="http://www.w3.org/2000/svg"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"
                                    />
                                </svg>
                            </button>

                            <!-- Settings Dropdown -->
                            <div class="ms-3 relative">
                                <Dropdown align="right" width="48">
                                    <template #trigger>
                                        <span class="inline-flex rounded-md">
                                            <button
                                                type="button"
                                                class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white hover:text-accent-light focus:outline-none transition ease-in-out duration-150"
                                            >
                                                {{ $page.props.auth.user.name }}

                                                <svg
                                                    class="ms-2 -me-0.5 h-4 w-4"
                                                    xmlns="http://www.w3.org/2000/svg"
                                                    viewBox="0 0 20 20"
                                                    fill="currentColor"
                                                >
                                                    <path
                                                        fill-rule="evenodd"
                                                        d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                                        clip-rule="evenodd"
                                                    />
                                                </svg>
                                            </button>
                                        </span>
                                    </template>

                                    <template #content>
                                        <DropdownLink :href="route('profile.edit')"> Profile </DropdownLink>
                                        <DropdownLink :href="route('logout')" method="post" as="button">
                                            Log Out
                                        </DropdownLink>
                                    </template>
                                </Dropdown>
                            </div>
                        </div>

                        <!-- Hamburger and Dark Mode Toggle (Mobile) -->
                        <div class="-me-2 flex items-center gap-2 sm:hidden">
                            <!-- Dark Mode Toggle (Mobile) -->
                            <button
                                @click="toggleDarkMode"
                                type="button"
                                class="inline-flex items-center justify-center p-2 rounded-md text-white hover:text-accent-light hover:bg-secondary-light focus:outline-none transition duration-150 ease-in-out"
                                :title="isDark ? 'Cambiar a modo claro' : 'Cambiar a modo oscuro'"
                            >
                                <!-- Icono Sol (modo claro) -->
                                <svg
                                    v-if="isDark"
                                    class="h-5 w-5"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24"
                                    xmlns="http://www.w3.org/2000/svg"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"
                                    />
                                </svg>
                                <!-- Icono Luna (modo oscuro) -->
                                <svg
                                    v-else
                                    class="h-5 w-5"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24"
                                    xmlns="http://www.w3.org/2000/svg"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"
                                    />
                                </svg>
                            </button>

                            <button
                                @click="showingNavigationDropdown = !showingNavigationDropdown"
                                class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 dark:text-gray-500 hover:text-gray-500 dark:hover:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-900 focus:outline-none focus:bg-gray-100 dark:focus:bg-gray-900 focus:text-gray-500 dark:focus:text-gray-400 transition duration-150 ease-in-out"
                            >
                                <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                                    <path
                                        :class="{
                                            hidden: showingNavigationDropdown,
                                            'inline-flex': !showingNavigationDropdown,
                                        }"
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M4 6h16M4 12h16M4 18h16"
                                    />
                                    <path
                                        :class="{
                                            hidden: !showingNavigationDropdown,
                                            'inline-flex': showingNavigationDropdown,
                                        }"
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12"
                                    />
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Responsive Navigation Menu -->
                <div
                    :class="{ block: showingNavigationDropdown, hidden: !showingNavigationDropdown }"
                    class="sm:hidden"
                >
                    <div class="pt-2 pb-3 space-y-1">
                        <ResponsiveNavLink :href="route('dashboard')" :active="route().current('dashboard')">
                            Dashboard
                        </ResponsiveNavLink>
                        
                        <!-- Envío Individual -->
                        <div class="px-4 py-2">
                            <div class="font-medium text-base text-white mb-2">Envío Individual</div>
                            <div class="ml-4 space-y-1">
                                <ResponsiveNavLink :href="route('clientes.index')" :active="route().current('clientes.*')">
                                    Clientes
                                </ResponsiveNavLink>
                                <ResponsiveNavLink :href="route('kyc-send.create')" :active="route().current('kyc-send.*')">
                                    Nuevo Cliente
                                </ResponsiveNavLink>
                            </div>
                        </div>
                        
                        <!-- Configuración - Solo para administradores -->
                        <div v-if="$page.props.auth.user?.role === 'administrador'" class="px-4 py-2">
                            <div class="font-medium text-base text-white mb-2">Configuración</div>
                            <div class="ml-4 space-y-1">
                                <ResponsiveNavLink :href="route('admin.index')" :active="route().current('admin.*')">
                                    Administración
                                </ResponsiveNavLink>
                                <ResponsiveNavLink :href="route('employees.index')" :active="route().current('employees.*')">
                                    Empleados
                                </ResponsiveNavLink>
                            </div>
                        </div>
                    </div>

                    <!-- Responsive Settings Options -->
                    <div class="pt-4 pb-1 border-t border-secondary-dark">
                        <div class="px-4">
                            <div class="font-medium text-base text-white">
                                {{ $page.props.auth.user.name }}
                            </div>
                            <div class="font-medium text-sm text-accent">{{ $page.props.auth.user.email }}</div>
                        </div>

                        <div class="mt-3 space-y-1">
                            <ResponsiveNavLink :href="route('profile.edit')"> Profile </ResponsiveNavLink>
                            <ResponsiveNavLink :href="route('logout')" method="post" as="button">
                                Log Out
                            </ResponsiveNavLink>
                        </div>
                    </div>
                </div>
            </nav>

            <!-- Page Heading -->
            <header class="bg-white dark:bg-gray-800 shadow" v-if="$slots.header">
                <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                    <slot name="header" />
                </div>
            </header>

            <!-- Page Content -->
            <main>
                <slot />
            </main>
        </div>
    </div>
</template>
