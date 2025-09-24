<script setup>
import { reactive, ref } from 'vue'
import ProductCard from './ProductCard.vue';
import { Link } from '@inertiajs/vue3';
const props = defineProps({
    ProductBestSelling: Object
})

</script>
<template>
    <section class="py-8">
        <div class="max-w-7xl mx-auto px-4">
            <div class="bg-white p-4 md:p-6 rounded-2xl shadow-md">
                <div class=" pb-2">
                    <h3 class="border-b-4 border-primary inline-block w-full">
                        <span class="text-2xl px-6 py-2 font-bold section-title-main bg-primary text-white">
                            TOP SẢN PHẨM BÁN CHẠY
                        </span>
                    </h3>
                </div>
                <div class="grid grid-cols-2 md:grid-cols-2 lg:grid-cols-4 gap-2 md:gap-4">
                    <ProductCard v-for="p in ProductBestSelling" :key="p.id" :product="p"
                        @view="(product) => console.log('Xem chi tiết:', product)" />
                </div>
                <Link href="/san-pham" class="flex justify-center mt-6">
                <Button class="px-4 py-2 text-white rounded flex items-center gap-2">
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