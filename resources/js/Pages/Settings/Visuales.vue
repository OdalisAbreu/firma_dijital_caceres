<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import LoadingSpinner from '@/Components/LoadingSpinner.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({
    settings: {
        type: Object,
        default: () => ({}),
    },
});

const form = useForm({
    logo: null,
    favicon: null,
    apple_touch_icon: null,
});

const previews = ref({
    logo: null,
    favicon: null,
    apple_touch_icon: null,
});

const onFileChange = (field, event) => {
    const file = event.target.files[0] || null;
    form[field] = file;
    previews.value[field] = file ? URL.createObjectURL(file) : null;
};

const submit = () => {
    form.post(route('settings.visuales.update'), {
        preserveScroll: true,
        forceFormData: true,
        onSuccess: () => {
            form.reset();
            previews.value = { logo: null, favicon: null, apple_touch_icon: null };
        },
    });
};
</script>

<template>
    <Head title="Configuración Visual" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-secondary dark:text-white leading-tight">Configuración Visual</h2>
        </template>

        <div class="py-12">
            <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div v-if="$page.props.flash?.success" class="mb-4 p-4 bg-green-100 dark:bg-green-900 text-green-700 dark:text-green-300 rounded">
                            {{ $page.props.flash.success }}
                        </div>

                        <form @submit.prevent="submit" class="space-y-8">
                            <!-- Logo -->
                            <div>
                                <InputLabel for="logo" value="Logo de la Empresa" />
                                <p class="text-xs text-gray-500 dark:text-gray-400 mb-2">
                                    Se muestra en el login y en la barra de navegación. Formatos: PNG, JPG, SVG, WEBP (máx. 2MB).
                                </p>
                                <div class="flex items-center gap-4">
                                    <div class="w-20 h-20 flex items-center justify-center bg-gray-100 dark:bg-gray-900 rounded-md overflow-hidden border border-gray-300 dark:border-gray-700">
                                        <img
                                            v-if="previews.logo || settings.logo_url"
                                            :src="previews.logo || settings.logo_url"
                                            alt="Logo actual"
                                            class="max-w-full max-h-full object-contain"
                                        />
                                        <span v-else class="text-xs text-gray-400">Sin logo</span>
                                    </div>
                                    <input
                                        id="logo"
                                        type="file"
                                        accept=".png,.jpg,.jpeg,.svg,.webp"
                                        class="block w-full text-sm text-secondary dark:text-gray-300 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:bg-primary file:text-white hover:file:bg-primary-dark"
                                        @change="onFileChange('logo', $event)"
                                    />
                                </div>
                                <InputError class="mt-2" :message="form.errors.logo" />
                            </div>

                            <!-- Favicon -->
                            <div>
                                <InputLabel for="favicon" value="Favicon" />
                                <p class="text-xs text-gray-500 dark:text-gray-400 mb-2">
                                    Ícono que aparece en la pestaña del navegador. Formatos: ICO, PNG, SVG (máx. 512KB).
                                </p>
                                <div class="flex items-center gap-4">
                                    <div class="w-12 h-12 flex items-center justify-center bg-gray-100 dark:bg-gray-900 rounded-md overflow-hidden border border-gray-300 dark:border-gray-700">
                                        <img
                                            v-if="previews.favicon || settings.favicon_url"
                                            :src="previews.favicon || settings.favicon_url"
                                            alt="Favicon actual"
                                            class="max-w-full max-h-full object-contain"
                                        />
                                        <span v-else class="text-[10px] text-gray-400 text-center">Sin favicon</span>
                                    </div>
                                    <input
                                        id="favicon"
                                        type="file"
                                        accept=".ico,.png,.svg"
                                        class="block w-full text-sm text-secondary dark:text-gray-300 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:bg-primary file:text-white hover:file:bg-primary-dark"
                                        @change="onFileChange('favicon', $event)"
                                    />
                                </div>
                                <InputError class="mt-2" :message="form.errors.favicon" />
                            </div>

                            <!-- Apple Touch Icon -->
                            <div>
                                <InputLabel for="apple_touch_icon" value="Apple Touch Icon" />
                                <p class="text-xs text-gray-500 dark:text-gray-400 mb-2">
                                    Ícono usado al agregar el sitio a la pantalla de inicio en dispositivos Apple. Formatos: PNG, JPG (máx. 1MB, recomendado 180x180px).
                                </p>
                                <div class="flex items-center gap-4">
                                    <div class="w-16 h-16 flex items-center justify-center bg-gray-100 dark:bg-gray-900 rounded-md overflow-hidden border border-gray-300 dark:border-gray-700">
                                        <img
                                            v-if="previews.apple_touch_icon || settings.apple_touch_icon_url"
                                            :src="previews.apple_touch_icon || settings.apple_touch_icon_url"
                                            alt="Apple touch icon actual"
                                            class="max-w-full max-h-full object-contain"
                                        />
                                        <span v-else class="text-[10px] text-gray-400 text-center">Sin ícono</span>
                                    </div>
                                    <input
                                        id="apple_touch_icon"
                                        type="file"
                                        accept=".png,.jpg,.jpeg"
                                        class="block w-full text-sm text-secondary dark:text-gray-300 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:bg-primary file:text-white hover:file:bg-primary-dark"
                                        @change="onFileChange('apple_touch_icon', $event)"
                                    />
                                </div>
                                <InputError class="mt-2" :message="form.errors.apple_touch_icon" />
                            </div>

                            <div class="flex items-center justify-end">
                                <PrimaryButton :disabled="form.processing">
                                    <span v-if="form.processing" class="flex items-center gap-2">
                                        <LoadingSpinner size="sm" color="white" />
                                        Guardando...
                                    </span>
                                    <span v-else>Guardar Cambios</span>
                                </PrimaryButton>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
