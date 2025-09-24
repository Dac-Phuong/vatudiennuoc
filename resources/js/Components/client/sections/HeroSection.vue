<script setup>
import { ref } from "vue";
import Carousel from "primevue/carousel";
import { Link, usePage } from "@inertiajs/vue3";

const { categories, settings } = usePage().props;
const banners = ref(JSON.parse(settings?.banners) || [
    { id: 3, image: "https://bizweb.dktcdn.net/100/168/021/themes/833502/assets/slider_1.jpg?1753857903607" },
]);

</script>

<template>
    <section class="relative overflow-hidden pt-4">
        <div class="relative max-w-7xl mx-auto px-4 pb-8">
            <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
                <!-- Bên trái: Danh mục -->
                <aside
                    class="hidden lg:block lg:col-span-1 bg-white backdrop-blur-xl rounded-2xl py-4 px-4 border shadow-md">
                    <h2 class="text-xl font-semibold mb-4 ml-2 ">
                        Danh mục sản phẩm
                    </h2>
                    <ul class="space-y-3 max-h-[360px] overflow-y-auto custom-scrollbar">
                        <li v-for="cat in categories" :key="cat.id"
                            class="flex items-center justify-between rounded-lg hover:bg-primary hover:text-white cursor-pointer transition">
                            <Link :href="route('products.category', cat.slug)" class="flex-1 px-3 py-2">
                            <span>{{ cat.name }}</span>
                            </Link>
                            <!-- <i class="pi pi-angle-right text-emerald-400"></i> -->
                        </li>
                    </ul>
                </aside>
                <!-- Bên phải: Carousel -->
                <div class="lg:col-span-3 banner-carousel">
                    <Carousel :value="banners" :numVisible="1" :numScroll="1" circular :autoplayInterval="3000"
                        class="rounded-2xl overflow-hidden shadow-md bg-white relative md:h-[450px] h-auto"
                        :showNavigators="false">
                        <template #item="slotProps">
                            <img :src="slotProps.data.image || slotProps.data.url" alt="banner"
                                class="w-full object-cover h-full" />
                        </template>
                    </Carousel>
                </div>
            </div>
        </div>
    </section>
</template>
<style>
.custom-scrollbar::-webkit-scrollbar {
    width: 6px !important;
    height: 6px !important;
}

.custom-scrollbar::-webkit-scrollbar-thumb {
    background: #e5e7eb;
    border-radius: 4px;
}

.custom-scrollbar::-webkit-scrollbar-track {
    background: transparent;
}

.custom-scrollbar {
    scrollbar-width: thin;
    scrollbar-color: var(--primary-color) transparent;
}

.banner-carousel .p-carousel-indicator-list {
    position: absolute !important;
    bottom: -5px !important;
    left: 50%;
    transform: translateX(-50%);
}

.p-carousel-content-container,
.p-carousel-content {
    height: 100% !important;
    overflow: hidden !important;
}
</style>