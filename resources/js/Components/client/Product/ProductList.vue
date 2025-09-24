<script setup>
import { Link } from '@inertiajs/vue3';
import { ref } from 'vue'

const props = defineProps({
    product: {
        type: Object,
        required: true,
    },
})

const showDialog = ref(false)

</script>
<template>
    <div class="bg-white rounded-lg shadow-sm border p-4 flex gap-4 hover:shadow-md transition-shadow duration-200">
        <Image :src="product.thumbnail" :alt="product.name" class="w-24 h-24 object-cover rounded-lg flex-shrink-0" />
        <div class="flex-1">
            <div class="flex justify-between items-start">
                <div>
                    <h3 class="font-semibold text-gray-900 mb-1 text-base">{{ product.name }}
                    </h3>
                    <p class="text-sm text-gray-600 mb-2">{{ product.category.name }}</p>
                    <Button icon="pi pi-eye" severity="secondary" label="Xem nhanh" size="small"
                        @click="showDialog = true" class="bg-white/80 backdrop-blur-sm mb-2" />
                </div>
                <div class="text-right">
                    <div class="text-sm text-red-600 mb-2">
                        Liên hệ để biết giá
                    </div>
                    <Link :href="route('products.detail', product.slug)">
                    <Button label="Xem chi tiết" icon="pi pi-eye" size="small"
                        class="w-full mt-3 bg-blue-500 border-blue-500 hover:bg-blue-600" />
                    </Link>
                </div>
            </div>
        </div>
    </div>
    <Dialog v-model:visible="showDialog" header="" modal :style="{ width: '60rem' }" class="custom-dialog">
        <div class="flex flex-col md:flex-row gap-6 py-4">
            <div class="flex-shrink-0 flex justify-center items-center h-full w-full md:w-1/2">
                <Image preview :src="product.thumbnail" :alt="product.name"
                    class="w-full h-[450px] object-cover shadow-lg border img-fluid" />
            </div>
            <div class="flex flex-col justify-between w-full md:w-1/2">
                <div>
                    <h2 class="text-2xl font-bold mb-4 text-gray-800">{{ product.name }}</h2>
                    <div class="flex items-center gap-2 mb-3">
                        <span v-if="product.discount"
                            class="bg-red-500 text-white px-3 py-1 rounded-full text-xs font-semibold shadow">
                            -{{ product.discount }}%
                        </span>
                        <span class="bg-green-100 text-green-700 px-2 py-1 rounded text-xs font-medium">
                            Còn hàng
                        </span>

                    </div>
                    <div class="mb-4 text-gray-700 text-sm leading-relaxed">
                        <strong class="block mb-1 text-gray-900">Mô tả sản phẩm:</strong>
                        <span>{{ product.description || 'Chưa có mô tả cho sản phẩm này.' }}</span>
                    </div>
                    <div class="mb-4">
                        <span class="text-lg font-bold text-red-500">Giá: Liên hệ</span>
                    </div>
                    <div class="mt-4 flex flex-col gap-2">
                        <a href="tel:0123456789"
                            class="bg-blue-500 hover:bg-blue-600 text-white py-2 rounded-lg text-center font-semibold transition">
                            <i class="pi pi-phone mr-2"></i>Gọi ngay: 0123 456 789
                        </a>
                        <Link :href="'mailto:info@yourshop.com'"
                            class="bg-gray-100 hover:bg-gray-200 text-gray-800 py-2 rounded-lg text-center font-medium transition">
                        <i class="pi pi-envelope mr-2"></i>Gửi email: info@yourshop.com
                        </Link>
                        <Link href="/contact"
                            class="text-white py-2 bg-primary rounded-lg text-center font-semibold transition">
                        <i class="pi pi-comments mr-2"></i>Liên hệ tư vấn
                        </Link>
                    </div>
                </div>

            </div>
        </div>
    </Dialog>

</template>

<style>
.custom-dialog .p-dialog-header {
    position: relative !important;
    padding: 5px;
}

.custom-dialog .p-dialog-header-actions {
    position: absolute;
    top: 0.5rem !important;
    right: 0.5rem !important;
    z-index: 10 !important;
}


span.p-image.p-component.w-full.object-cover.shadow-lg.border.img-fluid img {
    width: 100% !important;
}

.p-card-body {
    padding: 0 !important;
}
</style>