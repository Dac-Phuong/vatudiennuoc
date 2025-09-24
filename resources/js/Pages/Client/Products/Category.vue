<script setup>
import { computed, ref } from 'vue'
import ClientLayout from '@/Layouts/ClientLayout.vue'
import { Head, router } from '@inertiajs/vue3'
import ProductCard from '@/Components/client/Product/ProductCard.vue'
import ProductList from '@/Components/client/Product/ProductList.vue'
import Breadcrumbs from '@/Components/client/Breadcrumbs.vue'
import formatNumber from '@/utils/formatNumber'
const props = defineProps({
    categories: Array,
    category: Object,
    products: Object,
    brands: Array,
    filters: Object,
})
// Reactive data
const viewMode = ref('grid')
const showDialog = ref(false)

const localFilters = ref({
    search: props.filters?.search || '',
    category_id: props.filters?.category_id || null,
    price_min: props.filters?.price_min || 0,
    price_max: props.filters?.price_max || 10000000,
    brand_id: props.filters?.brand_id || [],
    rating: props.filters?.rating || null,
    sortField: props.filters?.sortField || 'created_at',
    sortOrder: props.filters?.sortOrder || -1,
    per_page: props.filters?.per_page || 12,
})

// Sort options map đúng backend
const sortOptions = [
    { label: 'Tên A-Z', value: { field: 'name', order: 1 } },
    { label: 'Giá thấp đến cao', value: { field: 'price', order: 1 } },
    { label: 'Giá cao đến thấp', value: { field: 'price', order: -1 } },
    { label: 'Mới nhất', value: { field: 'created_at', order: -1 } },
]


// Xóa bộ lọc
const clearFilters = () => {
    localFilters.value = {
        search: '',
        category_id: null,
        price_min: 0,
        price_max: 10000000,
        brand_id: [],
        rating: null,
        sortField: 'created_at',
        sortOrder: -1,
        per_page: 12,
    }
    applyFilters()
}

// Gửi filters sang backend
const applyFilters = (page = 1) => {
    showDialog.value = false
    router.get(route('products.category', props.category.slug), {
        ...localFilters.value,
        page
    }, {
        preserveState: true,
        replace: true
    })
}

const selectedSortOption = computed(() =>
    sortOptions.find(
        opt =>
            opt.value.field === localFilters.sortField &&
            opt.value.order === localFilters.sortOrder
    )
)

function handleSortChange(val) {
    localFilters.sortField = val.value.field
    localFilters.sortOrder = val.value.order
    applyFilters()
}

</script>
<template>

    <Head :title="category.name">
        <meta name="description" :content="'Sản phẩm thuộc danh mục ' + category.name" />
        <meta property="og:title" :content="category.name" />
        <meta property="og:description" :content="'Sản phẩm thuộc danh mục ' + category.name" />
        <meta property="og:image" :content="category.thumbnail" />
        <meta property="og:url" :content="route('products.category', category.slug)" />
        <meta name="twitter:card" content="summary_large_image" />
        <meta name="twitter:title" :content="category.name" />
        <meta name="twitter:description" :content="'Sản phẩm thuộc danh mục ' + category.name" />
        <meta name="twitter:image" :content="category.thumbnail" />
        <meta name="keywords" :content="category.name" />
    </Head>
    <ClientLayout>
        <div class="min-h-screen bg-gray-100">
            <!-- Breadcrumb -->
            <Breadcrumbs :items="[{ label: category.name }]" />
            <!-- Header -->
            <div class="max-w-7xl mx-auto px-4 py-6">
                <div class="flex gap-2">
                    <!-- Sidebar Filters -->
                    <div class="w-[260px] flex-shrink-0 hidden md:block">
                        <div class="bg-white rounded-lg shadow-sm border p-4 sticky top-28">
                            <h2 class="text-lg font-semibold text-gray-900 mb-4">Bộ Lọc</h2>
                            <!-- Search -->
                            <div class="mb-6">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Tìm kiếm</label>
                                <InputText v-model="localFilters.search" placeholder="Tìm sản phẩm..." class="w-full" />
                            </div>
                            <!-- Price -->
                            <div class="mb-6">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Khoảng giá</label>
                                <Slider v-model="localFilters.priceRange" :min="0" :max="10000000" :step="100000" range
                                    class="w-full"
                                    @update:modelValue="([min, max]) => { localFilters.price_min = min; localFilters.price_max = max }" />
                                <div class="flex justify-between text-sm text-gray-600 mt-2">
                                    <span>{{ formatNumber(localFilters.price_min) }}</span>
                                    <span>{{ formatNumber(localFilters.price_max) }}</span>
                                </div>
                            </div>

                            <!-- Brands -->
                            <div class="mb-6">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Thương hiệu</label>
                                <div class="space-y-2">
                                    <div v-for="brand in brands" :key="brand.id" class="flex items-center">
                                        <Checkbox v-model="localFilters.brand_id" :inputId="`brand-${brand.id}`"
                                            :value="brand.id" />
                                        <label :for="`brand-${brand.id}`" class="ml-2 text-sm text-gray-700">{{
                                            brand.name }}</label>
                                    </div>
                                </div>
                            </div>

                            <!-- Clear / Apply -->
                            <Button size="small" label="Xóa bộ lọc" severity="secondary" outlined class="w-full mb-3"
                                @click="clearFilters" />
                            <Button size="small" label="Áp dụng bộ lọc" severity="primary" class="w-full"
                                @click="applyFilters()" />
                        </div>
                    </div>

                    <!-- Products Area -->
                    <div class="flex-1">
                        <!-- Toolbar -->
                        <div class="bg-white rounded-lg shadow-sm border p-4 mb-4">
                            <div class="flex items-center justify-between flex-wrap gap-4">
                                <span class="text-sm text-gray-600">Hiển thị {{ products.total }} sản phẩm</span>
                                <div class="flex items-center gap-4">
                                    <!-- View -->
                                    <div class="flex border rounded-lg overflow-hidden">
                                        <Button icon="pi pi-th-large"
                                            :class="viewMode === 'grid' ? 'bg-emerald-500 text-white' : 'bg-gray-100'"
                                            @click="viewMode = 'grid'" text />
                                        <Button icon="pi pi-list"
                                            :class="viewMode === 'list' ? 'bg-emerald-500 text-white' : 'bg-gray-100'"
                                            @click="viewMode = 'list'" text />
                                        <div class="block md:hidden">
                                            <Button icon="pi pi-filter" @click="showDialog = true" text
                                                v-tooltip.top="'Hiện bộ lọc'" />
                                        </div>
                                    </div>
                                    <!-- Sort -->
                                    <Dropdown :modelValue="selectedSortOption || sortOptions[0]" :options="sortOptions"
                                        optionLabel="label" class="w-48" @update:modelValue="handleSortChange" />
                                </div>
                            </div>
                        </div>

                        <!-- Products Grid/List -->
                        <template v-if="products.data.length === 0">
                            <div class="text-center text-gray-500 py-12 text-lg">Không có sản phẩm</div>
                        </template>
                        <template v-else>
                            <div v-if="viewMode === 'grid'"
                                class="grid grid-cols-2 md:grid-cols-2 lg:grid-cols-4 gap-2">
                                <ProductCard v-for="product in products.data" :key="product.id" :product="product" />
                            </div>
                            <div v-else class="space-y-4 mb-6">
                                <ProductList v-for="product in products.data" :key="product.id" :product="product" />
                            </div>
                        </template>
                        <!-- Pagination -->
                        <div class="flex justify-center mt-6">
                            <Paginator :first="(products.current_page - 1) * products.per_page"
                                :rows="products.per_page" :totalRecords="products.total"
                                :rowsPerPageOptions="[12, 24, 48]"
                                template="FirstPageLink PrevPageLink PageLinks NextPageLink LastPageLink RowsPerPageDropdown"
                                @page="({ page, rows }) => { localFilters.per_page = rows; applyFilters(page + 1); }" />
                        </div>
                    </div>
                </div>
            </div>
            <!-- Filter Dialog -->
            <Dialog v-model:visible="showDialog" modal :style="{ width: '25rem' }" class="custom-dialog">
                <div class="bg-white rounded-lg shadow-sm p-2 sticky top-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Bộ Lọc</h2> <!-- Search Filter -->
                    <div class="mb-6"> <label class="block text-sm font-medium text-gray-700 mb-2">Tìm kiếm</label>
                        <InputText v-model="localFilters.search" placeholder="Tìm kiếm sản phẩm..." class="w-full" />
                    </div> <!-- Category Filter -->
                    <!-- Price Range -->
                    <div class="mb-6"> <label class="block text-sm font-medium text-gray-700 mb-2">Khoảng giá</label>
                        <div class="px-2">
                            <Slider v-model="localFilters.priceRange" :min="0" :max="10000000" :step="100000" range
                                class="w-full" />
                            <div class="flex justify-between text-sm text-gray-600 mt-2"> <span>{{
                                formatNumber(localFilters.price_min) }}</span> <span>{{
                                        formatNumber(localFilters.price_max) }}</span> </div>
                        </div>
                    </div> <!-- Brand Filter -->
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Thương hiệu</label>
                        <div class="space-y-2">
                            <div v-for="brand in brands" :key="brand.id" class="flex items-center">
                                <Checkbox v-model="localFilters.brand_id" :inputId="`brand-${brand.id}`"
                                    :value="brand.id" />
                                <label :for="`brand-${brand.id}`" class="ml-2 text-sm text-gray-700">{{
                                    brand.name }}</label>
                            </div>
                        </div>
                    </div>
                    <!-- Clear Filters -->
                    <Button size="small" label="Xóa bộ lọc" severity="secondary" outlined class="w-full mb-3"
                        @click="clearFilters" />
                    <Button size="small" label="Áp dụng bộ lọc" severity="primary" class="w-full "
                        @click="applyFilters" />
                </div>
            </Dialog>
        </div>
    </ClientLayout>
</template>
