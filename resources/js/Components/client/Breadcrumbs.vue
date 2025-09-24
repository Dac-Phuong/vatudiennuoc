<template>
    <div class="bg-white border-b border-gray-200 hidden md:block">
        <div class="max-w-7xl mx-auto">
            <Breadcrumb :model="breadcrumbItems">
                <template #item="{ item, props }">
                    <Link v-if="item.route" :href="item.route" v-bind="props.action"
                        class="flex items-center space-x-2 px-3 py-2 rounded-lg  transition-all duration-200 group">
                    <i v-if="item.icon" :class="item.icon" class="text-gray-500 transition-colors duration-200"></i>
                    <span class="font-medium text-sm">{{ item.label }}</span>
                    </Link>
                    <span v-else v-bind="props.action"
                        class="flex items-center space-x-2  text-gray-800 cursor-default">
                        <i v-if="item.icon" :class="item.icon" class="text-primary"></i>
                        <span class="font-semibold text-sm">{{ item.label }}</span>
                    </span>
                </template>
            </Breadcrumb>
        </div>
    </div>
</template>

<script setup>
import { Link } from '@inertiajs/vue3'
import { computed } from 'vue'
// Props nhận từ component cha
const props = defineProps({
    items: {
        type: Array,
        required: true,
        default: () => []
    },
    showHome: {
        type: Boolean,
        default: true
    },

})

const breadcrumbItems = computed(() => {
    let items = [...props.items]
    const homeItem = {
        label: 'Trang chủ',
        icon: 'pi pi-home',
        route: '/'
    }
    if (items.length === 0 || items[0].route !== '/') {
        items.unshift(homeItem)
    }

    return items
})
</script>
<style>
.p-breadcrumb {
    padding: 8px !important;
}
</style>