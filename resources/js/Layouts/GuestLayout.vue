<script setup>
import { onMounted, ref } from 'vue';
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import LoadingSpinner from '@/Components/LoadingSpinner.vue';
import { Link, router } from '@inertiajs/vue3';

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
    <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-secondary relative">
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
                class="fixed inset-0 bg-secondary bg-opacity-75 z-50 flex items-center justify-center"
            >
                <div class="flex flex-col items-center gap-4">
                    <LoadingSpinner size="lg" color="white" />
                    <p class="text-white text-lg font-medium">Cargando...</p>
                </div>
            </div>
        </Transition>
        <div>
            <Link href="/">
                <ApplicationLogo class="w-20 h-20 fill-current text-white" />
            </Link>
        </div>

        <div
            class="w-full sm:max-w-md mt-6 px-6 py-4 bg-white shadow-md overflow-hidden sm:rounded-lg"
        >
            <slot />
        </div>
    </div>
</template>
