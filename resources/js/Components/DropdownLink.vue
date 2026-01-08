<script setup>
import { computed } from 'vue';
import { Link } from '@inertiajs/vue3';

const props = defineProps({
    href: {
        type: String,
        required: true,
    },
    active: {
        type: Boolean,
        default: false,
    },
});

const classes = computed(() => {
    const baseClasses = 'block w-full px-4 py-2 text-start text-sm leading-5 transition duration-150 ease-in-out';
    if (props.active) {
        return `${baseClasses} bg-primary/10 text-primary font-medium`;
    }
    return `${baseClasses} text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800 focus:outline-none focus:bg-gray-100 dark:focus:bg-gray-800`;
});
</script>

<template>
    <Link
        :href="href"
        :class="classes"
    >
        <slot />
    </Link>
</template>
