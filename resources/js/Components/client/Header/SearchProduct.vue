<script setup>
import { Link } from "@inertiajs/vue3"
import { ref, watch } from "vue"
import axios from "axios"

const searchQuery = ref("")
const searchResults = ref([])
const loading = ref(false)
let timeout = null

// Gọi API search (debounce 500ms)
watch(searchQuery, (newVal) => {
    loading.value = true
    clearTimeout(timeout)
    if (!newVal) {
        searchResults.value = []
        return
    }
    timeout = setTimeout(async () => {
        try {
            loading.value = true
            const res = await axios.get("/api/products/search", {
                params: { search: newVal }
            })
            searchResults.value = res.data.data
        } catch (e) {
            searchResults.value = []
        } finally {
            loading.value = false
        }
    }, 500)
})
</script>

<template>
    <!-- Search box -->
    <div class="hidden md:flex flex-1 max-w-2xl mx-8 relative">
        <div class="hidden md:flex flex-1 max-w-2xl mx-8 relative">
            <IconField class="flex-1">
                <InputIcon class="pi pi-search" />
                <InputText v-model="searchQuery" placeholder="Tìm kiếm sản phẩm..." class="flex-1 w-full rounded-lg" />
            </IconField>
            <!-- Box kết quả search -->
            <div v-if="searchQuery"
                class="absolute top-full left-0 w-full bg-white shadow-lg border mt-1 rounded-lg z-50">
                <!-- Loading -->
                <div v-if="loading" class="p-3 text-center text-gray-500">
                    Đang tìm kiếm...
                </div>
                <ul v-else-if="searchResults.length > 0" class="max-h-64 overflow-y-auto divide-y divide-gray-100">
                    <Link :href="`/san-pham/${product.slug}`" v-for="product in searchResults" :key="product.id"
                        class="px-4 py-2 hover:bg-gray-100 cursor-pointer flex items-center">
                    <img :src="product.thumbnail ?? '/placeholder.png'" alt="thumb"
                        class="w-10 h-10 object-cover rounded mr-3" />
                    <span class="text-gray-800">
                        {{ product.name }}
                    </span>
                    </Link>
                </ul>
                <!-- Không có kết quả -->
                <div v-else class="p-3 text-center text-gray-500">
                    Không tìm thấy sản phẩm nào
                </div>
            </div>
        </div>
    </div>
</template>
