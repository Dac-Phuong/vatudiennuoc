<script setup>
import ProductCard from './ProductCard.vue';
import { Link } from '@inertiajs/vue3';

const props = defineProps({
    productBrands: Object
})

</script>
<template>
    <section class="pb-8">
        <div class="max-w-7xl mx-auto px-4">
            <div class="bg-white p-4 md:p-6 rounded-2xl shadow-md">
                <!-- Tiêu đề -->
                <div class="pb-2">
                    <h3 class="border-b-4 border-primary inline-block w-full">
                        <span class="text-2xl px-6 py-2 font-bold section-title-main bg-primary text-white">
                            TOP THƯƠNG HIỆU NỔI BẬT
                        </span>
                    </h3>
                </div>
                <div class="relative w-full h-[150px] md:h-[340px] mb-6 rounded-xl overflow-hidden shadow-lg"
                    v-if="productBrands.logo">
                    <img :src="productBrands.logo" alt="Banner thương hiệu" class="w-full h-full  md:object-cover" />

                </div>
                <!-- Danh sách sản phẩm -->
                <div class="grid grid-cols-2 md:grid-cols-2 lg:grid-cols-4 gap-2 md:gap-4 mt-4">
                    <ProductCard v-for="p in productBrands.products" :key="p.id" :product="p" />
                </div>

                <!-- Nút xem thêm -->
                <Link href="/san-pham" class="flex justify-center mt-6">
                <Button class="px-4 py-2 text-white rounded flex items-center gap-2 bg-primary hover:bg-primary/90">
                    Xem thêm
                    <i class="pi pi-arrow-right"></i>
                </Button>
                </Link>
            </div>
        </div>
    </section>

</template>
<style scoped>
.group:hover .text-4xl {
    transform: scale(1.05);
    transition: transform 0.3s ease;
}

.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
</style>