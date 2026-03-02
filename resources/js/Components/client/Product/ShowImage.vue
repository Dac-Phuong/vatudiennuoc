<script setup>
import { computed } from 'vue'

const props = defineProps({
    src: {
        type: String,
        required: true
    },
    alt: {
        type: String,
        default: ''
    },
    class: {
        type: String,
        default: ''
    },
    type: {
        type: Number,
        default: 1
    }
})

const imageUrl = computed(() => {
    if (!props.src) return ''
    return `${import.meta.env.VITE_API_URL}/storage/${props.src}`
})
</script>

<template>
    <img v-if="props.type === 1" :src="imageUrl" :alt="alt" :class="props.class" />
    <Image v-else :src="imageUrl" :alt="alt" :class="props.class" />
</template>